<?php

namespace App\Http\Controllers;

use App\Services\WidgetService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class WidgetController extends Controller
{
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
     */
    public function index(): View
    {
        $widgets = $this->widgetService->getAllWidgets();

        return view('widgets.index', compact('widgets'));
    }

    /**
     * Show the form for creating a new widget.
     */
    public function create(): View
    {
        return view('widgets.create');
    }

    /**
     * Store a newly created widget in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'settings' => 'nullable|json',
        ]);

        $this->widgetService->createWidget($validated);

        return redirect()->route('widgets.index')
            ->with('success', 'Widget created successfully.');
    }

    /**
     * Display the specified widget.
     */
    public function show($id): View
    {
        $widget = $this->widgetService->findWidget($id);
        return view('widgets.show', compact('widget'));
    }

    /**
     * Show the form for editing the specified widget.
     */
    public function edit($id): View
    {
        $widget = $this->widgetService->findWidget($id);
        return view('widgets.edit', compact('widget'));
    }

    /**
     * Update the specified widget in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'settings' => 'nullable|json',
        ]);

        $this->widgetService->updateWidget($id, $validated);

        return redirect()->route('widgets.index')
            ->with('success', 'Widget updated successfully.');
    }

    /**
     * Remove the specified widget from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $this->widgetService->deleteWidget($id);

        return redirect()->route('widgets.index')
            ->with('success', 'Widget deleted successfully.');
    }

    /**
     * Update the position of a widget via AJAX.
     */
    public function updatePosition(Request $request, $id)
    {
        $validated = $request->validate([
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
        ]);

        $this->widgetService->updateWidget($id, $validated);

        return response()->json(['success' => true]);
    }

    /**
     * Update the size of a widget via AJAX.
     */
    public function updateSize(Request $request, $id)
    {
        $validated = $request->validate([
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ]);

        $this->widgetService->updateWidget($id, $validated);

        return response()->json(['success' => true]);
    }
    /**
     * Display a listing of the widgets (API).
     *
     * @return JsonResponse
     */
    public function indexApi(): JsonResponse
    {
        $widgets = $this->widgetService->getAllWidgets();

        return response()->json([
            'widgets' => $widgets
        ]);
    }

    /**
     * Store a newly created widget in storage (API).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'settings' => 'nullable|json',
        ]);

        $widget = $this->widgetService->createWidget($validated);

        return response()->json([
            'message' => 'Widget created successfully',
            'widget' => $widget
        ], 201);
    }

    /**
     * Display the specified widget (API).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showApi($id): JsonResponse
    {
        $widget = $this->widgetService->findWidget($id);

        return response()->json([
            'widget' => $widget
        ]);
    }

    /**
     * Update the specified widget in storage (API).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateApi(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'settings' => 'nullable|json',
        ]);

        $widget = $this->widgetService->updateWidget($id, $validated);

        return response()->json([
            'message' => 'Widget updated successfully',
            'widget' => $widget
        ]);
    }

    /**
     * Remove the specified widget from storage (API).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroyApi($id): JsonResponse
    {
        $this->widgetService->deleteWidget($id);

        return response()->json([
            'message' => 'Widget deleted successfully'
        ]);
    }
}
