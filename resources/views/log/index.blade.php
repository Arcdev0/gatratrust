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
                                <th>Referensi</th>
                                <th>Deskripsi</th>
                                <th>Dibuat pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="activityLogModal" tabindex="-1" role="dialog" aria-labelledby="activityLogModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Detail Activity Log</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <p><strong>User:</strong> <span id="modalUser"></span></p>
                            <p><strong>Reference:</strong> <span id="modalReference"></span></p>
                            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                            <p><strong>Tanggal:</strong> <span id="modalCreatedAt"></span></p>
                        </div>

                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#oldDataTab">Old Data</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#newDataTab">New Data</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            <div class="tab-pane fade show active" id="oldDataTab">
                                <div id="modalOldData"></div>
                            </div>
                            <div class="tab-pane fade" id="newDataTab">
                                <div id="modalNewData"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Tutup
                        </button>
                    </div>

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
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        function renderDataAsTable(data) {
            if (!data) return '<p><em>Tidak ada data</em></p>';

            let table = '<table class="table table-bordered table-sm">';
            table += '<thead class="thead-light"><tr><th>Field</th><th>Value</th></tr></thead><tbody>';

            for (const key in data) {
                if (['id', 'created_at', 'updated_at'].includes(key)) continue;

                let value = data[key];

                if (key === 'description') {
                    value =
                    `<div class="p-2 border rounded bg-light" style="max-height:200px;overflow:auto">${value}</div>`;
                }

                table += `<tr><td><strong>${key.replace(/_/g, ' ')}</strong></td><td>${value}</td></tr>`;
            }

            table += '</tbody></table>';
            return table;
        }

        // pakai event delegation karena button dibuat oleh DataTables
        $(document).on('click', '.view-detail', function() {
            let id = $(this).data('id');

            $.ajax({
                url: '/activity-logs/' + id,
                type: 'GET',
                success: function(response) {
                    // $('#modalTitle').text('Detail Log #' + response.id);
                    $('#modalUser').text(response.user);
                    $('#modalReference').text(response.reference);
                    $('#modalDescription').text(response.description);
                    $('#modalCreatedAt').text(response.created_at);

                    $('#modalOldData').html(renderDataAsTable(response.old_data));
                    $('#modalNewData').html(renderDataAsTable(response.new_data));

                    $('#activityLogModal').modal('show');
                },
                error: function(xhr) {
                    alert("Gagal mengambil data log");
                }
            });
        });

        // reload ketika tanggal berubah
        $('#filterRange').on('apply.daterangepicker', function() {
            table.ajax.reload();
        });
    </script>
@endsection
