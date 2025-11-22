<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::withCount('leads')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return Inertia::render('Admin/Formations/Index', [
            'formations' => $formations,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Formations/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'date_mode' => 'required|in:single,range',
            'date' => 'nullable|date|required_if:date_mode,single',
            'start_time' => 'nullable|date_format:H:i|required_if:date_mode,single',
            'end_time' => 'nullable|date_format:H:i|required_if:date_mode,single',
            'start_date' => 'nullable|date|required_if:date_mode,range',
            'end_date' => 'nullable|date|after_or_equal:start_date|required_if:date_mode,range',
            'duration_hours' => 'nullable|numeric|min:0',
            'duration_per_day_hours' => 'nullable|integer|min:1|required_if:date_mode,range',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('formations', 'public');
        }
        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('formations', 'public');
        }

        $formation = Formation::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'attachment_path' => $data['attachment_path'] ?? null,
            'date_mode' => $data['date_mode'],
            'date' => $data['date'] ?? null,
            'start_time' => $data['date_mode'] === 'single' ? ($data['start_time'] ?? null) : null,
            'end_time' => $data['date_mode'] === 'single' ? ($data['end_time'] ?? null) : null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'duration_hours' => $data['date_mode'] === 'single' ? ($data['duration_hours'] ?? null) : null,
            'duration_per_day_hours' => $data['date_mode'] === 'range' ? ($data['duration_per_day_hours'] ?? null) : null,
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? null,
        ]);

        return redirect()->route('admin.formations.index')
            ->with('success', 'Formation créée.');
    }

    public function edit(Formation $formation)
    {
        return Inertia::render('Admin/Formations/Edit', [
            'formation' => $formation,
        ]);
    }

    public function update(Request $request, Formation $formation)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'date_mode' => 'required|in:single,range',
            'date' => 'nullable|date|required_if:date_mode,single',
            'start_time' => 'nullable|date_format:H:i|required_if:date_mode,single',
            'end_time' => 'nullable|date_format:H:i|required_if:date_mode,single',
            'start_date' => 'nullable|date|required_if:date_mode,range',
            'end_date' => 'nullable|date|after_or_equal:start_date|required_if:date_mode,range',
            'duration_hours' => 'nullable|numeric|min:0',
            'duration_per_day_hours' => 'nullable|integer|min:1|required_if:date_mode,range',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
        ]);

        // Handle files
        if ($request->hasFile('image')) {
            // Delete old image if exists
            $oldImage = $formation->getRawOriginal('image_path');
            if (!empty($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $data['image_path'] = $request->file('image')->store('formations', 'public');
        }
        if ($request->hasFile('attachment')) {
            $oldAttachment = $formation->getRawOriginal('attachment_path');
            if (!empty($oldAttachment)) {
                Storage::disk('public')->delete($oldAttachment);
            }
            $data['attachment_path'] = $request->file('attachment')->store('formations', 'public');
        }

        // If title changed, update slug ensuring uniqueness
        if ($formation->title !== $data['title']) {
            $base = \Illuminate\Support\Str::slug($data['title']);
            $slug = $base;
            $i = 1;
            while (Formation::where('slug', $slug)->where('id', '!=', $formation->id)->exists()) {
                $slug = $base . '-' . (++$i);
            }
            $formation->slug = $slug;
        }

        // Prepare payload with date-mode logic
        $payload = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'date_mode' => $data['date_mode'],
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? null,
        ];
        if (isset($data['image_path'])) {
            $payload['image_path'] = $data['image_path'];
        }
        if (isset($data['attachment_path'])) {
            $payload['attachment_path'] = $data['attachment_path'];
        }
        if ($data['date_mode'] === 'single') {
            $payload['date'] = $data['date'] ?? null;
            $payload['start_time'] = $data['start_time'] ?? null;
            $payload['end_time'] = $data['end_time'] ?? null;
            $payload['duration_hours'] = $data['duration_hours'] ?? null;
            // reset range fields
            $payload['start_date'] = null;
            $payload['end_date'] = null;
            $payload['duration_per_day_hours'] = null;
        } else {
            $payload['start_date'] = $data['start_date'] ?? null;
            $payload['end_date'] = $data['end_date'] ?? null;
            $payload['duration_per_day_hours'] = $data['duration_per_day_hours'] ?? null;
            // reset single date fields
            $payload['date'] = null;
            $payload['start_time'] = null;
            $payload['end_time'] = null;
            $payload['duration_hours'] = null;
        }

        $formation->update($payload);

        return redirect()->route('admin.formations.edit', $formation->id)
            ->with('success', 'Formation mise à jour.');
    }

    // Public listing page
    public function frontIndex(Request $request)
    {
        $query = Formation::query()->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $formations = $query->paginate(9)->withQueryString();

        return Inertia::render('Front/Formations', [
            'formations' => $formations,
            'filters' => $request->only(['search']),
            'contactSettings' => $this->getContactSettings()

        ]);
    }

    // Public detail page
    public function show($slug)
    {
        $formation = Formation::where('slug',$slug)->firstOrFail();

        // Track unique daily view by IP address (YouTube-like behavior)
        try {
            $ip = request()->ip();
            $today = now()->toDateString();

            \App\Models\FormationView::firstOrCreate([
                'formation_id' => $formation->id,
                'ip_address' => $ip,
                'viewed_date' => $today,
            ]);
        } catch (\Throwable $e) {
            // Silently ignore tracking errors to not break the detail page
            // You may log the error if needed: \Log::warning('Formation view track failed', ['error' => $e->getMessage()]);
        }

        // Optional: provide counts to the front
        $todayCount = \App\Models\FormationView::where('formation_id', $formation->id)
            ->whereDate('viewed_date', now()->toDateString())
            ->count();
        $totalCount = \App\Models\FormationView::where('formation_id', $formation->id)->count();

        return Inertia::render('Front/FormationDetail', [
            'formation' => $formation,
            'viewStats' => [
                'today' => $todayCount,
                'total' => $totalCount,
            ],
            'contactSettings' => $this->getContactSettings()

        ]);
    }

    // Handle participation form submission (public)
    public function participate(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'attentes' => 'nullable|string',
        ]);

        // Find existing lead by email (and optionally phone), otherwise create
        $lead = Lead::where('email', $validated['email'])
            ->when(!empty($validated['phone']), function ($q) use ($validated) {
                $q->orWhere('phone', $validated['phone']);
            })
            ->first();

        if (!$lead) {
            $lead = Lead::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);
        } else {
            // Update lead's latest info
            $lead->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $lead->phone,
            ]);
        }

        // Attach lead to formation with attentes and default unpaid status (avoid duplicate via unique index)
        $lead->formations()->syncWithoutDetaching([
            $formation->id => [
                'attentes' => $validated['attentes'] ?? null,
                'status' => 'unpaid',
                'paid_at' => null,
            ]
        ]);

        return redirect()->back()->with('success', 'Votre participation a été enregistrée. Nous vous contacterons bientôt.');
    }

    // Admin detail page: show formation and participants
    public function adminShow(Formation $formation)
    {
        // Load leads with pivot data
        $formation->load(['leads' => function ($q) {
            $q->select('leads.id', 'first_name', 'last_name', 'email', 'phone');
        }]);

        // Optionally, get counts
        $leadsCount = $formation->leads()->count();

        return Inertia::render('Admin/Formations/Show', [
            'formation' => $formation,
            'leadsCount' => $leadsCount,
        ]);
    }

    public function destroy(Formation $formation)
    {
        if ($formation->image_path) {
            Storage::disk('public')->delete($formation->image_path);
        }
        if ($formation->attachment_path) {
            Storage::disk('public')->delete($formation->attachment_path);
        }
        $formation->delete();

        return redirect()->route('admin.formations.index')
            ->with('success', 'Formation supprimée.');
    }
}
