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
        // Category Fetch
        $specialDeals = Category::where('name', 'Special Deals')->first();
        $otcMedicine = Category::where('name', 'OTC & Medicine')->first();
        $grocery = Category::where('name', 'Grocery')->first();
        $personalCare = Category::where('name', 'Personal Care')->first();

        // Realistic Indian Items Dataset
        $indianProducts = [
            // Dairy & Breakfast
            [
                'title' => 'Amul Taaza Toned Milk 500 ml',
                'description' => 'Fresh pasteurized toned milk by Amul. Perfect for daily tea and coffee.',
                'price' => 28.00,
                'sale_price' => 27.00,
                'stock' => 150,
                'sku' => 'AMUL-MILK-500',
                'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&q=80',
                'categories' => [$specialDeals?->id, $grocery?->id],
            ],
            [
                'title' => 'Amul Gold Full Cream Milk 500 ml',
                'description' => 'Rich and creamy full cream milk, packed with nutrients.',
                'price' => 34.00,
                'sale_price' => 33.00,
                'stock' => 120,
                'sku' => 'AMUL-GOLD-500',
                'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=500&q=80',
                'categories' => [$grocery?->id],
            ],
            [
                'title' => 'Mother Dairy Cow Milk 500 ml',
                'description' => 'Pure cow milk loaded with vitamin A and calcium.',
                'price' => 30.00,
                'sale_price' => null,
                'stock' => 90,
                'sku' => 'MD-COWMILK-500',
                'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&q=80',
                'categories' => [$grocery?->id],
            ],

            // Indian Grocery Staples
            [
                'title' => 'Fortune Sunlite Sunflower Oil 1 L',
                'description' => 'Refined sunflower oil for daily healthy cooking.',
                'price' => 145.00,
                'sale_price' => 128.00,
                'stock' => 80,
                'sku' => 'FORTUNE-OIL-1L',
                'image' => 'https://images.unsplash.com/photo-1620706857370-e1b9770e8bb1?w=500&q=80',
                'categories' => [$specialDeals?->id, $grocery?->id],
            ],
            [
                'title' => 'Aashirvaad Shuddh Chakki Atta 5 kg',
                'description' => '100% pure whole wheat flour for soft and fluffy rotis.',
                'price' => 240.00,
                'sale_price' => 215.00,
                'stock' => 60,
                'sku' => 'AASHIRVAAD-ATTA-5KG',
                'image' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=500&q=80',
                'categories' => [$grocery?->id],
            ],
            [
                'title' => 'Tata Salt Vacuum Evaporated Iodised Salt 1 kg',
                'description' => 'Desh ka namak, enriched with essential iodine.',
                'price' => 28.00,
                'sale_price' => 25.00,
                'stock' => 200,
                'sku' => 'TATA-SALT-1KG',
                'image' => 'https://images.unsplash.com/photo-1518110168401-f282b6ac0932?w=500&q=80',
                'categories' => [$grocery?->id],
            ],

            // Personal Care & Hygiene
            [
                'title' => 'Colgate Strong Teeth Toothpaste 150 g',
                'description' => 'Calci-Lock technology for stronger and healthier teeth.',
                'price' => 110.00,
                'sale_price' => 95.00,
                'stock' => 110,
                'sku' => 'COLGATE-ST-150G',
                'image' => 'https://images.unsplash.com/photo-1559598467-f8b76c8155d0?w=500&q=80',
                'categories' => [$personalCare?->id],
            ],
            [
                'title' => 'Surf Excel Easy Wash Detergent Powder 1 kg',
                'description' => 'Removes tough stains easily while keeping clothes fresh.',
                'price' => 150.00,
                'sale_price' => 135.00,
                'stock' => 75,
                'sku' => 'SURF-EXCEL-1KG',
                'image' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=500&q=80',
                'categories' => [$specialDeals?->id, $personalCare?->id],
            ],
            [
                'title' => 'Dettol Antiseptic Liquid 250 ml',
                'description' => 'First aid antiseptic for personal hygiene and surface cleaning.',
                'price' => 135.00,
                'sale_price' => 120.00,
                'stock' => 95,
                'sku' => 'DETTOL-250ML',
                'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=500&q=80',
                'categories' => [$personalCare?->id, $otcMedicine?->id],
            ],

            // Puja Samagri
            [
                'title' => 'Mangaldeep Sandalwood Agarbatti 100 Sticks',
                'description' => 'Fragrant sandalwood incense sticks for daily morning puja.',
                'price' => 85.00,
                'sale_price' => 75.00,
                'stock' => 140,
                'sku' => 'MANGAL-AGARBATTI-100',
                'image' => 'https://images.unsplash.com/photo-1602928321679-560bb453f190?w=500&q=80',
                'categories' => [$grocery?->id],
            ],
            [
                'title' => 'Brass Diya for Puja (Set of 2)',
                'description' => 'Handcrafted traditional brass oil lamps for mandir and festivals.',
                'price' => 299.00,
                'sale_price' => 249.00,
                'stock' => 50,
                'sku' => 'BRASS-DIYA-SET2',
                'image' => 'https://images.unsplash.com/photo-1605883705077-8d3d3a7138f6?w=500&q=80',
                'categories' => [$specialDeals?->id],
            ],

            // Traditional Indian Jewellery & Accessories
            [
                'title' => 'Traditional Gold Plated Kundan Necklace Set',
                'description' => 'Ethnic bridal jewellery set with matching earrings for weddings.',
                'price' => 2499.00,
                'sale_price' => 1899.00,
                'stock' => 20,
                'sku' => 'JEWEL-KUNDAN-01',
                'image' => 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=500&q=80',
                'categories' => [$specialDeals?->id],
            ],
            [
                'title' => 'Silver Oxidised Jhumka Earrings',
                'description' => 'Lightweight oxidised silver ethnic jhumkas for daily wear.',
                'price' => 499.00,
                'sale_price' => 349.00,
                'stock' => 40,
                'sku' => 'JEWEL-JHUMKA-SILVER',
                'image' => 'https://images.unsplash.com/photo-1630019852942-f89202989a59?w=500&q=80',
                'categories' => [$specialDeals?->id],
            ],
        ];

        // Seed Defined Indian Products
        foreach ($indianProducts as $item) {
            $categoriesToAttach = array_filter($item['categories']);

            $product = Product::create([
                'title' => $item['title'],
                'slug' => Str::slug($item['title'] . '-' . Str::random(4)),
                'description' => $item['description'],
                'price' => $item['price'],
                'sale_price' => $item['sale_price'],
                'stock_quantity' => $item['stock'],
                'sku' => $item['sku'],
                'prescription_required' => false,
                'is_active' => true,
            ]);

            if (!empty($categoriesToAttach)) {
                $product->categories()->attach($categoriesToAttach);
            }

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $item['image'],
                'is_primary' => true,
            ]);
        }

        // Generate additional items via Factory if categories exist
        $allCategories = Category::all();
        if ($allCategories->isNotEmpty()) {
            foreach (range(1, 40) as $i) {
                $factoryProduct = Product::factory()->create();
                $factoryProduct->categories()->attach($allCategories->random(rand(1, 2))->pluck('id'));

                ProductImage::create([
                    'product_id' => $factoryProduct->id,
                    'image_path' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80',
                    'is_primary' => true,
                ]);
            }
        }
    }
}