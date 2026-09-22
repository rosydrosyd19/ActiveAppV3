<?php

namespace App\Filament\Resources\HrAttendanceResource\Pages;

use App\Filament\Resources\HrAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHrAttendance extends EditRecord
{
    protected static string $resource = HrAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
