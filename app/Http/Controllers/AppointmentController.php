<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentBooked;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments in admin panel.
     */
    public function index()
    {
        // Get all appointments with their relationships (including new user/lead links)
        $appointments = Appointment::with(['client', 'schedule', 'services', 'user', 'lead'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Appointments/AppointmentIndex', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Display the specified appointment in admin panel.
     */
    public function show(Appointment $appointment)
    {
        // Load the appointment with its relationships (including new user/lead)
        $appointment->load(['client', 'schedule', 'services', 'user', 'lead']);

        return Inertia::render('Admin/Appointments/AppointmentShow', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Validate the request
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
            'description' => 'nullable|string|max:1000',
        ]);

        // Update the appointment
        $appointment->update($validated);

        return redirect()->back()->with('success', 'Rendez-vous mis à jour avec succès.');
    }

 /**
 * Update the specified appointment in storage.
 */
public function updateStatus(Request $request, Appointment $appointment)
{
    try {
        // Si c'est une mise à jour de statut
        if ($request->has('status')) {
            $request->validate([
                'status' => 'required|string|in:pending,confirmed,cancelled,completed',
            ]);

            $appointment->update(['status' => $request->status]);
            return back();
        }

        return redirect()->back()->with('success', 'Rendez-vous mis à jour avec succès.');

    } catch (\Exception $e) {
        return back()->with('error', 'Erreur lors de la mise à jour du rendez-vous.');
    }
}

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Delete the appointment
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('success', 'Rendez-vous supprimé avec succès.');
    }

    /**
     * Confirm the specified appointment.
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        // Update the appointment status to confirmed
        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Rendez-vous confirmé avec succès.');
    }

    /**
     * Display the appointment booking form.
     */
    public function create()
    {
        // Get all active services
        $services = Service::all();

        // Get all active schedules with available slots
        $schedules = Schedule::where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->whereRaw('(SELECT COUNT(*) FROM appointments WHERE appointments.schedule_id = schedules.id) < schedules.slots')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return Inertia::render('Front/Appointment/Create', [
            'services' => $services,
            'schedules' => $schedules,
            'contactSettings' => $this->getContactSettings()

        ]);
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'schedule_id' => 'required|exists:schedules,id',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check if the schedule is still available
        $schedule = Schedule::findOrFail($validated['schedule_id']);
        $appointmentCount = Appointment::where('schedule_id', $schedule->id)->count();

        if ($appointmentCount >= $schedule->slots) {
            return back()->withErrors([
                'schedule_id' => 'Ce créneau n\'est plus disponible. Veuillez en choisir un autre.',
            ]);
        }

        // Start a database transaction
        return DB::transaction(function () use ($validated) {
            $authUser = auth()->user();

            // Ensure we have a User + Lead: if authenticated, use them; otherwise create them from the form
            if ($authUser) {
                // Make sure the user has an associated Lead profile
                if (!$authUser->relationLoaded('lead')) {
                    $authUser->load('lead');
                }
                if (!$authUser->lead) {
                    $authUser->lead()->create([
                        'first_name' => $validated['first_name'],
                        'last_name'  => $validated['last_name'],
                        'phone'      => $validated['phone'] ?? null,
                    ]);
                    $authUser->refresh();
                } else {
                    // Optionally refresh lead contact info with latest submission
                    $authUser->lead->update([
                        'first_name' => $validated['first_name'],
                        'last_name'  => $validated['last_name'],
                        'phone'      => $validated['phone'] ?? $authUser->lead->phone,
                    ]);
                }
                $user = $authUser;
                $lead = $authUser->lead;
            } else {
                // Guest flow: create a user account and a lead profile
                $user = User::firstOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                        'role' => User::ROLE_LEAD,
                        'password' => Str::random(32), // hashed by cast
                    ]
                );

                // Create or update lead profile for this user
                if (!$user->relationLoaded('lead')) {
                    $user->load('lead');
                }
                if (!$user->lead) {
                    $lead = $user->lead()->create([
                        'first_name' => $validated['first_name'],
                        'last_name'  => $validated['last_name'],
                        'phone'      => $validated['phone'] ?? null,
                    ]);
                } else {
                    $user->lead->update([
                        'first_name' => $validated['first_name'],
                        'last_name'  => $validated['last_name'],
                        'phone'      => $validated['phone'] ?? $user->lead->phone,
                    ]);
                    $lead = $user->lead;
                }

                // Optionally send password reset link so guest can set a password
                try {
                    Password::sendResetLink(['email' => $user->email]);
                } catch (\Throwable $e) {
                    // fail silently; consider logging
                    \Log::warning('Unable to send password reset link: ' . $e->getMessage());
                }

                // Automatically authenticate the guest now that the account exists
                Auth::login($user, true);
            }

            // Transitional: still maintain a Client record for legacy code until we drop it
            $client = Client::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'is_lead' => true,
                ]
            );

            // Create the appointment with new FKs as well
            $appointment = Appointment::create([
                'client_id' => $client->id, // temporary during transition
                'user_id' => $user->id ?? null,
                'lead_id' => $lead->id ?? null,
                'schedule_id' => $validated['schedule_id'],
                'subject' => $validated['subject'],
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
            ]);

            // Attach services to the appointment
            $appointment->services()->attach($validated['service_ids']);

            // Send notification email to site contact after transaction commits
            DB::afterCommit(function () use ($appointment) {
                try {
                    $to = ['glainnguema@gmail.com'];
                    if (!empty($to)) {
                        Mail::to($to)->send(new AppointmentBooked($appointment));
                    }
                } catch (\Throwable $e) {
                    // Silently fail to avoid breaking the UX; consider logging in real env
                    \Log::error('Failed to send appointment email: ' . $e->getMessage());
                }
            });

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Votre rendez-vous a été enregistré avec succès.',
                    'redirect' => route('appointment.confirmation', $appointment->id),
                ], 201);
            }

            return redirect()->route('appointment.confirmation', $appointment->id)
                ->with('success', 'Votre rendez-vous a été enregistré avec succès.');
        });
    }

    /**
     * Display the appointment confirmation page.
     */
    public function confirmation(Appointment $appointment)
    {
        // Load the appointment with its relationships
        $appointment->load(['client', 'schedule', 'services', 'user', 'lead']);

        return Inertia::render('Front/Appointment/Confirmation', [
            'appointment' => $appointment,
        ]);
    }
}
