<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(10000, 500000),
            'stock' => $this->faker->numberBetween(0, 100),
            'image_url' => $this->faker->imageUrl(640, 480, 'products', true),
            'category' => $this->faker->randomElement([
                'Makanan',
                'Minuman',
                'Elektronik',
                'Fashion',
                'Kecantikan',
                'Kesehatan',
                'Rumah Tangga',
                'Buku',
                'Olahraga',
                'Otomotif',
                'Hobi',
                'Bayi & Anak',
                'Perlengkapan Hewan',
                'Sembako',
            ]),
            'prep_time' => $this->faker->numberBetween(5, 60),
            'is_available' => $this->faker->boolean(90),
        ];
    }
}
