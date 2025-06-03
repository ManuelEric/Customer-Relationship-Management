<?php

namespace App\Mail\Receipt;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendToClient extends Mailable
{
    use Queueable, SerializesModels;

    public $receiptData;
    public $s3FilePath;
    public $attachmentName;

    /**
     * Create a new message instance.
     */
    public function __construct(array $receiptData, string $s3FilePath, ?string $attachmentName = null)
    {
        $this->receiptData = $receiptData;
        $this->s3FilePath = $s3FilePath;
        $this->attachmentName = $attachmentName ?: basename($s3FilePath); // Use original name if not provided
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@edu-all.com', 'No Reply'),
            subject: $this->receiptData['title']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'pages.receipt.client-program.mail.client-view',
            with: [
                'recipient' => $this->receiptData['recipient'],
                'program_name' => $this->receiptData['program_name']
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
        return [
            Attachment::fromStorageDisk('s3', $this->s3FilePath)
                ->as($this->attachmentName) // Optional: Specify the file name seen by the recipient
                ->withMime('application/pdf') // Optional: Specify MIME type if not auto-detected
        ];
    }
}
