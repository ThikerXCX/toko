<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembelian extends EditRecord
{
    protected static string $resource = PembelianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    foreach ($this->record->details as $detail) {
                        $product = $detail->product;
                        $product->stok -= $detail->qty;
                        $product->save();
                    }
                }),
        ];
    }

    protected function beforeSave(): void
    {
        // Kembalikan stok lama sebelum diubah
        foreach ($this->record->details as $detail) {
            $product = $detail->product;
            $product->stok -= $detail->qty; // kurangi stok sebelumnya
            $product->save();
        }
    }

    protected function afterSave(): void
    {
        // Tambahkan stok baru
        foreach ($this->record->details as $detail) {
            $product = $detail->product;
            $product->stok += $detail->qty;
            $product->save();
        }
    }

}
