<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('code', strtoupper($state))
                            )
                            ->required(),
                        Select::make('type')
                            ->options(['fixed' => 'Fixed', 'percentage' => 'Percentage'])
                            ->default('percentage')
                            ->live()
                            ->required(),
                        TextInput::make('value')
                            ->required()
                            ->minValue(0)
                            ->prefix(fn(callable $get) => $get('type') === 'fixed' ? '$' : null)
                            ->suffix(fn(callable $get) => $get('type') === 'percentage' ? '%' : null)
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required(),
                    ]),

                Section::make('Condotions & Limits')
                    ->schema([
                        TextInput::make('minimum_order_value')
                            ->prefix('$')
                            ->minValue(0)
                            ->numeric()
                            ->default(null),
                        TextInput::make('maximum_discount')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->visible(fn(callable $get) => $get('type') === 'percentage')
                            ->default(null),
                        TextInput::make('usage_limit')
                            ->minValue(1)
                            ->numeric()
                            ->default(null),
                        TextInput::make('usage_limit_per_customer')
                            ->numeric()
                            ->minValue(1)
                            ->default(null),
                    ]),

                Section::make('Validity Period')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->native(false)
                            ->helperText('When the coupon becomes active '),
                        DateTimePicker::make('expires_at')
                            ->native(false),
                    ])






            ]);
    }
}
