<?php

namespace App\Filament\Resources\ProviderProfiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProviderProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Toggle::make('is_available')
                    ->required(),
                TextInput::make('max_travel_distance')
                    ->required()
                    ->numeric()
                    ->default(10),
                FileUpload::make('id_card_image')
                    ->image(),
                FileUpload::make('certificate_image')
                    ->image(),
                TextInput::make('kyc_status')
                    ->required()
                    ->default('pending'),
            ]);
    }
}
