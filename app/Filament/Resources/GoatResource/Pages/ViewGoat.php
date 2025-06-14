<?php

namespace App\Filament\Resources\GoatResource\Pages;

use App\Filament\Resources\GoatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGoat extends ViewRecord
{
    protected static string $resource = GoatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
