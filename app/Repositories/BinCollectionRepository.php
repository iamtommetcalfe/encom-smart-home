<?php

namespace App\Repositories;

use App\Models\BinCollection;
use Carbon\Carbon;

class BinCollectionRepository extends BaseRepository implements BinCollectionRepositoryInterface
{
    /**
     * BinCollectionRepository constructor.
     *
     * @param BinCollection $model
     */
    public function __construct(BinCollection $model)
    {
        parent::__construct($model);
    }

    /**
     * Get upcoming bin collections.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcoming(int $limit = 5)
    {
        return $this->model->where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all unique bin types.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBinTypes()
    {
        return $this->model->select('bin_type')
            ->distinct()
            ->pluck('bin_type');
    }

    /**
     * Get the next collection for a specific bin type.
     *
     * @param string $binType
     * @return \App\Models\BinCollection|null
     */
    public function getNextCollectionForType(string $binType)
    {
        return $this->model->where('bin_type', $binType)
            ->where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->first();
    }
}
