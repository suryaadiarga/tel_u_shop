<?php

namespace Tests\Unit;

use App\Services\QueryService;
use Illuminate\Http\Request;
use Tests\TestCase;

class WalletUnitTest extends TestCase
{
    public function testPerPagePositive(): void
    {
        $request = Request::create('/wallet/transactions', 'GET', ['per_page' => 5]);

        $perPage = QueryService::perPage($request, 20);

        $this->assertSame(5, $perPage);
    }

    public function testPerPageNegative(): void
    {
        $request = Request::create('/wallet/transactions', 'GET', ['per_page' => 0]);

        $perPage = QueryService::perPage($request, 20);

        $this->assertSame(20, $perPage);
    }
}
