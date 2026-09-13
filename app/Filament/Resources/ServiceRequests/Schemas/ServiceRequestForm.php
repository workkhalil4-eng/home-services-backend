<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('provider_id')
                    ->numeric(),
                TextInput::make('service_id')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                TextInput::make('address'),
                DateTimePicker::make('scheduled_at'),
                TextInput::make('total_price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('payment_status')
                    ->required()
                    ->default('pending'),
            ]);
    }
}
