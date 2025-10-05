<?php

namespace App\Domain\Order;

use App\Infrastructure\OrderFileStore;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function __construct(
        private CatalogService $catalog,
        private OrderFileStore $store
    ) {
    }

    public function create(array $input): array
    {
        // 1) Validasi product_id + hitung harga
        $lines = [];
        $subtotal = 0;

        foreach ($input['items'] as $i) {
            $prod = $this->catalog->find($i['product_id']);
            if (!$prod) {
                throw new RuntimeException('PRODUCT_NOT_FOUND: ' . $i['product_id']);
            }
            $qty = (int) $i['qty'];
            $lineTotal = $qty * $prod['price'];
            $lines[] = [
                'product_id' => $prod['id'],
                'name' => $prod['name'],
                'category' => $prod['category'],
                'price' => $prod['price'],
                'qty' => $qty,
                'line_total' => $lineTotal,
            ];
            $subtotal += $lineTotal;
        }

        // 2) Hitung total (tanpa pajak/ongkir dulu; bisa ditambah nanti)
        $total = $subtotal;

        // 3) Bentuk order
        $id = $this->store->nextId();
        $code = 'ORD-' . Str::upper(Str::random(6));

        $order = [
            'id' => $id,
            'code' => $code,
            'customer_id' => $input['customer_id'],
            'status' => 'UNPAID',
            'items' => $lines,
            'subtotal' => $subtotal,
            'total' => $total,
            'notes' => $input['notes'] ?? null,
            'payment' => null,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        // 4) Simpan
        $this->store->put($order);
        return $order;
    }

    public function pay(array $order, string $method): array
    {
        if ($order['status'] !== 'UNPAID') {
            throw new RuntimeException('INVALID_STATUS_TRANSITION');
        }

        $order['status'] = 'PAID';
        $order['payment'] = ['method' => $method, 'paid_at' => now()->toIso8601String()];
        $order['updated_at'] = now()->toIso8601String();

        $this->store->put($order);
        return $order;
    }
}
