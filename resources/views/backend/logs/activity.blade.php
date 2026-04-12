@extends('backend.layout.main')

@section('title', 'Log Aktivitas Pengguna')
@section('header', 'Log Aktivitas')
@section('description', 'Rekaman semua aktivitas pengguna di sistem')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Log Aktivitas Pengguna</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Log Aktivitas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Summary Stat Cards --}}
        @php
            use Spatie\Activitylog\Models\Activity;
            $totalAll   = Activity::count();
            $totalLogin = Activity::where('log_name', 'auth')->count();
            $totalData  = Activity::whereIn('event', ['created','updated','deleted'])->count();
            $totalNav   = Activity::where('log_name', 'system')->count();
        @endphp

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-gradient-info">
                    <span class="info-box-icon"><i class="fas fa-list-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Log</span>
                        <span class="info-box-number">{{ number_format($totalAll) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-gradient-success">
                    <span class="info-box-icon"><i class="fas fa-sign-in-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Login</span>
                        <span class="info-box-number">{{ number_format($totalLogin) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-gradient-warning">
                    <span class="info-box-icon"><i class="fas fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Perubahan Data</span>
                        <span class="info-box-number">{{ number_format($totalData) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-gradient-primary">
                    <span class="info-box-icon"><i class="fas fa-mouse-pointer"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Navigasi & Download</span>
                        <span class="info-box-number">{{ number_format($totalNav) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('backend.activity-logs') }}" class="form-inline flex-wrap gap-2">
                    <input type="text" name="search" class="form-control form-control-sm mr-2 mb-1"
                           placeholder="Cari deskripsi..." value="{{ $search ?? '' }}">
                    <input type="text" name="user" class="form-control form-control-sm mr-2 mb-1"
                           placeholder="Nama pengguna..." value="{{ $user_filter ?? '' }}">
                    <select name="log_name" class="form-control form-control-sm mr-2 mb-1">
                        <option value="">Semua Tipe</option>
                        <option value="auth" {{ request('log_name') == 'auth' ? 'selected' : '' }}>Login</option>
                        <option value="system" {{ request('log_name') == 'system' ? 'selected' : '' }}>Navigasi/Download</option>
                        <option value="default" {{ request('log_name') == 'default' ? 'selected' : '' }}>Perubahan Data</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm mr-1 mb-1">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('backend.activity-logs') }}" class="btn btn-secondary btn-sm mb-1">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                    <button type="button" class="btn btn-info btn-sm mb-1 ml-auto" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                </form>
            </div>
        </div>

        {{-- Log Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i> Riwayat Aktivitas</h3>
                <div class="card-tools">
                    <span class="badge badge-secondary">{{ $activities->total() }} total entri</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 50px">#</th>
                                <th>Pengguna</th>
                                <th>Tipe</th>
                                <th>Deskripsi Aktivitas</th>
                                <th>Model Terdampak</th>
                                <th>IP Address</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                @php
                                    $logName = $activity->log_name;
                                    $event   = $activity->event ?? '-';
                                    $props   = $activity->properties ?? collect();
                                    $ip      = data_get($props, 'ip', '-');

                                    // Badge color per event/log
                                    if ($logName === 'auth') {
                                        $badgeClass = 'badge-success'; $icon = 'fa-sign-in-alt';
                                    } elseif ($event === 'created') {
                                        $badgeClass = 'badge-primary'; $icon = 'fa-plus-circle';
                                    } elseif ($event === 'updated') {
                                        $badgeClass = 'badge-warning'; $icon = 'fa-edit';
                                    } elseif ($event === 'deleted') {
                                        $badgeClass = 'badge-danger'; $icon = 'fa-trash';
                                    } else {
                                        $badgeClass = 'badge-info'; $icon = 'fa-mouse-pointer';
                                    }

                                    // Subject label
                                    $subjectType = $activity->subject_type
                                        ? class_basename($activity->subject_type)
                                        : null;
                                @endphp
                                <tr>
                                    <td class="text-center text-muted small">{{ $activity->id }}</td>
                                    <td>
                                        @if($activity->causer)
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2" style="width:30px;height:30px;border-radius:50%;background:#007bff;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">
                                                    {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold small">{{ $activity->causer->name }}</div>
                                                    <div class="text-muted" style="font-size:11px">{{ $activity->causer->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small"><i class="fas fa-robot mr-1"></i>System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            <i class="fas {{ $icon }} mr-1"></i>
                                            {{ ucfirst($event ?: $logName) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="max-width: 360px; word-break: break-word;" class="small">
                                            {{ $activity->description }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($subjectType)
                                            <span class="badge badge-light border">{{ $subjectType }}</span>
                                            @if($activity->subject_id)
                                                <small class="text-muted"> #{{ $activity->subject_id }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-monospace text-muted">{{ $ip }}</small>
                                    </td>
                                    <td>
                                        <small class="d-block">{{ $activity->created_at->format('d M Y H:i') }}</small>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-3x mb-3 d-block"></i>
                                        Belum ada aktivitas terekam.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                <div class="float-left">
                    <small class="text-muted">
                        Menampilkan {{ $activities->firstItem() }}–{{ $activities->lastItem() }}
                        dari {{ $activities->total() }} entri
                    </small>
                </div>
                <div class="float-right">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Auto-refresh every 60 seconds
    setInterval(function () {
        if (!document.hidden) {
            window.location.reload();
        }
    }, 60000);
</script>
@endpush
@endsection