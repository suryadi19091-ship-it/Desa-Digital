@extends('backend.layout.main')

@section('title', 'Manajemen Banner')
@section('header', 'Manajemen Banner')
@section('description', 'Kelola banner yang tampil di halaman depan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Banner</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Banners</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas fa-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-images mr-2"></i> Daftar Banner</h3>
                <div class="card-tools">
                    <a href="{{ route('backend.banners.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Banner
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Image</th>
                            <th>Title & Subtitle</th>
                            <th>Display Order</th>
                            <th>Status</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $index => $banner)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($banner->image_path)
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="img-thumbnail" style="max-height: 80px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $banner->title }}</strong>
                                    @if($banner->subtitle)
                                        <br><small class="text-muted">{{ $banner->subtitle }}</small>
                                    @endif
                                </td>
                                <td>{{ $banner->display_order }}</td>
                                <td>
                                    @if($banner->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('backend.banners.edit', $banner->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('backend.banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus banner ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada banner yang ditambahkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
