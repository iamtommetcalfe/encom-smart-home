<?php

namespace App\Http\Controllers;

use App\Services\BinCollectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BinCollectionController extends Controller
{
    /**
     * The bin collection service instance.
     *
     * @var BinCollectionService
     */
    protected $binCollectionService;

    /**
     * Create a new controller instance.
     *
     * @param BinCollectionService $binCollectionService
     * @return void
     */
    public function __construct(BinCollectionService $binCollectionService)
    {
        $this->binCollectionService = $binCollectionService;
    }

    /**
     * Get upcoming bin collections.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upcoming(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);
        $collections = $this->binCollectionService->getUpcomingCollections($limit);

        return response()->json([
            'collections' => $collections,
        ]);
    }

    /**
     * Get the next collection for each bin type.
     *
     * @return JsonResponse
     */
    public function nextCollections(): JsonResponse
    {
        $collections = $this->binCollectionService->getNextCollections();

        return response()->json([
            'collections' => $collections,
        ]);
    }
}
