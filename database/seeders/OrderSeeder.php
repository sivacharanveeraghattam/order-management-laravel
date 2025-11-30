<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create test user (user_id = 1)
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123')
            ]
        );

        // Ensure products exist with stock
        $products = [
            ['name' => 'iPhone 15 Pro', 'sku' => 'IPH15PRO', 'price' => 999.99, 'stock' => 20],
            ['name' => 'MacBook Air M2', 'sku' => 'MBAIR', 'price' => 1299.99, 'stock' => 10],
            ['name' => 'AirPods Pro 2', 'sku' => 'AIRP2', 'price' => 249.99, 'stock' => 30],
            ['name' => 'Samsung S24', 'sku' => 'S24', 'price' => 799.99, 'stock' => 15],
            ['name' => 'Google Pixel 8', 'sku' => 'PIXEL8', 'price' => 699.99, 'stock' => 12]
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        // Create 3 test orders
        $orders = [
            [
                'items' => [
                    ['product_sku' => 'IPH15PRO', 'quantity' => 1],
                    ['product_sku' => 'AIRP2', 'quantity' => 2]
                ],
                'status' => Order::PENDING
            ],
            [
                'items' => [
                    ['product_sku' => 'MBAIR', 'quantity' => 1],
                    ['product_sku' => 'PIXEL8', 'quantity' => 1]
                ],
                'status' => Order::CONFIRMED
            ],
            [
                'items' => [
                    ['product_sku' => 'S24', 'quantity' => 2]
                ],
                'status' => Order::CANCELLED
            ]
        ];

        foreach ($orders as $orderData) {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($orderData['items'] as $item) {
                $product = Product::where('sku', $item['product_sku'])->first();
                if ($product && $product->stock >= $item['quantity']) {
                    $subtotal = $product->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price
                    ];

                    // Reduce stock
                    $product->decrement('stock', $item['quantity']);
                }
            }

            if (!empty($orderItems)) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'status' => $orderData['status']
                ]);

                foreach ($orderItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }
            }
        }

        $this->command->info('✅ 3 test orders created with items!');
    }
}
