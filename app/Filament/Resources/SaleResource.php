<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = "Data Utama";

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('user_id')
                            ->label('Kasir')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive(),
    
                        DatePicker::make('sale_date')
                            ->label('Tanggal Penjualan')
                            ->default(now())
                            ->required(),
    
                        Repeater::make('details')
                            ->label('Detail Penjualan')
                            ->relationship('details')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produk')
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $product = Product::find($state);
                                        $set('sale_price', $product?->price);
                                        $set('stock_available', $product?->stock);
                                    })
                                    ->afterStateHydrated(function ($state, $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            $set('sale_price', $product?->price);
                                            $set('stock_available', $product?->stock);
                                        }
                                    }),
    
                                // Badge stok menggunakan komponen bawaan
                                TextInput::make('stock_available')
                                    ->label('Stok Tersedia')
                                    ->disabled()
                                    ->hidden(fn($get) => !$get('product_id'))
                                    ->formatStateUsing(fn ($state) => $state)
                                    ->helperText(function ($get) {
                                        $stock = $get('stock_available');
                                        if ($stock === null || !$get('product_id')) return null;
                                        
                                        if ($stock > 10) {
                                            return new HtmlString('<span class="text-success-600 font-medium">Stok Aman</span>');
                                        } elseif ($stock > 5) {
                                            return new HtmlString('<span class="text-warning-600 font-medium">Stok Menipis</span>');
                                        } else {
                                            return new HtmlString('<span class="text-danger-600 font-medium">Stok Kritis</span>');
                                        }
                                    }),
    
                                TextInput::make('sale_price')
                                    ->label('Harga Jual')
                                    ->prefix('Rp')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
    
                                TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $stockAvailable = $get('stock_available');
                                        $price = $get('sale_price');
                                        
                                        if ($state > $stockAvailable) {
                                            Notification::make()
                                                ->title("Stok hanya tersedia {$stockAvailable}!")
                                                ->danger()
                                                ->send();
                                            
                                            $set('quantity', $stockAvailable);
                                            $state = $stockAvailable;
                                        }
                                        
                                        $set('subtotal', $price * $state);
                                    }),
    
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->required()
                            ->createItemButtonLabel('Tambah Produk')
                            ->collapsible()
                            ->cloneable()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $total = collect($state ?? [])->sum(fn($item) => 
                                    ($item['quantity'] ?? 0) * ($item['sale_price'] ?? 0)
                                );
                                $set('total_amount', $total);
                            }),
    
                        // Total penjualan menggunakan TextInput dengan format
                        TextInput::make('total_amount')
                            ->label('Total Penjualan')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated()
                            ->extraAttributes(['class' => 'text-xl font-bold'])
                            ->afterStateHydrated(function ($record, callable $set) {
                                $total = $record?->details->sum('subtotal') ?? 0;
                                $set('total_amount', $total);
                            }),
    
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer',
                                'qris' => 'QRIS',
                            ])
                            ->default('cash')
                            ->required(),
    
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Selesai',
                                'canceled' => 'Dibatalkan',
                            ])
                            ->default('completed')
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sale_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR')),

                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'transfer' => 'warning',
                        'qris' => 'primary',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'canceled' => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name'),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                        'canceled' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('user');
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'view' => Pages\ViewSale::route('/{record}'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
