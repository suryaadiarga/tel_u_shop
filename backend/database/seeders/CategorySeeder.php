<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
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
        ];

        $categoryMap = [];
        foreach ($categories as $name) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => 'Kategori ' . $name,
                ]
            );
            $categoryMap[$name] = $category->id;
        }

        Product::whereNotNull('category')
            ->whereNull('category_id')
            ->chunkById(200, function ($products) use ($categoryMap) {
                foreach ($products as $product) {
                    $categoryId = $categoryMap[$product->category] ?? null;
                    if ($categoryId) {
                        $product->update(['category_id' => $categoryId]);
                    }
                }
            });
    }
}
