<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Mengambil informasi stok tersedia untuk setiap produk pada detail penjualan
        if (isset($data['details']) && is_array($data['details'])) {
            foreach ($data['details'] as $key => $detail) {
                if (isset($detail['product_id'])) {
                    $product = Product::find($detail['product_id']);
                    $data['details'][$key]['stock_available'] = $product?->stock;
                }
            }
        }
        
        return $data;
    }
    
    public function mount($record): void
    {
        parent::mount($record);
        
        // Dipicu setelah mount untuk mengatur ulang nilai stock_available
        foreach ($this->data['details'] ?? [] as $itemKey => $item) {
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                $this->data['details'][$itemKey]['stock_available'] = $product?->stock;
            }
        }
    }
}
