<?php

namespace App\Repositories;

use App\Models\SmartHomePlatform;

class SmartHomePlatformRepository extends BaseRepository implements SmartHomePlatformRepositoryInterface
{
    /**
     * SmartHomePlatformRepository constructor.
     *
     * @param SmartHomePlatform $model
     */
    public function __construct(SmartHomePlatform $model)
    {
        parent::__construct($model);
    }
}
