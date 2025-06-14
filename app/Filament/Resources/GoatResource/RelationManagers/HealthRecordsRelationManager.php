<?php

namespace App\Filament\Resources\GoatResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HealthRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'healthRecords';
    protected static ?string $title = 'Health Records';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('record_date')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('diagnosis')
                    ->required(),
                Forms\Components\Textarea::make('treatment')
                    ->required()
                    ->rows(3),
                Forms\Components\TextInput::make('medicine_given'),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('vet_name'),
                Forms\Components\DatePicker::make('next_checkup_date'),
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('diagnosis')
            ->columns([
                Tables\Columns\TextColumn::make('record_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('diagnosis')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('treatment')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->treatment),
                Tables\Columns\TextColumn::make('medicine_given')
                    ->placeholder('—')
                    ->limit(20),
                Tables\Columns\TextColumn::make('cost')
                    ->money('USD')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('vet_name')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('next_checkup_date')
                    ->date()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->next_checkup_date && $record->next_checkup_date->isPast() ? 'danger' : null),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('record_date', 'desc');
    }
}
