<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartHomePlatform extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'credentials',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'json',
    ];

    /**
     * Get the devices for the platform.
     */
    public function devices()
    {
        return $this->hasMany(SmartDevice::class, 'platform_id');
    }
}
