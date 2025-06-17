<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintPenjualanController extends Controller
{
    // public function print(Penjualan $penjualan)
    // {
    //     $pdf = Pdf::loadView('penjualan.print', [
    //         'penjualan' => $penjualan,
    //     ]);

    //     return $pdf->stream('nota-penjualan-' . $penjualan->kode . '.pdf');
    // }
    public function cetak($id)
    {
        $penjualan = Penjualan::with('details.product', 'user')->findOrFail($id);

        $pdf = Pdf::loadView('nota.penjualan', compact('penjualan'));
        
        // Ukuran nota kecil: 80mm x panjang dinamis (pakai ukuran custom)
        $customPaper = [0, 0, 226.77, 1000]; // 80mm x tinggi max 800pt

        $pdf->setPaper($customPaper, 'portrait');

        return $pdf->stream('nota-penjualan-' . $penjualan->kode . '.pdf');
    }
}
