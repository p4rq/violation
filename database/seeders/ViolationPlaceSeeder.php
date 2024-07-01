<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ViolationPlace;

class ViolationPlaceSeeder extends Seeder
{
    public function run()
    {
        // Create 200 parent records in the violation_places table without child elements
        $parents = ViolationPlace::factory()->count(1000)->create();

        // Create child elements for each parent
        foreach ($parents as $parent) {
            ViolationPlace::factory()->count(4)->create(['parent_id' => $parent->id]);
        }
    }
}
