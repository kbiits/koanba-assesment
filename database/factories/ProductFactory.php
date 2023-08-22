<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'productName' => $this->faker->name(),
            'productPrice' => $this->faker->randomNumber(8, false),
            'productDescription' => $this->faker->paragraph(5),
            'stock' => $this->faker->numberBetween(0, 100_000),
        ];
    }
}
