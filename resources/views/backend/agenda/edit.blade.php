@extends('backend.layout.main')

@section('title', 'Edit Agenda')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Edit Agenda</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('backend.agenda.index') }}">Agenda</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit mr-2"></i> Form Edit Agenda</h3>
            </div>
            <form action="{{ route('backend.agenda.update', $agenda->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Judul Agenda <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $agenda->title) }}" required>
                                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach(['rapat' => 'Rapat', 'pelayanan' => 'Pelayanan', 'olahraga' => 'Olahraga',
                                              'gotong_royong' => 'Gotong Royong', 'keagamaan' => 'Keagamaan',
                                              'pendidikan' => 'Pendidikan', 'kesehatan' => 'Kesehatan', 'budaya' => 'Budaya'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('category', $agenda->category) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $agenda->description) }}</textarea>
                        @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Kegiatan <span class="text-danger">*</span></label>
                                <input type="date" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
                                       value="{{ old('event_date', $agenda->event_date?->format('Y-m-d')) }}" required>
                                @error('event_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                       value="{{ old('start_time', $agenda->start_time) }}" required>
                                @error('start_time') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                       value="{{ old('end_time', $agenda->end_time) }}" required>
                                @error('end_time') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                       value="{{ old('location', $agenda->location) }}" required>
                                @error('location') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penyelenggara</label>
                                <input type="text" name="organizer" class="form-control"
                                       value="{{ old('organizer', $agenda->organizer) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Narahubung</label>
                                <input type="text" name="contact_person" class="form-control"
                                       value="{{ old('contact_person', $agenda->contact_person) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>No. Telepon</label>
                                <input type="text" name="contact_phone" class="form-control"
                                       value="{{ old('contact_phone', $agenda->contact_phone) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Maks. Peserta</label>
                                <input type="number" name="max_participants" class="form-control" min="1"
                                       value="{{ old('max_participants', $agenda->max_participants) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_public"
                                           name="is_public" value="1" {{ old('is_public', $agenda->is_public) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_public">Agenda Publik</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_completed"
                                           name="is_completed" value="1" {{ old('is_completed', $agenda->is_completed) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_completed">Tandai Selesai</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Perbarui Agenda
                    </button>
                    <a href="{{ route('backend.agenda.index') }}" class="btn btn-default float-right">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
