<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('brand_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                Textarea::make('short_description')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('compare_price')
                    ->numeric()
                    ->default(null)
                    ->prefix('$'),
                TextInput::make('cost_price')
                    ->numeric()
                    ->default(null)
                    ->prefix('$'),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('low_stock_threshold')
                    ->required()
                    ->numeric()
                    ->default(10),
                Toggle::make('manage_stock')
                    ->required(),
                Select::make('stock_status')
                    ->options(['in_stock' => 'In stock', 'out_of_stock' => 'Out of stock', 'on_backorder' => 'On backorder'])
                    ->default('in_stock')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('has_variants')
                    ->required(),
                TextInput::make('weight')
                    ->numeric()
                    ->default(null),
                TextInput::make('meta_title')
                    ->default(null),
                Textarea::make('meta_description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('views_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
