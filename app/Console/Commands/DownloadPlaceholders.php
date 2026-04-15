<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DownloadPlaceholders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download-placeholders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all necessary product and category placeholder images';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categoriesDir = storage_path('app/public/categories');
        $productsDir = storage_path('app/public/products');
        
        if (!is_dir($categoriesDir)) mkdir($categoriesDir, 0777, true);
        if (!is_dir($productsDir)) mkdir($productsDir, 0777, true);

        // 1. Update Category Images to use permanent external URLs
        $this->info('Linking category images to permanent URLs...');
        $categories = \App\Models\Category::all();
        foreach ($categories as $cat) {
            $kw = 'store';
            $name = $cat->name;
            if (stripos($name, 'electronic') !== false) $kw = 'electronics';
            if (stripos($name, 'fashion') !== false) $kw = 'clothing';
            if (stripos($name, 'home') !== false) $kw = 'home';
            if (stripos($name, 'sport') !== false) $kw = 'sports';
            if (stripos($name, 'book') !== false) $kw = 'books';
            if (stripos($name, 'beauty') !== false) $kw = 'beauty';
            if (stripos($name, 'toy') !== false) $kw = 'toys';
            if (stripos($name, 'auto') !== false) $kw = 'car';
            if (stripos($name, 'health') !== false) $kw = 'health';
            if (stripos($name, 'pet') !== false) $kw = 'pets';

            // We use a high-quality static source
            $url = "https://loremflickr.com/800/800/{$kw}/all?lock={$cat->id}";
            $cat->update(['image' => $url]);
            $this->line("Linked category $name to: $url");
        }

        // 2. Update Product Images
        $this->info('Linking product images to permanent URLs...');
        $products = \App\Models\Product::all();
        $productKeywords = ['electronics', 'fashion', 'shoes', 'furniture', 'watch'];
        
        foreach ($products as $index => $product) {
            $kw = $productKeywords[$index % count($productKeywords)];
            $url = "https://loremflickr.com/800/800/product,{$kw}/all?lock={$product->id}";
            
            // Update or create primary image
            $product->images()->updateOrCreate(
                ['is_primary' => true],
                [
                    'image_path' => $url,
                    'alt_text' => $product->name,
                    'sort_order' => 0
                ]
            );
            $this->line("Linked product {$product->name} to: $url");
        }

        $this->info('All images linked to permanent internet URLs successfully!');
    }
}
