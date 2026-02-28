<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefaultNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $subject,
        private readonly string $message,
        private readonly ?string $url = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return notification_drivers();
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->markdown('mail.notification', $this->getData());
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(mixed $notifiable): array
    {
        return $this->getData();
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getData(): array
    {
        return [
            'subject' => $this->getSubject(),
            'message' => $this->getMessage(),
            'url' => $this->getUrl(),
        ];
    }
}
