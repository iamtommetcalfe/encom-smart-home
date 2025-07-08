<?php

namespace App\Repositories;

interface BinCollectionRepositoryInterface extends RepositoryInterface
{
    /**
     * Get upcoming bin collections.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcoming(int $limit = 5);

    /**
     * Get all unique bin types.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBinTypes();

    /**
     * Get the next collection for a specific bin type.
     *
     * @param string $binType
     * @return \App\Models\BinCollection|null
     */
    public function getNextCollectionForType(string $binType);
}
