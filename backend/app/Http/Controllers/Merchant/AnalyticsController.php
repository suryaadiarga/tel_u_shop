<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Dashboard overview with key metrics.
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            $period = $request->get('period', '30'); // days

            $startDate = Carbon::now()->subDays($period);

            // Total products
            $totalProducts = Product::where('merchant_id', $user->id)->count();

            // Total sales
            $totalSales = OrderItem::whereHas('product', function ($query) use ($user) {
                $query->where('merchant_id', $user->id);
            })->whereHas('order', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                    ->where('status', 'completed');
            })->sum('qty');

            // Total revenue
            $totalRevenue = OrderItem::whereHas('product', function ($query) use ($user) {
                $query->where('merchant_id', $user->id);
            })->whereHas('order', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                    ->where('status', 'completed');
            })->sum(DB::raw('qty * price'));

            // Average rating of products
            $averageRating = Review::whereHas('product', function ($query) use ($user) {
                $query->where('merchant_id', $user->id);
            })->avg('rating') ?? 0;

            // Top products
            $topProducts = Product::where('merchant_id', $user->id)
                ->withSum(['orderItems as total_sold' => function ($query) use ($startDate) {
                    $query->whereHas('order', function ($orderQuery) use ($startDate) {
                        $orderQuery->where('created_at', '>=', $startDate)
                            ->where('status', 'completed');
                    });
                }], 'qty')
                ->orderBy('total_sold', 'desc')
                ->take(5)
                ->get();

            // Sales trend (last 7 days)
            $salesTrend = OrderItem::whereHas('product', function ($query) use ($user) {
                $query->where('merchant_id', $user->id);
            })->whereHas('order', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->where('status', 'completed');
            })->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(qty) as total_quantity'),
                DB::raw('SUM(qty * price) as total_revenue')
            )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'overview' => [
                        'total_products' => $totalProducts,
                        'total_sales' => $totalSales,
                        'total_revenue' => $totalRevenue,
                        'average_rating' => round($averageRating, 1),
                    ],
                    'top_products' => $topProducts,
                    'sales_trend' => $salesTrend,
                    'period_days' => $period,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch dashboard data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detailed sales analytics.
     */
    public function salesAnalytics(Request $request)
    {
        try {
            $user = $request->user();
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', Carbon::now()->toDateString());

            // Product sales
            $productSales = Product::where('merchant_id', $user->id)
                ->with(['orderItems' => function ($query) use ($startDate, $endDate) {
                    $query->whereHas('order', function ($orderQuery) use ($startDate, $endDate) {
                        $orderQuery->whereBetween('created_at', [$startDate, $endDate])
                            ->where('status', 'completed');
                    })->select(
                        'product_id',
                        DB::raw('SUM(qty) as total_quantity'),
                        DB::raw('SUM(qty * price) as total_revenue')
                    )->groupBy('product_id');
                }])
                ->get()
                ->map(function ($product) {
                    $orderItem = $product->orderItems->first();
                    return [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'total_quantity' => $orderItem ? $orderItem->total_quantity : 0,
                        'total_revenue' => $orderItem ? $orderItem->total_revenue : 0,
                        'average_rating' => $product->reviews->avg('rating') ?? 0,
                    ];
                });

            // Daily sales
            $dailySales = OrderItem::whereHas('product', function ($query) use ($user) {
                $query->where('merchant_id', $user->id);
            })->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed');
            })->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(qty) as total_quantity'),
                DB::raw('SUM(qty * price) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as total_orders')
            )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Order status distribution
            $orderStatus = Order::whereHas('items.product', function ($query) use ($user) {
                $query->where('merchant_id', $user->id);
            })->whereBetween('created_at', [$startDate, $endDate])
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'product_sales' => $productSales,
                    'daily_sales' => $dailySales,
                    'order_status' => $orderStatus,
                    'date_range' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sales analytics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Product performance analytics.
     */
    public function productPerformance(Request $request)
    {
        try {
            $user = $request->user();

            $products = Product::where('merchant_id', $user->id)
                ->with(['reviews', 'orderItems' => function ($query) {
                    $query->whereHas('order', function ($orderQuery) {
                        $orderQuery->where('status', 'completed');
                    });
                }])
                ->get()
                ->map(function ($product) {
                    $totalSold = $product->orderItems->sum('qty');
                    $totalRevenue = $product->orderItems->sum(function ($item) {
                        return $item->qty * $item->price;
                    });
                    $averageRating = $product->reviews->avg('rating') ?? 0;
                    $reviewCount = $product->reviews->count();

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'is_available' => $product->is_available,
                        'total_sold' => $totalSold,
                        'total_revenue' => $totalRevenue,
                        'average_rating' => round($averageRating, 1),
                        'review_count' => $reviewCount,
                        'performance_score' => $this->calculatePerformanceScore($totalSold, $totalRevenue, $averageRating),
                    ];
                })
                ->sortByDesc('performance_score')
                ->values();

            return response()->json([
                'status' => 'success',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch product performance data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Customer analytics.
     */
    public function customerAnalytics(Request $request)
    {
        try {
            $user = $request->user();

            // Top customers by purchase amount
            $topCustomers = DB::table('users')
                ->join('orders', 'users.id', '=', 'orders.user_id')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.merchant_id', $user->id)
                ->where('orders.status', 'completed')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                    DB::raw('SUM(order_items.qty) as total_quantity'),
                    DB::raw('SUM(order_items.qty * order_items.price) as total_spent'),
                    DB::raw('MAX(orders.created_at) as last_order_date')
                )
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderBy('total_spent', 'desc')
                ->take(10)
                ->get();

            // Customer acquisition trend
            $firstPurchases = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.merchant_id', $user->id)
                ->where('orders.status', 'completed')
                ->select('orders.user_id', DB::raw('MIN(orders.created_at) as first_purchase_at'))
                ->groupBy('orders.user_id');

            $customerAcquisition = DB::query()
                ->fromSub($firstPurchases, 'first_purchases')
                ->select(
                    DB::raw('DATE(first_purchase_at) as first_purchase_date'),
                    DB::raw('COUNT(*) as new_customers')
                )
                ->groupBy(DB::raw('DATE(first_purchase_at)'))
                ->orderBy('first_purchase_date')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'top_customers' => $topCustomers,
                    'customer_acquisition' => $customerAcquisition,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch customer analytics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate performance score for a product.
     */
    private function calculatePerformanceScore($totalSold, $totalRevenue, $averageRating)
    {
        // Weight: 40% sales volume, 40% revenue, 20% rating
        $salesScore = min($totalSold / 100, 1) * 40; // Max score at 100 sales
        $revenueScore = min($totalRevenue / 10000000, 1) * 40; // Max score at 10M revenue
        $ratingScore = ($averageRating / 5) * 20; // Rating out of 5

        return $salesScore + $revenueScore + $ratingScore;
    }
}
