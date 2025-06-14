<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenjualan extends EditRecord
{
    protected static string $resource = PenjualanResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Kurangi stok lama sebelum update
        foreach ($this->record->details as $detail) {
            $product = $detail->product;
            $product->stok += $detail->qty; // Balikkan stok (karena akan diganti)
            $product->save();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Kurangi stok sesuai detail baru
        foreach ($this->record->details as $detail) {
            $product = $detail->product;
            $product->stok -= $detail->qty;
            $product->save();
        }
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
