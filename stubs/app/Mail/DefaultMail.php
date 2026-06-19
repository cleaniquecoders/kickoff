<?php

declare(strict_types=1);

namespace App\Mail;

use CleaniqueCoders\MailHistory\Concerns\InteractsWithMailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DefaultMail extends Mailable
{
    use InteractsWithMailMetadata, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $title, public string $message)
    {
        // Tag the message with a mailhistory hash so its delivery status can be
        // tracked (Sent / Delivered / Opened) and provider webhook correlation works.
        $this->configureMetadataHash();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.default-mail',
            with: [
                'message' => $this->message,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
