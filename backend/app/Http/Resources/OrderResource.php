<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status' => 'ok',
            'data' => [
                'id' => $this->id,
                'customer_id' => $this->customer_id,
                'status' => $this->status,
                'total' => $this->total,
                'notes' => $this->notes,
                'created_at' => $this->created_at?->toDateTimeString(),
                'updated_at' => $this->updated_at?->toDateTimeString(),

                // relasi items (misalnya detail produk dalam order)
                'items' => $this->whenLoaded('items', function () {
                    return $this->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'name' => $item->product->name ?? null,
                            'qty' => $item->qty,
                            'price' => $item->price,
                            'subtotal' => $item->qty * $item->price,
                        ];
                    });
                }),
            ],
        ];
    }
}