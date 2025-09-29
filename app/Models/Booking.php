<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Booking extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes;


    protected $table = 'bookings';
    protected $fillable = ['user_id', 'ticket_id', 'quantity', 'status'];


    protected $casts = [
        'status' => BookingStatus::class,
    ];


    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function ticket() : BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }


    public function payment() : hasOne
    {
        return $this->hasOne(Payment::class);
    }
}
