<?php

namespace App\Http\Controllers;

use App\Http\Traits\ValidatesWidgetRequests;
use App\Services\WidgetService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class WidgetController extends Controller
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
        $validated = $request->validate($this->widgetValidationRules());

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
        $validated = $request->validate($this->widgetValidationRules());

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
        $validated = $request->validate($this->positionValidationRules());

        $this->widgetService->updateWidget($id, $validated);

        return response()->json(['success' => true]);
    }

    /**
     * Update the size of a widget via AJAX.
     */
    public function updateSize(Request $request, $id)
    {
        $validated = $request->validate($this->sizeValidationRules());

        $this->widgetService->updateWidget($id, $validated);

        return response()->json(['success' => true]);
    }
}
