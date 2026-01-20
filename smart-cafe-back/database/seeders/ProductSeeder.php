<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\StoreProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le premier admin et les stores
        $admin = User::role('admin')->first();
        $stores = Store::all();

        if (! $admin || $stores->isEmpty()) {
            $this->command->warn('Admin ou Store non trouvé. Exécutez d\'abord les seeders User et Store.');

            return;
        }

        // Récupérer les catégories
        $cafesCategory = ProductCategory::where('slug', 'cafes')->first();
        $thesCategory = ProductCategory::where('slug', 'thes')->first();
        $viennoiseriesCategory = ProductCategory::where('slug', 'viennoiseries')->first();

        if (! $cafesCategory) {
            $this->command->warn('Catégories non trouvées. Exécutez d\'abord ProductCategorySeeder.');

            return;
        }

        // Produits café
        $this->createCoffeeProducts($cafesCategory, $stores, $admin);

        // Produits thé
        if ($thesCategory) {
            $this->createTeaProducts($thesCategory, $stores, $admin);
        }

        // Viennoiseries
        if ($viennoiseriesCategory) {
            $this->createPastryProducts($viennoiseriesCategory, $stores, $admin);
        }
    }

    private function createCoffeeProducts(ProductCategory $category, $stores, User $admin): void
    {
        // Espresso
        $espresso = Product::create([
            'name' => 'Espresso',
            'slug' => 'espresso',
            'description' => 'Un espresso classique, intense et aromatique.',
            'product_category_id' => $category->id,
            'created_by' => $admin->id,
            'is_active' => true,
            'is_featured' => true,
        ]);

        // Associer le produit aux stores
        $espresso->stores()->attach($stores->pluck('id'));

        $espressoVariant = ProductVariant::create([
            'product_id' => $espresso->id,
            'sku' => 'ESP-001',
            'price_cent_ht' => 180, // 1,80€
            'is_default' => true,
        ]);

        // Ajouter le stock dans chaque store (null = illimité)
        foreach ($stores as $s) {
            StoreProductVariant::create([
                'store_id' => $s->id,
                'product_variant_id' => $espressoVariant->id,
                'stock' => null, // Illimité
            ]);
        }

        // Cappuccino avec options
        $cappuccino = Product::create([
            'name' => 'Cappuccino',
            'slug' => 'cappuccino',
            'description' => 'Espresso avec lait mousseux et une touche de cacao.',
            'product_category_id' => $category->id,
            'created_by' => $admin->id,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $cappuccino->stores()->attach($stores->pluck('id'));

        // Variants par taille
        $capS = ProductVariant::create([
            'product_id' => $cappuccino->id,
            'sku' => 'CAP-S',
            'price_cent_ht' => 350,
            'is_default' => true,
        ]);

        $capM = ProductVariant::create([
            'product_id' => $cappuccino->id,
            'sku' => 'CAP-M',
            'price_cent_ht' => 420,
            'is_default' => false,
        ]);

        $capL = ProductVariant::create([
            'product_id' => $cappuccino->id,
            'sku' => 'CAP-L',
            'price_cent_ht' => 490,
            'is_default' => false,
        ]);

        // Stocks pour les variants du cappuccino
        foreach ($stores as $s) {
            StoreProductVariant::create([
                'store_id' => $s->id,
                'product_variant_id' => $capS->id,
                'stock' => null,
            ]);
            StoreProductVariant::create([
                'store_id' => $s->id,
                'product_variant_id' => $capM->id,
                'stock' => null,
            ]);
            StoreProductVariant::create([
                'store_id' => $s->id,
                'product_variant_id' => $capL->id,
                'stock' => null,
            ]);
        }

        // Options pour le cappuccino
        $tailleOption = ProductOption::create([
            'product_id' => $cappuccino->id,
            'name' => 'Taille',
            'is_required' => true,
        ]);

        ProductOptionValue::create([
            'product_option_id' => $tailleOption->id,
            'value' => 'Small',
            'price_add_cent_ht' => 0,
        ]);

        ProductOptionValue::create([
            'product_option_id' => $tailleOption->id,
            'value' => 'Medium',
            'price_add_cent_ht' => 70,
        ]);

        ProductOptionValue::create([
            'product_option_id' => $tailleOption->id,
            'value' => 'Large',
            'price_add_cent_ht' => 140,
        ]);

        $laitOption = ProductOption::create([
            'product_id' => $cappuccino->id,
            'name' => 'Type de lait',
            'is_required' => false,
        ]);

        ProductOptionValue::create([
            'product_option_id' => $laitOption->id,
            'value' => 'Lait entier',
            'price_add_cent_ht' => 0,
        ]);

        ProductOptionValue::create([
            'product_option_id' => $laitOption->id,
            'value' => 'Lait d\'avoine',
            'price_add_cent_ht' => 50,
        ]);

        ProductOptionValue::create([
            'product_option_id' => $laitOption->id,
            'value' => 'Lait d\'amande',
            'price_add_cent_ht' => 50,
        ]);

        // Latte
        $latte = Product::create([
            'name' => 'Latte',
            'slug' => 'latte',
            'description' => 'Espresso doux avec beaucoup de lait velouté.',
            'product_category_id' => $category->id,
            'created_by' => $admin->id,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $latte->stores()->attach($stores->pluck('id'));

        $latteVariant = ProductVariant::create([
            'product_id' => $latte->id,
            'sku' => 'LAT-001',
            'price_cent_ht' => 400,
            'is_default' => true,
        ]);

        foreach ($stores as $s) {
            StoreProductVariant::create([
                'store_id' => $s->id,
                'product_variant_id' => $latteVariant->id,
                'stock' => null,
            ]);
        }
    }

    private function createTeaProducts(ProductCategory $category, $stores, User $admin): void
    {
        $products = [
            ['name' => 'Thé Earl Grey', 'price' => 320],
            ['name' => 'Thé vert Sencha', 'price' => 350],
            ['name' => 'Infusion Camomille', 'price' => 300],
        ];

        foreach ($products as $index => $data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => 'Un excellent '.strtolower($data['name']).'.',
                'product_category_id' => $category->id,
                'created_by' => $admin->id,
                'is_active' => true,
            ]);

            $product->stores()->attach($stores->pluck('id'));

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => 'TEA-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'price_cent_ht' => $data['price'],
                'is_default' => true,
            ]);

            foreach ($stores as $s) {
                StoreProductVariant::create([
                    'store_id' => $s->id,
                    'product_variant_id' => $variant->id,
                    'stock' => null, // Illimité
                ]);
            }
        }
    }

    private function createPastryProducts(ProductCategory $category, $stores, User $admin): void
    {
        $products = [
            ['name' => 'Croissant', 'price' => 150, 'featured' => true, 'stock' => 50],
            ['name' => 'Pain au chocolat', 'price' => 180, 'featured' => true, 'stock' => 50],
            ['name' => 'Chausson aux pommes', 'price' => 200, 'featured' => false, 'stock' => 30],
        ];

        foreach ($products as $index => $data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => 'Délicieux '.strtolower($data['name']).' du jour.',
                'product_category_id' => $category->id,
                'created_by' => $admin->id,
                'is_active' => true,
                'is_featured' => $data['featured'],
            ]);

            $product->stores()->attach($stores->pluck('id'));

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => 'VIE-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'price_cent_ht' => $data['price'],
                'is_default' => true,
            ]);

            // Les viennoiseries ont un stock limité par store
            foreach ($stores as $s) {
                StoreProductVariant::create([
                    'store_id' => $s->id,
                    'product_variant_id' => $variant->id,
                    'stock' => $data['stock'],
                ]);
            }
        }
    }
}
