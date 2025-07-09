<?php

namespace App\Services;

use App\Repositories\SmartDeviceWidgetConfigRepositoryInterface;
use App\Repositories\SmartDeviceRepositoryInterface;

class SmartHomeWidgetService
{
    /**
     * The smart device widget config repository instance.
     *
     * @var SmartDeviceWidgetConfigRepositoryInterface
     */
    protected $widgetConfigRepository;

    /**
     * The smart device repository instance.
     *
     * @var SmartDeviceRepositoryInterface
     */
    protected $deviceRepository;

    /**
     * Create a new service instance.
     *
     * @param SmartDeviceWidgetConfigRepositoryInterface $widgetConfigRepository
     * @param SmartDeviceRepositoryInterface $deviceRepository
     * @return void
     */
    public function __construct(
        SmartDeviceWidgetConfigRepositoryInterface $widgetConfigRepository,
        SmartDeviceRepositoryInterface $deviceRepository
    ) {
        $this->widgetConfigRepository = $widgetConfigRepository;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * Get all widget configurations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWidgetConfigs()
    {
        return $this->widgetConfigRepository->all();
    }

    /**
     * Get widget configurations for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWidgetConfigsForUser($userId)
    {
        return $this->widgetConfigRepository->all()->where('user_id', $userId);
    }

    /**
     * Get a specific widget configuration.
     *
     * @param int $id
     * @return \App\Models\SmartDeviceWidgetConfig|null
     */
    public function getWidgetConfig($id)
    {
        return $this->widgetConfigRepository->find($id);
    }

    /**
     * Create a new widget configuration.
     *
     * @param array $data
     * @return \App\Models\SmartDeviceWidgetConfig
     */
    public function createWidgetConfig(array $data)
    {
        return $this->widgetConfigRepository->create($data);
    }

    /**
     * Update a widget configuration.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\SmartDeviceWidgetConfig
     */
    public function updateWidgetConfig($id, array $data)
    {
        return $this->widgetConfigRepository->update($id, $data);
    }

    /**
     * Delete a widget configuration.
     *
     * @param int $id
     * @return bool
     */
    public function deleteWidgetConfig($id)
    {
        return $this->widgetConfigRepository->delete($id);
    }

    /**
     * Get devices for a widget configuration.
     *
     * @param int $widgetConfigId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDevicesForWidget($widgetConfigId)
    {
        $widgetConfig = $this->getWidgetConfig($widgetConfigId);

        if (!$widgetConfig) {
            return collect();
        }

        $deviceIds = $widgetConfig->devices;
        $devices = collect();

        foreach ($deviceIds as $deviceId) {
            $device = $this->deviceRepository->find($deviceId);
            if ($device) {
                $devices->push($device);
            }
        }

        return $devices;
    }

    /**
     * Add a device to a widget configuration.
     *
     * @param int $widgetConfigId
     * @param int $deviceId
     * @return \App\Models\SmartDeviceWidgetConfig|null
     */
    public function addDeviceToWidget($widgetConfigId, $deviceId)
    {
        $widgetConfig = $this->getWidgetConfig($widgetConfigId);

        if (!$widgetConfig) {
            return null;
        }

        $deviceIds = $widgetConfig->devices;

        if (!in_array($deviceId, $deviceIds)) {
            $deviceIds[] = $deviceId;
            return $this->updateWidgetConfig($widgetConfigId, ['devices' => $deviceIds]);
        }

        return $widgetConfig;
    }

    /**
     * Remove a device from a widget configuration.
     *
     * @param int $widgetConfigId
     * @param int $deviceId
     * @return \App\Models\SmartDeviceWidgetConfig|null
     */
    public function removeDeviceFromWidget($widgetConfigId, $deviceId)
    {
        $widgetConfig = $this->getWidgetConfig($widgetConfigId);

        if (!$widgetConfig) {
            return null;
        }

        $deviceIds = $widgetConfig->devices;

        if (($key = array_search($deviceId, $deviceIds)) !== false) {
            unset($deviceIds[$key]);
            $deviceIds = array_values($deviceIds); // Re-index the array
            return $this->updateWidgetConfig($widgetConfigId, ['devices' => $deviceIds]);
        }

        return $widgetConfig;
    }
}
