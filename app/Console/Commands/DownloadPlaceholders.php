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

        // 1. Download Product Placeholders
        $this->info('Downloading product placeholders...');
        $productKeywords = ['electronics', 'fashion', 'shoes', 'furniture', 'watch'];
        foreach ($productKeywords as $index => $kw) {
            $i = $index + 1;
            $url = "https://loremflickr.com/800/800/product,{$kw}/all";
            $imgData = @file_get_contents($url);
            if ($imgData !== false) {
                file_put_contents("$productsDir/placeholder-$i.jpg", $imgData);
                $this->line("Downloaded product placeholder-$i.jpg ($kw)");
            }
        }

        // 2. Download Category Images
        $this->info('Downloading category images...');
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

            $url = "https://loremflickr.com/800/800/{$kw}/all";
            $imgData = @file_get_contents($url);
            if ($imgData !== false) {
                $path = "categories/cat-{$cat->id}.jpg";
                file_put_contents(storage_path("app/public/$path"), $imgData);
                $cat->update(['image' => $path]);
                $this->line("Updated category: $name");
            }
        }

        $this->info('All images downloaded successfully.');
    }
}
