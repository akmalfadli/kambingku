<?php

namespace App\Filament\Resources\FeedingLogResource\Pages;

use App\Filament\Resources\FeedingLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeedingLog extends EditRecord
{
    protected static string $resource = FeedingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
