<?php

namespace App\Filament\Resources\ApprovalItemResource\Pages;

use App\Filament\Resources\ApprovalItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApprovalItem extends EditRecord
{
    protected static string $resource = ApprovalItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
