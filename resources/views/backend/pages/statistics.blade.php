@extends('backend.layout.main')

@section('title', 'Statistik Desa')
@section('header', 'Statistik Detail')

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
                    <span class="ml-1 text-gray-500 md:ml-2 font-medium">Statistik</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Population Demographics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gender Distribution -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Distribusi Jenis Kelamin</h3>
            <div class="h-64">
                <canvas id="genderChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                <div class="p-3 bg-blue-50 rounded">
                    <p class="text-xs text-blue-600 font-bold uppercase">Laki-laki</p>
                    <p class="text-xl font-bold text-blue-800">{{ $demographics['gender']['male'] }}</p>
                </div>
                <div class="p-3 bg-pink-50 rounded">
                    <p class="text-xs text-pink-600 font-bold uppercase">Perempuan</p>
                    <p class="text-xl font-bold text-pink-800">{{ $demographics['gender']['female'] }}</p>
                </div>
            </div>
        </div>

        <!-- Age Groups -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Kelompok Usia</h3>
            <div class="h-64">
                <canvas id="ageGroupChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="p-2 border rounded">
                    <p class="text-gray-500">Anak (<15)</p>
                    <p class="font-bold">{{ $demographics['age_groups']['child'] }}</p>
                </div>
                <div class="p-2 border rounded">
                    <p class="text-gray-500">Produktif (15-64)</p>
                    <p class="font-bold">{{ $demographics['age_groups']['productive'] }}</p>
                </div>
                <div class="p-2 border rounded">
                    <p class="text-gray-500">Lansia (>=65)</p>
                    <p class="font-bold">{{ $demographics['age_groups']['elderly'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Statistics -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Statistik Anggaran (Total Seluruh Tahun)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="h-80">
                <canvas id="budgetComparisonChart"></canvas>
            </div>
            <div class="space-y-4">
                <div class="p-4 border-l-4 border-green-500 bg-green-50">
                    <h4 class="text-sm font-bold text-green-800">Total Rencana Pendapatan</h4>
                    <p class="text-2xl font-bold">Rp {{ number_format($budgetStats['total_income'], 0, ',', '.') }}</p>
                    <p class="text-xs text-green-600 mt-1">Realisasi: Rp {{ number_format($budgetStats['realization_income'], 0, ',', '.') }}</p>
                </div>
                <div class="p-4 border-l-4 border-red-500 bg-red-50">
                    <h4 class="text-sm font-bold text-red-800">Total Rencana Belanja</h4>
                    <p class="text-2xl font-bold">Rp {{ number_format($budgetStats['total_expense'], 0, ',', '.') }}</p>
                    <p class="text-xs text-red-600 mt-1">Realisasi: Rp {{ number_format($budgetStats['realization_expense'], 0, ',', '.') }}</p>
                </div>
                <div class="h-40">
                    <canvas id="budgetRealizationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Religion and Occupation -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Religion -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Distribusi Agama</h3>
            <div class="h-64">
                <canvas id="religionChart"></canvas>
            </div>
        </div>

        <!-- Occupation -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Top 5 Pekerjaan</h3>
            <div class="h-64">
                <canvas id="occupationChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Shared options
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        };

        // Gender Chart
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $demographics['gender']['male'] }}, {{ $demographics['gender']['female'] }}],
                    backgroundColor: ['#3B82F6', '#EC4899'],
                    hoverOffset: 4
                }]
            },
            options: chartOptions
        });

        // Age Group Chart
        new Chart(document.getElementById('ageGroupChart'), {
            type: 'bar',
            data: {
                labels: ['Anak-anak', 'Usia Produktif', 'Lansia'],
                datasets: [{
                    label: 'Jumlah Jiwa',
                    data: [
                        {{ $demographics['age_groups']['child'] }}, 
                        {{ $demographics['age_groups']['productive'] }}, 
                        {{ $demographics['age_groups']['elderly'] }}
                    ],
                    backgroundColor: ['#10B981', '#F59E0B', '#6366F1']
                }]
            },
            options: chartOptions
        });

        // Budget Comparison Chart
        new Chart(document.getElementById('budgetComparisonChart'), {
            type: 'bar',
            data: {
                labels: ['Pendapatan', 'Belanja'],
                datasets: [
                    {
                        label: 'Rencana',
                        data: [{{ $budgetStats['total_income'] }}, {{ $budgetStats['total_expense'] }}],
                        backgroundColor: '#93C5FD'
                    },
                    {
                        label: 'Realisasi',
                        data: [{{ $budgetStats['realization_income'] }}, {{ $budgetStats['realization_expense'] }}],
                        backgroundColor: '#3B82F6'
                    }
                ]
            },
            options: chartOptions
        });

        // Religion Chart
        new Chart(document.getElementById('religionChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($demographics['religion']->pluck('religion')) !!},
                datasets: [{
                    data: {!! json_encode($demographics['religion']->pluck('total')) !!},
                    backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']
                }]
            },
            options: chartOptions
        });

        // Occupation Chart
        new Chart(document.getElementById('occupationChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($demographics['occupation']->pluck('occupation')) !!},
                datasets: [{
                    label: 'Jumlah Jiwa',
                    axis: 'y',
                    data: {!! json_encode($demographics['occupation']->pluck('total')) !!},
                    backgroundColor: '#10B981'
                }]
            },
            options: {
                indexAxis: 'y',
                ...chartOptions
            }
        });
    });
</script>
@endpush
