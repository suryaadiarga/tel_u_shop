<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $categoryProducts = [
            'Makanan & Minuman' => [
                'Kopi Bubuk',
                'Teh Celup',
                'Mie Instan',
                'Biskuit Cokelat',
                'Susu UHT',
            ],
            'Sembako' => [
                'Beras Premium 5kg',
                'Gula Pasir 1kg',
                'Minyak Goreng 1L',
                'Tepung Terigu 1kg',
                'Telur Ayam 1kg',
            ],
            'Elektronik' => [
                'TV LED 32 Inch',
                'Speaker Bluetooth',
                'Kipas Angin',
                'Rice Cooker',
                'Setrika Uap',
            ],
            'Handphone & Aksesoris' => [
                'Smartphone 128GB',
                'Power Bank 10000mAh',
                'Casing HP',
                'Kabel Data Type C',
                'Headset Wireless',
            ],
            'Fashion Pria' => [
                'Kaos Pria',
                'Kemeja Flanel',
                'Celana Jeans Pria',
                'Jaket Hoodie',
                'Sepatu Sneakers Pria',
            ],
            'Fashion Wanita' => [
                'Blouse Wanita',
                'Dress Casual',
                'Rok Plisket',
                'Cardigan Rajut',
                'Sepatu Flat',
            ],
            'Kecantikan' => [
                'Sabun Wajah',
                'Toner',
                'Serum Wajah',
                'Lipstik Matte',
                'Masker Wajah',
            ],
            'Kesehatan' => [
                'Vitamin C',
                'Masker Medis',
                'Hand Sanitizer',
                'Termometer Digital',
                'Obat Flu',
            ],
            'Rumah Tangga' => [
                'Detergen',
                'Pewangi Pakaian',
                'Sapu Lantai',
                'Pel Lantai',
                'Lampu LED',
            ],
            'Peralatan Dapur' => [
                'Panci Stainless',
                'Wajan Anti Lengket',
                'Pisau Dapur',
                'Spatula Silikon',
                'Talenan',
            ],
            'Buku & Alat Tulis' => [
                'Buku Tulis',
                'Pulpen Gel',
                'Pensil 2B',
                'Penghapus',
                'Novel Indonesia',
            ],
            'Olahraga' => [
                'Bola Futsal',
                'Matras Yoga',
                'Sepatu Lari',
                'Raket Badminton',
                'Skipping Rope',
            ],
            'Otomotif' => [
                'Oli Mesin',
                'Helm Full Face',
                'Kampas Rem',
                'Sarung Jok',
                'Kunci Sok',
            ],
            'Hobi & Koleksi' => [
                'Action Figure',
                'Miniatur Mobil',
                'Puzzle 1000 pcs',
                'Kartu Koleksi',
                'Alat Lukis',
            ],
            'Bayi & Anak' => [
                'Popok Bayi',
                'Susu Formula',
                'Mainan Edukasi',
                'Baju Bayi',
                'Stroller',
            ],
            'Perlengkapan Hewan' => [
                'Makanan Kucing',
                'Makanan Anjing',
                'Pasir Kucing',
                'Kalung Hewan',
                'Shampoo Hewan',
            ],
            'Perlengkapan Ibadah' => [
                'Mukena',
                'Sajadah',
                'Sarung',
                'Al Quran',
                'Tasbih',
            ],
            'Pulsa & Tagihan' => [
                'Voucher Pulsa 20k',
                'Paket Data 10GB',
                'Token Listrik 50k',
                'Voucher Game',
                'Top Up E-Wallet',
            ],
        ];

        $category = $this->faker->randomElement(array_keys($categoryProducts));
        $name = $this->faker->randomElement($categoryProducts[$category]);

        return [
            'name' => $name,
            'description' => 'Produk ' . $name . ' pilihan untuk kebutuhan harian.',
            'price' => $this->faker->numberBetween(10000, 500000),
            'stock' => $this->faker->numberBetween(0, 100),
            'image_url' => $this->faker->imageUrl(640, 480, 'products', true),
            'category' => $category,
            'prep_time' => $this->faker->numberBetween(5, 60),
            'is_available' => $this->faker->boolean(90),
        ];
    }
}
