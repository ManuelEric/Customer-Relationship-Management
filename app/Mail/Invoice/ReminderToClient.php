<?php

namespace App\Mail\Invoice;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderToClient extends Mailable
{
    use Queueable, SerializesModels;

    public $client_program;

    /**
     * Create a new message instance.
     */
    public function __construct($client_program)
    {
        $this->client_program = $client_program;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $program_name = $this->client_program->invoice_program_name;
        return new Envelope(
            subject: "7 Days Left until the Payment Deadline for {$program_name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'pages.invoice.client-program.mail.reminder-payment',
            with: [
                'parent_fullname' => $this->client_program->parent_fullname,
                'parent_mail' => $this->client_program->parent_mail,
                'program_name' => $this->client_program->invoice_program_name,
                'due_date' => date('d/m/Y', strtotime($this->client_program->inv_duedate)),
                'child_fullname' => $this->client_program->fullname,
                'inv_paymentmethod' => $this->client_program->inv_paymentmethod,
                'total_payment_other' => $this->client_program->currency != 'idr' ? $this->formatCurrency($this->client_program->currency, $this->client_program->inv_totalprice_idr, $this->client_program->inv_totalprice ?? 0) : 0,
                'total_payment_idr' => $this->formatCurrency('idr', $this->client_program->inv_totalprice_idr, $this->client_program->inv_totalprice ?? 0),
                'pic_email' => $this->client_program->internalPic->email,
                'currency' => $this->client_program->currency
            ]
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
