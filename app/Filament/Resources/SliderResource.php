<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = "Data Konten";

    public static function getNavigationSort(): ?int
    {
        return 1;
    }


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
                ->schema([
                    TextInput::make('title')
                        ->label('Judul Slider')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    // TextInput::make('slug')
                    //     ->label('Slug')
                    //     ->required()
                    //     ->maxLength(255)
                    //     ->unique(ignoreRecord: true)
                    //     ->helperText('Otomatis dibuat dari judul jika dikosongkan')
                    //     ->nullable(),

                    // FileUpload::make('image')
                    //     ->label('Gambar Slider')
                    //     ->image()
                    //     ->directory('sliders')
                    //     ->visibility('public')
                    //     ->required(),

                    TextInput::make('link')
                        ->label('Tautan')
                        ->url()
                        ->placeholder('https://')
                        ->helperText('Opsional - Tautan saat slider diklik')
                        ->nullable(),

                    TextInput::make('order')
                        ->label('Urutan')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->minValue(1)
                        ->maxValue(100),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Aktif',
                            'inactive' => 'Nonaktif',
                        ])
                        ->default('active')
                        ->required(),

                        FileUpload::make('image')
                        ->label('Gambar Slider')
                        ->image()
                        ->directory('sliders')
                        ->visibility('public')
                        ->required(),
                ])
                ->columns(2),

            // Relasi dengan produk (opsional)
            Card::make()
                ->schema([
                    Forms\Components\Select::make('products')
                        ->label('Produk Terkait')
                        ->multiple()
                        ->relationship('products', 'name')
                        ->preload()
                        ->searchable()
                        ->helperText('Pilih produk yang ingin ditampilkan di slider ini'),
                ])
                ->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->height(40)
                    ->width(80),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Buat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'active' => 'Aktif',
                    'inactive' => 'Nonaktif',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
