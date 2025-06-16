<?php

namespace App\Filament\Widgets;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\product;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsCount extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
        $tanggalAwal = now()->startOfMonth()->toDateString();
        $tanggalAkhir = now()->endOfMonth()->toDateString();
        return [
            Stat::make('Products kurang dari minimal stock', product::whereColumn('stok', '<', 'stok_minimal')->count())
                ->color('danger')
                ->icon('heroicon-o-cube'),
            Stat::make('Total Penjualan bulan Sekarang', Penjualan::whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->count())
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Total Pembelian bulan Sekarang', Pembelian::whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->count())
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}
