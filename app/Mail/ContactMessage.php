<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $subject = 'Nouveau message de contact: ' . ($this->data['subject'] ?? 'Sans sujet');

        return $this
            ->subject($subject)
            ->view('emails.contact_message')
            ->with(['data' => $this->data]);
    }
}
