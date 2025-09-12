@extends('layout.app')

@section('title', 'Data Kwitansi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary font-weight-bold">Data Kwitansi</h3>
        <a href="{{ route('kwitansi.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Kwitansi
                </a>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="container">
                <table id="kwitansiTable" class="table table-bordered table-striped w-100">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Jumlah Dibayarkan</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let table = $('#kwitansiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('kwitansi.data') }}",
        columns: [
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'payment_date', name: 'payment_date' },
            { 
                data: 'amount_paid', 
                name: 'amount_paid',
                render: function(data) {
                    return 'Rp. ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { data: 'note', name: 'note' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false 
            },
        ]
    });

    // Delete data dengan SweetAlert
    $(document).on('click', '.deleteKwitansi', function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data kwitansi ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Server error'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection