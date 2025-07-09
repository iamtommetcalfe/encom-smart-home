<?php

namespace App\Repositories;

use App\Models\SmartDevice;

class SmartDeviceRepository extends BaseRepository implements SmartDeviceRepositoryInterface
{
    /**
     * SmartDeviceRepository constructor.
     *
     * @param SmartDevice $model
     */
    public function __construct(SmartDevice $model)
    {
        parent::__construct($model);
    }
}
