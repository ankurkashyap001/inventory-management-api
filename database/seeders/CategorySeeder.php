<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 22,
                'name' => 'Special Deals',
                'max_discount' => 35,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/765991-1751388056.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/686444-1751388056.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.us-east-1.amazonaws.com/prod/sales_categories/logo/Combo+Deals_web.png',
            ],
            [
                'id' => 10,
                'name' => 'OTC & Medicine',
                'max_discount' => 14,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/151368-1751386200.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/143057-1751386199.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/Medication_web.png',
            ],
            [
                'id' => 29,
                'name' => 'Supplements',
                'max_discount' => 0,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/511372-1751679450.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/422148-1751679450.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/593665-1751679450.png',
            ],
            [
                'id' => 9,
                'name' => 'Health Devices',
                'max_discount' => 17,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/837510-1751386159.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/692287-1751386158.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/814018-1751988174.png',
            ],
            [
                'id' => 3,
                'name' => 'Fitness and Wellness',
                'max_discount' => 17,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/891534-1751385921.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/721746-1751385921.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/596133-1752600135.png',
            ],
            [
                'id' => 19,
                'name' => 'Grocery',
                'max_discount' => 35,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/380593-1751676828.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/Cosmetics%403x.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/Grocery_web.png',
            ],
            [
                'id' => 1,
                'name' => 'Snacks',
                'max_discount' => 23,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/373084-1751387768.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/184018-1751385831.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/148831-1752599357.png',
            ],
            [
                'id' => 2,
                'name' => 'Drinks',
                'max_discount' => 14,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/121031-1751385867.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/694987-1751385867.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/622697-1751988211.png',
            ],
            [
                'id' => 20,
                'name' => 'Ice Cream',
                'max_discount' => 35,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/953614-1751387244.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/711856-1751387243.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/442462-1751988164.png',
            ],
            [
                'id' => 5,
                'name' => 'Personal Care',
                'max_discount' => 14,
                'banner' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/banner/773802-1751386021.png',
                'logo' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/853552-1751386021.png',
                'logo_web' => 'https://nft-epharmacy-int.s3.amazonaws.com/prod/sales_categories/logo/personal+care+and+hygine_web.png',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['id' => $cat['id']],
                [
                    'name' => $cat['name'],
                    'slug' => Str::slug($cat['name']),
                    'prescription_required' => false,
                    'max_discount' => $cat['max_discount'],
                    'banner_image' => $cat['banner'],
                    'logo_image' => $cat['logo'],
                    'logo_image_web' => $cat['logo_web'],
                    'is_active' => true,
                ]
            );
        }
    }
}