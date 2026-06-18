<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public string $action;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $action)
    {
        $this->appointment = $appointment;
        $this->action = $action;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->action) {
            'booked' => "[Jcob] New Booking - {$this->appointment->customer_name}",
            'updated' => "[Jcob] Booking Updated - {$this->appointment->customer_name}",
            'cancelled' => "[Jcob] Booking Cancelled - {$this->appointment->customer_name}",
            default => "[Jcob] Appointment Notification",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-notification',
            with: [
                'appointment' => $this->appointment,
                'action' => $this->action,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
