@extends('layout.app')
@section('title', 'Karyawan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Karyawan</h3>
                    <a href="{{ route('karyawan.create') }}" class="btn btn-success">
                        Tambah Karyawan
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <table class="table table-bordered table-striped" id="karyawanTable" style="width:100%;">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No. Karyawan</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DataTables akan load otomatis via AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#karyawanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('karyawan.data') }}',
                columns: [{
                        data: 'no_karyawan',
                        name: 'no_karyawan'
                    }, // No Karyawan
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
