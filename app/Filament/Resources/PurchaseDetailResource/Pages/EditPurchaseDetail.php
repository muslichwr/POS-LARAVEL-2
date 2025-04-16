<?php

namespace App\Filament\Resources\PurchaseDetailResource\Pages;

use App\Filament\Resources\PurchaseDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseDetail extends EditRecord
{
    protected static string $resource = PurchaseDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
