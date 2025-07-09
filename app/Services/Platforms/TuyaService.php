<?php

namespace App\Services\Platforms;

use App\Services\Interfaces\SmartHomePlatformInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TuyaService implements SmartHomePlatformInterface
{
    /**
     * The base URL for the Tuya API.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The client ID for the Tuya API.
     *
     * @var string|null
     */
    protected $clientId = null;

    /**
     * The client secret for the Tuya API.
     *
     * @var string|null
     */
    protected $clientSecret = null;

    /**
     * The region for the Tuya API.
     *
     * @var string|null
     */
    protected $region = null;

    /**
     * The access token for the Tuya API.
     *
     * @var string|null
     */
    protected $accessToken = null;

    /**
     * The timestamp of when the access token expires.
     *
     * @var int|null
     */
    protected $tokenExpires = null;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->clientId = config('services.tuya.client_id');
        $this->clientSecret = config('services.tuya.client_secret');
        $this->region = config('services.tuya.region', 'eu');

        // Set the base URL based on the region
        $this->baseUrl = "https://openapi.tuya{$this->region}.com";
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
            if (isset($credentials['client_id'])) {
                $this->clientId = $credentials['client_id'];
            }

            if (isset($credentials['client_secret'])) {
                $this->clientSecret = $credentials['client_secret'];
            }

            if (isset($credentials['region'])) {
                $this->region = $credentials['region'];
                $this->baseUrl = "https://openapi.tuya{$this->region}.com";
            }

            // Get an access token
            return $this->getAccessToken();
        } catch (\Exception $e) {
            Log::error('Failed to connect to Tuya: ' . $e->getMessage());
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
        $this->clientId = null;
        $this->clientSecret = null;
        $this->accessToken = null;
        $this->tokenExpires = null;
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
            if (!$this->ensureAccessToken()) {
                return [];
            }

            $response = $this->makeRequest('GET', '/v1.0/users/me/devices');

            if ($response->successful()) {
                $devices = $response->json('result', []);
                return $this->formatDevices($devices);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Tuya devices: ' . $e->getMessage());
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
            if (!$this->ensureAccessToken()) {
                return [];
            }

            $response = $this->makeRequest('GET', "/v1.0/devices/{$deviceId}/status");

            if ($response->successful()) {
                $status = $response->json('result', []);
                return $this->formatDeviceState($status);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Tuya device state: ' . $e->getMessage());
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
            if (!$this->ensureAccessToken()) {
                return false;
            }

            // Convert our generic state format to Tuya-specific format
            $commands = $this->convertToTuyaCommands($state);

            if (empty($commands)) {
                Log::error('No valid commands generated for Tuya device');
                return false;
            }

            // Log the commands being sent
            Log::info('Sending commands to Tuya device', [
                'deviceId' => $deviceId,
                'commands' => $commands,
                'state' => $state
            ]);

            $response = $this->makeRequest('POST', "/v1.0/devices/{$deviceId}/commands", [
                'commands' => $commands
            ]);

            if (!$response->successful()) {
                Log::error('Tuya API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            Log::info('Successfully set Tuya device state', [
                'deviceId' => $deviceId,
                'response' => $response->json()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to set Tuya device state: ' . $e->getMessage(), [
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
        // For Tuya, refreshing devices is the same as getting devices
        return $this->getDevices();
    }

    /**
     * Get an access token from the Tuya API.
     *
     * @return bool
     */
    protected function getAccessToken(): bool
    {
        try {
            if (!$this->clientId || !$this->clientSecret) {
                return false;
            }

            // Calculate the signature
            $timestamp = round(microtime(true) * 1000);
            $stringToSign = $this->clientId . $timestamp;
            $signStr = hash_hmac('sha256', $stringToSign, $this->clientSecret);
            $sign = strtoupper($signStr);

            $response = Http::post("{$this->baseUrl}/v1.0/token?grant_type=1", [
                'headers' => [
                    'client_id' => $this->clientId,
                    'sign' => $sign,
                    'sign_method' => 'HMAC-SHA256',
                    't' => $timestamp,
                    'nonce' => '',
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json('result', []);
                $this->accessToken = $data['access_token'] ?? null;
                $this->tokenExpires = time() + ($data['expire_time'] ?? 7200);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to get Tuya access token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure we have a valid access token.
     *
     * @return bool
     */
    protected function ensureAccessToken(): bool
    {
        if (!$this->accessToken || !$this->tokenExpires || time() >= $this->tokenExpires) {
            return $this->getAccessToken();
        }

        return true;
    }

    /**
     * Make a request to the Tuya API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        // Calculate the signature
        $timestamp = round(microtime(true) * 1000);
        $stringToSign = $this->clientId . $this->accessToken . $timestamp;
        $signStr = hash_hmac('sha256', $stringToSign, $this->clientSecret);
        $sign = strtoupper($signStr);

        $headers = [
            'client_id' => $this->clientId,
            'access_token' => $this->accessToken,
            'sign' => $sign,
            'sign_method' => 'HMAC-SHA256',
            't' => $timestamp,
            'nonce' => '',
        ];

        $url = $this->baseUrl . $endpoint;

        if ($method === 'GET') {
            return Http::withHeaders($headers)->get($url, $data);
        } elseif ($method === 'POST') {
            return Http::withHeaders($headers)->post($url, $data);
        } elseif ($method === 'PUT') {
            return Http::withHeaders($headers)->put($url, $data);
        } elseif ($method === 'DELETE') {
            return Http::withHeaders($headers)->delete($url, $data);
        }

        throw new \InvalidArgumentException("Unsupported HTTP method: {$method}");
    }

    /**
     * Format the devices from the Tuya API to our standard format.
     *
     * @param array $devices
     * @return array
     */
    protected function formatDevices(array $devices): array
    {
        $formattedDevices = [];

        foreach ($devices as $device) {
            $deviceId = $device['id'] ?? '';
            $capabilities = [];
            $deviceType = 'unknown';

            // Determine device type and capabilities based on device category
            $category = $device['category'] ?? '';

            if (strpos($category, 'light') !== false) {
                $deviceType = 'light';
                $capabilities[] = 'on_off';

                // Check if the device supports brightness and color
                if (isset($device['status'])) {
                    foreach ($device['status'] as $status) {
                        if ($status['code'] === 'bright') {
                            $capabilities[] = 'brightness';
                        }
                        if ($status['code'] === 'colour_data') {
                            $capabilities[] = 'color';
                        }
                    }
                }
            } elseif (strpos($category, 'switch') !== false || strpos($category, 'socket') !== false) {
                $deviceType = 'switch';
                $capabilities[] = 'on_off';
            } elseif (strpos($category, 'thermostat') !== false) {
                $deviceType = 'thermostat';
                $capabilities[] = 'temperature';
            } elseif (strpos($category, 'sensor') !== false) {
                $deviceType = 'sensor';
                $capabilities[] = 'temperature';
            }

            // Get device state
            $state = $this->getDeviceState($deviceId);
            $isOn = $state['power'] ?? false;

            $formattedDevices[] = [
                'device_id' => $deviceId,
                'name' => $device['name'] ?? 'Unknown Device',
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
     * Format the device state from the Tuya API to our standard format.
     *
     * @param array $status
     * @return array
     */
    protected function formatDeviceState(array $status): array
    {
        $state = [
            'power' => false,
            'brightness' => null,
            'color' => null,
            'temperature' => null,
        ];

        foreach ($status as $item) {
            if (!isset($item['code']) || !isset($item['value'])) {
                continue;
            }

            $code = $item['code'];
            $value = $item['value'];

            if ($code === 'switch' || $code === 'switch_1') {
                $state['power'] = $value === true || $value === 'true' || $value === 1 || $value === '1';
            } elseif ($code === 'bright' || $code === 'brightness') {
                $state['brightness'] = $value;
            } elseif ($code === 'colour_data' || $code === 'color') {
                $state['color'] = $value;
            } elseif ($code === 'temp_current' || $code === 'temperature') {
                $state['temperature'] = $value;
            }
        }

        return $state;
    }

    /**
     * Convert our generic state format to Tuya-specific command format.
     *
     * @param array $state
     * @return array
     */
    protected function convertToTuyaCommands(array $state): array
    {
        $commands = [];

        if (isset($state['power'])) {
            $commands[] = [
                'code' => 'switch_1',
                'value' => $state['power']
            ];
        }

        if (isset($state['brightness'])) {
            $commands[] = [
                'code' => 'bright',
                'value' => $state['brightness']
            ];
        }

        if (isset($state['color'])) {
            $commands[] = [
                'code' => 'colour_data',
                'value' => $state['color']
            ];
        }

        if (isset($state['temperature'])) {
            $commands[] = [
                'code' => 'temp_set',
                'value' => $state['temperature']
            ];
        }

        return $commands;
    }
}
