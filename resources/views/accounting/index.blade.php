@extends('layout.app')
@section('title', 'Accounting')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Jurnal</h3>
                    <a href="{{ route('accounting.create') }}" id="openModalBtn" class="btn btn-success">
                        Tambah Jurnal
                    </a>
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

                            <table class="table table-bordered" id="accountingTable">
                                <thead>
                                    <tr>
                                        <th>No Jurnal</th>
                                        <th>Tipe Jurnal</th>
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
@endsection
@section('script')
    <script>
        $(function() {
            $('#accountingTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('accounting.data') }}',
                columns: [{
                        data: 'no_jurnal',
                        name: 'no_jurnal'
                    },
                    {
                        data: 'tipe_jurnal',
                        name: 'tipe_jurnal'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'total',
                        name: 'total',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    let api = this.api();
                    let totalDebit = api.column(4, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                    let totalCredit = api.column(5, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                    let saldo = totalDebit - totalCredit;

                    $('#totalDebit').text(formatRupiah(totalDebit));
                    $('#totalCredit').text(formatRupiah(totalCredit));
                    $('#totalSaldo').text(formatRupiah(saldo));
                }
            });

            // Fungsi Format Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + parseFloat(angka)
                    .toFixed(0)
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
                        console.log(file);
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
    </script>

@endsection
