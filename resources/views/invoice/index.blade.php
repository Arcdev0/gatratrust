@extends('layout.app')

@section('title', 'Invoice List')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary font-weight-bold">Invoice</h3>
        <a href="{{ route('invoice.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Invoice
            </a>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="invoiceTable" class="table table-bordered table-striped table-hover w-100 mx-auto">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Deskripsi</th>
                        <th>Down Payment</th>
                        <th>Net Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#invoiceTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("invoice.data") }}',
        columns: [
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'customer', name: 'customer' },
            { data: 'deskripsi', name: 'deskripsi' },
            { 
                data: 'down_payment', 
                name: 'down_payment',
                render: function(data) {
                    return data ? 'Rp ' + parseFloat(data.replace(/\./g, '')).toLocaleString('id-ID') : 'Rp 0';
                }
            },
            { 
                data: 'net_total', 
                name: 'net_total',
                render: function(data) {
                    return data ? 'Rp ' + parseFloat(data.replace(/\./g, '')).toLocaleString('id-ID') : 'Rp 0';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    let badgeClass = data === 'Lunas' ? 'success' : 'warning';
                    return `<span class="badge bg-${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'aksi', 
                name: 'aksi',
                orderable: false, 
                searchable: false 
            }
        ],
        order: [[1, 'desc']]
    });

    // Event delete dummy
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        if (confirm("Yakin ingin menghapus invoice " + id + "?")) {
            $.ajax({
                url: '/invoice/' + id,
                type: 'DELETE',
                success: function(res) {
                    alert(res.message);
                    table.ajax.reload();
                }
            });
        }
    });

    // Event edit dummy
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        alert("Simulasi Edit Invoice: " + id);
    });
});
</script>
@endsection