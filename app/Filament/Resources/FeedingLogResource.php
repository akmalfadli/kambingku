<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedingLogResource\Pages;
use App\Models\FeedingLog;
use App\Models\Goat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeedingLogResource extends Resource
{
    protected static ?string $model = FeedingLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Operasional';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feeding Details')
                    ->schema([
                        Forms\Components\Toggle::make('is_group_feeding')
                            ->label('Group Feeding')
                            ->reactive(),
                        Forms\Components\Select::make('goat_id')
                            ->relationship('goat', 'tag_number')
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => !$get('is_group_feeding'))
                            ->required(fn (Forms\Get $get) => !$get('is_group_feeding')),
                        Forms\Components\Select::make('goat_ids')
                            ->label('Select Goats')
                            ->multiple()
                            ->options(Goat::where('status', 'active')->pluck('tag_number', 'id'))
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => $get('is_group_feeding'))
                            ->required(fn (Forms\Get $get) => $get('is_group_feeding')),
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
                            ->prefix('Rp'),
                        Forms\Components\DatePicker::make('feeding_date')
                            ->default(now())
                            ->required()
                            ->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('feeding_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_group_feeding')
                    ->label('Group')
                    ->boolean(),
                Tables\Columns\TextColumn::make('goat.tag_number')
                    ->label('Goat')
                    ->placeholder('Group Feeding')
                    ->searchable(),
                Tables\Columns\TextColumn::make('feed_type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->suffix(' kg')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_kg')
                    ->label('Cost/kg')
                    ->state(fn (FeedingLog $record): string => 'Rp' . number_format($record->cost / $record->quantity, 2))
                    ->sortable(),
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
                Tables\Filters\Filter::make('feeding_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('feeding_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('feeding_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('feeding_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedingLogs::route('/'),
            'create' => Pages\CreateFeedingLog::route('/create'),
            'view' => Pages\ViewFeedingLog::route('/{record}'),
            'edit' => Pages\EditFeedingLog::route('/{record}/edit'),
        ];
    }
}
