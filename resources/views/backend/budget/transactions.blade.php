@extends('backend.layout.main')

@section('page_title', 'Transaksi Anggaran')
@section('breadcrumb')
<li class="breadcrumb-item">Admin</li>
<li class="breadcrumb-item"><a href="{{ route('backend.budget.index') }}">Anggaran Desa</a></li>
<li class="breadcrumb-item active">Transaksi</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Budget Info & Summary -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Informasi Anggaran</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nama Item:</strong><br>{{ $budget->sub_category ?? $budget->category }}</p>
                    <p><strong>Tahun:</strong> {{ $budget->fiscal_year }}</p>
                    <p><strong>Tipe:</strong> 
                        <span class="badge badge-{{ $budget->budget_type == 'pendapatan' ? 'success' : 'primary' }}">
                            {{ ucfirst($budget->budget_type) }}
                        </span>
                    </p>
                    <hr>
                    <div class="info-box bg-light shadow-sm">
                        <div class="info-box-content">
                            <span class="info-box-text">Target Anggaran</span>
                            <span class="info-box-number text-primary">Rp {{ number_format($budget->planned_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @php
                        $realized = $transactions->sum('amount');
                        $remaining = $budget->planned_amount - $realized;
                        $percentage = $budget->planned_amount > 0 ? ($realized / $budget->planned_amount) * 100 : 0;
                    @endphp
                    <div class="info-box bg-light shadow-sm">
                        <div class="info-box-content">
                            <span class="info-box-text">Total Realisasi</span>
                            <span class="info-box-number text-success">Rp {{ number_format($realized, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="info-box bg-light shadow-sm">
                        <div class="info-box-content">
                            <span class="info-box-text">Sisa Anggaran</span>
                            <span class="info-box-number {{ $remaining < 0 ? 'text-danger' : 'text-info' }}">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="progress-group">
                        Persentase Realisasi
                        <span class="float-right"><b>{{ number_format($percentage, 1) }}</b>%</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar {{ $percentage > 100 ? 'bg-danger' : 'bg-success' }}" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Transaction Form -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Tambah Transaksi Baru</h3>
                </div>
                <form action="{{ route('backend.budget.add-transaction', $budget) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Tanggal Transaksi</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Tipe Transaksi</label>
                            <select name="transaction_type" class="form-control" required>
                                <option value="expense" {{ $budget->budget_type == 'belanja' ? 'selected' : '' }}>Pengeluaran (-)</option>
                                <option value="income" {{ $budget->budget_type == 'pendapatan' ? 'selected' : '' }}>Pemasukan (+)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jumlah (Rp)</label>
                            <input type="number" name="amount" class="form-control" placeholder="Contoh: 5000000" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi/Keterangan</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat transaksi..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Nomor Referensi (Opsional)</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="Contoh: KW-001/INV/2024">
                        </div>
                        <div class="form-group">
                            <label>Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Detail tambahan jika ada..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info btn-block">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Riwayat Transaksi Anggaran</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="15%">Tanggal</th>
                                    <th>Keterangan</th>
                                    <th width="20%">Jumlah</th>
                                    <th width="15%">Tipe</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $transaction->description }}</strong>
                                        @if($transaction->reference_number)
                                            <br><small class="text-muted">Ref: {{ $transaction->reference_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-weight-bold {{ $transaction->transaction_type == 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->transaction_type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->transaction_type == 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->transaction_type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('backend.budget.delete-transaction', $transaction) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data transaksi untuk item ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .info-box {
        min-height: 60px;
        padding: 0.5rem;
    }
    .info-box-content {
        padding: 0 5px;
    }
    .info-box-text {
        font-size: 0.8rem;
        margin-bottom: 0;
    }
    .info-box-number {
        font-size: 1rem;
    }
    .card-title {
        font-weight: 600;
    }
    /* Dark mode adjustments if any */
    [class*="dark-mode"] .info-box {
        background-color: #343a40 !important;
    }
</style>
@endpush
