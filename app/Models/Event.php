<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'starts_at'];

    /**
     * Scope a query to only include events that occure between two dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  DateTime  $startsAt
     * @param  DateTime  $endsAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsBetween($query, $startsAt, $endsAt)
    {
        if ($startsAt && !$endsAt) {
            $query->whereDate('events.starts_at', '=', $startsAt);
        } elseif ($startsAt && $endsAt) {
            $query->whereDate('events.starts_at', '>', $startsAt)
                  ->whereDate('events.starts_at', '<', $endsAt);
        }
        return $query;
    }

    /**
     * Scope a query to only include events that occure between two dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  DateTime  $startsAt
     * @param  DateTime  $endsAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDate($query)
    {
        return $query->orderBy('starts_at');
    }
}
