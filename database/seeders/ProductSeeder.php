<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori yang sudah kamu buat
        $categoryNames = [
            'Toys',
            'Fashion',
            'Health & Beauty',
            'Books',
        ];

        // Kosongkan hanya tabel products agar tidak bentrok slug/duplikat
        // (opsional, boleh dihapus kalau tidak ingin truncate)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Kamus nama untuk variasi per kategori (biar UI terlihat realistis)
        $namePools = [
            'Toys' => [
                'Wooden Toy Car','Puzzle Blocks','Eco Plush Bear','Recycled Robot',
                'Balance Bike','Stacking Rings','Shape Sorter','Pull-Along Duck',
                'Marble Run','Wooden Train','Kite','Yo-Yo','Jump Rope','Building Bricks',
                'DIY Craft Kit','Magnetic Tiles','Memory Cards','Board Game',
                'Mini Xylophone','Doll House','Toy Kitchen','Rattle Set',
                'Coloring Set','Water Doodle Mat','Ring Toss'
            ],
            'Fashion' => [
                'Organic Cotton T-Shirt','Bamboo Socks','Hemp Tote Bag','Recycled Denim Jacket',
                'Linen Shirt','Cork Wallet','Canvas Sneakers','Upcycled Scarf',
                'Organic Hoodie','Bamboo Leggings','Eco Beanie','Hemp Cap',
                'Recycled Belt','Tencel Dress','Organic Polo','Linen Pants',
                'Cork Cardholder','Canvas Backpack','Upcycled Tote','Bamboo Tee',
                'Organic Tank','Hemp Shirt','Recycled Windbreaker','Tencel Skirt','Eco Sandals'
            ],
            'Health & Beauty' => [
                'Herbal Shampoo','Natural Soap Bar','Bamboo Toothbrush','Organic Face Wash',
                'Deodorant Stick','Lip Balm','Aloe Vera Gel','Coconut Conditioner',
                'Charcoal Toothpowder','Body Scrub','Hand Cream','Face Serum',
                'Sunscreen SPF 30','Beard Oil','Bath Salt','Rosewater Toner',
                'Clay Mask','Essential Oil','Body Butter','Mouthwash Tablets',
                'Shower Gel','Foot Cream','Hair Mask','Toothpaste Tablets','Face Moisturizer'
            ],
            'Books' => [
                'Eco Living Guide','Zero Waste Handbook','Urban Gardening 101','Sustainable Fashion',
                'Green Kids Activity','Composting Basics','Renewable Energy Intro','Mindful Eating',
                'Plastic-Free Life','Minimalism Journey','Ethical Consumer','Nature Almanac',
                'Wildlife Atlas','Upcycling Ideas','Green Tech Trends','Forest Stories',
                'Ocean Tales','Climate 101','Herbal Remedies','Eco Parenting',
                'Slow Travel','Green Architecture','Fair Trade World','Clean Water',
                'Circular Economy'
            ],
        ];

        // URL gambar sederhana per kategori (boleh ganti ke CDN kamu)
        $imgByCategory = [
            'Toys'            => 'https://images.unsplash.com/photo-1601758124510-52d01f2b0aa4?q=80&w=1200&auto=format&fit=crop',
            'Fashion'         => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?q=80&w=1200&auto=format&fit=crop',
            'Health & Beauty' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1200&auto=format&fit=crop',
            'Books'           => 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?q=80&w=1200&auto=format&fit=crop',
        ];

        foreach ($categoryNames as $catIndex => $catName) {
            /** @var \App\Models\Category $category */
            $category = Category::where('name', $catName)->first();

            if (!$category) {
                // Jika kategori tidak ditemukan, lanjut kategori lain (atau bisa throw)
                $this->command?->warn("Kategori '{$catName}' tidak ditemukan. Lewati...");
                continue;
            }

            // Ambil pool nama untuk kategori ini
            $pool = $namePools[$catName] ?? [];
            if (count($pool) < 25) {
                // Jika kurang dari 25, gandakan sampai cukup
                while (count($pool) < 25) {
                    $pool = array_merge($pool, $pool);
                }
            }

            // Buat 25 produk
            for ($i = 1; $i <= 25; $i++) {
                $baseName = $pool[$i - 1];

                // Tambah varian agar unik dan terasa variasi di UI
                $variant = match ($catName) {
                    'Toys'            => ['Mini','Classic','Deluxe','Eco','Pro'][($i-1)%5],
                    'Fashion'         => ['Black','Natural','Olive','Navy','Sand'][($i-1)%5],
                    'Health & Beauty' => ['Lavender','Charcoal','Coconut','Aloe','Mint'][($i-1)%5],
                    'Books'           => ['Vol. I','Vol. II','Pocket','Hardcover','Revised'][($i-1)%5],
                    default           => 'V'.(($i-1)%5+1),
                };

                $name = "{$baseName} {$variant}";
                // Slug global unik: nama + kode kategori + urutan
                $slug = Str::slug($baseName.' '.$variant) . '-c' . ($catIndex+1) . "-{$i}";

                // Harga per kategori biar masuk akal
                [$min, $max] = match ($catName) {
                    'Toys'            => [35000, 180000],
                    'Fashion'         => [45000, 350000],
                    'Health & Beauty' => [15000, 200000],
                    'Books'           => [30000, 250000],
                    default           => [20000, 200000],
                };

                // decimal(12,2)
                $price = number_format(random_int($min, $max) + (random_int(0,99)/100), 2, '.', '');

                $stock     = random_int(5, 120);
                $ecoScore  = random_int(60, 95);
                $imageUrl  = $imgByCategory[$catName] ?? 'https://via.placeholder.com/600x450?text=EcoMart';
                $desc      = "Eco-friendly {$baseName} {$variant} in {$catName} category. Sustainable, durable, and designed with lower environmental impact.";

                Product::create([
                    'category_id' => $category->id,
                    'name'        => $name,
                    'slug'        => $slug,
                    'description' => $desc,
                    'price'       => $price,     // decimal(12,2)
                    'stock'       => $stock,
                    'image'       => $imageUrl,
                    'eco_score'   => $ecoScore,  // tinyInteger
                ]);
            }
        }
    }
}
