<?php

namespace Rainwaves\LaraAuthSuite\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TwoFactorChallenge extends Model
{
    protected $table = 'two_factor_challenges';

    protected $guarded = [];

    protected $casts = [
        'last_sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('authx.user_model'), 'user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at instanceof Carbon && $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return ! is_null($this->consumed_at);
    }

    public function canAttempt(): bool
    {
        return $this->attempts < $this->max_attempts && ! $this->isExpired() && ! $this->isConsumed();
    }

    public function markConsumed(): void
    {
        $this->consumed_at = now();
        $this->save();
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    public function verifyCode(string $plain): bool
    {
        return Hash::check($plain, $this->code_hash);
    }
}
