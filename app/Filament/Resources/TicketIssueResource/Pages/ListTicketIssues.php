<?php

namespace App\Filament\Resources\TicketIssueResource\Pages;

use App\Filament\Resources\TicketIssueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketIssues extends ListRecords
{
    protected static string $resource = TicketIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
