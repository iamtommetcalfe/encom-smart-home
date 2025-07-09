<?php

namespace App\Services\Interfaces;

interface SmartHomePlatformInterface
{
    /**
     * Connect to the platform.
     *
     * @param array $credentials
     * @return bool
     */
    public function connect(array $credentials): bool;

    /**
     * Disconnect from the platform.
     *
     * @return bool
     */
    public function disconnect(): bool;

    /**
     * Get all devices from the platform.
     *
     * @return array
     */
    public function getDevices(): array;

    /**
     * Get the state of a specific device.
     *
     * @param string $deviceId
     * @return array
     */
    public function getDeviceState(string $deviceId): array;

    /**
     * Set the state of a specific device.
     *
     * @param string $deviceId
     * @param array $state
     * @return bool
     */
    public function setDeviceState(string $deviceId, array $state): bool;

    /**
     * Refresh the list of devices from the platform.
     *
     * @return array
     */
    public function refreshDevices(): array;
}
