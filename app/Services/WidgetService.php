<?php

namespace App\Services;

use App\Models\Widget;
use App\Repositories\WidgetRepositoryInterface;

class WidgetService
{
    /**
     * The widget repository instance.
     *
     * @var WidgetRepositoryInterface
     */
    protected $widgetRepository;

    /**
     * Create a new service instance.
     *
     * @param WidgetRepositoryInterface $widgetRepository
     * @return void
     */
    public function __construct(WidgetRepositoryInterface $widgetRepository)
    {
        $this->widgetRepository = $widgetRepository;
    }

    /**
     * Get all widgets.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWidgets()
    {
        return $this->widgetRepository->all();
    }

    /**
     * Find a widget by ID.
     *
     * @param int $id
     * @return Widget|null
     */
    public function findWidget($id)
    {
        return $this->widgetRepository->find($id);
    }

    /**
     * Create a new widget.
     *
     * @param array $data
     * @return Widget
     */
    public function createWidget(array $data)
    {
        return $this->widgetRepository->create($data);
    }

    /**
     * Update a widget.
     *
     * @param int $id
     * @param array $data
     * @return Widget
     */
    public function updateWidget($id, array $data)
    {
        return $this->widgetRepository->update($id, $data);
    }

    /**
     * Delete a widget.
     *
     * @param int $id
     * @return bool
     */
    public function deleteWidget($id)
    {
        return $this->widgetRepository->delete($id);
    }
}
