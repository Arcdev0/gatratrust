@extends('layout.app')

@section('title', 'Daftar SPK')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Daftar SPK</h3>
            <a href="{{ route('spk.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah SPK
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="spkTable" class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor SPK</th>
                                <th>Tanggal</th>
                                <th>Nama Pegawai</th>
                                <th>Tujuan Dinas</th>
                                <th style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const table = $('#spkTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('spk.datatable') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nomor',
                        name: 'nomor'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'pegawai_nama',
                        name: 'pegawai_nama'
                    },
                    {
                        data: 'tujuan_dinas',
                        name: 'tujuan_dinas'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    processing: 'Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data per halaman',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(disaring dari _MAX_ total data)',
                    loadingRecords: 'Memuat...',
                    zeroRecords: 'Tidak ada data yang ditemukan',
                    emptyTable: 'Tidak ada data tersedia',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    timer: 2000,
                    showConfirmButton: false,
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json(session('error')),
                });
            @endif

            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                const nomor = $(this).data('nomor');

                Swal.fire({
                    title: 'Hapus SPK?',
                    text: `Data ${nomor} akan dihapus permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/spk') }}/" + id,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error!', response.message || 'Gagal menghapus data.',
                                        'error');
                                }
                            },
                            error: function(xhr) {
                                let message = 'Terjadi kesalahan saat menghapus data.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire('Error!', message, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
