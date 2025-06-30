<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'position_x',
        'position_y',
        'width',
        'height',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'json',
    ];

    /**
     * Get the formatted settings for the widget.
     *
     * @return array
     */
    public function getFormattedSettings(): array
    {
        return $this->settings ?? [];
    }

    /**
     * Get the view component for this widget type.
     *
     * @return string
     */
    public function getViewComponent(): string
    {
        return match($this->type) {
            'weather' => 'widgets.weather',
            'bin-collection' => 'widgets.bin-collection',
            'plant-watering' => 'widgets.plant-watering',
            default => 'widgets.default',
        };
    }
}