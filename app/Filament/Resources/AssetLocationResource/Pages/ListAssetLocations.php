<?php

namespace App\Filament\Resources\AssetLocationResource\Pages;

use App\Filament\Resources\AssetLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssetLocations extends ListRecords
{
    protected static string $resource = AssetLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
