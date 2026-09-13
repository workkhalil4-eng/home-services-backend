<?php

namespace App\Filament\Resources\ProviderProfiles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProviderProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                IconEntry::make('is_available')
                    ->boolean(),
                TextEntry::make('max_travel_distance')
                    ->numeric(),
                ImageEntry::make('id_card_image')
                    ->placeholder('-'),
                ImageEntry::make('certificate_image')
                    ->placeholder('-'),
                TextEntry::make('kyc_status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
