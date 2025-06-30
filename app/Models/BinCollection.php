<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BinCollection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_date',
        'bin_type',
        'color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'collection_date' => 'date',
    ];

    /**
     * Get upcoming bin collections.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function upcoming($limit = 5)
    {
        return static::where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the next collection date for a specific bin type.
     *
     * @param string $binType
     * @return \App\Models\BinCollection|null
     */
    public static function nextCollectionForType($binType)
    {
        return static::where('bin_type', $binType)
            ->where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->first();
    }

    /**
     * Get the days until the next collection.
     *
     * @return int
     */
    public function daysUntilCollection()
    {
        return Carbon::today()->diffInDays($this->collection_date, false);
    }

    /**
     * Get a human-readable string for the days until collection.
     *
     * @return string
     */
    public function daysUntilCollectionForHumans()
    {
        $days = $this->daysUntilCollection();

        if ($days === 0) {
            return 'Today';
        } elseif ($days === 1) {
            return 'Tomorrow';
        } elseif ($days > 1) {
            return "In {$days} days";
        } else {
            return 'Past collection';
        }
    }
}
