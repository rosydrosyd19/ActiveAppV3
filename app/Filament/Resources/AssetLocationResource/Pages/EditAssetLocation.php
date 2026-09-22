<?php

namespace App\Filament\Resources\AssetLocationResource\Pages;

use App\Filament\Resources\AssetLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetLocation extends EditRecord
{
    protected static string $resource = AssetLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
