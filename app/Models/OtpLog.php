<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'generated_at',
        'ip_address',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
