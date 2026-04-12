@extends('backend.layout.main')

@section('title', 'Laporan Desa')
@section('header', 'Laporan & Ringkasan Data')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('backend.dashboard') }}" class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-1"></i>
                    <span class="ml-1 text-gray-500 md:ml-2 font-medium">Laporan</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Export Section -->
    <div class="bg-white shadow rounded-lg p-6 flex flex-col md:flex-row items-center justify-between border-l-4 border-blue-600">
        <div>
            <h3 class="text-xl font-bold text-gray-900">Ekspor Data Desa</h3>
            <p class="text-gray-500 text-sm mt-1">Unduh ringkasan data kependudukan, anggaran, dan konten dalam format CSV.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('backend.reports.export') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-download mr-2"></i> Ekspor Laporan (.CSV)
            </a>
        </div>
    </div>

    <!-- Summary Tables -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Population Summary -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-users text-blue-600 mr-2"></i> Ringkasan Kependudukan
                </h3>
                <a href="{{ route('backend.population.statistics') }}" class="text-xs text-blue-600 hover:underline">Lihat Detail Statistik</a>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <label class="text-xs font-bold text-blue-600 uppercase">Total Penduduk</label>
                        <p class="text-2xl font-bold">{{ number_format($reports['population']['total'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="text-xs font-bold text-gray-600 uppercase">Laki-laki</label>
                        <p class="text-2xl font-bold">{{ number_format($reports['population']['male'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="text-xs font-bold text-gray-600 uppercase">Perempuan</label>
                        <p class="text-2xl font-bold">{{ number_format($reports['population']['female'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Summary -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-money-bill-wave text-green-600 mr-2"></i> Ringkasan Anggaran (Akumulatif)
                </h3>
                <a href="{{ route('backend.budget.index') }}" class="text-xs text-green-600 hover:underline">Kelola Anggaran</a>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-green-50 rounded-lg">
                        <label class="text-xs font-bold text-green-600 uppercase">Total Rencana Anggaran</label>
                        <p class="text-2xl font-bold">Rp {{ number_format($reports['budget']['planned'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <label class="text-xs font-bold text-blue-600 uppercase">Total Realisasi</label>
                        <p class="text-2xl font-bold">Rp {{ number_format($reports['budget']['realized'], 0, ',', '.') }}</p>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="mt-6">
                    @php
                        $percentage = $reports['budget']['planned'] > 0 ? ($reports['budget']['realized'] / $reports['budget']['planned']) * 100 : 0;
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Persentase Realisasi Global</span>
                        <span class="text-sm font-bold text-blue-600">{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full transition-all duration-1000" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Summary -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-newspaper text-purple-600 mr-2"></i> Ringkasan Konten & Aktivitas
                </h3>
            </div>
            <div class="p-6">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Modul</th>
                            <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Total Data</th>
                            <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">Berita & Artikel</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $reports['content']['news'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('backend.news.index') }}" class="text-blue-600 hover:text-blue-900">Kelola</a></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">Agenda Kegiatan</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $reports['content']['agendas'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('backend.agenda.index') }}" class="text-blue-600 hover:text-blue-900">Kelola</a></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">Pesan Kontak</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $reports['content']['messages'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('backend.contact.index') }}" class="text-blue-600 hover:text-blue-900">Baca</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
