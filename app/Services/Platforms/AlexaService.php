<?php

namespace App\Services\Platforms;

use App\Services\Interfaces\SmartHomePlatformInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlexaService implements SmartHomePlatformInterface
{
    /**
     * The base URL for the Alexa Smart Home Skill API.
     *
     * @var string
     */
    protected $baseUrl = 'https://api.amazonalexa.com/v3/';

    /**
     * The access token for the Alexa API.
     *
     * @var string|null
     */
    protected $accessToken = null;

    /**
     * The refresh token for the Alexa API.
     *
     * @var string|null
     */
    protected $refreshToken = null;

    /**
     * The client ID for the Alexa API.
     *
     * @var string|null
     */
    protected $clientId = null;

    /**
     * The client secret for the Alexa API.
     *
     * @var string|null
     */
    protected $clientSecret = null;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->clientId = config('services.alexa.client_id');
        $this->clientSecret = config('services.alexa.client_secret');
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
            if (isset($credentials['access_token'])) {
                $this->accessToken = $credentials['access_token'];
            }

            if (isset($credentials['refresh_token'])) {
                $this->refreshToken = $credentials['refresh_token'];
            }

            // If we have an access token, try to use it to verify the connection
            if ($this->accessToken) {
                $response = Http::withToken($this->accessToken)
                    ->get($this->baseUrl . 'users/~current/skills');

                if ($response->successful()) {
                    return true;
                }
            }

            // If we have a refresh token, try to refresh the access token
            if ($this->refreshToken) {
                return $this->refreshAccessToken();
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to connect to Alexa: ' . $e->getMessage());
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
        $this->accessToken = null;
        $this->refreshToken = null;
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
            if (!$this->accessToken) {
                return [];
            }

            $response = Http::withToken($this->accessToken)
                ->get($this->baseUrl . 'devices');

            if ($response->successful()) {
                $devices = $response->json('devices', []);
                return $this->formatDevices($devices);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Alexa devices: ' . $e->getMessage());
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
            if (!$this->accessToken) {
                return [];
            }

            $response = Http::withToken($this->accessToken)
                ->get($this->baseUrl . 'devices/' . $deviceId . '/state');

            if ($response->successful()) {
                return $response->json('state', []);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Alexa device state: ' . $e->getMessage());
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
            if (!$this->accessToken) {
                return false;
            }

            // Convert our generic state format to Alexa-specific format
            $alexaState = $this->convertToAlexaState($state);

            $response = Http::withToken($this->accessToken)
                ->post($this->baseUrl . 'devices/' . $deviceId . '/directives', [
                    'directive' => [
                        'header' => [
                            'namespace' => 'Alexa.PowerController',
                            'name' => $alexaState['power'] ? 'TurnOn' : 'TurnOff',
                            'messageId' => uniqid(),
                            'correlationToken' => uniqid(),
                        ],
                        'endpoint' => [
                            'endpointId' => $deviceId,
                        ],
                        'payload' => [],
                    ],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to set Alexa device state: ' . $e->getMessage());
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
        // For Alexa, refreshing devices is the same as getting devices
        return $this->getDevices();
    }

    /**
     * Refresh the access token using the refresh token.
     *
     * @return bool
     */
    protected function refreshAccessToken(): bool
    {
        try {
            if (!$this->refreshToken || !$this->clientId || !$this->clientSecret) {
                return false;
            }

            $response = Http::post('https://api.amazon.com/auth/o2/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to refresh Alexa access token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format the devices from the Alexa API to our standard format.
     *
     * @param array $devices
     * @return array
     */
    protected function formatDevices(array $devices): array
    {
        $formattedDevices = [];

        foreach ($devices as $device) {
            $capabilities = [];
            $deviceType = 'unknown';

            // Extract capabilities and determine device type
            if (isset($device['capabilities'])) {
                foreach ($device['capabilities'] as $capability) {
                    if (isset($capability['interface'])) {
                        $capabilities[] = $capability['interface'];

                        if ($capability['interface'] === 'Alexa.PowerController') {
                            $capabilities[] = 'on_off';
                        }

                        if ($capability['interface'] === 'Alexa.BrightnessController') {
                            $capabilities[] = 'brightness';
                        }

                        if ($capability['interface'] === 'Alexa.ColorController') {
                            $capabilities[] = 'color';
                        }

                        // Determine device type based on capabilities
                        if (strpos($capability['interface'], 'Light') !== false) {
                            $deviceType = 'light';
                        } elseif (strpos($capability['interface'], 'Switch') !== false ||
                                 strpos($capability['interface'], 'PowerController') !== false) {
                            $deviceType = 'switch';
                        } elseif (strpos($capability['interface'], 'Thermostat') !== false) {
                            $deviceType = 'thermostat';
                        } elseif (strpos($capability['interface'], 'TemperatureSensor') !== false) {
                            $deviceType = 'sensor';
                        }
                    }
                }
            }

            // Get device state
            $state = $this->getDeviceState($device['id']);
            $isOn = false;

            if (isset($state['power'])) {
                $isOn = $state['power'];
            }

            $formattedDevices[] = [
                'device_id' => $device['id'],
                'name' => $device['displayName'] ?? $device['friendlyName'] ?? 'Unknown Device',
                'device_type' => $deviceType,
                'room' => $device['room'] ?? null,
                'capabilities' => $capabilities,
                'last_state' => [
                    'power' => $isOn,
                ],
            ];
        }

        return $formattedDevices;
    }

    /**
     * Convert our generic state format to Alexa-specific format.
     *
     * @param array $state
     * @return array
     */
    protected function convertToAlexaState(array $state): array
    {
        $alexaState = [];

        if (isset($state['power'])) {
            $alexaState['power'] = (bool) $state['power'];
        }

        return $alexaState;
    }
}
