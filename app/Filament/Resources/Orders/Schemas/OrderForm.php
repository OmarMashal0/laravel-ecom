<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required(),
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('coupon_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_full_name')
                    ->required(),
                TextInput::make('shipping_phone')
                    ->tel()
                    ->required(),
                TextInput::make('shipping_address_line_1')
                    ->required(),
                TextInput::make('shipping_address_line_2')
                    ->default(null),
                TextInput::make('shipping_city')
                    ->required(),
                TextInput::make('shipping_state')
                    ->default(null),
                TextInput::make('shipping_postal_code')
                    ->required(),
                TextInput::make('shipping_country')
                    ->required(),
                Select::make('payment_method')
                    ->options(['stripe' => 'Stripe', 'cash_on_delivery' => 'Cash on delivery'])
                    ->default('stripe')
                    ->required(),
                TextInput::make('payment_status')
                    ->required()
                    ->default('pending'),
                TextInput::make('transaction_id')
                    ->default(null),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('tracking_number')
                    ->default(null),
                Textarea::make('customer_notes')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
