<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'IPH15PRO001',
                'price' => 999.99,
                'stock' => 25
            ],
            [
                'name' => 'MacBook Air M2',
                'sku' => 'MBAIR002',
                'price' => 1299.99,
                'stock' => 15
            ],
            [
                'name' => 'AirPods Pro 2',
                'sku' => 'AIRP003',
                'price' => 249.99,
                'stock' => 50
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
