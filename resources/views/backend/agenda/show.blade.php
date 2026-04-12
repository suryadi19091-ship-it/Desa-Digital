@extends('backend.layout.main')

@section('title', 'Detail Agenda')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Detail Agenda</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('backend.agenda.index') }}">Agenda</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        <div class="card card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-calendar-check mr-2"></i> {{ $agenda->title }}
                </h3>
                <span class="badge {{ $agenda->is_completed ? 'badge-success' : 'badge-secondary' }} ml-2">
                    {{ $agenda->is_completed ? 'Selesai' : 'Belum Selesai' }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="35%">Judul</th>
                                <td>{{ $agenda->title }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td><span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $agenda->category)) }}</span></td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $agenda->description }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ $agenda->event_date?->translatedFormat('l, d F Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Waktu</th>
                                <td>{{ $agenda->start_time }} — {{ $agenda->end_time }} WIB</td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>{{ $agenda->location }}</td>
                            </tr>
                            <tr>
                                <th>Penyelenggara</th>
                                <td>{{ $agenda->organizer ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Narahubung</th>
                                <td>{{ $agenda->contact_person ?: '-' }} {{ $agenda->contact_phone ? '('.$agenda->contact_phone.')' : '' }}</td>
                            </tr>
                            <tr>
                                <th>Maks. Peserta</th>
                                <td>{{ $agenda->max_participants ? number_format($agenda->max_participants) . ' orang' : 'Tidak terbatas' }}</td>
                            </tr>
                            <tr>
                                <th>Visibilitas</th>
                                <td>
                                    @if($agenda->is_public)
                                        <span class="badge badge-success"><i class="fas fa-globe mr-1"></i>Publik</span>
                                    @else
                                        <span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i>Privat</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dibuat</th>
                                <td>{{ $agenda->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('backend.agenda.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('backend.agenda.edit', $agenda->id) }}" class="btn btn-warning ml-2">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('backend.agenda.destroy', $agenda->id) }}" method="POST" class="d-inline ml-2"
                      onsubmit="return confirm('Hapus agenda ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
