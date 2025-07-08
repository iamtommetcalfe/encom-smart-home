<?php

namespace App\Http\Traits;

trait ValidatesWidgetRequests
{
    /**
     * Get the validation rules for a widget request.
     *
     * @return array
     */
    protected function widgetValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'settings' => 'nullable|json',
        ];
    }

    /**
     * Get the validation rules for a widget position update.
     *
     * @return array
     */
    protected function positionValidationRules(): array
    {
        return [
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
        ];
    }

    /**
     * Get the validation rules for a widget size update.
     *
     * @return array
     */
    protected function sizeValidationRules(): array
    {
        return [
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ];
    }
}
