<?php

namespace Database\Factories;

use App\Models\ViolationPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ViolationPlaceFactory extends Factory
{
    protected $model = ViolationPlace::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'is_active' => $this->faker->boolean,
            'is_selectable' => $this->faker->boolean,
            'parent_id' => null, // For initial records, the parent will be null
        ];
    }
}
