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
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
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
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Total Pendapatan</h6>
                                <h2 class="responsive-title" id="w-total-payments">Rp -</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-dollar-sign"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block" id="w-growth">-</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Outstanding Payment -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Pembayaran Belum Selesai</h6>
                                <h2 id="w-outstanding">Rp -</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-alert-circle"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">Harus ditagih</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projects as $p)
                                    <tr class="clickable-row" data-href="{{ route('projects.show', $p->id) }}"
                                        style="cursor: pointer;">
                                        <td>{{ $p->no_project }}</td>
                                        <td>{{ $p->nama_project }}</td>
                                        <td>{{ $p->client_name }}</td>
                                        <td>{{ $p->start }}</td>
                                        <td>{{ $p->end }}</td>
                                        <td>Rp {{ number_format($p->total_biaya_project, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge badge-success">Aktif</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection



@section('script')
    <script>
        $(document).ready(function() {
            $('.clickable-row').on('click', function() {
                window.location = $(this).data('href');
            });
        });
    </script>

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

            // render summary widgets
            function renderSummary(summary) {
                $('#w-total-project').text(summary.totalProjects ?? 0);
                // kalau API tidak mengirim activeProjects, tampilkan '-' (kamu bisa tambahkan di controller)
                $('#w-active-projects').text((summary.activeProjects ?? '-') + ' Active Projects');
                $('#w-total-nominal').text(formatRupiah(summary.totalNominalProject ?? 0));
                if (summary.invoiceStatus) {
                    $('#w-invoice-status-count').text(
                        `${summary.invoiceStatus.open ?? 0} Open, ${summary.invoiceStatus.close ?? 0} Closed`);
                } else {
                    $('#w-invoice-status-count').text('-');
                }
                $('#w-total-payments').text(formatRupiah(summary.totalPayments ?? 0));
                $('#w-outstanding').text(formatRupiah(summary.outstanding ?? 0));

                const gp = summary.growthPercentage;
                if (gp === null || gp === undefined) {
                    $('#w-growth').text('-');
                } else {
                    $('#w-growth').text((gp >= 0 ? '+' : '') + gp + '% dibanding bulan lalu');
                }
            }

            // render stacked bar (paid - green, unpaid - red)
            function renderRevenueChart(labels, paidData, unpaidData, projectNominalLabels) {
                const ctx = $('#chartRevenue')[0].getContext('2d');

                // custom plugin: draw label di atas tiap bar (top of stack)
                const topLabelPlugin = {
                    id: 'topLabelPlugin',
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        const datasets = chart.data.datasets;
                        const meta0 = chart.getDatasetMeta(0); // dataset 0 = Paid
                        const meta1 = chart.getDatasetMeta(1); // dataset 1 = Unpaid

                        // loop tiap bar index
                        for (let i = 0; i < chart.data.labels.length; i++) {
                            // cari posisi paling atas (y terkecil) dari kedua stack untuk index i
                            let ys = [];
                            [meta0, meta1].forEach(meta => {
                                if (meta && meta.data[i]) {
                                    const el = meta.data[i];
                                    // bar rectangle top y = el.y (Chart v3+ menggunakan el.y)
                                    // tapi untuk bar stacked, el.y adalah top/bottom bergantung dataset — aman ambil el.getProps if needed
                                    // gunakan getProps jika tersedia, fallback ke el.y
                                    let topY = (el && el.y !== undefined) ? el.y : (el._model ? el
                                        ._model.y : null);
                                    if (topY !== null && topY !== undefined) ys.push(topY);
                                }
                            });

                            if (ys.length === 0) continue;
                            const top = Math.min.apply(null, ys);

                            // posisi x: ambil dari salah satu elemen (meta0 atau meta1)
                            let x = null;
                            if (meta0 && meta0.data[i]) x = meta0.data[i].x;
                            else if (meta1 && meta1.data[i]) x = meta1.data[i].x;
                            if (x === null) continue;

                            // ambil nominal project untuk bulan i
                            const val = (projectNominalLabels && projectNominalLabels[i]) ?
                                projectNominalLabels[i] : 0;

                            // draw text
                            ctx.save();
                            ctx.font = '12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            ctx.fillStyle = '#111'; // warna teks
                            const text = formatRupiah(val);
                            // letakkan 6px di atas top bar
                            ctx.fillText(text, x, top - 6);
                            ctx.restore();
                        }
                    }
                };

                const datasets = [{
                        label: 'Paid',
                        data: paidData,
                        backgroundColor: '#4CAF50',
                        stack: 'stack1'
                    },
                    {
                        label: 'Unpaid',
                        data: unpaidData,
                        backgroundColor: '#F44336',
                        stack: 'stack1'
                    }
                ];

                if (revenueChart) {
                    // update data + plugin data (plugin akan mengambil projectNominalLabels dari closure)
                    revenueChart.data.labels = labels;
                    revenueChart.data.datasets[0].data = paidData;
                    revenueChart.data.datasets[1].data = unpaidData;
                    // tidak ada tempat langsung menyimpan projectNominalLabels di chart.data,
                    // jadi kita re-register plugin dengan closure capture: easiest way = destroy & recreate chart
                    revenueChart.destroy();
                    revenueChart = null;
                }

                // create chart and register plugin specifically for this chart instance
                revenueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Paid vs Unpaid per Bulan'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const lab = context.dataset.label || '';
                                        const val = context.parsed.y ?? context.parsed;
                                        return lab + ': ' + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                ticks: {
                                    callback: function(v) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        // wrapping plugin so it has access to projectNominalLabels via closure
                        id: 'topLabelWrapper',
                        afterDatasetsDraw: function(chart) {
                            // implement same logic as topLabelPlugin but using projectNominalLabels from outer scope
                            const ctx = chart.ctx;
                            const meta0 = chart.getDatasetMeta(0);
                            const meta1 = chart.getDatasetMeta(1);
                            for (let i = 0; i < chart.data.labels.length; i++) {
                                let ys = [];
                                [meta0, meta1].forEach(meta => {
                                    if (meta && meta.data[i]) {
                                        const el = meta.data[i];
                                        let topY = (el && el.y !== undefined) ? el.y :
                                            null;
                                        if (topY !== null) ys.push(topY);
                                    }
                                });
                                if (ys.length === 0) continue;
                                const top = Math.min.apply(null, ys);
                                let x = null;
                                if (meta0 && meta0.data[i]) x = meta0.data[i].x;
                                else if (meta1 && meta1.data[i]) x = meta1.data[i].x;
                                if (x === null) continue;

                                const val = (projectNominalLabels && projectNominalLabels[i]) ?
                                    projectNominalLabels[i] : 0;
                                ctx.save();
                                ctx.font = '12px Arial';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';
                                ctx.fillStyle = '#111';
                                const text = formatRupiah(val);
                                ctx.fillText(text, x, top - 6);
                                ctx.restore();
                            }
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
                        const paid = charts.paidByMonth || Array(12).fill(0);
                        const unpaid = charts.unpaidByMonth || Array(12).fill(0);
                        const projectNominal = charts.projectNominalByMonth || Array(12).fill(0);
                        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                            'Nov', 'Des'
                        ];
                        renderRevenueChart(labels, paid, unpaid, projectNominal);

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
        });
    </script>


@endsection
