<?php

namespace App\Models;

use App\Traits\CommonQueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes , CommonQueryScopes;

    protected $table = 'events';

    protected $fillable = ['title', 'description', 'date', 'location', 'created_by'];


    protected $casts = [
        'date' => 'datetime',
    ];


    public function creator() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function tickets() : hasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function delete(): ?bool
    {
        // Soft delete the related tickets
        $this->tickets()->each(function ($ticket) {
            $ticket->delete();
        });

        return parent::delete();
    }
}
