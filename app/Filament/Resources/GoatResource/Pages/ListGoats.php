<?php

namespace App\Filament\Resources\GoatResource\Pages;

use App\Filament\Resources\GoatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListGoats extends ListRecords
{
    protected static string $resource = GoatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('csv_file')
                        ->acceptedFileTypes(['text/csv', 'application/csv'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    // CSV import logic here
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->badge(fn () => \App\Models\Goat::count()),
            'active' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge(fn () => \App\Models\Goat::where('status', 'active')->count()),
            'fattening' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'fattening'))
                ->badge(fn () => \App\Models\Goat::where('type', 'fattening')->count()),
            'breeding' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'breeding'))
                ->badge(fn () => \App\Models\Goat::where('type', 'breeding')->count()),
        ];
    }
}
