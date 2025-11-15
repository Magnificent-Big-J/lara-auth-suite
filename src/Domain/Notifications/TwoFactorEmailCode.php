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
     * @param  string  $code        The one-time verification code.
     * @param  int     $ttlSeconds  How long the code is valid, in seconds.
     */
    public function __construct(
        protected string $code,
        protected int $ttlSeconds
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $expiryText = $this->humanExpiryText();

        $name = trim((string) ($notifiable->name ?? ''));
        $greetingName = $name !== '' ? $name : 'there';

        return (new MailMessage)
            ->subject('🔐 Your Verification Code')
            ->greeting('Hello '.$greetingName)
            ->line('Use the code below to complete your sign-in or verification process.')
            ->line('')
            ->line('**'.$this->code.'**')
            ->line('')
            ->line($expiryText)
            ->line('If you did not request this code, you can safely ignore this email.')
            ->salutation('— The Rainwaves Security Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'code'       => $this->code,
            'expires_at' => Carbon::now()->addSeconds($this->ttlSeconds)->toIso8601String(),
        ];
    }

    /**
     * Simple, human-friendly expiry text based only on TTL.
     */
    protected function humanExpiryText(): string
    {
        if ($this->ttlSeconds <= 0) {
            return 'This code has expired or will expire momentarily.';
        }

        if ($this->ttlSeconds < 60) {
            return 'This code will expire in less than a minute.';
        }

        $minutes = (int) ceil($this->ttlSeconds / 60);

        return 'This code will expire in '.$minutes.' minute'.($minutes === 1 ? '' : 's').'.';
    }
}
