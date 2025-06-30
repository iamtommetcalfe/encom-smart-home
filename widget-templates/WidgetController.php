<?php

namespace App\Http\Controllers;

use App\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WidgetController extends Controller
{
    /**
     * Display a listing of the widgets.
     */
    public function index(): View
    {
        $widgets = Widget::all();
        
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

        Widget::create($validated);

        return redirect()->route('widgets.index')
            ->with('success', 'Widget created successfully.');
    }

    /**
     * Display the specified widget.
     */
    public function show(Widget $widget): View
    {
        return view('widgets.show', compact('widget'));
    }

    /**
     * Show the form for editing the specified widget.
     */
    public function edit(Widget $widget): View
    {
        return view('widgets.edit', compact('widget'));
    }

    /**
     * Update the specified widget in storage.
     */
    public function update(Request $request, Widget $widget): RedirectResponse
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

        $widget->update($validated);

        return redirect()->route('widgets.index')
            ->with('success', 'Widget updated successfully.');
    }

    /**
     * Remove the specified widget from storage.
     */
    public function destroy(Widget $widget): RedirectResponse
    {
        $widget->delete();

        return redirect()->route('widgets.index')
            ->with('success', 'Widget deleted successfully.');
    }

    /**
     * Update the position of a widget via AJAX.
     */
    public function updatePosition(Request $request, Widget $widget)
    {
        $validated = $request->validate([
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
        ]);

        $widget->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Update the size of a widget via AJAX.
     */
    public function updateSize(Request $request, Widget $widget)
    {
        $validated = $request->validate([
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ]);

        $widget->update($validated);

        return response()->json(['success' => true]);
    }
}