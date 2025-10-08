@extends('layout.app')
@section('title', 'Karyawan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Aktifitas Log</h3>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <table class="table table-bordered" id="activityLogsTable" style="width:100%;">
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
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#activityLogsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('activity-logs.data') }}',
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
        });
    </script>
@endsection
