@extends('layout.app')
@section('title', 'Aktivitas Log')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h3 class="text-primary font-weight-bold">
                    Aktivitas Log
                </h3>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterRange" class="font-weight-bold">Range Tanggal</label>
                            <input type="text" id="filterRange" class="form-control" />
                        </div>
                    </div>
                    <table class="table table-bordered" id="activityLogsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Rerferensi</th>
                                <th>Deskripsi</th>
                                <th>Dibuat pada</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $('#filterRange').daterangepicker({
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' s/d '
            }
        });
        var table = $('#activityLogsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('activity-logs.data') }}',
                data: function(d) {
                    d.filterRange = $('#filterRange').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'reference',
                    name: 'reference'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ]
        });

        // reload ketika tanggal berubah
        $('#filterRange').on('apply.daterangepicker', function() {
            table.ajax.reload();
        });
    </script>
@endsection
