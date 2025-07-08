<?php

namespace App\Services;

use App\Repositories\SmartHomePlatformRepositoryInterface;
use App\Repositories\SmartDeviceRepositoryInterface;
use App\Services\Interfaces\SmartHomePlatformInterface;
use App\Services\Platforms\AlexaService;
use App\Services\Platforms\GoveeService;
use App\Services\Platforms\TuyaService;
use Illuminate\Support\Facades\Log;

class SmartHomeService
{
    /**
     * The smart home platform repository instance.
     *
     * @var SmartHomePlatformRepositoryInterface
     */
    protected $platformRepository;

    /**
     * The smart device repository instance.
     *
     * @var SmartDeviceRepositoryInterface
     */
    protected $deviceRepository;

    /**
     * Create a new service instance.
     *
     * @param SmartHomePlatformRepositoryInterface $platformRepository
     * @param SmartDeviceRepositoryInterface $deviceRepository
     * @return void
     */
    public function __construct(
        SmartHomePlatformRepositoryInterface $platformRepository,
        SmartDeviceRepositoryInterface $deviceRepository
    ) {
        $this->platformRepository = $platformRepository;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * Get all platforms.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlatforms()
    {
        return $this->platformRepository->all();
    }

    /**
     * Get a specific platform.
     *
     * @param int $id
     * @return \App\Models\SmartHomePlatform|null
     */
    public function getPlatform($id)
    {
        return $this->platformRepository->find($id);
    }

    /**
     * Get a platform by its slug.
     *
     * @param string $slug
     * @return \App\Models\SmartHomePlatform|null
     */
    public function getPlatformBySlug($slug)
    {
        return $this->platformRepository->all()->where('slug', $slug)->first();
    }

    /**
     * Create a new platform.
     *
     * @param array $data
     * @return \App\Models\SmartHomePlatform
     */
    public function createPlatform(array $data)
    {
        return $this->platformRepository->create($data);
    }

    /**
     * Update a platform.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\SmartHomePlatform
     */
    public function updatePlatform($id, array $data)
    {
        return $this->platformRepository->update($id, $data);
    }

    /**
     * Delete a platform.
     *
     * @param int $id
     * @return bool
     */
    public function deletePlatform($id)
    {
        return $this->platformRepository->delete($id);
    }

    /**
     * Get all devices.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDevices()
    {
        return $this->deviceRepository->all();
    }

    /**
     * Get devices for a specific platform.
     *
     * @param int $platformId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDevicesForPlatform($platformId)
    {
        return $this->deviceRepository->all()->where('platform_id', $platformId);
    }

    /**
     * Get a specific device.
     *
     * @param int $id
     * @return \App\Models\SmartDevice|null
     */
    public function getDevice($id)
    {
        return $this->deviceRepository->find($id);
    }

    /**
     * Create a new device.
     *
     * @param array $data
     * @return \App\Models\SmartDevice
     */
    public function createDevice(array $data)
    {
        return $this->deviceRepository->create($data);
    }

    /**
     * Update a device.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\SmartDevice
     */
    public function updateDevice($id, array $data)
    {
        return $this->deviceRepository->update($id, $data);
    }

    /**
     * Delete a device.
     *
     * @param int $id
     * @return bool
     */
    public function deleteDevice($id)
    {
        return $this->deviceRepository->delete($id);
    }

    /**
     * Toggle a device's state.
     *
     * @param int $id
     * @return \App\Models\SmartDevice|array
     */
    public function toggleDevice($id)
    {
        $device = $this->getDevice($id);

        if (!$device) {
            return null;
        }

        $currentState = $device->last_state ?? [];
        $isOn = $currentState['power'] ?? false;

        $newState = $currentState;
        $newState['power'] = !$isOn;

        // If the device is connected to a platform, use the platform service to toggle it
        if ($device->platform_id) {
            $platform = $this->getPlatform($device->platform_id);

            if ($platform && $platform->is_active) {
                $platformService = $this->getPlatformService($platform);

                if ($platformService) {
                    try {
                        $success = $platformService->setDeviceState($device->device_id, $newState);

                        if (!$success) {
                            Log::error("Failed to toggle device {$device->id} through platform service");
                            return ['success' => false, 'message' => 'Failed to toggle device through platform service'];
                        }
                    } catch (\Exception $e) {
                        Log::error("Exception toggling device {$device->id}: " . $e->getMessage());
                        return ['success' => false, 'message' => 'Error toggling device: ' . $e->getMessage()];
                    }
                }
            }
        }

        // Only update the device state if we successfully toggled the device or if it's not connected to a platform
        $updatedDevice = $this->updateDevice($id, [
            'last_state' => $newState,
            'last_updated' => now(),
        ]);

        return $updatedDevice;
    }

    /**
     * Get the appropriate platform service for a given platform.
     *
     * @param \App\Models\SmartHomePlatform $platform
     * @return SmartHomePlatformInterface|null
     */
    protected function getPlatformService($platform)
    {
        if (!$platform || !$platform->is_active) {
            return null;
        }

        try {
            switch ($platform->slug) {
                case 'alexa':
                    $service = new AlexaService();
                    if ($platform->credentials && isset($platform->credentials['access_token'])) {
                        $service->connect($platform->credentials);
                        return $service;
                    }
                    break;

                // Add cases for other platforms as they are implemented
                case 'govee':
                    $service = new GoveeService();
                    if ($platform->credentials && isset($platform->credentials['api_key'])) {
                        $service->connect($platform->credentials);
                        return $service;
                    }
                    break;
                case 'tuya':
                    $service = new TuyaService();
                    if ($platform->credentials && isset($platform->credentials['client_id']) && isset($platform->credentials['client_secret'])) {
                        $service->connect($platform->credentials);
                        return $service;
                    }
                    break;

                default:
                    Log::warning("Unsupported platform type: {$platform->slug}");
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Error creating platform service for {$platform->slug}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Connect to a platform and sync devices.
     *
     * @param int $platformId
     * @return bool|array Returns true on success, or an array with error details on failure
     */
    public function connectAndSyncPlatform($platformId)
    {
        $platform = $this->getPlatform($platformId);

        if (!$platform) {
            return ['success' => false, 'message' => 'Platform not found'];
        }

        if (!$platform->is_active) {
            return ['success' => false, 'message' => 'Platform is not active'];
        }

        if (!$platform->credentials) {
            return ['success' => false, 'message' => 'Platform credentials are missing'];
        }

        $platformService = $this->getPlatformService($platform);

        if (!$platformService) {
            return ['success' => false, 'message' => 'Failed to initialize platform service'];
        }

        try {
            // Get devices from the platform
            $devices = $platformService->getDevices();

            if (empty($devices)) {
                return ['success' => false, 'message' => 'No devices found for this platform'];
            }

            // For each device, create or update in our database
            foreach ($devices as $deviceData) {
                // Check if device already exists by device_id
                $existingDevice = $this->deviceRepository->all()
                    ->where('platform_id', $platform->id)
                    ->where('device_id', $deviceData['device_id'])
                    ->first();

                if ($existingDevice) {
                    // Update existing device, but preserve room and state if not provided by the platform
                    $updateData = [
                        'name' => $deviceData['name'],
                        'device_type' => $deviceData['device_type'],
                        'capabilities' => $deviceData['capabilities'],
                        'last_updated' => now(),
                    ];

                    // Only update room if provided by the platform and not null
                    if (isset($deviceData['room']) && $deviceData['room'] !== null) {
                        $updateData['room'] = $deviceData['room'];
                    }

                    // Always update the state for Govee devices to reflect the current state from the API
                    // This ensures the UI always shows the actual current state of the device
                    $updateData['last_state'] = $deviceData['last_state'];

                    $this->updateDevice($existingDevice->id, $updateData);
                } else {
                    // Create new device
                    $this->createDevice([
                        'platform_id' => $platform->id,
                        'name' => $deviceData['name'],
                        'device_id' => $deviceData['device_id'],
                        'device_type' => $deviceData['device_type'],
                        'room' => $deviceData['room'],
                        'is_active' => true,
                        'capabilities' => $deviceData['capabilities'],
                        'last_state' => $deviceData['last_state'],
                        'last_updated' => now(),
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            $errorMessage = "Error syncing devices for platform {$platform->id}: " . $e->getMessage();
            Log::error($errorMessage);
            return ['success' => false, 'message' => $errorMessage];
        }
    }

    /**
     * Refresh devices for a specific platform.
     *
     * @param int $platformId
     * @return bool|array Returns true on success, or an array with error details on failure
     */
    public function refreshPlatformDevices($platformId)
    {
        return $this->connectAndSyncPlatform($platformId);
    }
}
