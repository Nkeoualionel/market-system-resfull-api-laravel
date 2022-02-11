<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Buyers;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $seller = Seller::has('products')->get()->random();
        $buyer = User::all()->except($seller->id)->random();

        return [
            //
            "quantity" => $this->faker->randomNumber(1, 10),
            "buyer_id" => $buyer->id,
            "product_id" => $seller->products->random()->id,

        ];
    }
}