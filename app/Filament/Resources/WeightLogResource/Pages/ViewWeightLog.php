<?php

namespace App\Filament\Resources\WeightLogResource\Pages;

use App\Filament\Resources\WeightLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWeightLog extends ViewRecord
{
    protected static string $resource = WeightLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
