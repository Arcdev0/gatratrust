@extends('layout.app')
@section('title', 'Accounting')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Accounting</h3>
                    <a href="{{ route('accounting.create') }}" id="openModalBtn" class="btn btn-success">
                        Tambah Jurnal
                    </a>

                    {{-- <button class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                        Import Excel
                    </button> --}}
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container table-responsive">
                            <div class="mb-3 p-2 bg-light border rounded">
                                <div class="row text-center fw-bold">
                                    <div class="col">
                                        Total Debit: <span id="totalDebit"></span>
                                    </div>
                                    <div class="col">
                                        Total Credit: <span id="totalCredit"></span>
                                    </div>
                                    <div class="col">
                                        Saldo: <span id="totalSaldo"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label>Range Tanggal</label>
                                    <input type="text" id="filterRange" class="form-control">
                                </div>
                                <div class="col-md-3 align-self-end">
                                    <button class="btn btn-primary" id="btnFilter">Filter</button>
                                    <button class="btn btn-secondary" id="btnReset">Reset</button>
                                </div>
                            </div>

                            <table class="table table-bordered" id="accountingTable">
                                <thead>
                                    <tr>
                                        <th>No Jurnal</th>
                                        <th>Tipe Jurnal</th>
                                        <th>Tanggal</th>
                                        <th>Deskripsi</th>
                                        <th>Total</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Jurnal -->
    <div class="modal fade" id="showJurnalModal" tabindex="-1" aria-labelledby="showJurnalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showJurnalLabel">Preview Jurnal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalJurnalBody">

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Zoom Gambar -->
    <div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-body text-center">
                    <img id="zoomImage" src="" class="img-fluid" style="max-height: 80vh; cursor: zoom-in;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Jurnal dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Input File -->
                    <div class="form-group">
                        <label for="excelFile">Pilih File Excel</label>
                        <input type="file" id="excelFile" class="form-control" accept=".xlsx,.xls">
                    </div>

                    <!-- Preview Data -->
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="previewTable" style="display:none;">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnSaveImport" class="btn btn-success" style="display:none;">Simpan ke Database</button>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>
        $(function() {


            $('#filterRange').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' s/d '
                }
            });

            let table = $('#accountingTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('accounting.data') }}',
                    data: function(d) {
                        d.range = $('#filterRange').val();
                    }
                },
                columns: [{
                        data: 'no_jurnal',
                        name: 'no_jurnal'
                    },
                    {
                        data: 'tipe_jurnal',
                        name: 'tipe_jurnal'
                    },
                    {
                        data: 'tanggal_format',
                        name: 'tanggal',
                        orderable: true
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'total',
                        name: 'total',
                        render: data => formatRupiah(data)
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        render: data => formatRupiah(data)
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                        render: data => formatRupiah(data)
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                createdRow: function(row, data) {
                    if (parseFloat(data.debit) > 0) {
                        $(row).css('background-color', '#d4edda');
                    }
                },
                drawCallback: function(settings) {
                    let json = this.api().ajax.json();
                    $('#totalDebit').text(formatRupiah(json.totalDebit));
                    $('#totalCredit').text(formatRupiah(json.totalCredit));
                    $('#totalSaldo').text(formatRupiah(json.saldo));
                }
            });

            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            $('#btnReset').click(function() {
                $('#filterRange').data('daterangepicker').setStartDate(moment().startOf('month'));
                $('#filterRange').data('daterangepicker').setEndDate(moment().endOf('month'));
                table.ajax.reload();
            });

            function formatRupiah(angka) {
                angka = parseFloat(angka) || 0;
                return 'Rp ' + angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        });

        $(document).on('click', '.btnDelete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data jurnal akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/accounting/' + id + '/delete',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#accountingTable').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data gagal dihapus'
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btnShow', function() {
            let id = $(this).data('id');

            $('#modalJurnalBody').html('<p class="text-center text-muted">Memuat...</p>');
            $('#showJurnalModal').modal('show');

            $.get('/accounting/' + id + '/show', function(res) {
                let fileList = '';
                if (res.files.length > 0) {
                    fileList = '<div class="row">';
                    res.files.forEach(file => {
                        let ext = file.path.split('.').pop().toLowerCase();
                        let iconPreview = '';

                        if (ext === 'pdf') {
                            iconPreview =
                                `<img src="/images/default-pdf.png" class="img-fluid" style="max-height:100px;">`;
                        } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                            iconPreview =
                                `<img src="${file.path}" class="img-fluid" style="max-height:100px;">`;
                        } else {
                            iconPreview =
                                `<i class="bi bi-file-earmark-text" style="font-size:2rem;"></i>`;
                        }

                        fileList += `
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm file-preview" 
                             data-link="${file.path}" 
                             data-ext="${ext}"
                             style="cursor:pointer;">
                            <div class="card-body text-center">
                                ${iconPreview}
                                <p class="mt-2 mb-0">${file.name}</p>
                            </div>
                        </div>
                    </div>
                `;
                    });
                    fileList += '</div>';
                } else {
                    fileList = '<p class="text-muted">Tidak ada file</p>';
                }

                $('#modalJurnalBody').html(`
            <p><strong>No Jurnal:</strong> ${res.no_jurnal}</p>
            <p><strong>Tipe Jurnal:</strong> ${res.tipe_jurnal}</p>
            <p><strong>Deskripsi:</strong> ${res.deskripsi}</p>
            <p><strong>Total:</strong> ${res.total}</p>
            <hr>
            <h6>File Terlampir:</h6>
            ${fileList}
        `);
            });
        });

        // Klik file preview
        $(document).on('click', '.file-preview', function() {
            let fileLink = $(this).data('link');
            let fileExt = $(this).data('ext');

            if (fileExt === 'pdf') {
                // Buka PDF di tab baru
                window.open(fileLink, '_blank');
            } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                // Tampilkan gambar di modal zoom
                $('#zoomImage').attr('src', fileLink);
                $('#zoomModal').modal('show');
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {
            // Event saat file dipilih
            $('#excelFile').on('change', function(e) {
                var file = e.target.files[0];
                if (!file) return;

                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, {
                        type: 'array'
                    });
                    var sheetName = workbook.SheetNames[0];
                    var sheet = workbook.Sheets[sheetName];
                    var jsonData = XLSX.utils.sheet_to_json(sheet);

                    // Bersihkan preview
                    $('#previewBody').empty();

                    // Tampilkan data ke tabel
                    $.each(jsonData, function(index, row) {
                        let tanggal = row['tanggal'] || row['Tanggal'] || '';
                        let deskripsi = row['Description'] || row['Deskripsi'] || '';
                        let debit = row['Debit'] || 0;
                        let credit = row['Credit'] || 0;
                        let saldo = row['Saldo'] || 0;

                        $('#previewBody').append(`
                        <tr>
                            <td>${tanggal}</td>
                            <td>${deskripsi}</td>
                            <td>${debit}</td>
                            <td>${credit}</td>
                            <td>${saldo}</td>
                        </tr>
                    `);
                    });


                    $('#previewTable').show();
                    $('#btnSaveImport').show();
                };
                reader.readAsArrayBuffer(file);
            });

            // Event saat klik simpan
            $('#btnSaveImport').on('click', function() {
                var data = [];
                $('#previewBody tr').each(function() {
                    var tds = $(this).find('td');
                    data.push({
                        tanggal: $(tds[0]).text(),
                        deskripsi: $(tds[1]).text(),
                        debit: $(tds[2]).text(),
                        credit: $(tds[3]).text(),
                        saldo: $(tds[4]).text()
                    });
                });

                Swal.fire({
                    title: 'Mengimpor Data...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('accounting.import') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        data: data
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Import data gagal. Periksa file atau server.'
                        });
                    }
                });
            });
        });
    </script>

@endsection
