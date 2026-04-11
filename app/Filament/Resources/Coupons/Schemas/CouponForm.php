<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                Select::make('type')
                    ->options(['fixed' => 'Fixed', 'percentage' => 'Percentage'])
                    ->default('percentage')
                    ->required(),
                TextInput::make('value')
                    ->required()
                    ->numeric(),
                TextInput::make('minimum_order_value')
                    ->numeric()
                    ->default(null),
                TextInput::make('maximum_discount')
                    ->numeric()
                    ->default(null),
                TextInput::make('usage_limit')
                    ->numeric()
                    ->default(null),
                TextInput::make('usage_limit_per_customer')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('expires_at'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
