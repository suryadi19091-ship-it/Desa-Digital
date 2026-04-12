<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\News;
use App\Models\ContactMessage;
use App\Models\PopulationData;
use App\Models\Agenda;
use App\Models\VillageBudget;
use App\Models\Location;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display the admin dashboard
     */
    public function index(Request $request)
    {
        // Check if user can access admin dashboard
        if (!Gate::allows('access-admin-panel')) {
            abort(403, 'Unauthorized access to admin panel');
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get chart data for monthly statistics
        $chartData = $this->getChartData();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // If it's an AJAX request for stats refresh
        if ($request->ajax() && $request->get('ajax') === 'stats') {
            return response()->json($stats);
        }

        return view('backend.pages.dashboard', compact('stats', 'chartData', 'recentActivities'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $stats = [];
        
        try {
            // Users statistics
            if (Gate::allows('manage-users')) {
                $stats['total_users'] = User::count();
                $stats['new_users_this_month'] = User::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count();
            }

            // Population statistics
            if (Gate::allows('manage-population-data')) {
                $populationData = PopulationData::latest()->first();
                $stats['total_population'] = $populationData ? 
                    ($populationData->male_count + $populationData->female_count) : 0;
            }

            // Content statistics
            if (Gate::allows('manage-content')) {
                $stats['total_news'] = News::count();
                $stats['news_this_month'] = News::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count();
            }

            // Contact messages statistics
            if (Gate::allows('manage-contact-messages')) {
                $stats['total_messages'] = ContactMessage::count();
                $stats['unread_messages'] = ContactMessage::where('is_read', false)->count();
            }

            // Agenda statistics
            if (Gate::allows('manage-village-data')) {
                $stats['total_agendas'] = Agenda::count();
                $stats['upcoming_agendas'] = Agenda::where('date', '>=', Carbon::now())->count();
            }

            // Budget statistics
            if (Gate::allows('manage-village-budget')) {
                $currentYear = Carbon::now()->year;
                $stats['total_budgets'] = VillageBudget::where('fiscal_year', $currentYear)->count();
                $stats['budget_amount'] = VillageBudget::where('fiscal_year', $currentYear)->sum('planned_amount') ?? 0;
            }

            // Location statistics
            if (Gate::allows('manage-locations')) {
                $stats['total_locations'] = Location::count();
                $stats['locations_with_coordinates'] = Location::whereNotNull('latitude')->whereNotNull('longitude')->count();
            }

            // System statistics
            $stats['storage_used'] = $this->getStorageUsed();
            $stats['storage_total'] = '1 GB'; // Configurable
            $stats['storage_percentage'] = $this->getStoragePercentage();
            $stats['last_backup'] = $this->getLastBackupDate();

        } catch (\Exception $e) {
            \Log::error('Dashboard stats error: ' . $e->getMessage());
            // Return default stats if there's an error
            $stats = [
                'total_users' => 0,
                'new_users_this_month' => 0,
                'total_population' => 0,
                'total_news' => 0,
                'news_this_month' => 0,
                'total_messages' => 0,
                'unread_messages' => 0,
                'total_agendas' => 0,
                'upcoming_agendas' => 0,
                'total_budgets' => 0,
                'budget_amount' => 0,
                'total_locations' => 0,
                'locations_with_coordinates' => 0,
                'storage_used' => '0 MB',
                'storage_total' => '1 GB',
                'storage_percentage' => 0,
                'last_backup' => 'Never'
            ];
        }

        return $stats;
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        $chartData = [
            'months' => [],
            'users' => [],
            'news' => [],
            'messages' => [],
            'agendas' => []
        ];

        try {
            // Get last 6 months data
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthName = $date->locale('id')->format('M');
                
                $chartData['months'][] = $monthName;
                
                // Users data
                if (Gate::allows('manage-users')) {
                    $chartData['users'][] = User::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
                } else {
                    $chartData['users'][] = 0;
                }

                // News data
                if (Gate::allows('manage-content')) {
                    $chartData['news'][] = News::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
                } else {
                    $chartData['news'][] = 0;
                }

                // Messages data
                if (Gate::allows('manage-contact-messages')) {
                    $chartData['messages'][] = ContactMessage::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
                } else {
                    $chartData['messages'][] = 0;
                }

                // Agenda data
                if (Gate::allows('manage-village-data')) {
                    $chartData['agendas'][] = Agenda::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
                } else {
                    $chartData['agendas'][] = 0;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Chart data error: ' . $e->getMessage());
            // Return default chart data
            $chartData = [
                'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                'users' => [0, 0, 0, 0, 0, 0],
                'news' => [0, 0, 0, 0, 0, 0],
                'messages' => [0, 0, 0, 0, 0, 0],
                'agendas' => [0, 0, 0, 0, 0, 0]
            ];
        }

        return $chartData;
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities()
    {
        $activities = [];

        try {
            // Recent user registrations
            if (Gate::allows('manage-users')) {
                $recentUsers = User::latest()->take(3)->get();
                foreach ($recentUsers as $user) {
                    $activities[] = [
                        'title' => 'Pengguna baru mendaftar',
                        'description' => $user->name,
                        'time' => $user->created_at->diffForHumans(),
                        'icon' => 'fas fa-user-plus',
                        'color' => 'blue'
                    ];
                }
            }

            // Recent news
            if (Gate::allows('manage-content')) {
                $recentNews = News::latest()->take(2)->get();
                foreach ($recentNews as $news) {
                    $activities[] = [
                        'title' => 'Berita baru dipublikasi',
                        'description' => \Str::limit($news->title, 50),
                        'time' => $news->created_at->diffForHumans(),
                        'icon' => 'fas fa-newspaper',
                        'color' => 'green'
                    ];
                }
            }

            // Recent messages
            if (Gate::allows('manage-contact-messages')) {
                $recentMessages = ContactMessage::latest()->take(2)->get();
                foreach ($recentMessages as $message) {
                    $activities[] = [
                        'title' => 'Pesan baru diterima',
                        'description' => 'Dari: ' . $message->name,
                        'time' => $message->created_at->diffForHumans(),
                        'icon' => 'fas fa-envelope',
                        'color' => 'orange'
                    ];
                }
            }

            // Recent agendas
            if (Gate::allows('manage-village-data')) {
                $recentAgendas = Agenda::latest()->take(2)->get();
                foreach ($recentAgendas as $agenda) {
                    $activities[] = [
                        'title' => 'Agenda baru ditambahkan',
                        'description' => \Str::limit($agenda->title, 40),
                        'time' => $agenda->created_at->diffForHumans(),
                        'icon' => 'fas fa-calendar-plus',
                        'color' => 'purple'
                    ];
                }
            }

            // Recent budget entries
            if (Gate::allows('manage-village-budget')) {
                $recentBudgets = VillageBudget::latest()->take(1)->get();
                foreach ($recentBudgets as $budget) {
                    $activities[] = [
                        'title' => 'Anggaran baru diperbarui',
                        'description' => $budget->item_name . ' - Rp ' . number_format($budget->planned_amount, 0, ',', '.'),
                        'time' => $budget->created_at->diffForHumans(),
                        'icon' => 'fas fa-coins',
                        'color' => 'emerald'
                    ];
                }
            }

            // Sort by time (most recent first)
            usort($activities, function($a, $b) {
                // This is a simplified sort - in production you'd want to use actual timestamps
                return strcmp($b['time'], $a['time']);
            });

            // Limit to 5 most recent
            $activities = array_slice($activities, 0, 5);

        } catch (\Exception $e) {
            \Log::error('Recent activities error: ' . $e->getMessage());
            $activities = [];
        }

        return $activities;
    }

    /**
     * Get storage usage information
     */
    private function getStorageUsed()
    {
        try {
            $bytes = 0;
            $publicPath = public_path();
            $storagePath = storage_path();
            
            // Calculate public directory size
            $bytes += $this->getDirectorySize($publicPath);
            
            // Calculate storage directory size
            $bytes += $this->getDirectorySize($storagePath);
            
            return $this->formatBytes($bytes);
        } catch (\Exception $e) {
            \Log::error('Storage calculation error: ' . $e->getMessage());
            return '0 MB';
        }
    }

    /**
     * Get storage usage percentage
     */
    private function getStoragePercentage()
    {
        try {
            // This is a simplified calculation
            // In production, you'd want to get actual disk usage
            $usedMB = 50; // Example value
            $totalMB = 1024; // 1GB in MB
            
            return round(($usedMB / $totalMB) * 100, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get last backup date
     */
    private function getLastBackupDate()
    {
        try {
            // Check for backup files in storage
            $backupPath = storage_path('app/backups');
            
            if (!is_dir($backupPath)) {
                return 'Never';
            }

            $files = glob($backupPath . '/*');
            if (empty($files)) {
                return 'Never';
            }

            $latestFile = max($files);
            $fileTime = filemtime($latestFile);
            
            return Carbon::createFromTimestamp($fileTime)->diffForHumans();
        } catch (\Exception $e) {
            return 'Never';
        }
    }

    /**
     * Calculate directory size recursively
     */
    private function getDirectorySize($directory)
    {
        $size = 0;
        
        try {
            if (is_dir($directory)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directory)
                );
                
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                    }
                }
            }
        } catch (\Exception $e) {
            // Handle permission errors or other issues
            return 0;
        }
        
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get system information for dashboard
     */
    public function getSystemInfo()
    {
        if (!Gate::allows('view-system-info')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => $this->getDatabaseVersion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ];

        return response()->json($info);
    }

    /**
     * Get database version
     */
    private function getDatabaseVersion()
    {
        try {
            $result = DB::select('SELECT VERSION() as version');
            return $result[0]->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Show logs page
     */
    public function logs(Request $request)
    {
        if (!Gate::allows('view-logs')) {
            abort(403, 'You do not have permission to view logs.');
        }

        $logs = $this->getLogEntries($request);
        
        return view('backend.logs.index', [
            'logs' => $logs,
            'levels' => ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'],
            'currentLevel' => $request->get('level', ''),
            'currentDate' => $request->get('date', ''),
            'search' => $request->get('search', ''),
        ]);
    }

    /**
     * Show activity logs page
     */
    public function activityLogs(Request $request)
    {
        if (!Gate::allows('view-activity-logs')) {
            abort(403, 'You do not have permission to view activity logs.');
        }

        $query = \Spatie\Activitylog\Models\Activity::with('causer')->latest();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user')) {
            $user = $request->get('user');
            $query->whereHasMorph('causer', [\App\Models\User::class], function($q) use ($user) {
                $q->where('name', 'like', "%{$user}%");
            });
        }

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->get('log_name'));
        }

        $activities = $query->paginate(20)->withQueryString();

        return view('backend.logs.activity', [
            'activities'    => $activities,
            'search'        => $request->get('search', ''),
            'user_filter'   => $request->get('user', ''),
            'action_filter' => $request->get('action', ''),
        ]);
    }

    /**
     * Get log entries from Laravel log files
     */
    private function getLogEntries(Request $request)
    {
        $logs = collect();
        
        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (!file_exists($logPath)) {
                return $logs;
            }

            $content = file_get_contents($logPath);
            $lines = explode("\n", $content);
            
            // Parse last 100 log entries (simplified parsing)
            $entries = array_slice(array_reverse($lines), 0, 100);
            
            foreach ($entries as $line) {
                if (empty(trim($line))) continue;
                
                // Simple regex to parse Laravel log format
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.+)/', $line, $matches)) {
                    $entry = [
                        'datetime' => $matches[1],
                        'level' => $matches[2],
                        'message' => $matches[3],
                        'context' => '',
                    ];
                    
                    // Apply filters
                    if ($request->get('level') && $request->get('level') !== $entry['level']) {
                        continue;
                    }
                    
                    if ($request->get('search') && stripos($entry['message'], $request->get('search')) === false) {
                        continue;
                    }
                    
                    if ($request->get('date') && !str_contains($entry['datetime'], $request->get('date'))) {
                        continue;
                    }
                    
                    $logs->push($entry);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Error reading log file: ' . $e->getMessage());
        }
        
        return $logs->take(50); // Limit to 50 entries
    }

    /**
     * Clear log files
     */
    public function clearLogs(Request $request)
    {
        if (!Gate::allows('clear-logs')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (file_exists($logPath)) {
                // Clear the log file content
                file_put_contents($logPath, '');
            }

            return response()->json(['success' => true, 'message' => 'Logs cleared successfully!']);
        } catch (\Exception $e) {
            \Log::error('Error clearing logs: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to clear logs.']);
        }
    }

    /**
     * Show statistics page
     */
    public function statistics()
    {
        if (!Gate::allows('generate-reports')) {
            abort(403, 'Unauthorized access to statistics');
        }

        $demographics = [
            'gender' => [
                'male' => PopulationData::where('gender', 'L')->count(),
                'female' => PopulationData::where('gender', 'P')->count(),
            ],
            'age_groups' => [
                'child' => PopulationData::where('age', '<', 15)->count(),
                'productive' => PopulationData::whereBetween('age', [15, 64])->count(),
                'elderly' => PopulationData::where('age', '>=', 65)->count(),
            ],
            'religion' => PopulationData::select('religion', DB::raw('count(*) as total'))
                ->groupBy('religion')
                ->get(),
            'occupation' => PopulationData::select('occupation', DB::raw('count(*) as total'))
                ->groupBy('occupation')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get(),
        ];

        $budgetStats = [
            'total_income' => VillageBudget::where('budget_type', 'pendapatan')->sum('planned_amount'),
            'total_expense' => VillageBudget::where('budget_type', 'belanja')->sum('planned_amount'),
            'realization_income' => VillageBudget::where('budget_type', 'pendapatan')->sum('realized_amount'),
            'realization_expense' => VillageBudget::where('budget_type', 'belanja')->sum('realized_amount'),
        ];

        return view('backend.pages.statistics', compact('demographics', 'budgetStats'));
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        if (!Gate::allows('generate-reports')) {
            abort(403, 'Unauthorized access to reports');
        }

        $reports = [
            'population' => [
                'total' => PopulationData::count(),
                'male' => PopulationData::where('gender', 'L')->count(),
                'female' => PopulationData::where('gender', 'P')->count(),
            ],
            'content' => [
                'news' => News::count(),
                'agendas' => Agenda::count(),
                'messages' => ContactMessage::count(),
            ],
            'budget' => [
                'planned' => VillageBudget::sum('planned_amount'),
                'realized' => VillageBudget::sum('realized_amount'),
            ]
        ];

        return view('backend.pages.reports', compact('reports'));
    }

    /**
     * Export reports to CSV
     */
    public function exportReports()
    {
        if (!Gate::allows('export-data')) {
            abort(403, 'Unauthorized to export data');
        }

        $filename = "report-desa-" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Kategori', 'Item', 'Nilai', 'Keterangan'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Population data
            fputcsv($file, ['Penduduk', 'Total Penduduk', PopulationData::count(), 'Jiwa']);
            fputcsv($file, ['Penduduk', 'Laki-laki', PopulationData::where('gender', 'L')->count(), 'Jiwa']);
            fputcsv($file, ['Penduduk', 'Perempuan', PopulationData::where('gender', 'P')->count(), 'Jiwa']);

            // Content data
            fputcsv($file, ['Konten', 'Berita', News::count(), 'Artikel']);
            fputcsv($file, ['Konten', 'Agenda', Agenda::count(), 'Kegiatan']);

            // Budget data
            fputcsv($file, ['Anggaran', 'Total Rencana', VillageBudget::sum('planned_amount'), 'Rupiah']);
            fputcsv($file, ['Anggaran', 'Total Realisasi', VillageBudget::sum('realized_amount'), 'Rupiah']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}