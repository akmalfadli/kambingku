<?php

namespace App\Filament\Resources\FeedingLogResource\Pages;

use App\Filament\Resources\FeedingLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeedingLog extends ViewRecord
{
    protected static string $resource = FeedingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
