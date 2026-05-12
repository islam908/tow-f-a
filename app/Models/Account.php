<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'label',
        'email',
        'username',
        'password_encrypted',
        'secret_key_encrypted',
    ];

    protected $casts = [
        'password_encrypted' => 'encrypted',
        'secret_key_encrypted' => 'encrypted',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
