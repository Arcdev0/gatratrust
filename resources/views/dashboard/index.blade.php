@extends('layout.app')


@section('content')
    <div class="container-fluid">
        <div class="row clearfix">
            <!-- Total Project -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Total Project</h6>
                                <h2>{{ $totalProjects }}</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-briefcase"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">{{ $activeProjects }} Active Projects</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%"></div>
                    </div>
                </div>
            </div>

            <!-- Total Invoice -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Total Invoice</h6>
                                <h2>{{ $totalInvoices }}</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-file-text"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">{{ $invoiceStatus['open'] ?? 0 }} Open,
                            {{ $invoiceStatus['close'] ?? 0 }} Closed</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 80%"></div>
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
                                <h2 class="responsive-title">Rp {{ number_format($totalPayments, 0, ',', '.') }}</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-dollar-sign"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">+20% dibanding bulan lalu</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"></div>
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
                                <h2>Rp {{ number_format($outstanding, 0, ',', '.') }}</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-alert-circle"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">Harus ditagih</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 30%"></div>
                    </div>
                </div>
            </div>
        </div>

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
                        <h3 class="card-title">Status Invoice</h3>
                        <select id="filterYear" class="form-control" style="width:120px">
                            <!-- isi dinamis dari JS -->
                        </select>
                    </div>
                    <div class="card-body">
                        <canvas id="chartInvoiceStatus"></canvas>
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
                <table id="project_table" class="table">
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
                            <tr>
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
@endsection

@section('script')
    <script>
        const revenueData = {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
                label: "Revenue",
                data: [
                    @for ($i = 1; $i <= 12; $i++)
                        {{ $revenueByMonth[$i] ?? 0 }},
                    @endfor
                ],
                borderColor: "#36A2EB",
                backgroundColor: "rgba(54, 162, 235, 0.2)",
                tension: 0.3
            }]
        };

        const ctxRevenue = document.getElementById('chartRevenue').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'line',
            data: revenueData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Revenue per Bulan'
                    }
                }
            }
        });
    </script>


    <script>
        const invoiceDataByYear = @json($invoiceDataByYear);
        const currentYear = (new Date()).getFullYear();
        const yearSelect = document.getElementById('filterYear');

        let years = Object.keys(invoiceDataByYear).map(y => parseInt(y, 10)).sort((a, b) => a - b);

        if (years.length === 0) {
            years = [currentYear];
            invoiceDataByYear[currentYear] = {
                open: 0,
                close: 0
            };
        }

        years.forEach(year => {
            const opt = document.createElement('option');
            opt.value = year;
            opt.text = year;
            if (year === currentYear) opt.selected = true;
            yearSelect.appendChild(opt);
        });

        function createOrUpdateInvoiceChart(selectedYear) {
            const data = invoiceDataByYear[selectedYear] || {
                open: 0,
                close: 0
            };
            if (window.invoiceChart) {
                window.invoiceChart.data.datasets[0].data = [data.open, data.close];
                window.invoiceChart.options.plugins.title.text = `Status Invoice (${selectedYear})`;
                window.invoiceChart.update();
                return;
            }

            const ctx = document.getElementById('chartInvoiceStatus').getContext('2d');
            window.invoiceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Open', 'Close'],
                    datasets: [{
                        data: [data.open, data.close],
                        backgroundColor: ['#FF6384', '#4BC0C0']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: `Status Invoice (${selectedYear})`
                        }
                    }
                }
            });
        }

        createOrUpdateInvoiceChart(yearSelect.value || currentYear);

        yearSelect.addEventListener('change', function() {
            createOrUpdateInvoiceChart(this.value);
        });
    </script>
@endsection
