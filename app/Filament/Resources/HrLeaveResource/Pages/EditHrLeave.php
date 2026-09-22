<?php

namespace App\Filament\Resources\HrLeaveResource\Pages;

use App\Filament\Resources\HrLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHrLeave extends EditRecord
{
    protected static string $resource = HrLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
