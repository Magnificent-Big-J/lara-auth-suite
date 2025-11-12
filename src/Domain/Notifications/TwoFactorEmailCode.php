<?php

namespace Rainwaves\LaraAuthSuite\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class TwoFactorEmailCode extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string  $code  The one-time verification code.
     * @param  Carbon  $expiresAt  The expiration timestamp.
     */
    public function __construct(
        protected string $code,
        protected Carbon $expiresAt
    ) {}

    /**
     * Define the notification channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail message for the OTP email.
     */
    public function toMail($notifiable): MailMessage
    {
        $minutes = $this->expiresAt->diffInMinutes(now());

        return (new MailMessage)
            ->subject('🔐 Your Verification Code')
            ->greeting('Hello '.($notifiable->name ?? ''))
            ->line('Use the code below to complete your sign-in or verification process.')
            ->line('')
            ->line('**'.$this->code.'**')
            ->line('')
            ->line("This code will expire in {$minutes} minute".($minutes > 1 ? 's' : '').'.')
            ->line('If you did not request this code, you can safely ignore this email.')
            ->salutation('— The Rainwaves Security Team');
    }

    /**
     * Optional: custom array representation (for database channel, if needed).
     */
    public function toArray($notifiable): array
    {
        return [
            'code' => $this->code,
            'expires_at' => $this->expiresAt->toIso8601String(),
        ];
    }
}
