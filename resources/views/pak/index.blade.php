@extends('layout.app')

@section('title', 'Daftar Proposal Anggaran Kerja')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Daftar PAK</h3>
            <a href="{{ route('pak.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah PAK
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pakTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No. Project</th>
                                <th>Employee</th>
                                <th>Project Name</th>
                                <th>Nilai Project</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Show PAK -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showModalLabel">Detail PAK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="showModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Format Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            // Initialize DataTable
            const table = $('#pakTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pak.datatable') }}",
                columns: [
                    { data: 'project_number', name: 'project_number' },   // No. Project
                    {
                        data: 'employees', name: 'employees', render: function (data) {
                            return data.map(e => e.nama_lengkap).join(', ');
                        }
                    },
                    { data: 'project_name', name: 'project_name' },       // Project Name
                    {
                        data: 'project_value', name: 'project_value', render: function (data) {
                            return formatRupiah(data);
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],

                order: [[3, 'desc']], // Sort by date descending
                language: {
                    processing: "Memuat data...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    loadingRecords: "Memuat...",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Show Button
            $(document).on('click', '.showBtn', function () {
                const id = $(this).data('id');

                $.ajax({
                    url: "{{ url('/pak') }}/" + id,
                    method: 'GET',
                    dataType: 'json',
                    beforeSend: function () {
                        $('#showModalBody').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                        $('#showModal').modal('show');
                    },
                    success: function (response) {
                        if (response.success) {
                            const pak = response.data.pak;
                            const employees = response.data.employees;

                            let employeeList = employees.map(emp => emp.nama_lengkap).join(', ');

                            let itemsBySection = {
                                'honorarium': [],
                                'operational': [],
                                'consumable': []
                            };

                            pak.items.forEach(item => {
                                if (!itemsBySection[item.category]) {
                                    itemsBySection[item.category] = [];
                                }
                                itemsBySection[item.category].push(item);
                            });

                            let html = `
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="150">Project Name</th>
                                                <td>${pak.project_name}</td>
                                            </tr>
                                            <tr>
                                                <th>Project Number</th>
                                                <td>${pak.project_number}</td>
                                            </tr>
                                            <tr>
                                                <th>Project Value</th>
                                                <td>${formatRupiah(pak.project_value)}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="150">Location</th>
                                                <td>${pak.location_project}</td>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <td>${new Date(pak.date).toLocaleDateString('id-ID')}</td>
                                            </tr>
                                            <tr>
                                                <th>Employees</th>
                                                <td>${employeeList}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <h5 class="mb-3">Items Detail</h5>
                            `;

                            // Section A - Honorarium
                            if (itemsBySection.honorarium.length > 0) {
                                html += `
                                    <h6 class="bg-light p-2">A. Honorarium</h6>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Operational Needs</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Unit Cost</th>
                                                    <th>Total Cost</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                                let totalA = 0;
                                itemsBySection.honorarium.forEach((item, index) => {
                                    totalA += parseFloat(item.total_cost);
                                    html += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${item.operational_needs}</td>
                                            <td>${item.description || '-'}</td>
                                            <td>${item.unit_qty}</td>
                                            <td>${formatRupiah(item.unit_cost)}</td>
                                            <td>${formatRupiah(item.total_cost)}</td>
                                            <td><span class="badge badge-${item.status === 'OK' ? 'success' : 'danger'}">${item.status || 'OK'}</span></td>
                                        </tr>
                                    `;
                                });

                                html += `
                                                <tr class="font-weight-bold">
                                                    <td colspan="5" class="text-right">TOTAL I (MAX 70%)</td>
                                                    <td>${formatRupiah(totalA)}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                            }

                            // Section B - Operational
                            if (itemsBySection.operational.length > 0) {
                                html += `
                                    <h6 class="bg-light p-2">B. Operational</h6>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Operational Needs</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Unit Cost</th>
                                                    <th>Total Cost</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                                let totalB = 0;
                                itemsBySection.operational.forEach((item, index) => {
                                    totalB += parseFloat(item.total_cost);
                                    html += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${item.operational_needs}</td>
                                            <td>${item.description || '-'}</td>
                                            <td>${item.unit_qty}</td>
                                            <td>${formatRupiah(item.unit_cost)}</td>
                                            <td>${formatRupiah(item.total_cost)}</td>
                                            <td><span class="badge badge-${item.status === 'OK' ? 'success' : 'danger'}">${item.status || 'OK'}</span></td>
                                        </tr>
                                    `;
                                });

                                html += `
                                                <tr class="font-weight-bold">
                                                    <td colspan="5" class="text-right">TOTAL II (MAX 10%)</td>
                                                    <td>${formatRupiah(totalB)}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                            }

                            // Section C - Consumable
                            if (itemsBySection.consumable.length > 0) {
                                html += `
                                    <h6 class="bg-light p-2">C. Consumable</h6>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Operational Needs</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Unit Cost</th>
                                                    <th>Total Cost</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                                let totalC = 0;
                                itemsBySection.consumable.forEach((item, index) => {
                                    totalC += parseFloat(item.total_cost);
                                    html += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${item.operational_needs}</td>
                                            <td>${item.description || '-'}</td>
                                            <td>${item.unit_qty}</td>
                                            <td>${formatRupiah(item.unit_cost)}</td>
                                            <td>${formatRupiah(item.total_cost)}</td>
                                            <td><span class="badge badge-${item.status === 'OK' ? 'success' : 'danger'}">${item.status || 'OK'}</span></td>
                                        </tr>
                                    `;
                                });

                                html += `
                                                <tr class="font-weight-bold">
                                                    <td colspan="5" class="text-right">TOTAL III (MAX 5%)</td>
                                                    <td>${formatRupiah(totalC)}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                            }

                            // Grand Total
                            let grandTotal = 0;
                            pak.items.forEach(item => {
                                grandTotal += parseFloat(item.total_cost);
                            });

                            html += `
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-success">
                                            <h5 class="mb-0">GRAND TOTAL: ${formatRupiah(grandTotal)}</h5>
                                        </div>
                                    </div>
                                </div>
                            `;

                            $('#showModalBody').html(html);
                        } else {
                            $('#showModalBody').html('<div class="alert alert-danger">Data tidak ditemukan</div>');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan saat memuat data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        $('#showModalBody').html('<div class="alert alert-danger">' + msg + '</div>');
                    }
                });
            });

            // Delete Button
            $(document).on('click', '.deleteBtn', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus PAK?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/pak') }}/" + id,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message || 'PAK berhasil dihapus',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Error!', response.message || 'Gagal menghapus data', 'error');
                                }
                            },
                            error: function (xhr) {
                                let msg = 'Terjadi kesalahan saat menghapus data';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error!', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection