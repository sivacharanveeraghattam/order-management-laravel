<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create Order with Items
     */
    public function store(Request $request)
    {
        // **FIXED: Use session OR static user_id=1**
        $userId = auth()->check() ? auth()->id() : 1;

        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1'
            ], [
                'items.required' => 'Order items are required',
                'items.*.product_id.exists' => 'Product not found',
                'items.*.quantity.min' => 'Quantity must be at least 1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return DB::transaction(function () use ($request, $validator, $userId) {
                $totalAmount = 0;
                $orderItems = [];
                $errors = [];

                // Validate stock & calculate total
                foreach ($validator->validated()['items'] as $index => $item) {
                    $product = Product::where('id', $item['product_id'])
                        ->whereNull('deleted_at')
                        ->first();

                    if (!$product) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Product not found'
                        ], 404);
                    }

                    if ($product->stock < $item['quantity']) {
                        $errors[] = "Insufficient stock for '{$product->name}'. Available: {$product->stock}";
                        break;
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                    ];
                }

                if (!empty($errors)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock validation failed',
                        'errors' => $errors
                    ], 422);
                }

                // **FIXED: Use $userId instead of auth()->id()**
                $order = Order::create([
                    'user_id' => $userId,  // ✅ Session OR 1
                    'total_amount' => $totalAmount,
                    'status' => Order::PENDING // 0 = pending
                ]);

                // Create order items & reduce stock
                foreach ($orderItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);

                    Product::where('id', $item['product_id'])
                        ->decrement('stock', $item['quantity']);
                }

                // Load with relationships (No N+1)
                $order->load(['user:id,name,email', 'items.product:id,name,sku,price']);

                Log::info('Order created', [
                    'order_id' => $order->id,
                    'user_id' => $userId,
                    'total' => $totalAmount
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'data' => $order
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'user_id' => $userId,
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order'
            ], 500);
        }
    }

    /**
     * List Orders (User's orders only, with eager loading)
     */
    public function index(Request $request)
    {
        try {
            $orders = Order::withFullDetails()
                ->where('user_id', auth()->id())
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            Log::error('Orders list failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }
}
