<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Backend\AnnouncementController;
use App\Http\Controllers\Backend\BackupController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\BudgetController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\FileController;
use App\Http\Controllers\Backend\LetterTemplateController;
use App\Http\Controllers\Backend\LocationController;
use App\Http\Controllers\Backend\MaintenanceController;
use App\Http\Controllers\Backend\NewsController as BackendNewsController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\PermissionsTestController;
use App\Http\Controllers\Backend\PopulationController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SettlementController;
use App\Http\Controllers\Backend\TourismController;
use App\Http\Controllers\Backend\UmkmController as BackendUmkmController;
use App\Http\Controllers\Backend\UserController as BackendUserController;
use App\Http\Controllers\Backend\VillageController;
use App\Http\Controllers\Frontend\AgendaController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LetterRequestController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\UmkmController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Login Routes
// Auth Routes with rate limiting
Route::get('/aparat/login', [AuthController::class, 'showLogin'])
    ->name('login');
Route::post('/aparat/login', [AuthController::class, 'authenticate'])
    ->name('login.authenticate')
    ->middleware('throttle:6,1');

// Registration Routes
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');
Route::post('/register', [AuthController::class, 'store'])
    ->name('register.store')
    ->middleware('throttle:3,1');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email')
    ->middleware('throttle:3,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])
    ->name('password.store')
    ->middleware('throttle:3,1');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Profile Routes (Authenticated Users)
Route::middleware(['auth', 'gate:account-active'])->group(function () {
    Route::get('/my-profile', [AuthController::class, 'profile'])->name('user.profile');
    Route::put('/my-profile', [AuthController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/my-profile/password', [AuthController::class, 'changePassword'])->name('user.password.change');
});

/*
|--------------------------------------------------------------------------
| Frontend Routes (Public)
|--------------------------------------------------------------------------
*/

// Home & Main Pages
Route::get('/', [HomeController::class, 'index'])
    ->name('home');
Route::get('/profile-desa', [ProfileController::class, 'index'])
    ->name('profile');
Route::get('/sejarah', [ProfileController::class, 'history'])
    ->name('history');
Route::get('/visi-misi', [ProfileController::class, 'visionMission'])
    ->name('vision-mission');
Route::get('/struktur-pemerintahan', [ProfileController::class, 'government'])
    ->name('government');
Route::get('/perangkat-desa', [ProfileController::class, 'officials'])
    ->name('officials');

// News & Information
Route::get('/berita', [NewsController::class, 'index'])
    ->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])
    ->name('news.show');
Route::get('/berita/kategori/{category}', [NewsController::class, 'category'])
    ->name('news.category');
Route::get('/api/news/latest', [NewsController::class, 'getLatest'])
    ->name('news.latest');

// Announcements
Route::get('/pengumuman', [NewsController::class, 'announcements'])
    ->name('announcements.index');
Route::get('/pengumuman/{id}', [NewsController::class, 'announcementShow'])
    ->name('announcements.show');

// Population & Statistics
Route::get('/data-penduduk', [HomeController::class, 'populationData'])
    ->name('population.data');
Route::get('/statistik-penduduk', [HomeController::class, 'populationStats'])
    ->name('population.stats');

// UMKM
Route::get('/umkm', [UmkmController::class, 'index'])
    ->name('umkm.index');
Route::get('/umkm/{slug}', [UmkmController::class, 'show'])
    ->name('umkm.show');
Route::get('/umkm/kategori/{category}', [UmkmController::class, 'category'])
    ->name('umkm.category');

// Services
Route::get('/layanan-surat', [ServiceController::class, 'letters'])
    ->name('services.letters');
Route::get('/pengajuan-surat', [LetterRequestController::class, 'create'])
    ->name('services.letter-request');
Route::post('/pengajuan-surat', [LetterRequestController::class, 'store'])
    ->name('frontend.letter-request.store')
    ->middleware('throttle:uploads');

// Gallery
Route::get('/galeri', [GalleryController::class, 'index'])
    ->name('gallery.index');
Route::get('/galeri/unggulan', [GalleryController::class, 'featured'])
    ->name('gallery.featured');
Route::get('/galeri/{id}', [GalleryController::class, 'show'])
    ->name('gallery.show');
Route::post('/galeri/{id}/like', [GalleryController::class, 'like'])
    ->name('gallery.like')
    ->middleware('throttle:api');

// Agenda & Events
Route::get('/agenda', [AgendaController::class, 'index'])
    ->name('agenda.index');
Route::get('/agenda/{id}', [AgendaController::class, 'show'])
    ->name('agenda.show');
Route::get('/agenda/kalender/{year}/{month}', [AgendaController::class, 'calendar'])
    ->name('agenda.calendar');

// Tourism
Route::get('/wisata', [ProfileController::class, 'tourism'])
    ->name('tourism.index');
Route::get('/wisata/{slug}', [ProfileController::class, 'tourismShow'])
    ->name('tourism.show');

// Budget & Finance (Public)
Route::get('/apbdes', [HomeController::class, 'budget'])
    ->name('budget.index');
Route::get('/apbdes/anggaran', [HomeController::class, 'budgetPlan'])
    ->name('budget.plan');
Route::get('/apbdes/realisasi', [HomeController::class, 'budgetRealization'])
    ->name('budget.realization');
Route::get('/apbdes/laporan', [HomeController::class, 'budgetReport'])
    ->name('budget.report');

// Contact & Support
Route::get('/kontak', [ProfileController::class, 'contact'])
    ->name('contact');
Route::post('/kontak', [ProfileController::class, 'submitContact'])
    ->name('contact.submit')
    ->middleware('throttle:api');

// Legacy authentication routes (redirect to new auth system)
Route::get('/masuk', function () {
    return redirect()->route('login');
});
Route::get('/daftar', function () {
    return redirect()->route('register');
});
Route::get('/lupa-password', function () {
    return redirect()->route('password.request');
});

// API Routes for AJAX calls
Route::prefix('api')->group(function () {
    Route::get('/statistics', [HomeController::class, 'getStatistics']);
    Route::get('/recent-news', [NewsController::class, 'getRecentNews']);
    Route::get('/recent-gallery', [GalleryController::class, 'getRecentGallery']);
    Route::get('/upcoming-agenda', [AgendaController::class, 'getUpcomingAgenda']);
    Route::get('/agenda/calendar/{year}/{month}', [AgendaController::class, 'getCalendar']);
    Route::get('/agenda/{id}', [AgendaController::class, 'getAgendaDetail']);
    Route::get('/announcements', [NewsController::class, 'getAnnouncements']);
    Route::get('/umkm-search', [UmkmController::class, 'search']);
    Route::get('/umkm-filter', [UmkmController::class, 'filterUmkm']);
    Route::get('/news-search', [NewsController::class, 'search']);
    Route::get('/locations', [HomeController::class, 'getLocations']);
});

/*
|--------------------------------------------------------------------------
| Backend Routes (Admin Panel)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('backend.')
    ->middleware(['auth', 'gate:access-admin-panel'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/system-info', [DashboardController::class, 'getSystemInfo'])->name('dashboard.system-info')->middleware('gate:view-system-info');

        // Permission Management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('gate:manage-permissions');
        Route::post('/permissions/role', [PermissionController::class, 'updateRolePermissions'])->name('permissions.update-role')->middleware('gate:manage-permissions');
        Route::post('/permissions/user', [PermissionController::class, 'updateUserPermissions'])->name('permissions.update-user')->middleware('gate:manage-permissions');
        Route::get('/permissions/test', [PermissionController::class, 'testPermissions'])->name('permissions.test');

        // Legacy Permissions Test Route
        Route::get('/test-permissions', [PermissionsTestController::class, 'testPermissions'])->name('test-permissions');

        // User Management (New Admin System)
        Route::resource('users', AdminUserController::class)->middleware('gate:manage-users');
        Route::patch('users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.update-status');
        Route::post('users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
        Route::get('users-export', [AdminUserController::class, 'export'])->name('users.export');
        Route::get('role-permissions', [AdminUserController::class, 'getRolePermissions'])->name('users.role-permissions');

        // Legacy User Management (Keep for compatibility)
        Route::post('users/{user}/toggle-status', [BackendUserController::class, 'toggleStatus'])->name('users.toggle-status-legacy');
        Route::delete('users/{user}/force-delete', [BackendUserController::class, 'forceDelete'])->name('users.force-delete');

        // Population Data Management
        Route::resource('population', PopulationController::class)->middleware('gate:manage-population-data');
        Route::get('population/statistics', [PopulationController::class, 'statistics'])->name('population.statistics');
        Route::get('population/import/template', [PopulationController::class, 'downloadTemplate'])->name('population.template');
        Route::post('population/import', [PopulationController::class, 'import'])->name('population.import')->middleware('throttle:uploads');
        Route::get('population/export', [PopulationController::class, 'export'])->name('population.export');
        Route::post('population/bulk-delete', [PopulationController::class, 'bulkDelete'])->name('population.bulk-delete');

        // Settlement Management
        Route::resource('settlements', SettlementController::class);
        Route::post('settlements/{settlement}/toggle-status', [SettlementController::class, 'toggleStatus'])->name('settlements.toggle-status');

        // Location Management
        Route::resource('locations', LocationController::class)->middleware('gate:manage-locations');
        Route::post('locations/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('locations.toggle-status');
        Route::get('locations/export', [LocationController::class, 'export'])->name('locations.export');

        // News Management (New Admin System)
        Route::resource('news', AdminNewsController::class)->middleware('gate:manage-content');
        Route::patch('news/{news}/status', [AdminNewsController::class, 'updateStatus'])->name('news.update-status');
        Route::post('news/bulk-action', [AdminNewsController::class, 'bulkAction'])->name('news.bulk-action');
        Route::get('news-export', [AdminNewsController::class, 'export'])->name('news.export');

        // Legacy News Management (Keep for compatibility)
        Route::post('news/{news}/toggle-status', [BackendNewsController::class, 'toggleStatus'])->name('news.toggle-status-legacy');
        Route::post('news/{news}/feature', [BackendNewsController::class, 'toggleFeatured'])->name('news.toggle-featured');

        // Announcements Management
        Route::resource('announcements', AnnouncementController::class);
        Route::post('announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle-status');
        Route::post('announcements/bulk-action', [AnnouncementController::class, 'bulkAction'])->name('announcements.bulk-action');
        Route::get('announcements/active', [AnnouncementController::class, 'getActiveAnnouncements'])->name('announcements.active');

        // UMKM Management
        Route::resource('umkm', BackendUmkmController::class);
        Route::post('umkm/{umkm}/toggle-status', [BackendUmkmController::class, 'toggleStatus'])->name('umkm.toggle-status');
        Route::post('umkm/{umkm}/verify', [BackendUmkmController::class, 'verify'])->name('umkm.verify');

        // Gallery Management
        Route::resource('gallery', App\Http\Controllers\Backend\GalleryController::class);
        Route::post('gallery/{gallery}/toggle-status', [App\Http\Controllers\Backend\GalleryController::class, 'toggleStatus'])->name('gallery.toggle-status');
        Route::post('gallery/bulk-delete', [App\Http\Controllers\Backend\GalleryController::class, 'bulkDelete'])->name('gallery.bulk-delete');

        // Agenda Management
        Route::resource('agenda', App\Http\Controllers\Backend\AgendaController::class);
        Route::post('agenda/{agenda}/toggle-status', [App\Http\Controllers\Backend\AgendaController::class, 'toggleStatus'])->name('agenda.toggle-status');
        Route::get('agenda/export', [App\Http\Controllers\Backend\AgendaController::class, 'export'])->name('agenda.export');

        // Budget Management
        Route::resource('budget', BudgetController::class)->middleware('gate:manage-village-budget');
        Route::get('budget/transactions/{budget}', [BudgetController::class, 'transactions'])->name('budget.transactions');
        Route::post('budget/{budget}/add-transaction', [BudgetController::class, 'addTransaction'])->name('budget.add-transaction');
        Route::delete('budget/transactions/{transaction}', [BudgetController::class, 'deleteTransaction'])->name('budget.delete-transaction');
        Route::get('budget/reports/summary', [BudgetController::class, 'reportSummary'])->name('budget.report-summary');
        Route::get('budget/export/{budget}', [BudgetController::class, 'export'])->name('budget.export');

        // Letter Request Management
        Route::get('letter-requests', [App\Http\Controllers\Backend\ServiceController::class, 'letterRequests'])->name('letter-requests.index');
        Route::get('letter-requests/{letterRequest}', [App\Http\Controllers\Backend\ServiceController::class, 'showLetterRequest'])->name('letter-requests.show');
        Route::post('letter-requests/{letterRequest}/process', [App\Http\Controllers\Backend\ServiceController::class, 'processLetterRequest'])->name('letter-requests.process');
        Route::post('letter-requests/{letterRequest}/complete', [App\Http\Controllers\Backend\ServiceController::class, 'completeLetterRequest'])->name('letter-requests.complete');
        Route::post('letter-requests/{letterRequest}/reject', [App\Http\Controllers\Backend\ServiceController::class, 'rejectLetterRequest'])->name('letter-requests.reject');

        // Letter Template Management
        Route::resource('letter-templates', LetterTemplateController::class);
        Route::post('letter-templates/{letterTemplate}/duplicate', [LetterTemplateController::class, 'duplicate'])->name('letter-templates.duplicate');
        Route::post('letter-templates/{letterTemplate}/toggle-status', [LetterTemplateController::class, 'toggleStatus'])->name('letter-templates.toggle-status');
        Route::get('letter-templates/{letterTemplate}/preview', [LetterTemplateController::class, 'preview'])->name('letter-templates.preview');
        Route::post('letter-templates/{letterTemplate}/preview', [LetterTemplateController::class, 'downloadPDF'])->name('letter-templates.download-pdf');

        // Word Template specific routes
        Route::get('letter-templates/{letterTemplate}/bookmarks', [LetterTemplateController::class, 'getBookmarks'])->name('letter-templates.bookmarks');
        Route::get('letter-templates/{letterTemplate}/preview-word', [LetterTemplateController::class, 'previewWord'])->name('letter-templates.preview-word');
        Route::get('letter-templates/{letterTemplate}/download', [LetterTemplateController::class, 'downloadTemplate'])->name('letter-templates.download');
        Route::post('letter-templates/extract-bookmarks', [LetterTemplateController::class, 'extractBookmarks'])->name('letter-templates.extract-bookmarks');

        // Contact Message Management
        Route::resource('contact', ContactController::class)->only(['index', 'show', 'destroy'])->middleware('gate:manage-contact-messages');
        Route::get('contact/{contact}/reply', [ContactController::class, 'reply'])->name('contact.reply');
        Route::post('contact/{contact}/reply', [ContactController::class, 'storeReply'])->name('contact.store-reply');
        Route::patch('contact/{contact}/status', [ContactController::class, 'updateStatus'])->name('contact.update-status');
        Route::post('contact/bulk-action', [ContactController::class, 'bulkAction'])->name('contact.bulk-action');
        Route::get('contact-stats', [ContactController::class, 'getStats'])->name('contact.stats');

        // Tourism Management
        Route::resource('tourism', TourismController::class);
        Route::post('tourism/{tourism}/toggle-status', [TourismController::class, 'toggleStatus'])->name('tourism.toggle-status');

        // Banner Management
        Route::resource('banners', BannerController::class);
        Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');

        // Village Profile Management
        Route::get('village-profile', [VillageController::class, 'profile'])->name('village.profile');
        Route::put('village-profile', [VillageController::class, 'updateProfile'])->name('village.update-profile');
        Route::get('village-officials', [VillageController::class, 'officials'])->name('village.officials');
        Route::post('village-officials', [VillageController::class, 'storeOfficial'])->name('village.store-official');
        Route::put('village-officials/{official}', [VillageController::class, 'updateOfficial'])->name('village.update-official');
        Route::delete('village-officials/{official}', [VillageController::class, 'deleteOfficial'])->name('village.delete-official');

        // Statistics & Reports
        Route::get('statistics', [DashboardController::class, 'statistics'])->name('statistics')->middleware('gate:generate-reports');
        Route::get('reports', [DashboardController::class, 'reports'])->name('reports')->middleware('gate:generate-reports');
        Route::get('reports/export', [DashboardController::class, 'exportReports'])->name('reports.export')->middleware('gate:export-data');

        // Settings Management (New Admin System)
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('gate:manage-settings');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('gate:manage-settings');
        Route::post('settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email')->middleware('gate:manage-settings');

        // Legacy Settings (Keep for compatibility)
        Route::get('legacy-settings', [SettingController::class, 'index'])->name('legacy-settings.index');
        Route::put('legacy-settings', [SettingController::class, 'update'])->name('legacy-settings.update');

        // File Management
        Route::post('upload-image', [FileController::class, 'uploadImage'])->name('upload.image')->middleware('throttle:uploads');
        Route::post('upload-document', [FileController::class, 'uploadDocument'])->name('upload.document')->middleware('throttle:uploads');
        Route::delete('delete-file/{path}', [FileController::class, 'deleteFile'])->name('delete.file');

        // Backup Management (New Admin System)
        Route::get('backup', [BackupController::class, 'index'])->name('backup.index')->middleware('gate:manage-system-backup');
        Route::post('backup/create', [BackupController::class, 'createBackup'])->name('backup.create')->middleware(['gate:manage-system-backup', 'throttle:uploads']);
        Route::post('backup/restore', [BackupController::class, 'restoreBackup'])->name('backup.restore')->middleware(['gate:manage-system-backup', 'throttle:uploads']);
        Route::post('backup/preview', [BackupController::class, 'getBackupPreview'])->name('backup.preview')->middleware('throttle:api');
        Route::get('backup/statistics', [BackupController::class, 'getStatistics'])->name('backup.statistics');
        Route::get('backup/{filename}/download', [BackupController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('backup/{filename}', [BackupController::class, 'deleteBackup'])->name('backup.delete');

        // Activity Logs
        Route::get('activity-logs', [DashboardController::class, 'activityLogs'])->name('activity-logs')->middleware('gate:view-activity-logs');
        Route::get('logs', [DashboardController::class, 'logs'])->name('logs')->middleware('gate:view-logs');
        Route::post('logs/clear', [DashboardController::class, 'clearLogs'])->name('logs.clear')->middleware('gate:clear-logs');

        // Legacy Backup & Maintenance 
        Route::get('legacy-backup', [MaintenanceController::class, 'backup'])->name('legacy-backup.index');
        Route::post('legacy-backup/create', [MaintenanceController::class, 'createBackup'])->name('legacy-backup.create');
    });
