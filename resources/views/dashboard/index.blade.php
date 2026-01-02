@extends('layout.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">

        <select id="filterYear" class="form-control mb-3" style="width:140px">
            <!-- diisi oleh JS -->
        </select>

        <div class="row clearfix">

            <!-- Total Project -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Total Project</h6>
                                <h2 id="w-total-project">-</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-briefcase"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block" id="w-active-projects">- Active Projects</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" style="width:100%"></div>
                    </div>
                </div>
            </div>

            <!-- Total Nilai Project -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Total Nilai Project</h6>
                                <h2 id="w-total-nominal">Rp -</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-file-text"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block" id="w-invoice-status-count">-</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width:100%"></div>
                    </div>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Total Pengeluaran</h6>
                                <h2 id="w-total-expenses">Rp -</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-arrow-down-circle"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block" id="w-expenses-note">-</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" style="width:100%"></div>
                    </div>
                </div>
            </div>

            <!-- Pendapatan Bersih -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Pendapatan Bersih</h6>
                                <h2 id="w-net-income">Rp -</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-dollar-sign"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block" id="w-net-income-note">-</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" style="width:100%"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Revenue Bulanan</h3>
                        <canvas id="chartRevenue"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Status Project</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartProjectStatus"></canvas>
                        <div class="mt-3">
                            <small id="project-stats-summary">-</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header row">
                <div class="col col-sm-6">
                    <h3>Daftar Project Aktif</h3>
                </div>
                {{-- <div class="col col-sm-6 text-right">
                    <a href="#"><i class="ik ik-plus"></i></a>
                </div> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table id="project_table" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No Project</th>
                                    <th>Nama Project</th>
                                    <th>Client</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Total Biaya</th>
                                    <th>Status</th>
                                    {{-- <th>Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection



@section('script')
    {{-- <script>
        $(document).ready(function() {
            $('.clickable-row').on('click', function() {
                window.location = $(this).data('href');
            });
        });
    </script> --}}

    <script>
        $(function() {
            const apiUrl = '/dashboard/data';
            let revenueChart = null;
            let projectChart = null;

            // Ambil years & currentYear dari server-side (Blade)
            const serverYears = @json($availableYears ?? []);
            const serverCurrentYear = {{ $currentYear ?? date('Y') }};

            // helper format Rupiah
            function formatRupiah(n) {
                if (n === null || n === undefined) return 'Rp -';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
            }

            // populateYearSelect sudah ada, gunakan saja
            function populateYearSelect(years, selected) {
                const $sel = $('#filterYear');
                $sel.empty();
                $.each(years, function(i, y) {
                    const $opt = $('<option>').val(y).text(y);
                    if (String(y) === String(selected)) $opt.prop('selected', true);
                    $sel.append($opt);
                });
            }

            function renderSummary(summary) {

                // 1) Total Project
                $('#w-total-project').text(summary.totalProjects ?? 0);
                $('#w-active-projects').text((summary.activeProjects ?? 0) + ' Active Projects');

                // Helper arrow generator
                function makeArrowText(pct) {
                    if (pct === null || pct === undefined || isNaN(pct)) return '-';
                    const sign = pct >= 0 ? '+' : '';
                    const arrow = pct >= 0 ? '▲' : '▼';
                    const color = pct >= 0 ? '#2E7D32' : '#C62828';
                    return `<span style="color:${color}; font-weight:600">${arrow} ${sign}${pct}%</span> dari tahun sebelumnya`;
                }

                // 2) Total Nilai Project
                $('#w-total-nominal').text(formatRupiah(summary.totalProjectValue ?? 0));
                $('#w-invoice-status-count').html(
                    (summary.totalProjectValuePrev > 0) ?
                    makeArrowText(summary.totalProjectValueChangePct) :
                    '-'
                );

                // 3) Total Pengeluaran
                $('#w-total-expenses').text(formatRupiah(summary.totalExpenses ?? 0));
                $('#w-expenses-note').html(
                    (summary.totalExpensesPrev > 0) ?
                    makeArrowText(summary.totalExpensesChangePct) :
                    '-'
                );

                // 4) Pendapatan Bersih
                $('#w-net-income').text(formatRupiah(summary.netIncome ?? 0));
                $('#w-net-income-note').html(
                    (summary.netIncomePrev > 0) ?
                    makeArrowText(summary.netIncomeChangePct) :
                    '-'
                );
            }


            // render bar chart: Total Nilai Project per bulan
            function renderRevenueChart(labels, projectNominalData) {
                const ctx = $('#chartRevenue')[0].getContext('2d');

                // hancurkan chart lama kalau sudah ada
                if (revenueChart) {
                    revenueChart.destroy();
                    revenueChart = null;
                }

                revenueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Nilai Project',
                            data: projectNominalData,
                            backgroundColor: '#4CAF50'
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const val = context.parsed.y ?? context.parsed;
                                        return 'Total Nilai Project: ' + new Intl.NumberFormat('id-ID')
                                            .format(val);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: false
                            },
                            y: {
                                stacked: false,
                                ticks: {
                                    callback: function(v) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        // plugin untuk nulis nilai di atas bar
                        id: 'topLabelPlugin',
                        afterDatasetsDraw: function(chart) {
                            const ctx = chart.ctx;
                            const meta = chart.getDatasetMeta(0); // cuma 1 dataset
                            const data = chart.data.datasets[0].data || [];

                            ctx.save();
                            ctx.font = '12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            ctx.fillStyle = '#111';

                            for (let i = 0; i < data.length; i++) {
                                const el = meta.data[i];
                                if (!el) continue;

                                const value = Number(data[i] || 0);
                                if (!value) continue;

                                const x = el.x;
                                const y = el.y - 6; // sedikit di atas bar
                                const text = formatRupiah(value);

                                ctx.fillText(text, x, y);
                            }

                            ctx.restore();
                        }
                    }]
                });
            }


            // render donut project progress
            function renderProjectChart(finished, inProgress, avgPercent) {
                const ctx = $('#chartProjectStatus')[0].getContext('2d');
                const data = [finished, inProgress];

                if (projectChart) {
                    projectChart.data.datasets[0].data = data;
                    projectChart.options.plugins.title.text = `Project Status (${finished + inProgress} total)`;
                    projectChart.update();
                    $('#project-stats-summary').text(
                        `Finished: ${finished} — In Progress: ${inProgress} — Rata-rata progress: ${avgPercent}%`
                    );
                    return;
                }

                projectChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Finished', 'In Progress'],
                        datasets: [{
                            data: data,
                            backgroundColor: ['#4CAF50', '#F44336']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: `Project Status (${finished + inProgress} total)`
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const label = ctx.label || '';
                                        const value = ctx.raw || 0;
                                        return `${label}: ${value}`;
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                $('#project-stats-summary').text(
                    `Finished: ${finished} — In Progress: ${inProgress} — Rata-rata progress: ${avgPercent}%`);
            }


            // loadDashboard seperti sebelumnya
            function loadDashboard(year) {
                year = year || serverCurrentYear;
                $('#filterYear').prop('disabled', true);

                $.getJSON(apiUrl, {
                        year: year
                    })
                    .done(function(json) {
                        const summary = json.summary || {};
                        if (!('growthPercentage' in summary) && json.growthPercentage) {
                            summary.growthPercentage = json.growthPercentage;
                        }
                        renderSummary(summary);

                        const charts = json.charts || {};
                        const projectNominal = charts.projectNominalByMonth || Array(12).fill(0);
                        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                            'Nov', 'Des'
                        ];

                        renderRevenueChart(labels, projectNominal);

                        const pp = charts.projectProgress || {
                            finished: 0,
                            in_progress: 0,
                            avg_percent: 0
                        };
                        renderProjectChart(pp.finished || 0, pp.in_progress || 0, pp.avg_percent || 0);

                        // Gunakan serverYears sebagai sumber list option awal kecuali API mengembalikan availableYears
                        const apiYears = json.availableYears || serverYears;
                        populateYearSelect(apiYears, json.year || year);
                    })
                    .fail(function(xhr, status, err) {
                        console.error('AJAX error', status, err);
                        alert('Gagal memuat data dashboard. Cek console untuk detail.');
                    })
                    .always(function() {
                        $('#filterYear').prop('disabled', false);
                    });
            }

            // inisialisasi dropdown dari server segera saat DOM siap (sebelum loadDashboard)
            populateYearSelect(serverYears, serverCurrentYear);

            // initial load pakai year terpilih (serverCurrentYear)
            loadDashboard(serverCurrentYear);

            // handler perubahan tahun
            $('#filterYear').on('change', function() {
                const y = $(this).val();
                loadDashboard(y);
            });

            $(function() {
                const table = $('#project_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('dashboard.projects-data') }}',
                        data: function(d) {
                            d.year = $('#filterYear').val() || {{ $currentYear }};
                        }
                    },
                    columns: [{
                            data: 'no_project',
                            name: 'no_project'
                        },
                        {
                            data: 'nama_project',
                            name: 'nama_project'
                        },
                        {
                            data: 'client_name',
                            name: 'users.name'
                        },
                        {
                            data: 'start',
                            name: 'projects.start'
                        },
                        {
                            data: 'end',
                            name: 'projects.end'
                        },
                        {
                            data: 'total_biaya_project',
                            name: 'projects.total_biaya_project',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        }
                        // {
                        //     data: 'action',
                        //     name: 'action',
                        //     orderable: false,
                        //     searchable: false
                        // }
                    ],
                    createdRow: function(row, data, dataIndex) {
                        $(row).addClass('clickable-row').css('cursor', 'pointer');
                        // optional: klik baris navigasi ke link 'action' pertama
                        $(row).off('click').on('click', function(e) {
                            // jangan trigger saat klik tombol action
                            if ($(e.target).closest('a, button').length === 0) {
                                window.location.href = '/projects/show/' + data
                                    .id; // atau gunakan data.action_url jika dikirim
                            }
                        });
                    },
                    drawCallback: function(settings) {
                        // tooltip atau inisialisasi lain kalau perlu
                    }
                });

                // reload table saat select tahun berubah
                $('#filterYear').on('change', function() {
                    table.ajax.reload();
                    // juga panggil loadDashboard untuk charts jika perlu
                    if (typeof loadDashboard === 'function') loadDashboard($(this).val());
                });
            });

        });
    </script>


@endsection
