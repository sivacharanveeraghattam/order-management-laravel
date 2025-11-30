<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status' // 0=pending, 1=confirmed, 2=cancelled
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // Status constants
    const PENDING = 0;
    const CONFIRMED = 1;
    const CANCELLED = 2;

    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            self::PENDING => 'pending',
            self::CONFIRMED => 'confirmed',
            self::CANCELLED => 'cancelled',
            default => 'unknown'
        };
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Eager loading scope (No N+1)
    public function scopeWithFullDetails($query)
    {
        return $query->with([
            'user:id,name,email',
            'items.product:id,name,sku,price'
        ]);
    }
}
