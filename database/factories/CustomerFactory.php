<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $this->faker->name(),
            'owner' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => $this->faker->boolean(),
            'is_phone' => $this->faker->phoneNumber(),
            'work_type' => $this->faker->word(),
            'payment_day' => $this->faker->word(),
        ];
    }
}
