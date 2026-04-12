@extends('backend.layout.main')

@section('title', 'Manajemen Agenda')
@section('header', 'Manajemen Agenda')
@section('description', 'Kelola agenda dan kegiatan desa')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Agenda</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Agenda</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('backend.partials.search-filter', [
            'searchPlaceholder' => 'Cari judul, lokasi, atau deskripsi...',
            'filters' => [
                [
                    'name' => 'category',
                    'label' => 'Semua Kategori',
                    'options' => [
                        'rapat' => 'Rapat',
                        'pelayanan' => 'Pelayanan',
                        'olahraga' => 'Olahraga',
                        'gotong_royong' => 'Gotong Royong',
                        'keagamaan' => 'Keagamaan',
                        'pendidikan' => 'Pendidikan',
                        'kesehatan' => 'Kesehatan',
                        'budaya' => 'Budaya'
                    ]
                ],
                [
                    'name' => 'is_completed',
                    'label' => 'Status Agenda',
                    'options' => [
                        '0' => 'Belum Selesai',
                        '1' => 'Sudah Selesai'
                    ]
                ],
                [
                    'name' => 'month',
                    'label' => 'Semua Bulan',
                    'options' => array_combine(
                        range(1, 12),
                        ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                         'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                    )
                ]
            ],
            'sortOptions' => [
                'event_date' => 'Tanggal Kegiatan',
                'title' => 'Judul',
                'created_at' => 'Tanggal Dibuat',
                'updated_at' => 'Terakhir Diubah'
            ],
            'showStats' => true,
            'stats' => $stats,
            'actionButtons' => [
                [
                    'label' => 'Export',
                    'url' => '#',
                    'class' => 'outline-secondary',
                    'icon' => 'download',
                    'onclick' => 'exportAgenda()',
                    'permission' => 'export-agenda'
                ],
                [
                    'label' => 'Tambah Agenda',
                    'url' => route('backend.agenda.create'),
                    'class' => 'primary',
                    'icon' => 'plus',
                    'permission' => 'create-agenda'
                ]
            ]
        ])

        <!-- Agenda Table Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Daftar Agenda
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="select-all">
                                    <label for="select-all"></label>
                                </div>
                            </th>
                            <th>Agenda</th>
                            <th style="width: 120px;">Kategori</th>
                            <th style="width: 180px;">Tanggal & Waktu</th>
                            <th style="width: 150px;">Lokasi</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $agenda)
                        <tr>
                            <td>
                                <div class="icheck-primary">
                                    <input type="checkbox" class="row-checkbox" value="{{ $agenda->id }}" id="check{{ $agenda->id }}">
                                    <label for="check{{ $agenda->id }}"></label>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $agenda->title }}</strong>
                                    @if($agenda->description)
                                        <br><small class="text-muted">{{ Str::limit($agenda->description, 60) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $agenda->category)) }}</span>
                            </td>
                            <td>
                                <small>
                                    <strong>{{ $agenda->event_date ? \Carbon\Carbon::parse($agenda->event_date)->format('d M Y') : '-' }}</strong><br>
                                    {{ $agenda->start_time ? \Carbon\Carbon::parse($agenda->start_time)->format('H:i') : '' }} 
                                    @if($agenda->end_time) - {{ \Carbon\Carbon::parse($agenda->end_time)->format('H:i') }} @endif
                                </small>
                            </td>
                            <td>
                                <small>{{ $agenda->location ?: '-' }}</small>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($agenda->is_completed) badge-success
                                    @else badge-secondary
                                    @endif">
                                    {{ $agenda->is_completed ? 'Selesai' : 'Belum Selesai' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @can('view-agenda', $agenda)
                                    <a href="{{ route('backend.agenda.show', $agenda) }}" 
                                       class="btn btn-info btn-xs" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('edit-agenda', $agenda)
                                    <a href="{{ route('backend.agenda.edit', $agenda) }}" 
                                       class="btn btn-warning btn-xs" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('delete-agenda', $agenda)
                                    <button type="button" onclick="deleteAgenda({{ $agenda->id }})" 
                                            class="btn btn-danger btn-xs" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-alt fa-3x mb-3 d-block"></i>
                                    <h5>Belum ada agenda</h5>
                                    <p>Belum ada agenda yang ditambahkan ke sistem</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($agendas, 'hasPages') && $agendas->hasPages())
                <div class="card-footer clearfix">
                    @include('backend.partials.pagination', [
                        'paginator' => $agendas,
                        'perPageOptions' => $paginationInfo['per_page_options'] ?? [10, 15, 25, 50]
                    ])
                </div>
            @endif
        </div>

        <!-- Bulk Actions Panel -->
        <div id="bulk-actions" class="alert alert-info alert-dismissible d-none mt-3">
            <button type="button" class="close" onclick="clearSelection()">
                <span>&times;</span>
            </button>
            <h5><i class="icon fas fa-check-circle"></i> Aksi Massal</h5>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <strong><span id="selected-count">0</span> item dipilih</strong>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group btn-group-sm">
                        <button type="button" onclick="bulkDelete()" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let selectedRows = [];

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                if (this.checked) {
                    if (!selectedRows.includes(cb.value)) {
                        selectedRows.push(cb.value);
                    }
                } else {
                    selectedRows = selectedRows.filter(id => id !== cb.value);
                }
            });
            updateBulkActions();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox')) {
            if (e.target.checked) {
                if (!selectedRows.includes(e.target.value)) {
                    selectedRows.push(e.target.value);
                }
            } else {
                selectedRows = selectedRows.filter(id => id !== e.target.value);
            }
            updateBulkActions();
            updateSelectAll();
        }
    });
});

function updateBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (selectedRows.length > 0) {
        bulkActions?.classList.remove('d-none');
        if (selectedCount) selectedCount.textContent = selectedRows.length;
    } else {
        bulkActions?.classList.add('d-none');
    }
}

function updateSelectAll() {
    const selectAll = document.getElementById('select-all');
    if (!selectAll) return;
    
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    } else if (checkedBoxes.length === checkboxes.length) {
        selectAll.checked = true;
        selectAll.indeterminate = false;
    } else {
        selectAll.checked = false;
        selectAll.indeterminate = true;
    }
}

function clearSelection() {
    selectedRows = [];
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
    updateBulkActions();
}

function deleteAgenda(id) {
    if (confirm('Apakah Anda yakin ingin menghapus agenda ini?')) {
        let form = document.createElement('form');
        form.action = `{{ url('admin/agenda') }}/${id}`;
        form.method = 'POST';
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkDelete() {
    if (selectedRows.length === 0) {
        alert('Pilih agenda yang ingin dihapus');
        return;
    }
    
    if (confirm(`Hapus ${selectedRows.length} agenda yang dipilih? Tindakan ini tidak dapat dibatalkan.`)) {
        // Since there's no bulk delete route yet, we might need to implement it or delete one by one
        // For now, let's assume the user has a route or wants us to implement it.
        // If not, a simple loop with fetch could work, but a single request is better.
        console.log('Bulk delete:', selectedRows);
        // Implementation for bulk delete would typically go here
        alert('Fitur hapus massal akan segera diimplementasikan.');
    }
}

function exportAgenda() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('backend.agenda.export') }}?${params.toString()}`, '_blank');
}
</script>
@endsection