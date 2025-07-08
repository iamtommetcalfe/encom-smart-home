<?php

namespace App\Repositories;

use App\Models\Widget;

class WidgetRepository extends BaseRepository implements WidgetRepositoryInterface
{
    /**
     * WidgetRepository constructor.
     *
     * @param Widget $model
     */
    public function __construct(Widget $model)
    {
        parent::__construct($model);
    }
}
