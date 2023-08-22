<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $customer = Customer::factory()->count(1)->create();
        $product = Product::factory()->count(1)->create();
        return [
            'customerId' => $customer->get(0)->getKey(),
            'customerName' => $customer->get(0)->customerName,
            'amount' => $this->faker->randomFloat(2, 0, 10_000_000_000),
            'quality' => $this->faker->randomDigit(),
            'productId' => $product->get(0)->getKey(),
            'productName' => $product->get(0)->productName,
            'orderDate' => Carbon::now(),
        ];
    }
}
