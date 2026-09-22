<?php

namespace App\Filament\Resources\ApprovalItemResource\Pages;

use App\Filament\Resources\ApprovalItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovalItems extends ListRecords
{
    protected static string $resource = ApprovalItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
