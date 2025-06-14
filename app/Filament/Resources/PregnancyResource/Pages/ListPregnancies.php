<?php

namespace App\Filament\Resources\PregnancyResource\Pages;

use App\Filament\Resources\PregnancyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPregnancies extends ListRecords
{
    protected static string $resource = PregnancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
