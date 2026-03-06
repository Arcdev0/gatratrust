@extends('layout.app')

@section('title', 'Daftar Proposal Anggaran Kerja')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Daftar PAK</h3>
            @if ($canManagePak)
                <a href="{{ route('pak.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah PAK
                </a>
            @endif
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table id="pakTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No. PAK</th>
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
    </div>

    <!-- Modal Show PAK -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
        $(document).ready(function() {
            // Format Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            const table = $('#pakTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pak.datatable') }}",
                columns: [{
                        data: 'project_number',
                        name: 'project_number'
                    }, // No. Project
                    {
                        data: 'employees',
                        name: 'employees',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (!data) return '-';
                            // jika data adalah array of objects dengan property nama_lengkap
                            if (Array.isArray(data)) {
                                return data.map(function(e) {
                                    return e.nama_lengkap || e.name || e.nama || '-';
                                }).join(', ');
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'project_name',
                        name: 'project_name'
                    }, // Project Name
                    {
                        data: 'project_value',
                        name: 'project_value',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],

                // jangan override order; server sudah bisa meng-handle. Jika ingin specific sort,
                // tambahkan kolom date tersembunyi di server & client lalu gunakan order [[4,'desc']]
                // saya sarankan biarkan default (server-side) atau ganti sesuai kebutuhan.
                // order: [[3, 'desc']],

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
            $(document).on('click', '.showBtn', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: "{{ url('/pak') }}/" + id,
                    method: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#showModalBody').html(
                            '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
                        );
                        $('#showModal').modal('show');
                    },
                    success: function(response) {
                        if (!response || !response.success) {
                            $('#showModalBody').html(
                                '<div class="alert alert-danger">Data tidak ditemukan</div>'
                            );
                            return;
                        }

                        const pak = response.data.pak || {};
                        const employees = response.data.employees || response.data.karyawans ||
                            [];
                        const categories = response.data.categories ||
                    []; // <-- ambil dari response.show
                        const items = response.data.items || pak.items || [];

                        // fallback getter
                        const g = (obj, keys, def = '') => {
                            for (let k of keys) {
                                if (obj && (obj[k] !== undefined && obj[k] !== null))
                                    return obj[k];
                            }
                            return def;
                        };

                        const projectName = g(pak, ['project_name', 'pak_name', 'name']);
                        const projectNumber = g(pak, ['project_number', 'pak_number',
                            'number'
                        ]);
                        const projectValue = Number(g(pak, ['project_value', 'pak_value',
                            'value'
                        ], 0)) || 0;
                        const location = g(pak, ['location', 'location_project']);
                        const dateVal = g(pak, ['date', 'created_at']);

                        let employeeList = Array.isArray(employees) ? employees.map(emp => emp
                            .nama_lengkap || emp.name || emp.nama || '-').join(', ') : '-';

                        // Build category maps and order
                        const categoryMapById = {};
                        const categoryOrder = [];
                        if (Array.isArray(categories)) {
                            categories.forEach(cat => {
                                const cid = Number(cat.id);
                                categoryMapById[cid] = cat;
                                categoryOrder.push(cid);
                            });
                        }

                        // init itemsByCategory based on categories (keep order)
                        let itemsByCategory = {};
                        categoryOrder.forEach(cid => itemsByCategory[cid] = []);

                        // assign items to categories; fallback to first category if missing
                        items.forEach(item => {
                            const cidRaw = item.category_id ?? item.category ?? null;
                            const cid = (cidRaw !== null && cidRaw !== undefined) ?
                                Number(cidRaw) : (categoryOrder[0] ?? null);
                            const key = (cid && categoryMapById[cid]) ? cid : (
                                categoryOrder[0] ?? null);
                            if (key !== null) {
                                itemsByCategory[key] = itemsByCategory[key] || [];
                                itemsByCategory[key].push(item);
                            }
                        });

                        // build header
                        let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th width="150">Project Name</th><td>${escapeHtml(projectName)}</td></tr>
                            <tr><th>Project Number</th><td>${escapeHtml(projectNumber)}</td></tr>
                            <tr><th>Project Value</th><td>${formatRupiah(projectValue)}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                         <tr>
                            <th width="150">Location</th>
                            <td>${escapeHtml(location === 'dalam_kota' ? 'Batam' : 'Luar Batam')}</td>
                         </tr>
                            <tr><th>Date</th><td>${dateVal ? new Date(dateVal).toLocaleDateString('id-ID') : '-'}</td></tr>
                            <tr><th>Employees</th><td>${escapeHtml(employeeList)}</td></tr>
                        </table>
                    </div>
                </div>
                <h5 class="mb-3">Items Detail</h5>
            `;

                        // render each category/section in defined order
                        categoryOrder.forEach(catId => {
                            const cat = categoryMapById[catId];
                            const rows = itemsByCategory[catId] || [];

                            // skip if empty (optional)
                            if (!rows.length) return;

                            // compute allowed for this category
                            const maxPct = Number(cat.max_percentage) || 0;
                            const allowed = Math.round(projectValue * (maxPct / 100));

                            // prepare table
                            html += `
                    <h6 class="bg-light p-2">${escapeHtml(cat.code)}. ${escapeHtml(cat.name)} <small class="text-muted"> (Max ${maxPct}%)</small></h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:40px">No</th>
                                    <th>Operational Needs</th>
                                    <th>Description</th>
                                    <th style="width:60px">Qty</th>
                                    <th style="width:120px">Unit Cost</th>
                                    <th style="width:120px">Total Cost</th>
                                    <th style="width:120px">Row Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                            // running total to decide which rows push section over allowed
                            let running = 0;
                            rows.forEach((item, idx) => {
                                const name = item.operational_needs ?? item
                                    .name ?? '-';
                                const desc = item.description ?? item.remarks ??
                                    '-';
                                const qty = Number(item.unit_qty ?? item
                                    .quantity ?? 0) || 0;
                                const unitCost = Number(item.unit_cost ?? 0) ||
                                    0;
                                const totalCost = Number(item.total_cost ?? (
                                    qty * unitCost) ?? 0) || 0;

                                running += totalCost;

                                // compute status: if running > allowed then this row is OVER; else OK
                                // also respect explicit item.status if set but computed status takes precedence for visibility
                                const computedStatus = (allowed > 0 && running >
                                    allowed) ? 'OVER' : 'OK';
                                const finalStatus =
                                    computedStatus; // change to item.status ?? computedStatus if you prefer server status
                                const badgeClass = finalStatus === 'OK' ?
                                    'badge-success' : 'badge-danger';

                                // optional: highlight only rows that are OVER
                                const rowStyle = (finalStatus === 'OVER') ?
                                    'style="background:#fff5f5"' : '';

                                html += `
                        <tr ${rowStyle}>
                            <td>${idx + 1}</td>
                            <td>${escapeHtml(name)}</td>
                            <td>${escapeHtml(desc || '-')}</td>
                            <td class="text-center">${escapeHtml(qty)}</td>
                            <td class="text-right">${formatRupiah(unitCost)}</td>
                            <td class="text-right">${formatRupiah(totalCost)}</td>
                            <td class="text-center"><span class="badge ${badgeClass}">${escapeHtml(finalStatus)}</span></td>
                        </tr>
                    `;
                            });

                            // section totals (compute total again)
                            let sectionTotal = rows.reduce((s, it) => s + (Number(it
                                .total_cost ?? ((it.unit_qty ?? it
                                    .quantity ?? 0) * (it.unit_cost ??
                                    0))) || 0), 0);
                            const percentOfProject = projectValue ? (sectionTotal /
                                projectValue * 100) : 0;
                            const sectionStatus = (allowed > 0 && sectionTotal >
                                allowed) ? 'OVER' : 'OK';
                            const sectionBadge = sectionStatus === 'OK' ?
                                'badge-success' : 'badge-danger';

                            html += `
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">TOTAL (${escapeHtml(cat.code)})</td>
                            <td class="text-right">Allowed: ${formatRupiah(allowed)}</td>
                            <td class="text-right">${formatRupiah(sectionTotal)}<br><small>${percentOfProject.toFixed(2)}%</small></td>
                            <td class="text-center"><span class="badge ${sectionBadge}">${sectionStatus}</span></td>
                        </tr>
                    </tbody>
                </table>
                </div>
                `;
                        });

                        // grand total
                        let grandTotal = items.reduce((s, it) => s + (Number(it.total_cost ?? ((
                            it.unit_qty ?? it.quantity ?? 0) * (it
                            .unit_cost ?? 0))) || 0), 0);

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
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan saat memuat data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        $('#showModalBody').html('<div class="alert alert-danger">' + msg +
                            '</div>');
                    }
                });
            });


            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }


            // Delete Button
            @if ($canManagePak)
            $(document).on('click', '.deleteBtn', function() {
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
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message ||
                                            'PAK berhasil dihapus',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Error!', response.message ||
                                        'Gagal menghapus data', 'error');
                                }
                            },
                            error: function(xhr) {
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
            @endif
        });
    </script>
@endsection
