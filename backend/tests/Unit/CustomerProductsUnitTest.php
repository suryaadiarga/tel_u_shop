<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class CustomerProductsUnitTest extends TestCase
{
    public function testStockStatusPositive(): void
    {
        $product = new Product(['stock' => 5]);

        $this->assertSame('Tersedia', $product->stock_status);
    }

    public function testStockStatusNegative(): void
    {
        $product = new Product(['stock' => 0]);

        $this->assertSame('Habis', $product->stock_status);
    }
}
