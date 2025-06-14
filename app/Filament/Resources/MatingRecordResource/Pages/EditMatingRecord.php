<?php

namespace App\Filament\Resources\MatingRecordResource\Pages;

use App\Filament\Resources\MatingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMatingRecord extends EditRecord
{
    protected static string $resource = MatingRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
