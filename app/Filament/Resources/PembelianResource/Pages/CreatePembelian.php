<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    protected function afterCreate(): void
{
    foreach ($this->record->details as $detail) {
        $product = $detail->product;
        $product->stok += $detail->qty;
        $product->save();
    }
}

}
