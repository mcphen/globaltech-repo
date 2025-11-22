<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentBooked extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment->loadMissing(['client', 'schedule', 'services']);
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $client = $this->appointment->client;
        $schedule = $this->appointment->schedule;

        $subject = 'Nouveau rendez-vous: ' . ($this->appointment->subject ?: 'Demande de rendez-vous') . ' - ' . ($client?->first_name . ' ' . $client?->last_name);

        return $this
            ->subject($subject)
            ->view('emails.appointment_booked', [
                'appointment' => $this->appointment,
                'client' => $client,
                'schedule' => $schedule,
                'services' => $this->appointment->services,
            ]);
    }
}
