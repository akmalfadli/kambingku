<?php

namespace App\Filament\Resources\HealthRecordResource\Pages;

use App\Filament\Resources\HealthRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHealthRecord extends ViewRecord
{
    protected static string $resource = HealthRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
