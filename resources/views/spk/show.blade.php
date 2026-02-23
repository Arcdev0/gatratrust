@extends('layout.app')

@section('title', 'Detail SPK')

@section('content')
    <div class="page-wrap">
        <div class="main-content">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-7">
                            <div class="page-header-title">
                                <h5>Detail SPK</h5>
                                <span>{{ $spk->nomor }}</span>
                            </div>
                        </div>
                        <div class="col-lg-5 text-right">
                            <a href="{{ route('spk.exportPdf', $spk) }}" target="_blank" class="btn btn-secondary">Export PDF</a>
                            <a href="{{ route('spk.edit', $spk) }}" class="btn btn-warning">Edit</a>
                            <a href="{{ route('spk.index') }}" class="btn btn-light">Kembali</a>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr><th width="30%">Nomor</th><td>{{ $spk->nomor }}</td></tr>
                            <tr><th>Tanggal</th><td>{{ optional($spk->tanggal)->format('d-m-Y') }}</td></tr>
                            <tr><th>Nama Pegawai</th><td>{{ $spk->pegawai_nama }}</td></tr>
                            <tr><th>Jabatan Pegawai</th><td>{{ $spk->pegawai_jabatan }}</td></tr>
                            <tr><th>Divisi</th><td>{{ $spk->pegawai_divisi ?: '-' }}</td></tr>
                            <tr><th>NIK/ID Pegawai</th><td>{{ $spk->pegawai_nik_id ?: '-' }}</td></tr>
                            <tr><th>Tujuan Dinas</th><td>{{ $spk->tujuan_dinas }}</td></tr>
                            <tr><th>Lokasi Perusahaan Tujuan</th><td>{{ $spk->lokasi_perusahaan_tujuan ?: '-' }}</td></tr>
                            <tr><th>Alamat Lokasi</th><td>{{ $spk->alamat_lokasi ?: '-' }}</td></tr>
                            <tr><th>Maksud/Ruang Lingkup</th><td>{{ $spk->maksud_ruang_lingkup ?: '-' }}</td></tr>
                            <tr><th>Tanggal Berangkat</th><td>{{ optional($spk->tanggal_berangkat)->format('d-m-Y') }}</td></tr>
                            <tr><th>Tanggal Kembali</th><td>{{ optional($spk->tanggal_kembali)->format('d-m-Y') }}</td></tr>
                            <tr><th>Lama Perjalanan</th><td>{{ $spk->lama_perjalanan }} hari</td></tr>
                            <tr><th>Sumber Biaya</th><td>{{ $spk->sumber_biaya ?: '-' }}</td></tr>
                            <tr><th>Moda Transportasi</th><td>{{ ucfirst($spk->moda_transportasi) }}</td></tr>
                            <tr><th>Sumber Biaya Opsi</th><td>{{ ucfirst($spk->sumber_biaya_opsi) }}</td></tr>
                            <tr><th>Ditugaskan Oleh</th><td>{{ $spk->ditugaskan_oleh_nama }} ({{ $spk->ditugaskan_oleh_jabatan }})</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
