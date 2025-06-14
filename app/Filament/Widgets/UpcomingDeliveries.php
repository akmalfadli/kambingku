<?php

namespace App\Filament\Widgets;

use App\Models\Pregnancy;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingDeliveries extends BaseWidget
{
    protected static ?string $heading = 'Kelahiran yang Akan Datang (30 Hari Ke Depan)';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pregnancy::query()
                    ->where('status', 'pregnant')
                    ->whereBetween('expected_delivery_date', [now(), now()->addDays(30)])
                    ->orderBy('expected_delivery_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('femaleGoat.tag_number')
                    ->label('Female Goat'),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label('Expected Date')
                    ->date(),
                Tables\Columns\TextColumn::make('days_until')
                    ->label('Days Until')
                    ->state(fn (Pregnancy $record): string => now()->diffInDays($record->expected_delivery_date) . ' days')
                    ->color(function (Pregnancy $record): string {
                        $days = now()->diffInDays($record->expected_delivery_date);
                        return $days <= 7 ? 'danger' : 'warning';
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Pregnancy $record): string => route('filament.admin.resources.pregnancies.view', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
