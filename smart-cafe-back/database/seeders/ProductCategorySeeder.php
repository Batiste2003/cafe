<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Catégories principales
        $categories = [
            [
                'name' => 'Boissons chaudes',
                'description' => 'Cafés, thés et autres boissons chaudes',
                'children' => [
                    ['name' => 'Cafés', 'description' => 'Expressos, cappuccinos, lattes...'],
                    ['name' => 'Thés', 'description' => 'Thés noirs, verts, infusions...'],
                    ['name' => 'Chocolats', 'description' => 'Chocolats chauds et mochas'],
                ],
            ],
            [
                'name' => 'Boissons froides',
                'description' => 'Boissons rafraîchissantes',
                'children' => [
                    ['name' => 'Cafés glacés', 'description' => 'Iced lattes, cold brews...'],
                    ['name' => 'Smoothies', 'description' => 'Smoothies aux fruits'],
                    ['name' => 'Jus de fruits', 'description' => 'Jus frais pressés'],
                ],
            ],
            [
                'name' => 'Pâtisseries',
                'description' => 'Viennoiseries et desserts',
                'children' => [
                    ['name' => 'Viennoiseries', 'description' => 'Croissants, pains au chocolat...'],
                    ['name' => 'Gâteaux', 'description' => 'Parts de gâteaux et tartes'],
                    ['name' => 'Cookies', 'description' => 'Cookies et biscuits'],
                ],
            ],
            [
                'name' => 'Snacks',
                'description' => 'Petits encas salés et sucrés',
                'children' => [
                    ['name' => 'Sandwichs', 'description' => 'Sandwichs frais du jour'],
                    ['name' => 'Salades', 'description' => 'Salades composées'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $parent = ProductCategory::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'is_active' => true,
            ]);

            if (isset($categoryData['children'])) {
                foreach ($categoryData['children'] as $childData) {
                    ProductCategory::create([
                        'name' => $childData['name'],
                        'slug' => Str::slug($childData['name']),
                        'description' => $childData['description'],
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
