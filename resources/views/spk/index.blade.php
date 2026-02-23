@extends('layout.app')

@section('title', 'SPK')

@section('content')
    <div class="page-wrap">
        <div class="main-content">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <h5>SPK</h5>
                                <span>Surat Perintah Kerja / Dinas</span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-right">
                            <a href="{{ route('spk.create') }}" class="btn btn-primary">Tambah SPK</a>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="form-inline mb-3">
                            <input type="text" name="search" class="form-control mr-2" placeholder="Cari nomor / nama / tujuan"
                                value="{{ $search }}">
                            <button type="submit" class="btn btn-outline-primary">Search</button>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>Pegawai</th>
                                        <th>Tujuan Dinas</th>
                                        <th style="width: 220px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($spks as $item)
                                        <tr>
                                            <td>{{ $loop->iteration + ($spks->currentPage() - 1) * $spks->perPage() }}</td>
                                            <td>{{ $item->nomor }}</td>
                                            <td>{{ optional($item->tanggal)->format('d-m-Y') }}</td>
                                            <td>{{ $item->pegawai_nama }}</td>
                                            <td>{{ $item->tujuan_dinas }}</td>
                                            <td>
                                                <a href="{{ route('spk.show', $item) }}" class="btn btn-sm btn-info"><i
                                                        class="fas fa-eye"></i></a>
                                                <a href="{{ route('spk.edit', $item) }}" class="btn btn-sm btn-warning"><i
                                                        class="fas fa-edit"></i></a>
                                                <a href="{{ route('spk.exportPdf', $item) }}" target="_blank"
                                                    class="btn btn-sm btn-secondary"><i class="fas fa-file-pdf"></i></a>
                                                <form action="{{ route('spk.destroy', $item) }}" method="POST"
                                                    style="display:inline-block"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus SPK ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Data SPK belum tersedia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $spks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
