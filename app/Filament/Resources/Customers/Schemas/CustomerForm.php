<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                ->schema([
                    TextInput::make('name')
                    ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->unique(ignoreRecord: true)
                        ->email()
                        ->required(),
                    DateTimePicker::make('email_verified_at'),
                    
                    TextInput::make('phone')
                        ->tel()
                        ->default(null),
                    DatePicker::make('date_of_birth')
                    ->native(false)
                    ->native()
                    ->displayFormat('M d, Y'),
                    Select::make('gender')
                        ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'])
                        ->default(null)
                        ->native(false),
                    Toggle::make('is_active')
                        ->required(),
                ])
                ->columns(2),
                Section::make('Password Infos')
                ->schema([
                    TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->revealable()
                    ->required(),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->same('password')
                        ->revealable()
                        ->dehydrated(false)
                        ->required(fn(string $operation) => $operation === 'create'),
                ])
                
            ]);
    }
}
