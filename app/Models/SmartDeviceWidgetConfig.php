<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartDeviceWidgetConfig extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'devices',
        'layout',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'devices' => 'json',
        'layout' => 'json',
    ];

    /**
     * Get the user that owns the widget configuration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
