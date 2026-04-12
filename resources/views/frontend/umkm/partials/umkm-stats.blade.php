@php
    $categoryColors = [
        'makanan' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/40', 'text' => 'text-yellow-600'],
        'kerajinan' => ['bg' => 'bg-purple-50 dark:bg-purple-900/40', 'text' => 'text-purple-600'],
        'pertanian' => ['bg' => 'bg-green-50 dark:bg-green-900/40', 'text' => 'text-green-600'],
        'jasa' => ['bg' => 'bg-blue-50 dark:bg-blue-900/40', 'text' => 'text-blue-600'],
        'tekstil' => ['bg' => 'bg-pink-50 dark:bg-pink-900/40', 'text' => 'text-pink-600'],
        'lainnya' => ['bg' => 'bg-orange-50 dark:bg-orange-900/40', 'text' => 'text-orange-600']
    ];
@endphp

@foreach($categoryStats as $stat)
    @php
        $colors = $categoryColors[$stat->category] ?? ['bg' => 'bg-gray-50 dark:bg-gray-900', 'text' => 'text-gray-600 dark:text-gray-400 dark:text-gray-500'];
    @endphp
    <div class="text-center p-3 {{ $colors['bg'] }} rounded-lg">
        <div class="text-xl font-bold {{ $colors['text'] }}">{{ $stat->count }}</div>
        <div class="text-sm text-gray-600 dark:text-gray-400 dark:text-gray-500">{{ ucfirst($stat->category) }}</div>
    </div>
@endforeach