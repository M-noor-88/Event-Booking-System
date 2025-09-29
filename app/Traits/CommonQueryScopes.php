<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    /**
     * Filter by a date column (defaults to 'date').
     * Supports 'date_from' and 'date_to' keys in $filters (YYYY-MM-DD or datetime).
     *
     * Usage: use from repository via Event::query()->filterByDate(...)
     * Model::filterByDate($filters, 'date')->get();
     */
    public function scopeFilterByDate(Builder $query, array $filters = [], string $column = 'date'): Builder
    {
        if (!empty($filters['date_from'])) {
            $query->whereDate($column, '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate($column, '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Search by title (or any text column). Adds where title LIKE %q% on 'q' key.
     *
     * Usage: Model::searchByTitle($filters, 'title')->get();
     */
    public function scopeSearchByTitle(Builder $query, array $filters = [], string $column = 'title'): Builder
    {
        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            if ($q !== '') {
                $query->where($column, 'like', "%{$q}%");
            }
        }

        return $query;
    }
}
