<?php

namespace App\Http\Controllers;

use App\Services\SmartHomeWidgetService;
use App\Services\SmartHomeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SmartHomeWidgetController extends Controller
{
    /**
     * The smart home widget service instance.
     *
     * @var SmartHomeWidgetService
     */
    protected $widgetService;

    /**
     * The smart home service instance.
     *
     * @var SmartHomeService
     */
    protected $smartHomeService;

    /**
     * Create a new controller instance.
     *
     * @param SmartHomeWidgetService $widgetService
     * @param SmartHomeService $smartHomeService
     * @return void
     */
    public function __construct(SmartHomeWidgetService $widgetService, SmartHomeService $smartHomeService)
    {
        $this->widgetService = $widgetService;
        $this->smartHomeService = $smartHomeService;
    }

    /**
     * Display a listing of the widget configurations.
     *
     * @return View
     */
    public function index(): View
    {
        $widgetConfigs = $this->widgetService->getWidgetConfigs();
        $devices = $this->smartHomeService->getDevices();

        return view('smart-home.widgets.index', compact('widgetConfigs', 'devices'));
    }

    /**
     * Show the form for creating a new widget configuration.
     *
     * @return View
     */
    public function create(): View
    {
        $devices = $this->smartHomeService->getDevices();

        return view('smart-home.widgets.create', compact('devices'));
    }

    /**
     * Store a newly created widget configuration.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'devices' => 'required|array',
            'devices.*' => 'exists:smart_devices,id',
            'layout' => 'nullable|array'
        ]);

        $widgetConfig = $this->widgetService->createWidgetConfig($validated);

        return response()->json([
            'message' => 'Widget configuration created successfully',
            'widget_config' => $widgetConfig
        ], 201);
    }

    /**
     * Display the specified widget configuration.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $widgetConfig = $this->widgetService->getWidgetConfig($id);

        if (!$widgetConfig) {
            return response()->json([
                'message' => 'Widget configuration not found'
            ], 404);
        }

        $devices = $this->widgetService->getDevicesForWidget($id);

        return response()->json([
            'widget_config' => $widgetConfig,
            'devices' => $devices
        ]);
    }

    /**
     * Show the form for editing the specified widget configuration.
     *
     * @param int $id
     * @return View
     */
    public function edit($id): View
    {
        $widgetConfig = $this->widgetService->getWidgetConfig($id);
        $devices = $this->smartHomeService->getDevices();
        $widgetDevices = $this->widgetService->getDevicesForWidget($id);

        return view('smart-home.widgets.edit', compact('widgetConfig', 'devices', 'widgetDevices'));
    }

    /**
     * Update the specified widget configuration.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $widgetConfig = $this->widgetService->getWidgetConfig($id);

        if (!$widgetConfig) {
            return response()->json([
                'message' => 'Widget configuration not found'
            ], 404);
        }

        $validated = $request->validate([
            'user_id' => 'exists:users,id',
            'name' => 'string|max:255',
            'devices' => 'array',
            'devices.*' => 'exists:smart_devices,id',
            'layout' => 'nullable|array'
        ]);

        $widgetConfig = $this->widgetService->updateWidgetConfig($id, $validated);

        return response()->json([
            'message' => 'Widget configuration updated successfully',
            'widget_config' => $widgetConfig
        ]);
    }

    /**
     * Remove the specified widget configuration.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $widgetConfig = $this->widgetService->getWidgetConfig($id);

        if (!$widgetConfig) {
            return response()->json([
                'message' => 'Widget configuration not found'
            ], 404);
        }

        $this->widgetService->deleteWidgetConfig($id);

        return response()->json([
            'message' => 'Widget configuration deleted successfully'
        ]);
    }

    /**
     * Get widget configurations for the current user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserWidgetConfigs(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $widgetConfigs = $this->widgetService->getWidgetConfigsForUser($userId);

        return response()->json([
            'widget_configs' => $widgetConfigs
        ]);
    }

    /**
     * Add a device to a widget configuration.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addDeviceToWidget(Request $request, $id): JsonResponse
    {
        $widgetConfig = $this->widgetService->getWidgetConfig($id);

        if (!$widgetConfig) {
            return response()->json([
                'message' => 'Widget configuration not found'
            ], 404);
        }

        $validated = $request->validate([
            'device_id' => 'required|exists:smart_devices,id'
        ]);

        $widgetConfig = $this->widgetService->addDeviceToWidget($id, $validated['device_id']);

        return response()->json([
            'message' => 'Device added to widget configuration successfully',
            'widget_config' => $widgetConfig
        ]);
    }

    /**
     * Remove a device from a widget configuration.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function removeDeviceFromWidget(Request $request, $id): JsonResponse
    {
        $widgetConfig = $this->widgetService->getWidgetConfig($id);

        if (!$widgetConfig) {
            return response()->json([
                'message' => 'Widget configuration not found'
            ], 404);
        }

        $validated = $request->validate([
            'device_id' => 'required|exists:smart_devices,id'
        ]);

        $widgetConfig = $this->widgetService->removeDeviceFromWidget($id, $validated['device_id']);

        return response()->json([
            'message' => 'Device removed from widget configuration successfully',
            'widget_config' => $widgetConfig
        ]);
    }
}
