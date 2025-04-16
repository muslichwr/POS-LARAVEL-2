<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchase extends ViewRecord
{
    protected static string $resource = PurchaseResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Utama')
                    ->description('Detail transaksi pembelian')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextEntry::make('supplier.name')
                                            ->label('Nama Supplier')
                                            ->icon('heroicon-o-user-group'),
                                        TextEntry::make('purchase_date')
                                            ->label('Tanggal Pembelian')
                                            ->date('d F Y')
                                            ->icon('heroicon-o-calendar'),
                                        TextEntry::make('status')
                                            ->label('Status Pembelian')
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
                                            ->label('Total Pembayaran')
                                            ->money('IDR')
                                            ->icon('heroicon-o-currency-rupee')
                                            ->size('lg'),
                                        TextEntry::make('created_at')
                                            ->label('Dibuat Pada')
                                            ->dateTime('d/m/Y, H:i')
                                            ->icon('heroicon-o-clock'),
                                    ]),
                            ]),
                    ]),

                Section::make('Daftar Produk')
                    ->description('Barang yang dibeli dalam transaksi ini')
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
                                                    ->circular(),
                                                Grid::make(1)
                                                    ->schema([
                                                        TextEntry::make('product.name')
                                                            ->label('Nama Produk'),
                                                        TextEntry::make('quantity')
                                                            ->label('Jumlah')
                                                            ->suffix(' unit'),
                                                    ]),
                                                Grid::make(1)
                                                    ->schema([
                                                        TextEntry::make('purchase_price')
                                                            ->label('Harga Satuan')
                                                            ->money('IDR'),
                                                        TextEntry::make('subtotal')
                                                            ->label('Subtotal')
                                                            ->money('IDR')
                                                    ]),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan('full'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Kembali
            Actions\Action::make('back')
            ->label('Kembali') // Label tombol
            ->icon('heroicon-o-arrow-left') // Ikon panah kiri
            ->url(fn () => static::$resource::getUrl('index')) // Redirect ke halaman index
            ->color('secondary'), // Warna tombol
            Actions\EditAction::make()
        ];
    }
}