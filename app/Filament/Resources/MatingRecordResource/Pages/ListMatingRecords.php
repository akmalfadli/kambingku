<?php

namespace App\Filament\Resources\MatingRecordResource\Pages;

use App\Filament\Resources\MatingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMatingRecords extends ListRecords
{
    protected static string $resource = MatingRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
