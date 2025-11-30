<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Test User
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123')
            ]
        );

        // 5 Test Products
        $products = [
            ['name' => 'iPhone 15 Pro', 'sku' => 'IPH15PRO', 'price' => 999.99, 'stock' => 10],
            ['name' => 'MacBook Air', 'sku' => 'MBAIR', 'price' => 1299.99, 'stock' => 5],
            ['name' => 'AirPods Pro', 'sku' => 'AIRP', 'price' => 249.99, 'stock' => 20],
            ['name' => 'Samsung S24', 'sku' => 'S24', 'price' => 799.99, 'stock' => 15],
            ['name' => 'Google Pixel 8', 'sku' => 'PIXEL8', 'price' => 699.99, 'stock' => 8]
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
