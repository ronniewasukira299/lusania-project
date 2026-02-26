<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('TRUNCATE TABLE products RESTART IDENTITY CASCADE');

        $products = [
            // IMAGE BASED PRODUCTS
            [
                'name'         => 'Half Chicken mix with chips',
                'description'  => 'A delicious serving of Chicken Lusania — perfect for one.',
                'price'        => 20000,
                'is_available' => true,
                'image'        => '20k.jpg',
                'video'        => null,
                'name2'        => null,
                'price2'       => null,
            ],
            [
                'name'         => 'Dry Half Chicken Pilau with chip',
                'description'  => 'A generous serving of Chicken Lusania for a light sharing.',
                'price'        => 35000,
                'is_available' => true,
                'image'        => '35k.jpg',
                'video'        => null,
                'name2'        => null,
                'price2'       => null,
            ],
            [
                'name'         => 'Full chicken mix with chips',
                'description'  => 'A satisfying family-size serving of Chicken Lusania.',
                'price'        => 50000,
                'is_available' => true,
                'image'        => '50k.jpg',
                'video'        => null,
                'name2'        => null,
                'price2'       => null,
            ],
            [
                'name'         => 'Lusaniya for 2/3 people',
                'description'  => 'A large serving of Chicken Lusania, great for small gatherings.',
                'price'        => 70000,
                'is_available' => true,
                'image'        => '70k.jpg',
                'video'        => null,
                'name2'        => null,
                'price2'       => null,
            ],
            [
                'name'         => 'Lusaniya for 5/6 people',
                'description'  => 'Our special serving with extra accompaniments for a group.',
                'price'        => 70000,
                'is_available' => true,
                'image'        => '70kk.jpg',
                'video'        => null,
                'name2'        => null,
                'price2'       => null,
            ],
            [
                'name'         => 'Lusaniya for 8 people',
                'description'  => 'Our premium large serving — the ultimate Lusania experience.',
                'price'        => 90000,
                'is_available' => true,
                'image'        => '90k.jpg',
                'video'        => null,
                'name2'        => null,
                'price2'       => null,
            ],

            // VIDEO BASED PRODUCTS
            [
                'name'         => 'Full Chicken Plain',
                'description'  => 'A whole chicken, perfectly seasoned and fried to perfection.',
                'price'        => 28000,
                'is_available' => true,
                'image'        => null,
                'video'        => '28k.mp4',
                'name2'        => null,
                'price2'       => null,
            ],
            [
                'name'         => 'Half Chicken Pilau',
                'description'  => 'Half chicken served with aromatic pilau rice.',
                'price'        => 30000,
                'is_available' => true,
                'image'        => null,
                'video'        => '40k.mp4',
                'name2'        => 'Half Chicken Pilau with Chips',
                'price2'       => 35000,
            ],
            [
                'name'         => 'Full Chicken Pilau',
                'description'  => 'A full chicken served with aromatic pilau rice.',
                'price'        => 40000,
                'is_available' => true,
                'image'        => null,
                'video'        => '40k.mp4',
                'name2'        => 'Full Chicken Pilau with Chips',
                'price2'       => 45000,
            ],
            [
                'name'         => 'Lusaniya for 10 people',
                'description'  => 'The ultimate feast — feeds a crowd in style.',
                'price'        => 200000,
                'is_available' => true,
                'image'        => null,
                'video'        => '200k.mp4',
                'name2'        => null,
                'price2'       => null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
