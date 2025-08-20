<?php

namespace App\Mail\Invb2b;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendToClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $s3path;

    public $filename;

    public $view;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data, string $s3path, string $filename, string $view)
    {
        $this->data = $data;
        $this->s3path = $s3path;
        $this->filename = $filename;
        $this->view = $view;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@edu-all.com', 'No Reply'),
            subject: $this->data['title']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if (! view()->exists($this->view)) {
            throw new \Exception("The view '{$this->view}' does not exist.");
        }

        // view : 'pages.invoice.'.$this->module['segment'].'.mail.client-view'
        return new Content(
            view: $this->view,
            with: $this->data
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
            Attachment::fromStorageDisk('s3', $this->s3path)
                ->as($this->filename) // Optional: Specify the file name seen by the recipient
                ->withMime('application/pdf'), // Optional: Specify MIME type if not auto-detected
        ];
    }
}
