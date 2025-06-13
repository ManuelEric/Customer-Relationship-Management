<?php

namespace App\Mail\Invoice;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportToFinanceTeam extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient_who_doesnt_have_email;
    public $recipient_category;

    /**
     * Create a new message instance.
     */
    public function __construct($recipient_who_doesnt_have_email, $recipient_category)
    {
        $this->recipient_who_doesnt_have_email = $recipient_who_doesnt_have_email;
        $this->recipient_category = $recipient_category;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Clients Unable to Receive Invoice Reminders',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        switch ($this->recipient_category)
        {
            case "client":
                $view = 'pages.invoice.client-program.mail.reminder-finance';
                $with = [
                    'finance_name' => env('FINANCE_NAME'),
                    'parents_have_no_email' => $this->recipient_who_doesnt_have_email,
                ];
                break;

            case "partner":
                $view = 'pages.invoice.corporate-program.mail.reminder-finance';
                $with = [
                    'finance_name' => env('FINANCE_NAME'),
                    'partner_have_no_pic ' => $this->recipient_who_doesnt_have_email,
                ];
                break;
        }

        return new Content(
            view: $view,
            with: $with
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
