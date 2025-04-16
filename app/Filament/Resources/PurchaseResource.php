<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
// use Filament\Forms;
use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
// use Filament\Resources\Form;
// use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = "Data Utama";

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
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(Supplier::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive(),
    
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->default(now())
                            ->required(),
    
                        Repeater::make('details')
                            ->label('Detail Pembelian')
                            ->relationship('details')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produk')
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => 
                                        $state ? $set('purchase_price', Product::find($state)?->price) : null),
    
                                Forms\Components\TextInput::make('purchase_price')
                                    ->label('Harga Beli')
                                    ->prefix('Rp')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
    
                                    TextInput::make('quantity')
                                    ->reactive()
                                    ->afterStateUpdated(function ($set, $state, $get) {
                                        $price = $get('purchase_price');
                                        $subtotal = $price ? $state * $price : 0;
                                        $set('subtotal', $subtotal);
                                    }), 
    
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated()
                                    ->afterStateHydrated(function ($record, callable $set, $get) {
                                        if ($record && $record->quantity && $record->purchase_price) {
                                            $set('subtotal', $record->quantity * $record->purchase_price);
                                        }
                                    })
                                    ->reactive()
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->required()
                            ->createItemButtonLabel('Tambah Produk')
                            ->collapsible()
                            ->cloneable()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $totalAmount = collect($state ?? [])->sum(function ($item) {
                                    return ($item['quantity'] ?? 0) * ($item['purchase_price'] ?? 0);
                                });
                                $set('total_amount', $totalAmount);
                            }),
    
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Pembelian')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated()
                            ->afterStateHydrated(function ($record, callable $set) {
                                if ($record && $record->details) {
                                    $set('total_amount', $record->details->sum(function ($detail) {
                                        return $detail->quantity * $detail->purchase_price;
                                    }));
                                }
                            }),
    
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Selesai',
                                'canceled' => 'Dibatalkan',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
        ]);
        
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('supplier.name')
                ->label('Supplier')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('purchase_date')
                ->label('Tanggal')
                ->date('d/m/Y')
                ->sortable(),

            Tables\Columns\TextColumn::make('total_amount')
                ->label('Total')
                ->money('IDR')
                ->sortable(),

            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'completed',
                    'danger' => 'canceled',
                ])
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal Input')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier')
                    ->relationship('supplier', 'name'),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                        'canceled' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('supplier');
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'view' => Pages\ViewPurchase::route('/{record}'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
