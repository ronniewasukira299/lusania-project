<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the products table with 9 Lusania products
     */
    public function run(): void
    {
        $products = [
            // Set 1
            [
                'name' => 'Classic Chicken Lusaniya',
                'price' => 50000,
                'description' => 'Our signature classic fried chicken lusaniya',
            ],
            [
                'name' => 'Spicy Chicken Lusaniya',
                'price' => 50000,
                'description' => 'Hot and spicy chicken lusaniya with extra seasoning',
            ],
            [
                'name' => 'Family Pack Lusaniya',
                'price' => 50000,
                'description' => 'Perfect for families - serves 4-5 people',
            ],
            // Set 2
            [
                'name' => 'Classic Chicken Lusaniya',
                'price' => 50000,
                'description' => 'Our signature classic fried chicken lusaniya',
            ],
            [
                'name' => 'Spicy Chicken Lusaniya',
                'price' => 50000,
                'description' => 'Hot and spicy chicken lusaniya with extra seasoning',
            ],
            [
                'name' => 'Family Pack Lusaniya',
                'price' => 50000,
                'description' => 'Perfect for families - serves 4-5 people',
            ],
            // Set 3
            [
                'name' => 'Classic Chicken Lusaniya',
                'price' => 50000,
                'description' => 'Our signature classic fried chicken lusaniya',
            ],
            [
                'name' => 'Spicy Chicken Lusaniya',
                'price' => 50000,
                'description' => 'Hot and spicy chicken lusaniya with extra seasoning',
            ],
            [
                'name' => 'Family Pack Lusaniya',
                'price' => 50000,
                'description' => 'Perfect for families - serves 4-5 people',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
