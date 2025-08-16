<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Cek produk duplikat
    $productIds = collect($data['details'])->pluck('product_id');
    if ($productIds->duplicates()->isNotEmpty()) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'details' => 'Terdapat produk yang dipilih lebih dari satu kali.',
        ]);
    }

    // Kode otomatis
    $tanggal = \Carbon\Carbon::parse($data['tanggal'])->format('dmy'); // contoh: 140625
    $random = str_pad(random_int(0, 999), 5, '0', STR_PAD_LEFT);       // contoh: 034
    $data['kode'] = 'CT-' . $tanggal . '-' . $random;

    // Tambahkan ID user yang login
    $data['user_id'] = Auth::id();

    return $data;
}


    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $tanggal = \Carbon\Carbon::parse($data['tanggal'])->format('dmy'); // contoh: 140625
    //     $random = str_pad(random_int(0, 999), 5, '0', STR_PAD_LEFT);       // contoh: 034
    //     $data['kode'] = 'CT-' . $tanggal . '-' . $random;

    //     $data['user_id'] = Auth::id(); // Ambil ID user yang login
    //     return $data;
    // }

    protected function afterCreate(): void
    {
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
