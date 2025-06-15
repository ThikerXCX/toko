<x-filament-panels::page>
    <form wire:submit.prevent class="mb-6">
            {{ $this->form }}
    </form>
<div class="flex justify-between items-center mb-6">
    <div class="mt-4">
                <button
                    type="button"
                    onclick="printLaporan()"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded shadow hover:bg-primary-700 transition"
                >
                    <x-heroicon-o-printer class="w-5 h-5 mr-2" />
                    Cetak Laporan
                </button>
            </div>
</div>
    
    <div class="bg-white rounded-xl shadow border p-6">
        <h2 class="text-lg font-bold mb-4 text-gray-700 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-primary-600" />
            Laporan Transaksi
        </h2>
        <div class="w-full overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-primary-50">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Kode</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Tipe Transaksi</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laporan as $row)
                        <tr class="hover:bg-primary-50 transition">
                            <td class="px-4 py-2 whitespace-nowrap text-gray-800">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-800">{{ $row['tanggal'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-800">{{ $row['kode'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $row['tipe'] == 'Penjualan' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $row['tipe'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right font-semibold text-gray-900">
                                Rp {{ number_format($row['total'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </table>

            <div class="mt-4 p-4 bg-gray-50 rounded-md border text-sm text-gray-700">
                <div class="flex flex-wrap justify-between gap-4">
                    @if ($jenis === 'semua' || $jenis === 'pembelian')
                        <div class="flex-1 min-w-[200px] font-semibold text-blue-700">
                            Total Pembelian: Rp {{ number_format($totalPembelian, 0, ',', '.') }}
                        </div>
                    @endif

                    @if ($jenis === 'semua' || $jenis === 'penjualan')
                        <div class="flex-1 min-w-[200px] font-semibold text-green-700">
                            Total Penjualan: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <script>
    function printLaporan() {
        let printContents = document.querySelector('.bg-white.rounded-xl.shadow.border.p-6').innerHTML;
        let originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
    </script>
</x-filament-panels::page>
