<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesWidgetRequests;
use App\Services\WidgetService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WidgetApiController extends Controller
{
    use ValidatesWidgetRequests;

    /**
     * The widget service instance.
     *
     * @var WidgetService
     */
    protected $widgetService;

    /**
     * Create a new controller instance.
     *
     * @param WidgetService $widgetService
     * @return void
     */
    public function __construct(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }

    /**
     * Display a listing of the widgets.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $widgets = $this->widgetService->getAllWidgets();

        return response()->json([
            'widgets' => $widgets
        ]);
    }

    /**
     * Store a newly created widget in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->widgetValidationRules());

        $widget = $this->widgetService->createWidget($validated);

        return response()->json([
            'message' => 'Widget created successfully',
            'widget' => $widget
        ], 201);
    }

    /**
     * Display the specified widget.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $widget = $this->widgetService->findWidget($id);

        return response()->json([
            'widget' => $widget
        ]);
    }

    /**
     * Update the specified widget in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate($this->widgetValidationRules());

        $widget = $this->widgetService->updateWidget($id, $validated);

        return response()->json([
            'message' => 'Widget updated successfully',
            'widget' => $widget
        ]);
    }

    /**
     * Remove the specified widget from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->widgetService->deleteWidget($id);

        return response()->json([
            'message' => 'Widget deleted successfully'
        ]);
    }

    /**
     * Update the position of a widget.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePosition(Request $request, $id): JsonResponse
    {
        $validated = $request->validate($this->positionValidationRules());

        $this->widgetService->updateWidget($id, $validated);

        return response()->json(['success' => true]);
    }

    /**
     * Update the size of a widget.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateSize(Request $request, $id): JsonResponse
    {
        $validated = $request->validate($this->sizeValidationRules());

        $this->widgetService->updateWidget($id, $validated);

        return response()->json(['success' => true]);
    }
}
