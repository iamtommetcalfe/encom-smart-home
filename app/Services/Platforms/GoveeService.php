<?php

namespace App\Services\Platforms;

use App\Services\Interfaces\SmartHomePlatformInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoveeService implements SmartHomePlatformInterface
{
    /**
     * The base URL for the Govee API.
     *
     * @var string
     */
    protected $baseUrl = 'https://developer-api.govee.com/v1/';

    /**
     * The API key for the Govee API.
     *
     * @var string|null
     */
    protected $apiKey = null;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        // No configuration needed at construction time
    }

    /**
     * Connect to the platform.
     *
     * @param array $credentials
     * @return bool
     */
    public function connect(array $credentials): bool
    {
        try {
            if (isset($credentials['api_key'])) {
                $this->apiKey = $credentials['api_key'];
            } else {
                return false;
            }

            // Verify the API key by making a test request
            $response = Http::withHeaders([
                'Govee-API-Key' => $this->apiKey
            ])->get($this->baseUrl . 'devices');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to connect to Govee: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Disconnect from the platform.
     *
     * @return bool
     */
    public function disconnect(): bool
    {
        $this->apiKey = null;
        return true;
    }

    /**
     * Get all devices from the platform.
     *
     * @return array
     */
    public function getDevices(): array
    {
        try {
            if (!$this->apiKey) {
                return [];
            }

            $response = Http::withHeaders([
                'Govee-API-Key' => $this->apiKey
            ])->get($this->baseUrl . 'devices');

            if ($response->successful()) {
                $data = $response->json('data', []);
                $devices = $data['devices'] ?? [];
                return $this->formatDevices($devices);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Govee devices: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get the state of a specific device.
     *
     * @param string $deviceId
     * @return array
     */
    public function getDeviceState(string $deviceId): array
    {
        try {
            if (!$this->apiKey) {
                return [];
            }

            // Extract the device and model from the combined ID
            $parts = explode(':', $deviceId);
            if (count($parts) < 2) {
                Log::error('Invalid device ID format in getDeviceState', ['deviceId' => $deviceId]);
                return [];
            }

            // For device IDs with multiple colons, the model is the last part
            // and the device is everything else joined back together
            $model = array_pop($parts);
            $device = implode(':', $parts);

            Log::info('Parsed device ID in getDeviceState', [
                'originalDeviceId' => $deviceId,
                'parsedDevice' => $device,
                'parsedModel' => $model
            ]);

            // Validate device and model
            if (empty($device) || empty($model)) {
                Log::error('Empty device or model in getDeviceState', ['device' => $device, 'model' => $model]);
                return [];
            }

            $response = Http::withHeaders([
                'Govee-API-Key' => $this->apiKey
            ])->get($this->baseUrl . 'devices/state', [
                'device' => $device,
                'model' => $model
            ]);

            if ($response->successful()) {
                $data = $response->json('data', []);
                $properties = $data['properties'] ?? [];
                return $this->formatDeviceState($properties);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Govee device state: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Set the state of a specific device.
     *
     * @param string $deviceId
     * @param array $state
     * @return bool
     */
    public function setDeviceState(string $deviceId, array $state): bool
    {
        try {
            if (!$this->apiKey) {
                Log::error('Govee API key is not set');
                return false;
            }

            // Extract the device and model from the combined ID
            $parts = explode(':', $deviceId);
            if (count($parts) < 2) {
                Log::error('Invalid device ID format', ['deviceId' => $deviceId]);
                return false;
            }

            // For device IDs with multiple colons, the model is the last part
            // and the device is everything else joined back together
            $model = array_pop($parts);
            $device = implode(':', $parts);

            Log::info('Parsed device ID in setDeviceState', [
                'originalDeviceId' => $deviceId,
                'parsedDevice' => $device,
                'parsedModel' => $model
            ]);

            // Validate device and model
            if (empty($device) || empty($model)) {
                Log::error('Empty device or model', ['device' => $device, 'model' => $model]);
                return false;
            }

            // Convert our generic state format to Govee-specific format
            $command = $this->convertToGoveeCommand($state);

            // Log the command being sent
            Log::info('Sending command to Govee device', [
                'device' => $device,
                'model' => $model,
                'command' => $command,
                'state' => $state
            ]);

            if (empty($command)) {
                Log::error('No valid command generated for Govee device');
                return false;
            }

            $response = Http::withHeaders([
                'Govee-API-Key' => $this->apiKey
            ])->put($this->baseUrl . 'devices/control', [
                'device' => $device,
                'model' => $model,
                'cmd' => $command
            ]);

            if (!$response->successful()) {
                Log::error('Govee API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            Log::info('Successfully set Govee device state', [
                'device' => $device,
                'response' => $response->json()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to set Govee device state: ' . $e->getMessage(), [
                'exception' => $e,
                'deviceId' => $deviceId,
                'state' => $state
            ]);
            return false;
        }
    }

    /**
     * Refresh the list of devices from the platform.
     *
     * @return array
     */
    public function refreshDevices(): array
    {
        // For Govee, refreshing devices is the same as getting devices
        return $this->getDevices();
    }

    /**
     * Format the devices from the Govee API to our standard format.
     *
     * @param array $devices
     * @return array
     */
    protected function formatDevices(array $devices): array
    {
        $formattedDevices = [];

        foreach ($devices as $device) {
            $deviceId = $device['device'] . ':' . $device['model'];

            Log::info('Created device ID in formatDevices', [
                'deviceId' => $deviceId,
                'device' => $device['device'],
                'model' => $device['model']
            ]);
            $capabilities = [];
            $deviceType = 'unknown';

            // Determine device type and capabilities based on device properties
            if (isset($device['supportCmds'])) {
                foreach ($device['supportCmds'] as $cmd) {
                    // Check if $cmd is an array before trying to access its elements
                    if (is_array($cmd) && isset($cmd['name'])) {
                        if ($cmd['name'] === 'turn') {
                            $capabilities[] = 'on_off';
                        }
                        if ($cmd['name'] === 'brightness') {
                            $capabilities[] = 'brightness';
                        }
                        if ($cmd['name'] === 'color') {
                            $capabilities[] = 'color';
                        }
                    } elseif (is_string($cmd)) {
                        // If $cmd is a string, add it directly as a capability
                        if ($cmd === 'turn') {
                            $capabilities[] = 'on_off';
                        }
                        if ($cmd === 'brightness') {
                            $capabilities[] = 'brightness';
                        }
                        if ($cmd === 'color') {
                            $capabilities[] = 'color';
                        }
                    }
                }
            }

            // Determine device type based on capabilities and device name
            if (strpos(strtolower($device['deviceName']), 'light') !== false ||
                strpos(strtolower($device['deviceName']), 'lamp') !== false ||
                in_array('brightness', $capabilities) ||
                in_array('color', $capabilities)) {
                $deviceType = 'light';
            } elseif (strpos(strtolower($device['deviceName']), 'plug') !== false ||
                      strpos(strtolower($device['deviceName']), 'switch') !== false) {
                $deviceType = 'switch';
            }

            // Get device state
            $state = $this->getDeviceState($deviceId);
            $isOn = $state['power'] ?? false;

            $formattedDevices[] = [
                'device_id' => $deviceId,
                'name' => $device['deviceName'] ?? 'Unknown Device',
                'device_type' => $deviceType,
                'room' => null, // Govee API doesn't provide room information
                'capabilities' => $capabilities,
                'last_state' => [
                    'power' => $isOn,
                ],
            ];
        }

        return $formattedDevices;
    }

    /**
     * Format the device state from the Govee API to our standard format.
     *
     * @param array $properties
     * @return array
     */
    protected function formatDeviceState(array $properties): array
    {
        $state = [
            'power' => false,
            'brightness' => null,
            'color' => null,
        ];

        foreach ($properties as $property) {
            // Handle properties in the format with 'name' and 'value' keys
            if (is_array($property) && isset($property['name']) && isset($property['value'])) {
                if ($property['name'] === 'powerState') {
                    $state['power'] = $property['value'] === 'on';
                }
                if ($property['name'] === 'brightness') {
                    $state['brightness'] = $property['value'];
                }
                if ($property['name'] === 'color') {
                    $state['color'] = $property['value'];
                }
            }
            // Handle properties in the format with direct key-value pairs
            elseif (is_array($property) && count($property) === 1) {
                $key = key($property);
                $value = $property[$key];

                if ($key === 'online') {
                    // 'online' property doesn't directly map to our state format, but we can log it
                    Log::info('Device online status', ['online' => $value]);
                }
                elseif ($key === 'powerState') {
                    $state['power'] = $value === 'on';
                }
                elseif ($key === 'brightness') {
                    $state['brightness'] = $value;
                }
                elseif ($key === 'color') {
                    $state['color'] = $value;
                }
            }
            else {
                Log::error('Invalid property format in formatDeviceState', ['property' => $property]);
            }
        }

        return $state;
    }

    /**
     * Convert our generic state format to Govee-specific command format.
     * The Govee API only accepts one command at a time, so we prioritize power commands
     * when toggling a device.
     *
     * @param array $state
     * @return array
     */
    protected function convertToGoveeCommand(array $state): array
    {
        // Prioritize power commands for toggling devices
        if (isset($state['power'])) {
            return [
                'name' => 'turn',
                'value' => $state['power'] ? 'on' : 'off'
            ];
        }

        // Handle other commands if power is not being changed
        if (isset($state['brightness'])) {
            return [
                'name' => 'brightness',
                'value' => $state['brightness']
            ];
        }

        if (isset($state['color'])) {
            return [
                'name' => 'color',
                'value' => $state['color']
            ];
        }

        // Return empty command if no properties are set
        return [];
    }
}
