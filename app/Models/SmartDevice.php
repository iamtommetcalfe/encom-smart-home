<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartDevice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'platform_id',
        'name',
        'device_id',
        'device_type',
        'room',
        'is_active',
        'capabilities',
        'last_state',
        'last_updated',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'capabilities' => 'json',
        'last_state' => 'json',
        'last_updated' => 'datetime',
        'settings' => 'json',
    ];

    /**
     * Get the platform that owns the device.
     */
    public function platform()
    {
        return $this->belongsTo(SmartHomePlatform::class);
    }
}
