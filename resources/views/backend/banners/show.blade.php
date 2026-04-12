@extends('backend.layout.main')

@section('title', 'Detail Banner')
@section('header', 'Detail Banner')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detail Banner</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('backend.banners.index') }}">Banners</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Informasi Banner</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Judul</th>
                                <td>{{ $banner->title }}</td>
                            </tr>
                            <tr>
                                <th>Subtitle</th>
                                <td>{{ $banner->subtitle ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $banner->description ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Teks Tombol</th>
                                <td>{{ $banner->button_text ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Link Tombol</th>
                                <td>{{ $banner->button_link ? '<a href="'.$banner->button_link.'" target="_blank">'.$banner->button_link.'</a>' : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Urutan Tampil</th>
                                <td>{{ $banner->display_order }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($banner->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ $banner->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4 text-center">
                        <p class="font-weight-bold">Preview Gambar</p>
                        @if($banner->image_path)
                            <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="img-fluid img-thumbnail">
                        @else
                            <div class="p-5 bg-light text-muted border">Tidak ada gambar</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('backend.banners.index') }}" class="btn btn-default">Kembali</a>
                <a href="{{ route('backend.banners.edit', $banner->id) }}" class="btn btn-warning float-right">Edit Banner</a>
            </div>
        </div>
    </div>
</section>
@endsection
