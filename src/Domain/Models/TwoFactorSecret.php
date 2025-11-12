<?php

namespace Rainwaves\LaraAuthSuite\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorSecret extends Model
{
    protected $table = 'two_factor_secrets';

    protected $guarded = [];

    protected $casts = [
        'secret' => 'encrypted',        // Laravel encrypted cast
        'recovery_codes' => 'encrypted:array',  // store as array, encrypted at rest
        'enabled_at' => 'datetime',
        'revoked_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('authx.user_model'), 'user_id');
    }

    public function enabled(): bool
    {
        return ! is_null($this->enabled_at) && is_null($this->revoked_at);
    }
}
