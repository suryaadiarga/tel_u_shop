<?php

namespace App\Infrastructure;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderFileStore
{
    private string $file = 'orders.json';

    private function read(): array
    {
        if (!Storage::exists($this->file))
            return [];
        return json_decode(Storage::get($this->file), true) ?: [];
    }

    private function write(array $data): void
    {
        Storage::put($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function nextId(): string
    {
        return (string) Str::uuid();
    }

    public function put(array $order): void
    {
        $data = $this->read();
        $data[$order['id']] = $order;
        $this->write($data);
    }

    public function get(string $id): ?array
    {
        $data = $this->read();
        return $data[$id] ?? null;
    }
}
