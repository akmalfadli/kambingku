<?php

namespace App\Filament\Resources\PregnancyResource\Pages;

use App\Filament\Resources\PregnancyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPregnancy extends ViewRecord
{
    protected static string $resource = PregnancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
