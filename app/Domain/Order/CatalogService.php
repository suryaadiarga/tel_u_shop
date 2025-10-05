<?php

namespace App\Domain\Order;

class CatalogService
{
    // Contoh item umum di koperasi kampus (silakan edit sesuai lapangan)
    public function all(): array
    {
        return [
            // MINUMAN
            ['id' => 'DR-AQUA-600', 'name' => 'Aqua 600ml', 'category' => 'MINUMAN', 'price' => 5000],
            ['id' => 'DR-TEHBOT', 'name' => 'Teh Botol Sosro 350ml', 'category' => 'MINUMAN', 'price' => 6000],
            ['id' => 'DR-GOODDAY', 'name' => 'Good Day Cappuccino', 'category' => 'MINUMAN', 'price' => 8000],
            ['id' => 'DR-POCARI', 'name' => 'Pocari Sweat 350ml', 'category' => 'MINUMAN', 'price' => 9000],
            // MAKANAN
            ['id' => 'FD-INDOMIEG', 'name' => 'Indomie Goreng (cup)', 'category' => 'MAKANAN', 'price' => 7000],
            ['id' => 'FD-ROTI', 'name' => 'Roti Tawar/Isian', 'category' => 'MAKANAN', 'price' => 8000],
            ['id' => 'FD-RISOL', 'name' => 'Risol Mayo', 'category' => 'MAKANAN', 'price' => 6000],
            ['id' => 'FD-PASTEL', 'name' => 'Pastel', 'category' => 'MAKANAN', 'price' => 6000],
        ];
    }

    public function find(string $productId): ?array
    {
        foreach ($this->all() as $p) {
            if ($p['id'] === $productId)
                return $p;
        }
        return null;
    }
}
