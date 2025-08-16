<?php

namespace App\Filament\Pages;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Traits\FilamentPermissionAwareNavigation;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Collection;

class Laporan extends Page 
{
    use FilamentPermissionAwareNavigation;
    protected static string $requiredPermission = 'page_laporan';
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessMenu(); // Panggil method yang sudah aman
    }
    
    public static function canView(): bool
    {
        $user = auth()->user();

        if (!$user) return false;

        if ($user->hasRole('super_admin')) {
            return true;
        }
        return false;
    }



    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $title = 'Laporan Transaksi';
    protected static string $view = 'filament.pages.laporan';
    protected static ?int $navigationSort = 5;

    public ?string $tanggalAwal = null;
    public ?string $tanggalAkhir = null;
    public ?string $jenis = 'semua';

    public function mount(): void
    {
        $this->tanggalAwal = now()->startOfMonth()->toDateString();
        $this->tanggalAkhir = now()->endOfMonth()->toDateString();
        $this->jenis = 'semua';
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('tanggalAwal')
                ->label('Tanggal Awal')
                ->default(now()->startOfMonth())
                ->reactive(),
            DatePicker::make('tanggalAkhir')
                ->label('Tanggal Akhir')
                ->default(now()->endOfMonth())
                ->reactive(),
            Select::make('jenis')
                ->label('Jenis Transaksi')
                ->options([
                    'semua' => 'Semua',
                    'penjualan' => 'Penjualan',
                    'pembelian' => 'Pembelian',
                ])
                ->default('semua')
                ->reactive(),
        ])->columns(3);
    }

    protected function getLaporanRecords(): Collection
    {
        $awal = $this->tanggalAwal ?? now()->startOfMonth()->toDateString();
        $akhir = $this->tanggalAkhir ?? now()->endOfMonth()->toDateString();

        $laporan = collect();

        if ($this->jenis === 'semua' || $this->jenis === 'penjualan') {
            $penjualan = Penjualan::with('details.product')
                ->whereBetween('tanggal', [$awal, $akhir])
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => $item->tanggal,
                        'kode' => $item->kode,
                        'tipe' => 'Penjualan',
                        'total' => $item->total,
                    ];
                });

            $laporan = $laporan->merge($penjualan);
        }

        if ($this->jenis === 'semua' || $this->jenis === 'pembelian') {
            $pembelian = Pembelian::with('details.product')
                ->whereBetween('tanggal', [$awal, $akhir])
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => $item->tanggal,
                        'kode' => $item->no_faktur,
                        'tipe' => 'Pembelian',
                        'total' => $item->total,
                    ];
                });

            $laporan = $laporan->merge($pembelian);
        }

        return $laporan->sortBy('tanggal')->values();
    }

    public function getViewData(): array
    {
        $laporan = $this->getLaporanRecords();

        $totalPenjualan = $laporan->where('tipe', 'Penjualan')->sum('total');
        $totalPembelian = $laporan->where('tipe', 'Pembelian')->sum('total');

        return [
            'laporan' => $laporan,
            'jenis' => $this->jenis,
            'totalPenjualan' => $totalPenjualan,
            'totalPembelian' => $totalPembelian,
        ];
    }
}