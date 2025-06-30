<?php

namespace Database\Factories;

use App\Models\BinCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

class BinCollectionFactory extends Factory
{
    protected $model = BinCollection::class;

    public function definition(): array
    {
        static $week = 1;
        static $date = null;

        if (!$date) {
            $date = \Carbon\Carbon::create(2025, 3, 5); // Start date: Wed 5th March 2025
        }

        $binType = $week % 2 === 1 ? 'Rubbish' : 'Recycling & Garden Waste';
        $color = $week % 2 === 1 ? 'Black' : 'Green/Red';

        $current = clone $date;

        $date->addWeek();
        $week++;

        return [
            'collection_date' => $current->format('Y-m-d'),
            'bin_type' => $binType,
            'color' => $color,
        ];
    }
}
