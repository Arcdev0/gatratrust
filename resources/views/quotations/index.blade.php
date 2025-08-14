@extends('layout.app')

@section('title', 'Quotation List')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Quotation List</h4>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">+ Create Quotation</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table class="table" id="quotationTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Quotation No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            let table = $('#quotationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('quotations.getDataTable') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'quo_no',
                        name: 'quo_no'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, 'Rp ')
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Delete quotation
            $(document).on('click', '.deleteBtn', function() {
                if (confirm('Are you sure you want to delete this quotation?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: '/quotations/delete/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    </script>
@endsection
