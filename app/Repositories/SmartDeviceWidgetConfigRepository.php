<?php

namespace App\Repositories;

use App\Models\SmartDeviceWidgetConfig;

class SmartDeviceWidgetConfigRepository extends BaseRepository implements SmartDeviceWidgetConfigRepositoryInterface
{
    /**
     * SmartDeviceWidgetConfigRepository constructor.
     *
     * @param SmartDeviceWidgetConfig $model
     */
    public function __construct(SmartDeviceWidgetConfig $model)
    {
        parent::__construct($model);
    }
}
