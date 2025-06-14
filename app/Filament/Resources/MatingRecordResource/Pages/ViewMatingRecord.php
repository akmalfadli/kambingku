<?php

namespace App\Filament\Resources\MatingRecordResource\Pages;

use App\Filament\Resources\MatingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMatingRecord extends ViewRecord
{
    protected static string $resource = MatingRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
