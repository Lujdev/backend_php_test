<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'created_by',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeFilterByDate($query, $date)
    {
        return $date ? $query->whereDate('date', $date) : $query;
    }

    public function scopeSearchByTitle($query, $search)
    {
        return $search ? $query->where('title', 'like', "%$search%") : $query;
    }
}
