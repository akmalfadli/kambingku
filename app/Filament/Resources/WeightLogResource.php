<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeightLogResource\Pages;
use App\Models\WeightLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WeightLogResource extends Resource
{
    protected static ?string $model = WeightLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Operasional';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Weight Record')
                    ->schema([
                        Forms\Components\Select::make('goat_id')
                            ->relationship('goat', 'tag_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->required()
                            ->suffix('kg'),
                        Forms\Components\DatePicker::make('weigh_date')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('goat.tag_number')
                    ->label('Goat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weigh_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->suffix(' kg')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight_change')
                    ->label('Change from Previous')
                    ->state(function (WeightLog $record) {
                        $previous = WeightLog::where('goat_id', $record->goat_id)
                            ->where('weigh_date', '<', $record->weigh_date)
                            ->orderBy('weigh_date', 'desc')
                            ->first();

                        if (!$previous) return '—';

                        $change = $record->weight - $previous->weight;
                        return ($change >= 0 ? '+' : '') . number_format($change, 1) . ' kg';
                    })
                    ->color(function (WeightLog $record) {
                        $previous = WeightLog::where('goat_id', $record->goat_id)
                            ->where('weigh_date', '<', $record->weigh_date)
                            ->orderBy('weigh_date', 'desc')
                            ->first();

                        if (!$previous) return 'gray';

                        $change = $record->weight - $previous->weight;
                        return $change >= 0 ? 'success' : 'danger';
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\Filter::make('weigh_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('weigh_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('weigh_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('update_current_weight')
                    ->label('Set as Current')
                    ->icon('heroicon-o-arrow-up')
                    ->color('success')
                    ->action(function (WeightLog $record): void {
                        $record->goat->update(['current_weight' => $record->weight]);
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('weigh_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeightLogs::route('/'),
            'create' => Pages\CreateWeightLog::route('/create'),
            'view' => Pages\ViewWeightLog::route('/{record}'),
            'edit' => Pages\EditWeightLog::route('/{record}/edit'),
        ];
    }
}
