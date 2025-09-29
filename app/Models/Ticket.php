<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets';

    protected $fillable = ['type', 'price', 'quantity', 'event_id'];


    protected $casts = [
        'price' => 'decimal:2',
    ];


    public function event() : BelongsTo
    {
        return $this->belongsTo(Event::class);
    }


    public function bookings() : hasMany
    {
        return $this->hasMany(Booking::class);
    }
}
