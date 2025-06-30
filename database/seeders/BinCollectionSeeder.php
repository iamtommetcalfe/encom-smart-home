<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BinCollection;
use Carbon\Carbon;

class BinCollectionSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::create(2025, 7, 2); // First confirmed collection: Black bin on Wed 2nd July
        $end = Carbon::create(2025, 11, 26); // Last date in the PDF

        $date = $start->copy();
        $week = 1;

        while ($date->lte($end)) {
            $binType = $week % 2 === 1 ? 'Rubbish' : 'Recycling & Garden Waste';
            $color = $week % 2 === 1 ? 'Black' : 'Green/Red';

            BinCollection::create([
                'collection_date' => $date->format('Y-m-d'),
                'bin_type' => $binType,
                'color' => $color,
            ]);

            $date->addWeek();
            $week++;
        }
    }
}
