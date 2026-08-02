<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Existing Categories fetch kar rahe hain
        $specialDeals = Category::where('name', 'Special Deals')->first();
        $otcMedicine = Category::where('name', 'OTC & Medicine')->first();
        $supplements = Category::where('name', 'Supplements')->first();
        $healthDevices = Category::where('name', 'Health Devices')->first();

        // Sample Product 1 (Multiple Categories: Special Deals + OTC & Medicine)
        if ($specialDeals && $otcMedicine) {
            $product1 = Product::create([
                'title' => 'Multivitamin Immunity Boost Tablets (60 Caps)',
                'slug' => Str::slug('Multivitamin Immunity Boost Tablets 60 Caps'),
                'description' => 'Daily essential health supplement packed with Zinc, Vitamin C, and D3 for overall immunity boost.',
                'price' => 999.00,
                'sale_price' => 649.00,
                'stock_quantity' => 100,
                'sku' => 'SUPP-MULTI-001',
                'prescription_required' => false,
                'is_active' => true,
            ]);

            // Pivot Table Tagging (Attach Multiple Categories)
            $product1->categories()->attach([$specialDeals->id, $otcMedicine->id]);

            // Product Images
            $product1->images()->createMany([
                [
                    'image_path' => 'https://images.stylight.net/image/upload/e_trim/t_web_product_330x440max_nobg/q_auto:eco,f_auto/nuihnmf62uddfcu3hmrs.jpg',
                    'is_primary' => true,
                ],
                [
                    'image_path' => 'https://images.stylight.net/image/upload/e_trim/t_web_product_330x440max_nobg/q_auto:eco,f_auto/nuihnmf62uddfcu3hmrs.jpg',
                    'is_primary' => false,
                ]
            ]);
        }

        // Sample Product 2 (Multiple Categories: Health Devices + Special Deals)
        if ($healthDevices && $specialDeals) {
            $product2 = Product::create([
                'title' => 'Digital Pulse Oximeter & Heart Rate Monitor',
                'slug' => Str::slug('Digital Pulse Oximeter Heart Rate Monitor'),
                'description' => 'Accurate Fingertip Pulse Oximeter with OLED Display for measuring SpO2 oxygen saturation levels.',
                'price' => 1499.00,
                'sale_price' => 1199.00,
                'stock_quantity' => 45,
                'sku' => 'DEV-OXI-002',
                'prescription_required' => false,
                'is_active' => true,
            ]);

            $product2->categories()->attach([$healthDevices->id, $specialDeals->id]);

            $product2->images()->create([
                'image_path' => 'https://images.stylight.net/image/upload/e_trim/t_web_product_330x440max_nobg/q_auto:eco,f_auto/nuihnmf62uddfcu3hmrs.jpg',
                'is_primary' => true,
            ]);
        }
    }
}