<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'account_id',
        'name',
        'token',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function otpLogs(): HasMany
    {
        return $this->hasMany(OtpLog::class);
    }

    public function remainingUsage(): int
    {
        return max(0, $this->usage_limit - $this->usage_count);
    }
}
