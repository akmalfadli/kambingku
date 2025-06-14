<?php

namespace App\Filament\Resources\GoatResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WeightLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'weightLogs';
    protected static ?string $title = 'Weight History';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('weight')
                    ->required()
                    ->numeric()
                    ->suffix('kg'),
                Forms\Components\DatePicker::make('weigh_date')
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('weight')
            ->columns([
                Tables\Columns\TextColumn::make('weigh_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->suffix(' kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight_change')
                    ->label('Change')
                    ->state(function ($record) {
                        $previous = $this->getOwnerRecord()
                            ->weightLogs()
                            ->where('weigh_date', '<', $record->weigh_date)
                            ->orderBy('weigh_date', 'desc')
                            ->first();

                        if (!$previous) return '—';

                        $change = $record->weight - $previous->weight;
                        return ($change >= 0 ? '+' : '') . number_format($change, 1) . ' kg';
                    })
                    ->color(function ($record) {
                        $previous = $this->getOwnerRecord()
                            ->weightLogs()
                            ->where('weigh_date', '<', $record->weigh_date)
                            ->orderBy('weigh_date', 'desc')
                            ->first();

                        if (!$previous) return 'gray';

                        $change = $record->weight - $previous->weight;
                        return $change >= 0 ? 'success' : 'danger';
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->placeholder('—'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Update the goat's current weight
                        $this->getOwnerRecord()->update(['current_weight' => $data['weight']]);
                        return $data;
                    }),
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
            ->defaultSort('weigh_date', 'desc');
    }
}
