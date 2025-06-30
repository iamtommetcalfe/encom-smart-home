<?php

namespace App\Services;

use App\Models\BinCollection;
use Carbon\Carbon;

class BinCollectionService
{
    /**
     * Get upcoming bin collections.
     *
     * @param int $limit
     * @return array
     */
    public function getUpcomingCollections(int $limit = 5): array
    {
        $collections = BinCollection::where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->limit($limit)
            ->get();

        return $this->formatCollections($collections);
    }

    /**
     * Get the next collection for each bin type.
     *
     * @return array
     */
    public function getNextCollections(): array
    {
        // Get all unique bin types
        $binTypes = BinCollection::select('bin_type')
            ->distinct()
            ->pluck('bin_type');

        $nextCollections = [];

        foreach ($binTypes as $binType) {
            $collection = $this->getNextCollectionForType($binType);

            if ($collection) {
                $nextCollections[] = $collection;
            }
        }

        // Sort by days until collection
        usort($nextCollections, function ($a, $b) {
            return $a['days_until'] - $b['days_until'];
        });

        return $nextCollections;
    }

    /**
     * Get the next collection for a specific bin type.
     *
     * @param string $binType
     * @return array|null
     */
    public function getNextCollectionForType(string $binType): ?array
    {
        $collection = BinCollection::where('bin_type', $binType)
            ->where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->first();

        if (!$collection) {
            return null;
        }

        return $this->formatCollection($collection);
    }

    /**
     * Format a collection for API response.
     *
     * @param BinCollection $collection
     * @return array
     */
    protected function formatCollection(BinCollection $collection): array
    {
        return [
            'id' => $collection->id,
            'collection_date' => $collection->collection_date->format('Y-m-d'),
            'bin_type' => $collection->bin_type,
            'color' => $collection->color,
            'days_until' => $collection->daysUntilCollection(),
            'days_until_human' => $collection->daysUntilCollectionForHumans(),
        ];
    }

    /**
     * Format a collection of bin collections for API response.
     *
     * @param \Illuminate\Database\Eloquent\Collection $collections
     * @return array
     */
    protected function formatCollections($collections): array
    {
        return $collections->map(function ($collection) {
            return $this->formatCollection($collection);
        })->toArray();
    }
}
