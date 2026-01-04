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
            'Makanan & Minuman',
            'Sembako',
            'Elektronik',
            'Handphone & Aksesoris',
            'Fashion Pria',
            'Fashion Wanita',
            'Kecantikan',
            'Kesehatan',
            'Rumah Tangga',
            'Peralatan Dapur',
            'Buku & Alat Tulis',
            'Olahraga',
            'Otomotif',
            'Hobi & Koleksi',
            'Bayi & Anak',
            'Perlengkapan Hewan',
            'Perlengkapan Ibadah',
            'Pulsa & Tagihan',
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
