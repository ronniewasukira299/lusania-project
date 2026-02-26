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
                'name' => 'Half Chicken mix with chips',
                'price' => 20000,
                'description' => 'Our signature classic fried chicken lusaniya',
                'is_available' => true,
                'image' => '20k.jpg',
            ],
            [
                'name' => 'Dry Half Chicken Pilau with chip',
                'price' => 35000,
                'description' => 'Hot and spicy chicken lusaniya with extra seasoning',
                'is_available' => true,
                'image' => '35k.jpg',
            ],
            [
                'name' => 'Full chicken mix with chips',
                'price' => 35000,
                'description' => 'Perfect for families - serves 4-5 people',
                'is_available' => true,
                'image' => '20k.jpg',
            ],
            // Set 2
            [
                'name' => 'Lusaniya for 2/3 people',
                'price' => 50000,
                'description' => 'Our signature classic fried chicken lusaniya',
                'is_available' => true,
                'image' => '50k.jpg',
            ],
            [
                'name' => 'Lusaniya for 4 people',
                'price' => 70000,
                'description' => 'Hot and spicy chicken lusaniya with extra seasoning',
                'is_available' => true,
                'image' => '70k.jpg',
            ],
            [
                'name' => 'Lusaniya for 8 people',
                'price' => 90000,
                'description' => 'Perfect for families - serves 8 people',
                'is_available' => true,
                'image' => '90k.jpg',
            ],
            // Set 3
            [
                'name' => 'Lusaniya for 5/6 people',
                'price' => 70000,
                'description' => 'Our signature classic fried chicken lusaniya',
                'is_available' => true,
                'image' => '70kk.jpg'
            ],
            [
                'name' => 'Dry Half Chicken with chip',
                'price' => 20000,
                'description' => 'Hot and spicy chicken lusaniya with extra seasoning',
                'is_available' => true,
                'image' => '20k.jpg'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
