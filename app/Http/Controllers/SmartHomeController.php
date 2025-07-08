<?php

namespace App\Http\Controllers;

use App\Services\SmartHomeService;
use App\Services\SmartHomeWidgetService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SmartHomeController extends Controller
{
    /**
     * The smart home service instance.
     *
     * @var SmartHomeService
     */
    protected $smartHomeService;

    /**
     * The smart home widget service instance.
     *
     * @var SmartHomeWidgetService
     */
    protected $smartHomeWidgetService;

    /**
     * Create a new controller instance.
     *
     * @param SmartHomeService $smartHomeService
     * @param SmartHomeWidgetService $smartHomeWidgetService
     * @return void
     */
    public function __construct(SmartHomeService $smartHomeService, SmartHomeWidgetService $smartHomeWidgetService)
    {
        $this->smartHomeService = $smartHomeService;
        $this->smartHomeWidgetService = $smartHomeWidgetService;
    }

    /**
     * Display a listing of the platforms and devices.
     *
     * @return View
     */
    public function index(): View
    {
        $platforms = $this->smartHomeService->getPlatforms();
        $devices = $this->smartHomeService->getDevices();

        return view('smart-home.index', compact('platforms', 'devices'));
    }

    /**
     * Get all platforms.
     *
     * @return JsonResponse
     */
    public function getPlatforms(): JsonResponse
    {
        $platforms = $this->smartHomeService->getPlatforms();

        return response()->json([
            'platforms' => $platforms
        ]);
    }

    /**
     * Get a specific platform.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getPlatform($id): JsonResponse
    {
        $platform = $this->smartHomeService->getPlatform($id);

        if (!$platform) {
            return response()->json([
                'message' => 'Platform not found'
            ], 404);
        }

        return response()->json([
            'platform' => $platform
        ]);
    }

    /**
     * Store a newly created platform.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storePlatform(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:smart_home_platforms',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'credentials' => 'nullable|array'
        ]);

        $platform = $this->smartHomeService->createPlatform($validated);

        return response()->json([
            'message' => 'Platform created successfully',
            'platform' => $platform
        ], 201);
    }

    /**
     * Update the specified platform.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePlatform(Request $request, $id): JsonResponse
    {
        $platform = $this->smartHomeService->getPlatform($id);

        if (!$platform) {
            return response()->json([
                'message' => 'Platform not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|max:255|unique:smart_home_platforms,slug,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'credentials' => 'nullable|array'
        ]);

        $platform = $this->smartHomeService->updatePlatform($id, $validated);

        return response()->json([
            'message' => 'Platform updated successfully',
            'platform' => $platform
        ]);
    }

    /**
     * Remove the specified platform.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deletePlatform($id): JsonResponse
    {
        $platform = $this->smartHomeService->getPlatform($id);

        if (!$platform) {
            return response()->json([
                'message' => 'Platform not found'
            ], 404);
        }

        $this->smartHomeService->deletePlatform($id);

        return response()->json([
            'message' => 'Platform deleted successfully'
        ]);
    }

    /**
     * Get all devices.
     *
     * @return JsonResponse
     */
    public function getDevices(): JsonResponse
    {
        $devices = $this->smartHomeService->getDevices();

        return response()->json([
            'devices' => $devices
        ]);
    }

    /**
     * Get devices for a specific platform.
     *
     * @param int $platformId
     * @return JsonResponse
     */
    public function getDevicesForPlatform($platformId): JsonResponse
    {
        $platform = $this->smartHomeService->getPlatform($platformId);

        if (!$platform) {
            return response()->json([
                'message' => 'Platform not found'
            ], 404);
        }

        $devices = $this->smartHomeService->getDevicesForPlatform($platformId);

        return response()->json([
            'devices' => $devices
        ]);
    }

    /**
     * Get a specific device.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getDevice($id): JsonResponse
    {
        $device = $this->smartHomeService->getDevice($id);

        if (!$device) {
            return response()->json([
                'message' => 'Device not found'
            ], 404);
        }

        return response()->json([
            'device' => $device
        ]);
    }

    /**
     * Store a newly created device.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform_id' => 'required|exists:smart_home_platforms,id',
            'name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255',
            'device_type' => 'required|string|max:255',
            'room' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'capabilities' => 'nullable|array',
            'last_state' => 'nullable|array',
            'settings' => 'nullable|array'
        ]);

        $device = $this->smartHomeService->createDevice($validated);

        return response()->json([
            'message' => 'Device created successfully',
            'device' => $device
        ], 201);
    }

    /**
     * Update the specified device.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateDevice(Request $request, $id): JsonResponse
    {
        $device = $this->smartHomeService->getDevice($id);

        if (!$device) {
            return response()->json([
                'message' => 'Device not found'
            ], 404);
        }

        $validated = $request->validate([
            'platform_id' => 'exists:smart_home_platforms,id',
            'name' => 'string|max:255',
            'device_id' => 'string|max:255',
            'device_type' => 'string|max:255',
            'room' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'capabilities' => 'nullable|array',
            'last_state' => 'nullable|array',
            'settings' => 'nullable|array'
        ]);

        $device = $this->smartHomeService->updateDevice($id, $validated);

        return response()->json([
            'message' => 'Device updated successfully',
            'device' => $device
        ]);
    }

    /**
     * Remove the specified device.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteDevice($id): JsonResponse
    {
        $device = $this->smartHomeService->getDevice($id);

        if (!$device) {
            return response()->json([
                'message' => 'Device not found'
            ], 404);
        }

        $this->smartHomeService->deleteDevice($id);

        return response()->json([
            'message' => 'Device deleted successfully'
        ]);
    }

    /**
     * Toggle a device's state.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function toggleDevice($id): JsonResponse
    {
        $device = $this->smartHomeService->getDevice($id);

        if (!$device) {
            return response()->json([
                'message' => 'Device not found'
            ], 404);
        }

        $result = $this->smartHomeService->toggleDevice($id);

        // Check if the result is an error response
        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            return response()->json([
                'message' => $result['message'] ?? 'Failed to toggle device',
                'success' => false
            ], 400);
        }

        return response()->json([
            'message' => 'Device toggled successfully',
            'device' => $result,
            'success' => true
        ]);
    }

    /**
     * Connect to a platform and sync devices.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function connectAndSyncPlatform($id): JsonResponse
    {
        $platform = $this->smartHomeService->getPlatform($id);

        if (!$platform) {
            return response()->json([
                'message' => 'Platform not found'
            ], 404);
        }

        $result = $this->smartHomeService->connectAndSyncPlatform($id);

        // Check if the result is an error response
        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            // Log the specific reason for the failure
            Log::error("Failed to connect and sync platform: " . ($result['message'] ?? 'Unknown error'));

            return response()->json([
                'message' => 'Failed to connect and sync platform',
                'error' => $result['message'] ?? 'Unknown error'
            ], 500);
        }

        return response()->json([
            'message' => 'Platform connected and devices synced successfully'
        ]);
    }

    /**
     * Refresh devices for a specific platform.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function refreshPlatformDevices($id): JsonResponse
    {
        $platform = $this->smartHomeService->getPlatform($id);

        if (!$platform) {
            return response()->json([
                'message' => 'Platform not found'
            ], 404);
        }

        $result = $this->smartHomeService->refreshPlatformDevices($id);

        // Check if the result is an error response
        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            // Log the specific reason for the failure
            Log::error("Failed to refresh platform devices: " . ($result['message'] ?? 'Unknown error'));

            return response()->json([
                'message' => 'Failed to refresh platform devices',
                'error' => $result['message'] ?? 'Unknown error'
            ], 500);
        }

        return response()->json([
            'message' => 'Platform devices refreshed successfully'
        ]);
    }

    /**
     * Get the widget configuration.
     *
     * @return JsonResponse
     */
    public function getWidgetConfig(): JsonResponse
    {
        // For simplicity, we'll assume there's only one widget configuration
        // In a real application, you might want to get the configuration for the current user
        $widgetConfigs = $this->smartHomeWidgetService->getWidgetConfigs();
        $widgetConfig = $widgetConfigs->first();

        if (!$widgetConfig) {
            // If no widget configuration exists, create one
            $widgetConfig = $this->smartHomeWidgetService->createWidgetConfig([
                'user_id' => auth()->id(),
                'name' => 'Default Smart Device Widget',
                'devices' => []
            ]);
        }

        // Get the devices for the widget
        $devices = $this->smartHomeWidgetService->getDevicesForWidget($widgetConfig->id);

        return response()->json([
            'widgetConfig' => $widgetConfig,
            'devices' => $devices
        ]);
    }

    /**
     * Save the widget configuration.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveWidgetConfig(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'devices' => 'required|array',
            'devices.*' => 'integer|exists:smart_devices,id'
        ]);

        // For simplicity, we'll assume there's only one widget configuration
        // In a real application, you might want to get the configuration for the current user
        $widgetConfigs = $this->smartHomeWidgetService->getWidgetConfigs();
        $widgetConfig = $widgetConfigs->first();

        if (!$widgetConfig) {
            // If no widget configuration exists, create one
            $widgetConfig = $this->smartHomeWidgetService->createWidgetConfig([
                'user_id' => auth()->id(),
                'name' => 'Default Smart Device Widget',
                'devices' => $validated['devices']
            ]);
        } else {
            // Update the existing widget configuration
            $widgetConfig = $this->smartHomeWidgetService->updateWidgetConfig($widgetConfig->id, [
                'devices' => $validated['devices']
            ]);
        }

        return response()->json([
            'message' => 'Widget configuration saved successfully',
            'widgetConfig' => $widgetConfig
        ]);
    }
}
