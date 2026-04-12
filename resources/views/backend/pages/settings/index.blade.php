@extends('backend.layout.main')

@section('title', 'Pengaturan Sistem')
@section('header', 'Pengaturan Sistem')
@section('description', 'Konfigurasi dan pengaturan website desa')

@push('styles')
<style>
    .settings-nav-item {
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    .settings-nav-item.active {
        background-color: #3b82f6 !important;
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5);
    }
    .settings-nav-item.active i, 
    .settings-nav-item.active small {
        color: white !important;
    }
    .settings-nav-item:not(.active):hover {
        background-color: #f3f4f6;
        transform: translateX(4px);
    }
    body.dark-mode .settings-nav-item:not(.active):hover {
        background-color: #374151;
    }
    .settings-card {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .settings-content.hidden {
        display: none;
        opacity: 0;
        transform: translateY(10px);
    }
    .preview-container {
        position: relative;
        overflow: hidden;
        border-radius: 0.5rem;
        border: 2px dashed #d1d5db;
        transition: all 0.2s ease;
    }
    .preview-container:hover {
        border-color: #3b82f6;
    }
    body.dark-mode .preview-container {
        border-color: #4b5563;
    }
    .sticky-save-bar {
        position: sticky;
        top: 20px;
        z-index: 1020;
    }
</style>
@endpush

@section('content')
<div class="container-fluid pb-5">
    <div class="row">
        <!-- Left Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-sliders-h mr-2 text-primary"></i> Menu Pengaturan</h5>
                </div>
                <div class="card-body p-2">
                    <div class="nav flex-column" id="settings-tab">
                        <button type="button" onclick="showTab('general')" class="settings-nav-item active nav-link mb-2 py-3 px-4 rounded-lg d-flex align-items-center" data-tab="general">
                            <i class="fas fa-cog mr-3 fa-fw text-primary"></i>
                            <div>
                                <div class="font-weight-bold">Umum</div>
                                <small class="text-xs text-muted">Informasi dasar website</small>
                            </div>
                        </button>
                        <button type="button" onclick="showTab('appearance')" class="settings-nav-item nav-link mb-2 py-3 px-4 rounded-lg d-flex align-items-center" data-tab="appearance">
                            <i class="fas fa-palette mr-3 fa-fw text-primary"></i>
                            <div>
                                <div class="font-weight-bold text-dark">Tampilan</div>
                                <small class="text-xs text-muted">Tema dan mode website</small>
                            </div>
                        </button>
                        <button type="button" onclick="showTab('email')" class="settings-nav-item nav-link mb-2 py-3 px-4 rounded-lg d-flex align-items-center" data-tab="email">
                            <i class="fas fa-envelope mr-3 fa-fw text-primary"></i>
                            <div>
                                <div class="font-weight-bold text-dark">Email</div>
                                <small class="text-xs text-muted">Konfigurasi SMTP & driver</small>
                            </div>
                        </button>
                        <button type="button" onclick="showTab('security')" class="settings-nav-item nav-link mb-2 py-3 px-4 rounded-lg d-flex align-items-center" data-tab="security">
                            <i class="fas fa-shield-alt mr-3 fa-fw text-primary"></i>
                            <div>
                                <div class="font-weight-bold text-dark">Keamanan</div>
                                <small class="text-xs text-muted">Autentikasi & sesi</small>
                            </div>
                        </button>
                        <button type="button" onclick="showTab('backup')" class="settings-nav-item nav-link mb-2 py-3 px-4 rounded-lg d-flex align-items-center" data-tab="backup">
                            <i class="fas fa-database mr-3 fa-fw text-primary"></i>
                            <div>
                                <div class="font-weight-bold text-dark">Backup</div>
                                <small class="text-xs text-muted">Kelola cadangan data</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Info / Status Card -->
            <div class="card shadow-sm border-0 rounded-lg mt-4 bg-gradient-primary text-white">
                <div class="card-body">
                    <h6 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Status Sistem</h6>
                    <hr class="bg-white opacity-25">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Versi Laravel</span>
                        <span class="badge badge-light">11.x</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>PHP Version</span>
                        <span class="badge badge-light">8.x</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Environment</span>
                        <span class="badge badge-success">Production</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="col-md-9">
            <form id="settings-form" action="{{ route('backend.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- General Settings -->
                <div id="tab-general" class="settings-content settings-card card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary">Pengaturan Umum</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">Nama Website <span class="text-danger">*</span></label>
                                <input type="text" name="settings[site_name]" class="form-control form-control-lg border-gray-300" 
                                       value="{{ old('settings.site_name', $settings['site_name'] ?? '') }}" placeholder="Contoh: Website Resmi Desa Ciuwlan">
                                <small class="text-muted">Nama ini akan muncul di judul browser dan navbar.</small>
                            </div>
                            
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">Deskripsi Website</label>
                                <textarea name="settings[site_description]" rows="3" class="form-control border-gray-300">{{ old('settings.site_description', $settings['site_description'] ?? '') }}</textarea>
                                <small class="text-muted">Gunakan deskripsi yang SEO friendly untuk meningkatkan visibilitas di Google.</small>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold d-block text-dark">Logo Website</label>
                                <div class="preview-container p-3 d-flex align-items-center bg-light">
                                    <img id="logo-preview" class="img-fluid rounded mr-3" style="max-height: 80px;"
                                         src="{{ !empty($settings['site_logo']) ? Storage::url($settings['site_logo']) : 'https://placehold.co/200x80/eef2ff/6366f1?text=LOGO' }}">
                                    <div>
                                        <input type="file" id="site_logo" name="site_logo" accept="image/*" class="d-none">
                                        <label for="site_logo" class="btn btn-sm btn-primary mb-1 shadow-sm">Pilih File</label>
                                        <p class="mb-0 text-xs text-muted">PNG, JPG (Maks. 2MB)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold d-block text-dark">Favicon</label>
                                <div class="preview-container p-3 d-flex align-items-center bg-light">
                                    <img id="favicon-preview" class="img-fluid rounded mr-3 shadow-sm" style="height: 40px; width: 40px;"
                                         src="{{ !empty($settings['site_favicon']) ? Storage::url($settings['site_favicon']) : 'https://placehold.co/40x40/eef2ff/6366f1?text=F' }}">
                                    <div>
                                        <input type="file" id="site_favicon" name="site_favicon" accept="image/*" class="d-none">
                                        <label for="site_favicon" class="btn btn-sm btn-primary mb-1 shadow-sm">Pilih File</label>
                                        <p class="mb-0 text-xs text-muted">Format .ico atau .png</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="font-weight-bold text-dark mb-4"><i class="fas fa-address-book mr-2 text-primary"></i> Informasi Kontak & Alamat</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Email Desa</label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                                    </div>
                                    <input type="email" name="settings[contact_email]" class="form-control border-left-0" value="{{ $settings['contact_email'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Telepon / WhatsApp</label>
                                <div class="input-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-phone text-muted"></i></span>
                                    </div>
                                    <input type="text" name="settings[contact_phone]" class="form-control border-left-0" value="{{ $settings['contact_phone'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="font-weight-bold text-dark">Alamat Kantor Desa</label>
                                <textarea name="settings[contact_address]" rows="2" class="form-control shadow-sm">{{ $settings['contact_address'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div id="tab-appearance" class="settings-content settings-card card shadow-sm border-0 rounded-lg hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary">Tampilan & Tema</h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="row justify-content-center py-4">
                            <div class="col-md-10">
                                <i class="fas fa-paint-roller fa-4x text-light mb-4"></i>
                                <h4 class="font-weight-bold text-dark">Kustomisasi Visual</h4>
                                <p class="text-muted">Pilih skema warna utama untuk dashboard dan website publik.</p>
                                
                                <div class="row mt-5">
                                    <div class="col-md-6 mb-3">
                                        <div class="p-4 rounded-xl border-2 {{ ($settings['theme'] ?? 'default') === 'default' ? 'border-primary bg-primary-light' : 'border-gray-200 bg-white' }} shadow-sm cursor-pointer theme-select" onclick="setTheme('default', this)">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="h-8 w-8 bg-blue-600 rounded-full"></div>
                                                @if(($settings['theme'] ?? 'default') === 'default')
                                                    <i class="fas fa-check-circle text-primary"></i>
                                                @endif
                                            </div>
                                            <div class="font-weight-bold text-dark text-left">Modern Blue (Default)</div>
                                            <div class="text-xs text-muted text-left">Warna biru profesional dan bersih.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="p-4 rounded-xl border-2 {{ ($settings['theme'] ?? '') === 'green' ? 'border-primary bg-primary-light' : 'border-gray-200 bg-white' }} shadow-sm cursor-pointer theme-select" onclick="setTheme('green', this)">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="h-8 w-8 bg-green-600 rounded-full"></div>
                                                @if(($settings['theme'] ?? '') === 'green')
                                                    <i class="fas fa-check-circle text-primary"></i>
                                                @endif
                                            </div>
                                            <div class="font-weight-bold text-dark text-left">Nature Green</div>
                                            <div class="text-xs text-muted text-left">Warna hijau segar bertema alam pedesaan.</div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="settings[theme]" id="theme_input" value="{{ $settings['theme'] ?? 'default' }}">

                                <div class="mt-5 p-4 bg-light rounded-lg text-left border">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="font-weight-bold mb-1 text-dark">Mode Maintenance</h6>
                                            <p class="text-sm text-muted mb-0">Website publik tidak bisa diakses saat aktif.</p>
                                        </div>
                                        <div class="custom-control custom-switch custom-switch-lg">
                                            <input type="checkbox" name="settings[maintenance_mode]" value="1" class="custom-control-input" id="maintenanceMode" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label cursor-pointer" for="maintenanceMode"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Settings -->
                <div id="tab-email" class="settings-content settings-card card shadow-sm border-0 rounded-lg hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary">Konfigurasi Email (SMTP)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 rounded-lg shadow-sm">
                            <i class="fas fa-info-circle mr-2"></i> Pengaturan ini diperlukan agar sistem dapat mengirimkan notifikasi dan laporan secara otomatis.
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Mail Driver</label>
                                <select name="settings[mail_driver]" class="form-control custom-select shadow-sm">
                                    <option value="smtp" {{ ($settings['mail_driver'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP (GMAIL/MAILTRAP)</option>
                                    <option value="mailgun" {{ ($settings['mail_driver'] ?? '') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="log" {{ ($settings['mail_driver'] ?? '') === 'log' ? 'selected' : '' }}>Log (Hanya Testing)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Host SMTP</label>
                                <input type="text" name="settings[mail_host]" class="form-control shadow-sm" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-dark">Port</label>
                                <input type="number" name="settings[mail_port]" class="form-control shadow-sm" value="{{ $settings['mail_port'] ?? '587' }}">
                            </div>
                            <div class="col-md-8 mb-4">
                                <label class="font-weight-bold text-dark">Username / Email</label>
                                <input type="text" name="settings[mail_username]" class="form-control shadow-sm" value="{{ $settings['mail_username'] ?? '' }}">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">Password <small class="text-danger">(Kosongkan jika tidak ingin mengubah password)</small></label>
                                <input type="password" name="settings[mail_password]" class="form-control shadow-sm" placeholder="••••••••••••">
                            </div>
                        </div>

                        <h6 class="font-weight-bold border-top pt-4 mt-2 text-dark"><i class="fas fa-user-edit mr-2 text-muted"></i> Identitas Pengirim</h6>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Email Pengirim</label>
                                <input type="email" name="settings[mail_from_address]" class="form-control shadow-sm" value="{{ $settings['mail_from_address'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Nama Pengirim</label>
                                <input type="text" name="settings[mail_from_name]" class="form-control shadow-sm" value="{{ $settings['mail_from_name'] ?? '' }}">
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded-xl mt-3 border">
                            <h6 class="font-weight-bold mb-3 text-dark"><i class="fas fa-vial mr-2 text-warning"></i> Uji Koneksi Email</h6>
                            <p class="text-xs text-muted mb-3">Kirim email percobaan untuk memastikan konfigurasi SMTP Anda sudah benar.</p>
                            <div class="d-flex">
                                <input type="email" id="test_email" class="form-control mr-3 shadow-sm" placeholder="Email tujuan...">
                                <button type="button" onclick="sendTestEmail()" class="btn btn-warning px-4 shadow-sm font-weight-bold">
                                    <i class="fas fa-paper-plane mr-2"></i> Test
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div id="tab-security" class="settings-content settings-card card shadow-sm border-0 rounded-lg hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary">Keamanan Sesi & Akun</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Durasi Sesi (menit)</label>
                                <input type="number" name="settings[session_lifetime]" class="form-control shadow-sm" value="{{ $settings['session_lifetime'] ?? '120' }}">
                                <small class="text-xs text-muted">Pengguna akan otomatis logout jika tidak aktif selama durasi ini.</small>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Maksimal Gagal Login</label>
                                <input type="number" name="settings[max_login_attempts]" class="form-control shadow-sm" value="{{ $settings['max_login_attempts'] ?? '5' }}">
                                <small class="text-xs text-muted">Berapa kali percobaan login sebelum akun dikunci sementara.</small>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="font-weight-bold mb-4 text-dark"><i class="fas fa-key mr-2 text-muted"></i> Standar Keamanan Kata Sandi</h6>
                        
                        <div class="p-4 bg-light rounded-lg border">
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" name="settings[password_require_uppercase]" class="custom-control-input" id="checkUpper" value="1" {{ ($settings['password_require_uppercase'] ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold cursor-pointer" for="checkUpper">Wajib Huruf Besar (A-Z)</label>
                            </div>
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" name="settings[password_require_numbers]" class="custom-control-input" id="checkNum" value="1" {{ ($settings['password_require_numbers'] ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold cursor-pointer" for="checkNum">Wajib Angka (0-9)</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="settings[password_require_symbols]" class="custom-control-input" id="checkSym" value="1" {{ ($settings['password_require_symbols'] ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold cursor-pointer" for="checkSym">Wajib Simbol (@, #, $, !)</label>
                            </div>
                        </div>

                        <div class="mt-5 p-4 border-left border-warning bg-warning-light rounded-right">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-1">Dua Faktor Autentikasi (2FA)</h6>
                                    <p class="text-xs text-muted mb-0">Wajibkan verifikasi tambahan via Email/App saat login admin.</p>
                                </div>
                                <div class="custom-control custom-switch custom-switch-lg text-warning">
                                    <input type="checkbox" name="settings[enable_2fa]" class="custom-control-input" id="enable2fa" value="1" {{ ($settings['enable_2fa'] ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label cursor-pointer" for="enable2fa"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup Settings -->
                <div id="tab-backup" class="settings-content settings-card card shadow-sm border-0 rounded-lg hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary">Manajemen Pencadangan (Backup)</h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="p-5 bg-white border rounded-xl mb-4 shadow-sm">
                            <div class="mb-4">
                                <span class="bg-primary-light p-4 rounded-circle d-inline-block shadow-sm">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                </span>
                            </div>
                            <h4 class="font-weight-bold text-dark">Cadangkan Data Desa</h4>
                            <p class="text-muted mb-4 px-md-5">Seluruh database dan file yang diunggah dapat dicadangkan secara manual atau otomatis ke penyimpanan aman.</p>
                            
                            <div class="d-flex flex-wrap justify-content-center">
                                <button type="button" onclick="createBackup('database')" class="btn btn-outline-primary m-2 px-4 shadow-sm border-2 font-weight-bold d-flex align-items-center">
                                    <i class="fas fa-database mr-2"></i> Database Only
                                </button>
                                <button type="button" onclick="createBackup('files')" class="btn btn-outline-info m-2 px-4 shadow-sm border-2 font-weight-bold d-flex align-items-center">
                                    <i class="fas fa-folder-open mr-2"></i> Files Only
                                </button>
                                <button type="button" onclick="createBackup('full')" class="btn btn-primary m-2 px-5 py-2 shadow-lg font-weight-bold d-flex align-items-center">
                                    <i class="fas fa-archive mr-2"></i> FULL BACKUP
                                </button>
                            </div>
                        </div>

                        <div class="text-left py-3 px-1">
                            <h6 class="font-weight-bold mb-4 text-dark"><i class="fas fa-clock mr-2 text-muted"></i> Penjadwalan Otomatis</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-bold">Berapa Sering?</label>
                                    <select name="settings[backup_frequency]" class="form-control shadow-sm">
                                        <option value="daily" {{ ($settings['backup_frequency'] ?? '') === 'daily' ? 'selected' : '' }}>Sekali Sehari</option>
                                        <option value="weekly" {{ ($settings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Seminggu Sekali</option>
                                        <option value="monthly" {{ ($settings['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' }}>Sebulan Sekali</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-bold">Ingat Berapa Lama? (Hari)</label>
                                    <input type="number" name="settings[backup_retention]" class="form-control shadow-sm" value="{{ $settings['backup_retention'] ?? '30' }}">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="bg-light p-3 rounded-lg d-flex align-items-center justify-content-between border">
                                        <div class="font-weight-bold text-dark">Aktifkan Backup Otomatis</div>
                                        <div class="custom-control custom-switch custom-switch-lg">
                                            <input type="checkbox" name="settings[auto_backup]" class="custom-control-input" id="checkAuto" value="1" {{ ($settings['auto_backup'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label cursor-pointer" for="checkAuto"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Save Action Bar -->
                <div class="sticky-save-bar mt-4">
                    <div class="card shadow rounded-pill border-0">
                        <div class="card-body py-2 px-4 d-flex justify-content-between align-items-center bg-white rounded-pill">
                            <div class="d-flex align-items-center">
                                <div id="tab-label" class="badge badge-pill badge-primary px-3 py-2 text-uppercase font-weight-bold">Umum</div>
                                <span class="text-muted d-none d-lg-inline ml-3 text-sm">Pastikan semua data sudah benar sebelum menyimpan.</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" onclick="resetSettings()" class="btn btn-link text-danger font-weight-bold mr-3">Reset</button>
                                <button type="submit" id="save-settings" class="btn btn-primary rounded-pill px-5 shadow font-weight-bold d-flex align-items-center">
                                    <i class="fas fa-save mr-2"></i> 
                                    <span id="save-text">Simpan</span>
                                    <div id="save-loading" class="hidden ml-2">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extra Helper Classes -->
<style>
    .rounded-xl { border-radius: 1rem !important; }
    .bg-primary-light { background-color: rgba(59, 130, 246, 0.1) !important; }
    .bg-warning-light { background-color: rgba(245, 158, 11, 0.1) !important; }
    .custom-switch-lg .custom-control-label::before {
        height: 1.5rem;
        width: 2.75rem;
        border-radius: 1rem;
    }
    .custom-switch-lg .custom-control-label::after {
        width: calc(1.5rem - 4px);
        height: calc(1.5rem - 4px);
        background-color: #adb5bd;
        border-radius: 1rem;
    }
    .custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after {
        transform: translateX(1.25rem);
        background-color: #fff;
    }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupImagePreview('site_logo', 'logo-preview');
        setupImagePreview('site_favicon', 'favicon-preview');

        const form = document.getElementById('settings-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings();
        });
    });

    function showTab(tabName) {
        const navItems = document.querySelectorAll('.settings-nav-item');
        const contents = document.querySelectorAll('.settings-content');
        
        // Hide animate
        contents.forEach(content => {
            content.style.opacity = '0';
            content.style.transform = 'translateY(10px)';
            setTimeout(() => content.classList.add('hidden'), 50);
        });

        navItems.forEach(item => {
            item.classList.remove('active');
            item.querySelector('.font-weight-bold').classList.add('text-dark');
        });

        setTimeout(() => {
            const targetContent = document.getElementById(`tab-${tabName}`);
            targetContent.classList.remove('hidden');
            
            requestAnimationFrame(() => {
                targetContent.style.opacity = '1';
                targetContent.style.transform = 'translateY(0)';
            });

            const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
            activeBtn.classList.add('active');
            activeBtn.querySelector('.font-weight-bold').classList.remove('text-dark');
            
            const labelMap = {
                'general': 'Umum',
                'appearance': 'Tampilan',
                'email': 'Email',
                'security': 'Keamanan',
                'backup': 'Backup'
            };
            document.getElementById('tab-label').textContent = labelMap[tabName];
        }, 100);
    }

    function setTheme(theme, element) {
        document.getElementById('theme_input').value = theme;
        document.querySelectorAll('.theme-select').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary-light');
            card.classList.add('border-gray-200', 'bg-white');
            const icon = card.querySelector('.fa-check-circle');
            if(icon) icon.remove();
            
            // Fix text muted for labels
            const label = card.nextElementSibling;
            if(label) label.classList.add('text-muted');
        });

        element.classList.remove('border-gray-200', 'bg-white');
        element.classList.add('border-primary', 'bg-primary-light');
        if(element.nextElementSibling) element.nextElementSibling.classList.remove('text-muted');
        
        const check = document.createElement('i');
        check.className = 'fas fa-check-circle text-primary';
        element.querySelector('.flex').appendChild(check);
    }

    function setupImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input && preview) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    function saveSettings() {
        const saveBtn = document.getElementById('save-settings');
        const saveText = document.getElementById('save-text');
        const saveLoading = document.getElementById('save-loading');

        saveBtn.disabled = true;
        saveText.textContent = 'Menyimpan...';
        saveLoading.classList.remove('hidden');

        const form = document.getElementById('settings-form');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message || 'Pengaturan diperbarui', timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server' });
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveText.textContent = 'Simpan';
            saveLoading.classList.add('hidden');
        });
    }

    function sendTestEmail() {
        const email = document.getElementById('test_email').value;
        if(!email) { Swal.fire('Input Kosong', 'Harap masukkan email tujuan', 'warning'); return; }

        Swal.fire({ title: 'Mengirim...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        fetch(`{{ route('backend.settings.test-email') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if(data.success) Swal.fire('Berhasil', 'Email uji coba terkirim ke ' + email, 'success');
            else Swal.fire('Gagal', data.message, 'error');
        });
    }

    function createBackup(type) {
        Swal.fire({ title: 'Memulai Backup...', text: 'Tunggu sebentar...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        fetch(`{{ route('backend.backup.create') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ type: type })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if(data.success) Swal.fire('Selesai', 'Backup berhasil: ' + data.filename, 'success');
            else Swal.fire('Gagal', data.message, 'error');
        });
    }

    function resetSettings() {
        Swal.fire({ title: 'Reset?', text: 'Hapus input yang belum tersimpan?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Reset', cancelButtonText: 'Batal' })
            .then((result) => { if (result.isConfirmed) document.getElementById('settings-form').reset(); });
    }
</script>
@endpush