<?php

namespace App\Filament\Resources\PurchaseDetailResource\Pages;

use App\Filament\Resources\PurchaseDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseDetails extends ListRecords
{
    protected static string $resource = PurchaseDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
