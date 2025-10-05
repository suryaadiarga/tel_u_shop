<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\PayOrderRequest;
use App\Http\Resources\OrderResource;
use App\Domain\Order\OrderService;
use App\Domain\Order\CatalogService;
use App\Infrastructure\OrderFileStore;
use Illuminate\Http\Request;
use Throwable;

class OrderController extends Controller
{
    private OrderService $service;
    private OrderFileStore $store;

    public function __construct()
    {
        $catalog = new CatalogService();
        $this->store = new OrderFileStore();
        $this->service = new OrderService($catalog, $this->store);
    }

    public function store(CreateOrderRequest $request)
    {
        try {
            $order = $this->service->create($request->validated());
            return new OrderResource($order);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'reason' => str_contains($e->getMessage(), 'PRODUCT_NOT_FOUND')
                    ? 'PRODUCT_NOT_FOUND' : 'PROCESS_FAILED',
                'message' => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 422);
        }
    }

    public function show(string $id)
    {
        $order = $this->store->get($id);
        if (!$order) {
            return response()->json(['status' => 'error', 'reason' => 'NOT_FOUND'], 404);
        }
        return new OrderResource($order);
    }

    public function pay(PayOrderRequest $request, string $id)
    {
        $order = $this->store->get($id);
        if (!$order) {
            return response()->json(['status' => 'error', 'reason' => 'NOT_FOUND'], 404);
        }
        try {
            $updated = $this->service->pay($order, $request->validated()['payment_method']);
            return new OrderResource($updated);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'reason' => 'INVALID_STATUS_TRANSITION',
                'message' => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 422);
        }
    }
}
