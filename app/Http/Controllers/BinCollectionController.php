<?php

namespace App\Http\Controllers;

use App\Models\BinCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BinCollectionController extends Controller
{
    /**
     * Get upcoming bin collections.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upcoming(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);
        $collections = BinCollection::upcoming($limit);

        $formattedCollections = $collections->map(function ($collection) {
            return [
                'id' => $collection->id,
                'collection_date' => $collection->collection_date->format('Y-m-d'),
                'bin_type' => $collection->bin_type,
                'color' => $collection->color,
                'days_until' => $collection->daysUntilCollection(),
                'days_until_human' => $collection->daysUntilCollectionForHumans(),
            ];
        });

        return response()->json([
            'collections' => $formattedCollections,
        ]);
    }

    /**
     * Get the next collection for each bin type.
     *
     * @return JsonResponse
     */
    public function nextCollections(): JsonResponse
    {
        // Get all unique bin types
        $binTypes = BinCollection::select('bin_type')
            ->distinct()
            ->pluck('bin_type');

        $nextCollections = [];

        foreach ($binTypes as $binType) {
            $collection = BinCollection::nextCollectionForType($binType);

            if ($collection) {
                $nextCollections[] = [
                    'id' => $collection->id,
                    'collection_date' => $collection->collection_date->format('Y-m-d'),
                    'bin_type' => $collection->bin_type,
                    'color' => $collection->color,
                    'days_until' => $collection->daysUntilCollection(),
                    'days_until_human' => $collection->daysUntilCollectionForHumans(),
                ];
            }
        }

        // Sort by days until collection
        usort($nextCollections, function ($a, $b) {
            return $a['days_until'] - $b['days_until'];
        });

        return response()->json([
            'collections' => $nextCollections,
        ]);
    }
}
