<?php

namespace App\Filament\Resources\GoatResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FeedingLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'feedingLogs';
    protected static ?string $title = 'Feeding History';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('feed_type')
                    ->options([
                        'grass' => 'Grass',
                        'concentrate' => 'Concentrate',
                        'hay' => 'Hay',
                        'mixed' => 'Mixed',
                        'supplements' => 'Supplements',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->suffix('kg'),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->prefix('$'),
                Forms\Components\DatePicker::make('feeding_date')
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('feed_type')
            ->columns([
                Tables\Columns\TextColumn::make('feeding_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('feed_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('quantity')
                    ->suffix(' kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_kg')
                    ->label('Cost/kg')
                    ->state(fn ($record) => '$' . number_format($record->cost / $record->quantity, 2)),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(30)
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('feed_type')
                    ->options([
                        'grass' => 'Grass',
                        'concentrate' => 'Concentrate',
                        'hay' => 'Hay',
                        'mixed' => 'Mixed',
                        'supplements' => 'Supplements',
                    ]),
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
            ->defaultSort('feeding_date', 'desc');
    }
}
