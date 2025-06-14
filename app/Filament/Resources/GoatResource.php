<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoatResource\Pages;
use App\Models\Goat;
use App\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GoatResource extends Resource
{
    protected static ?string $model = Goat::class;

    // Indonesian labels
    protected static ?string $modelLabel = 'Kambing';
    protected static ?string $pluralModelLabel = 'Kambing';
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Manajemen Ternak';
    protected static ?string $navigationLabel = 'Kambing';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('tag_number')
                            ->label('Nomor Tag')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama'),
                        Forms\Components\Select::make('breed')
                            ->label('Ras')
                            ->options([
                                'boer' => 'Boer',
                                'kacang' => 'Kacang',
                                'etawa' => 'Etawa',
                                'saanen' => 'Saanen',
                                'anglo_nubian' => 'Anglo Nubian',
                                'mixed' => 'Campuran',
                            ])
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male' => 'Jantan',
                                'female' => 'Betina',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Fisik & Tanggal')
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->native(false),
                        Forms\Components\TextInput::make('current_weight')
                            ->label('Berat Saat Ini')
                            ->numeric()
                            ->suffix('kg'),
                        Forms\Components\TextInput::make('origin')
                            ->label('Asal/Lokasi')
                            ->placeholder('Tempat asal kambing'),
                    ])->columns(3),

                Forms\Components\Section::make('Status & Tipe')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'breeding' => 'Breeding',
                                'fattening' => 'Penggemukan',
                            ])
                            ->default('fattening')
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'sold' => 'Terjual',
                                'deceased' => 'Mati',
                                'dead' => 'Mati',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Pembelian')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Harga Beli')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->helperText('Harga pembelian kambing'),
                    ])
                    ->visible(fn (Forms\Get $get): bool => $get('type') === 'fattening'),

                Forms\Components\Section::make('Keturunan (Untuk Breeding)')
                    ->schema([
                        Forms\Components\Select::make('father_id')
                            ->label('Pejantan (Ayah)')
                            ->relationship('father', 'tag_number', fn ($query) => $query->where('gender', 'male'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('mother_id')
                            ->label('Induk (Ibu)')
                            ->relationship('mother', 'tag_number', fn ($query) => $query->where('gender', 'female'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2)
                    ->visible(fn (Forms\Get $get): bool => $get('type') === 'breeding'),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('photos')
                            ->label('Foto Kambing')
                            ->collection('photos')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Upload foto kambing (maksimal 5 foto)'),
                    ]),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photos')
                    ->label('Foto')
                    ->collection('photos')
                    ->conversion('thumb')
                    ->size(50)
                    ->circular(),
                Tables\Columns\TextColumn::make('tag_number')
                    ->label('Nomor Tag')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->placeholder('Tidak ada nama'),
                Tables\Columns\TextColumn::make('breed')
                    ->label('Ras')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'boer' => 'Boer',
                        'kacang' => 'Kacang',
                        'etawa' => 'Etawa',
                        'saanen' => 'Saanen',
                        'anglo_nubian' => 'Anglo Nubian',
                        'mixed' => 'Campuran',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => 'Jantan',
                        'female' => 'Betina',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'pink',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('current_weight')
                    ->label('Berat')
                    ->suffix(' kg')
                    ->sortable()
                    ->placeholder('Belum diukur'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'breeding' => 'Breeding',
                        'fattening' => 'Penggemukan',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'breeding' => 'success',
                        'fattening' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->formatStateUsing(fn ($state) => $state ? CurrencyHelper::formatRupiah($state) : 'Tidak ada')
                    ->sortable()
                    ->toggleable()
                    ->visible(fn () => request()->get('tableFilters.type.value') === 'fattening'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'sold' => 'Terjual',
                        'deceased' => 'Mati',
                        'dead' => 'Mati',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'sold' => 'warning',
                        'deceased' => 'danger',
                        'dead' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('age')
                    ->label('Umur')
                    ->state(function (Goat $record): string {
                        return $record->age;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('origin')
                    ->label('Asal')
                    ->searchable()
                    ->placeholder('Tidak diketahui')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'breeding' => 'Breeding',
                        'fattening' => 'Penggemukan',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'sold' => 'Terjual',
                        'deceased' => 'Mati (Deceased)',
                        'dead' => 'Mati (Dead)',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Jantan',
                        'female' => 'Betina',
                    ]),
                Tables\Filters\SelectFilter::make('breed')
                    ->label('Ras')
                    ->options([
                        'boer' => 'Boer',
                        'kacang' => 'Kacang',
                        'etawa' => 'Etawa',
                        'saanen' => 'Saanen',
                        'anglo_nubian' => 'Anglo Nubian',
                        'mixed' => 'Campuran',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn (Goat $record): string => route('goat.qr', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('sell')
                    ->label('Jual Kambing')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->url(fn (Goat $record): string => route('filament.admin.resources.sales.create') . '?goat_id=' . $record->id)
                    ->visible(fn (Goat $record): bool => $record->status === 'active'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('tag_number', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoats::route('/'),
            'create' => Pages\CreateGoat::route('/create'),
            'view' => Pages\ViewGoat::route('/{record}'),
            'edit' => Pages\EditGoat::route('/{record}/edit'),
        ];
    }
}
