@extends('frontend.main')

@section('title', 'Perangkat Desa - ' . strtoupper($villageProfile->village_name ?? 'Desa Krandegan'))
@section('page_title', 'PERANGKAT DESA')
@section('header_icon', 'fas fa-user-friends')
@section('header_bg_color', 'bg-emerald-600')

@section('content')
<div class="xl:col-span-3">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Perangkat Desa Krandegan</h1>
            <p class="text-gray-600 dark:text-gray-400 dark:text-gray-500">Abdi masyarakat yang siap melayani dengan sepenuh hati</p>
        </div>
    </div>

    <!-- Village Leadership -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">Pimpinan Desa</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Kepala Desa --}}
            @php $kades = $groupedOfficials->get('kepala_desa')?->first(); @endphp
            <div class="text-center">
                <div class="w-40 h-40 bg-emerald-100 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden">
                    @if($kades && $kades->photo_path)
                        <img src="{{ asset('storage/' . $kades->photo_path) }}" alt="{{ $kades->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-emerald-500 flex items-center justify-center">
                            <i class="fas fa-user text-6xl text-white"></i>
                        </div>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 text-uppercase">{{ $kades->name ?? 'N/A' }}</h3>
                <p class="text-emerald-600 font-medium mb-2">{{ $kades->position_title ?? 'Kepala Desa' }}</p>
                @if($kades)
                <div class="text-sm text-gray-600 dark:text-gray-400 dark:text-gray-500 space-y-1 mb-4">
                    @if($kades->start_date && $kades->end_date)
                        <p>Periode: {{ $kades->start_date->format('Y') }}-{{ $kades->end_date->format('Y') }}</p>
                    @endif
                    @if($kades->education)
                        <p>Pendidikan: {{ $kades->education }}</p>
                    @endif
                    @if($kades->specialization)
                        <p>Keahlian: {{ $kades->specialization }}</p>
                    @endif
                </div>
                <div class="flex justify-center space-x-4 text-sm">
                    @if($kades->email)
                    <div class="flex items-center text-emerald-600">
                        <i class="fas fa-envelope mr-1"></i>
                        <span>{{ $kades->email }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            
            {{-- Sekretaris Desa --}}
            @php $sekdes = $groupedOfficials->get('sekretaris_desa')?->first(); @endphp
            <div class="text-center">
                <div class="w-40 h-40 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden">
                    @if($sekdes && $sekdes->photo_path)
                        <img src="{{ asset('storage/' . $sekdes->photo_path) }}" alt="{{ $sekdes->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-blue-500 flex items-center justify-center">
                            <i class="fas fa-user text-6xl text-white"></i>
                        </div>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 text-uppercase">{{ $sekdes->name ?? 'N/A' }}</h3>
                <p class="text-blue-600 font-medium mb-2">{{ $sekdes->position_title ?? 'Sekretaris Desa' }}</p>
                @if($sekdes)
                <div class="text-sm text-gray-600 dark:text-gray-400 dark:text-gray-500 space-y-1 mb-4">
                    @if($sekdes->nip)
                        <p>NIP: {{ $sekdes->nip }}</p>
                    @endif
                    @if($sekdes->education)
                        <p>Pendidikan: {{ $sekdes->education }}</p>
                    @endif
                    @if($sekdes->work_period)
                        <p>Masa Kerja: {{ $sekdes->work_period }}</p>
                    @endif
                </div>
                <div class="flex justify-center space-x-4 text-sm">
                    @if($sekdes->email)
                    <div class="flex items-center text-blue-600">
                        <i class="fas fa-envelope mr-1"></i>
                        <span>{{ $sekdes->email }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Kepala Urusan (Kaur) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
            <i class="fas fa-users-cog text-purple-600 mr-2"></i>
            Kepala Urusan (Kaur)
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $kaurs = $groupedOfficials->filter(function($value, $key) {
                    return str_starts_with($key, 'kaur');
                })->flatten();
                $colors = [
                    'green' => ['bg' => 'bg-green-50 dark:bg-green-900/40', 'border' => 'border-green-200', 'text' => 'text-green-600', 'icon-bg' => 'bg-green-500'],
                    'yellow' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/40', 'border' => 'border-yellow-200', 'text' => 'text-yellow-600', 'icon-bg' => 'bg-yellow-500'],
                    'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/40', 'border' => 'border-purple-200', 'text' => 'text-purple-600', 'icon-bg' => 'bg-purple-500']
                ];
                $colorKeys = array_keys($colors);
            @endphp

            @foreach($kaurs as $index => $kaur)
                @php $c = $colors[$colorKeys[$index % 3]]; @endphp
                <div class="{{ $c['bg'] }} border {{ $c['border'] }} rounded-lg p-6">
                    <div class="text-center mb-4">
                        <div class="w-24 h-24 {{ $c['icon-bg'] }} rounded-full mx-auto mb-3 flex items-center justify-center overflow-hidden">
                            @if($kaur->photo_path)
                                <img src="{{ asset('storage/' . $kaur->photo_path) }}" alt="{{ $kaur->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full border-4 border-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-3xl text-white"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 uppercase">{{ $kaur->name }}</h3>
                        <p class="{{ $c['text'] }} text-sm mb-2">{{ $kaur->position_title }}</p>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 dark:text-gray-500 mb-4">
                        @if($kaur->education)<p><span class="font-medium">Pendidikan:</span> {{ $kaur->education }}</p>@endif
                        @if($kaur->work_period)<p><span class="font-medium">Masa Kerja:</span> {{ $kaur->work_period }}</p>@endif
                        @if($kaur->specialization)<p><span class="font-medium">Spesialisasi:</span> {{ $kaur->specialization }}</p>@endif
                    </div>
                    
                    @if($kaur->specialization)
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Bidang Tugas:</h4>
                        <ul class="text-xs text-gray-600 dark:text-gray-400 dark:text-gray-500 space-y-1">
                            @foreach($kaur->specialization_list as $task)
                                <li>• {{ trim($task) }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Kepala Dusun (Kadus) -->
    @if($groupedOfficials->has('kadus'))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
            <i class="fas fa-map-marked-alt text-orange-600 mr-2"></i>
            Kepala Dusun (Kadus)
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $kadusColors = ['orange', 'green', 'blue', 'purple', 'teal'];
            @endphp
            @foreach($groupedOfficials->get('kadus') as $index => $kadus)
                @php $color = $kadusColors[$index % count($kadusColors)]; @endphp
                <div class="bg-{{ $color }}-50 dark:bg-{{ $color }}-900/40 border border-{{ $color }}-200 rounded-lg p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 bg-{{ $color }}-500 rounded-full flex items-center justify-center overflow-hidden">
                            @if($kadus->photo_path)
                                <img src="{{ asset('storage/' . $kadus->photo_path) }}" alt="{{ $kadus->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-user text-white text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 uppercase">{{ $kadus->name }}</h3>
                            <p class="text-{{ $color }}-600 text-sm">{{ $kadus->position_title }}</p>
                            @if($kadus->work_period)<p class="text-xs text-gray-600 dark:text-gray-400 dark:text-gray-500">Masa Jabatan: {{ $kadus->work_period }}</p>@endif
                        </div>
                    </div>
                    @if($kadus->work_area)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 mb-3">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Wilayah Kerja: {{ $kadus->work_area }}</h4>
                        <!-- Territory details could be fetched from a relationship in a real app, 
                             for now we show the work_area string -->
                    </div>
                    @endif
                    <div class="text-xs text-gray-600 dark:text-gray-400 dark:text-gray-500">
                        @if($kadus->phone)<p><strong>Kontak:</strong> {{ $kadus->phone }}</p>@endif
                        @if($kadus->email)<p><strong>Email:</strong> {{ $kadus->email }}</p>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Staff Support -->
    @if($groupedOfficials->has('staff'))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
            <i class="fas fa-hands-helping text-teal-600 mr-2"></i>
            Staf Pendukung
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $staffColors = ['teal', 'cyan', 'lime', 'rose', 'indigo', 'orange'];
            @endphp
            @foreach($groupedOfficials->get('staff') as $index => $staff)
                @php $color = $staffColors[$index % count($staffColors)]; @endphp
                <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-lg p-4 text-center">
                    <div class="w-12 h-12 bg-{{ $color }}-500 rounded-full mx-auto mb-2 flex items-center justify-center overflow-hidden">
                        @if($staff->photo_path)
                            <img src="{{ asset('storage/' . $staff->photo_path) }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user text-white"></i>
                        @endif
                    </div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm uppercase">{{ $staff->name }}</h4>
                    <p class="text-xs text-{{ $color }}-600">{{ $staff->position_title }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection