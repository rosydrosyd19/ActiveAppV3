<?php

namespace App\Filament\Resources\HrLeaveResource\Pages;

use App\Filament\Resources\HrLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHrLeaves extends ListRecords
{
    protected static string $resource = HrLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
