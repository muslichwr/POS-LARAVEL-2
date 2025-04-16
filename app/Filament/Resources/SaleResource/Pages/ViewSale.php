<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Penjualan')
                    ->description('Detail transaksi penjualan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('Kasir')
                                            ->icon('heroicon-o-user')
                                            ->size('lg'),
                                        TextEntry::make('sale_date')
                                            ->label('Tanggal Penjualan')
                                            ->date('d F Y')
                                            ->icon('heroicon-o-calendar'),
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->icon('heroicon-o-information-circle')
                                            ->color(fn (string $state): string => match ($state) {
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'canceled' => 'danger',
                                            }),
                                    ]),
                                Card::make()
                                    ->schema([
                                        TextEntry::make('total_amount')
                                            ->label('Total Penjualan')
                                            ->money('IDR')
                                            ->icon('heroicon-o-currency-rupee')
                                            ->size('lg'),
                                        TextEntry::make('created_at')
                                            ->label('Waktu Transaksi')
                                            ->dateTime('d/m/Y H:i')
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('payment_method')
                                            ->label('Metode Pembayaran')
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'cash' => 'Tunai',
                                                'transfer' => 'Transfer Bank',
                                                'qris' => 'QRIS',
                                            })
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'cash' => 'success',
                                                'transfer' => 'warning',
                                                'qris' => 'primary',
                                            }),
                                    ]),
                            ]),
                    ]),

                Section::make('Daftar Produk Terjual')
                    ->description('Produk yang terjual dalam transaksi ini')
                    ->schema([
                        RepeatableEntry::make('details')
                            ->schema([
                                Card::make()
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                ImageEntry::make('product.image')
                                                    ->label('Gambar')
                                                    ->height(70)
                                                    ->circular()
                                                    ->hidden(fn ($record) => !$record?->product?->image),
                                                Grid::make(1)
                                                    ->schema([
                                                        TextEntry::make('product.name')
                                                            ->label('Nama Produk')
                                                            ->size('lg'),
                                                        TextEntry::make('quantity')
                                                            ->label('Jumlah')
                                                            ->suffix(' unit')
                                                            ->size('lg'),
                                                    ]),
                                                Grid::make(1)
                                                    ->schema([
                                                        TextEntry::make('sale_price')
                                                            ->label('Harga Jual')
                                                            ->money('IDR')
                                                            ->size('lg'),
                                                        TextEntry::make('subtotal')
                                                            ->label('Subtotal')
                                                            ->money('IDR')
                                                            ->size('lg'),
                                                    ]),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan('full'),
                    ]),
            ]);
    }
}
