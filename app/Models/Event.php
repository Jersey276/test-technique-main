<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'starts_at', 'ends_at'];

    /**
     * Scope a query to only include events that occure between two dates.
     *
     * @param  Builder  $query
     * @param  DateTime  $startsAt
     * @param  DateTime  $endsAt
     * @return Builder
     */
    public function scopeIsBetween($query, $startsAt, $endsAt)
    {
        $query->when($startsAt && !$endsAt, function (Builder $query) use ($startsAt) {
            $query->whereDate('events.starts_at', '<=', $startsAt)->whereDate('events.ends_at', '>=', $startsAt);
        })->when($startsAt && $endsAt, function (Builder $query) use ($startsAt, $endsAt) {
            $query->where(function (Builder $query) use ($startsAt, $endsAt) {
                $query->where('events.starts_at', '<=', $endsAt)
                    ->where('events.ends_at', '>=', $startsAt);
            });
        });
        return $query;
    }

    /**
     * Scope a query to order events by their start date.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOrderByDate($query)
    {
        return $query->orderBy('starts_at');
    }
}
