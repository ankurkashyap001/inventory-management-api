<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Essential Categories Exist
        $categoriesData = [
            'Dairy & Breakfast' => 'Fresh milk, butter, bread, curd, and breakfast items',
            'Grocery & Staples' => 'Atta, rice, pulses, cooking oils, and spices',
            'Snacks & Munchies' => 'Chips, biscuits, chocolates, and namkeen',
            'Beverages' => 'Cold drinks, juices, energy drinks, and tea/coffee',
            'Personal Care' => 'Soaps, shampoos, toothpastes, and handwashes',
            'Special Deals' => 'Exciting discounts and everyday special offers',
            'OTC & Medicine' => 'First aid, antiseptics, and health supplements',
        ];

        $categories = [];
        foreach ($categoriesData as $name => $desc) {
            $categories[$name] = Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => $desc,
                    'is_active' => true,
                ]
            );
        }

        // Helper shortcuts for IDs
        $dairyId = $categories['Dairy & Breakfast']->id;
        $groceryId = $categories['Grocery & Staples']->id;
        $snacksId = $categories['Snacks & Munchies']->id;
        $bevId = $categories['Beverages']->id;
        $personalCareId = $categories['Personal Care']->id;
        $dealsId = $categories['Special Deals']->id;
        $otcId = $categories['OTC & Medicine']->id;

        // 2. Rich Realistic Indian Products Dataset
        $products = [
            // --- DAIRY & BREAKFAST ---
            [
                'title' => 'Amul Gold Full Cream Fresh Milk 500 ml',
                'description' => 'Pasteurised full cream milk with minimum 6.0% fat and 9.0% SNF. Ideal for tea, coffee, desserts, and curd.',
                'price' => 34.00,
                'sale_price' => 33.00,
                'stock' => 120,
                'sku' => 'AMUL-GOLD-500ML',
                'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=600&q=80',
                'categories' => [$dairyId, $dealsId],
            ],
            [
                'title' => 'Amul Pasteurised Butter 100 g',
                'description' => 'Utterly Butterly Delicious salted butter made from pure milk fat. Tastes great on toast and paranthas.',
                'price' => 60.00,
                'sale_price' => 58.00,
                'stock' => 90,
                'sku' => 'AMUL-BUTTER-100G',
                'image' => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?w=600&q=80',
                'categories' => [$dairyId],
            ],
            [
                'title' => 'Harvest Gold Soft White Bread 400 g',
                'description' => 'Freshly baked soft white bread. Perfect for morning sandwiches, toasts, and quick snacks.',
                'price' => 40.00,
                'sale_price' => 38.00,
                'stock' => 75,
                'sku' => 'HG-WHITEBREAD-400G',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&q=80',
                'categories' => [$dairyId],
            ],
            [
                'title' => 'Britannia Processed Cheese Slices 200 g (10 Slices)',
                'description' => 'Rich and cheesy slices, ideal for burgers, sandwiches, and rolls.',
                'price' => 155.00,
                'sale_price' => 140.00,
                'stock' => 50,
                'sku' => 'BRIT-CHEESE-200G',
                'image' => 'https://images.unsplash.com/photo-1624806992066-5ffcf7ca186b?w=600&q=80',
                'categories' => [$dairyId, $dealsId],
            ],

            // --- GROCERY & STAPLES ---
            [
                'title' => 'Aashirvaad Shuddh Chakki Atta 5 kg',
                'description' => '100% pure whole wheat flour processed with zero maida. Makes soft, fluffy, and sweet-tasting rotis.',
                'price' => 245.00,
                'sale_price' => 220.00,
                'stock' => 60,
                'sku' => 'AASHIRVAAD-ATTA-5KG',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=80',
                'categories' => [$groceryId, $dealsId],
            ],
            [
                'title' => 'Fortune Sunlite Refined Sunflower Oil 1 L',
                'description' => 'Light, healthy and easy-to-digest cooking oil enriched with Vitamin A & D for daily cooking.',
                'price' => 150.00,
                'sale_price' => 132.00,
                'stock' => 85,
                'sku' => 'FORTUNE-SUN-1L',
                'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=600&q=80',
                'categories' => [$groceryId, $dealsId],
            ],
            [
                'title' => 'Tata Salt Vacuum Evaporated Iodised Salt 1 kg',
                'description' => 'Desh Ka Namak - pure iodised salt ensuring mental development and daily health balance.',
                'price' => 28.00,
                'sale_price' => 25.00,
                'stock' => 200,
                'sku' => 'TATA-SALT-1KG',
                'image' => 'https://images.unsplash.com/photo-1518110168401-f282b6ac0932?w=600&q=80',
                'categories' => [$groceryId],
            ],
            [
                'title' => 'Daawat Rozana Super Basmati Rice 5 kg',
                'description' => 'Aromatic long-grain basmati rice, perfect for daily meals, biryani, and pulao.',
                'price' => 380.00,
                'sale_price' => 329.00,
                'stock' => 40,
                'sku' => 'DAAWAT-RICE-5KG',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=80',
                'categories' => [$groceryId, $dealsId],
            ],
            [
                'title' => 'Catch Superfine Turmeric Powder (Haldi) 200 g',
                'description' => '100% natural, aromatic, and rich golden turmeric powder for authentic Indian taste and color.',
                'price' => 52.00,
                'sale_price' => 45.00,
                'stock' => 110,
                'sku' => 'CATCH-HALDI-200G',
                'image' => 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=600&q=80',
                'categories' => [$groceryId],
            ],

            // --- SNACKS & MUNCHIES ---
            [
                'title' => 'Lays Classic Salted Potato Chips 50 g',
                'description' => 'Crispy, golden, thin-cut potato chips seasoned lightly with salt. The classic movie and tea snack.',
                'price' => 20.00,
                'sale_price' => 20.00,
                'stock' => 150,
                'sku' => 'LAYS-SALTED-50G',
                'image' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=600&q=80',
                'categories' => [$snacksId],
            ],
            [
                'title' => 'Kurkure Masala Munch Crunchy Snack 85 g',
                'description' => 'Tedha Hai Par Mera Hai! Spicy, tangy, and crunchy corn puffs seasoned with Indian chatpata spices.',
                'price' => 20.00,
                'sale_price' => 18.00,
                'stock' => 130,
                'sku' => 'KURKURE-MM-85G',
                'image' => 'https://images.unsplash.com/photo-1621447504864-d8686e12698c?w=600&q=80',
                'categories' => [$snacksId],
            ],
            [
                'title' => 'Cadbury Dairy Milk Silk Chocolate Bar 150 g',
                'description' => 'Indulge in the rich, smooth, and creamy melt-in-mouth cocoa experience of Dairy Milk Silk.',
                'price' => 175.00,
                'sale_price' => 159.00,
                'stock' => 60,
                'sku' => 'CADBURY-SILK-150G',
                'image' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=600&q=80',
                'categories' => [$snacksId, $dealsId],
            ],
            [
                'title' => 'Haldirams Nagpur Aloo Bhujia 200 g',
                'description' => 'Crispy potato-based savoury snack seasoned with mint and red chili powder.',
                'price' => 60.00,
                'sale_price' => 52.00,
                'stock' => 95,
                'sku' => 'HALDIRAM-BHUJIA-200G',
                'image' => 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?w=600&q=80',
                'categories' => [$snacksId],
            ],

            // --- BEVERAGES ---
            [
                'title' => 'Coca-Cola Original Soft Drink Bottle 750 ml',
                'description' => 'Crisp, fizzy, and refreshing original cola drink. Best served chilled with snacks or meals.',
                'price' => 45.00,
                'sale_price' => 40.00,
                'stock' => 100,
                'sku' => 'COCA-COLA-750ML',
                'image' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=600&q=80',
                'categories' => [$bevId],
            ],
            [
                'title' => 'Real Fruit Power Mixed Fruit Juice 1 L',
                'description' => 'Goodness of 9 natural fruits without added preservatives. Rich in Vitamin C and energy.',
                'price' => 135.00,
                'sale_price' => 115.00,
                'stock' => 50,
                'sku' => 'REAL-MIXJUICE-1L',
                'image' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=600&q=80',
                'categories' => [$bevId, $dealsId],
            ],
            [
                'title' => 'Sprite Lime Flavoured Soft Drink 750 ml',
                'description' => 'Clear, crisp, lemon-lime drink that instantly quenches thirst with clean taste.',
                'price' => 45.00,
                'sale_price' => 40.00,
                'stock' => 80,
                'sku' => 'SPRITE-750ML',
                'image' => 'https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?w=600&q=80',
                'categories' => [$bevId],
            ],

            // --- PERSONAL CARE & HEALTH ---
            [
                'title' => 'Colgate Strong Teeth Toothpaste 150 g',
                'description' => 'Calci-Lock technology provides strong teeth, cavity protection, and fresh mint breath.',
                'price' => 110.00,
                'sale_price' => 95.00,
                'stock' => 110,
                'sku' => 'COLGATE-ST-150G',
                'image' => 'https://images.unsplash.com/photo-1559598467-f8b76c8155d0?w=600&q=80',
                'categories' => [$personalCareId],
            ],
            [
                'title' => 'Dettol Antiseptic Liquid 250 ml',
                'description' => 'Trusted first-aid antiseptic protection for personal hygiene, cuts, shaving, and laundry.',
                'price' => 135.00,
                'sale_price' => 120.00,
                'stock' => 90,
                'sku' => 'DETTOL-250ML',
                'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600&q=80',
                'categories' => [$personalCareId, $otcId, $dealsId],
            ],
            [
                'title' => 'Surf Excel Easy Wash Detergent Powder 1 kg',
                'description' => 'Superior stain removal formula that cleans tough dirt from collars and cuffs effortlessly.',
                'price' => 150.00,
                'sale_price' => 135.00,
                'stock' => 70,
                'sku' => 'SURF-EXCEL-1KG',
                'image' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=600&q=80',
                'categories' => [$personalCareId],
            ],
            [
                'title' => 'Dove Cream Beauty Bathing Bar Soap 100 g (Pack of 3)',
                'description' => 'Enriched with 1/4th moisturizing cream for soft, smooth, and glowing skin.',
                'price' => 195.00,
                'sale_price' => 170.00,
                'stock' => 65,
                'sku' => 'DOVE-SOAP-3PACK',
                'image' => 'https://images.unsplash.com/photo-1607006344380-b6775a0824a7?w=600&q=80',
                'categories' => [$personalCareId, $dealsId],
            ],
        ];

        // 3. Seed Products into Database
        foreach ($products as $item) {
            $categoriesToAttach = array_filter($item['categories']);

            $product = Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'title' => $item['title'],
                    'slug' => Str::slug($item['title']),
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'sale_price' => $item['sale_price'],
                    'stock_quantity' => $item['stock'],
                    'prescription_required' => false,
                    'is_active' => true,
                ]
            );

            // Sync categories pivot table
            if (!empty($categoriesToAttach)) {
                $product->categories()->sync($categoriesToAttach);
            }

            // Sync or Create Primary Image
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'is_primary' => true,
                ],
                [
                    'image_path' => $item['image'],
                ]
            );
        }
    }
}