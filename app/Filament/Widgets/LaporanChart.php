<?php

namespace App\Filament\Widgets;

use App\Models\Pembelian;
use App\Models\Penjualan;
use Filament\Widgets\ChartWidget;

class LaporanChart extends ChartWidget
{
    // protected static ?string $heading = 'Chart';
    protected static ?string $heading = 'Grafik Transaksi Bulanan';
    protected static string $color = 'primary';

    protected function getData(): array
    {
        $bulan = collect(range(1, 12))->map(function ($bulan) {
            return str_pad($bulan, 2, '0', STR_PAD_LEFT);
        });

        $penjualan = $bulan->map(function ($b) {
            return Penjualan::whereMonth('tanggal', $b)->sum('total');
        });

        $pembelian = $bulan->map(function ($b) {
            return Pembelian::whereMonth('tanggal', $b)->sum('total');
        });

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan',
                    'data' => $penjualan->toArray(),
                    'backgroundColor' => '#22c55e', // hijau
                ],
                [
                    'label' => 'Pembelian',
                    'data' => $pembelian->toArray(),
                    'backgroundColor' => '#3b82f6', // biru
                ],
            ],
            'labels' => [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ],
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }
    public function getColumnSpan(): int|string|array
{
    return 'full'; // atau 12 untuk grid 12 kolom
}
}
