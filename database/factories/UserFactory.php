<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    // protected $model = User::class;
    protected $model = Item::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return[
            'name' => $this->faker->name,
            'price' => $this->faker->numerify('###'),
            'category_id' => 3,


        ];
        return[
            'name' => $this->faker->name,
            'phone_no' => $this->faker->numerify('98########'),
        ];

        // return [
        //     'name' => $this->faker->name,
        //     'email' => $this->faker->unique()->safeEmail,
        //     'email_verified_at' => now(),
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //     'remember_token' => Str::random(10),
        // ];
    }
}
