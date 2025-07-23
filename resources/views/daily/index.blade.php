@extends('layout.app')
@section('title', 'Daily Activity')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Daily Activity</h3>
                    <button id="openModalBtn" class="btn btn-success">
                        Tambah Daily Activity
                    </button>
                </div>
                <div class="card">
                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Filter Tanggal</h5>
                            <input type="date" id="filterTanggal" class="form-control w-auto">
                        </div>
                    </div>
                    <div class="card-body" id="dailyCardList">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pekerjaan -->
    <div class="modal fade" id="tambahDailyModal" tabindex="-1" aria-labelledby="tambahDailyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahDailyModalLabel">Tambah Daily Activity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addDailyForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="datetime-local" name="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Plan Today</label>
                            <div id="planTodayEditor" style="height: 150px;"></div>
                            <input type="hidden" name="plan_today">
                        </div>
                        <div class="form-group">
                            <label>Plan Tomorrow</label>
                            <div id="planTomorrowEditor" style="height: 150px;"></div>
                            <input type="hidden" name="plan_tomorrow">
                        </div>
                        <div class="form-group">
                            <label>Problem</label>
                            <div id="problemEditor" style="height: 150px;"></div>
                            <input type="hidden" name="problem">
                        </div>
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="upload_file" class="form-control-file">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="savePekerjaanBtn" class="btn btn-primary">Simpan Pekerjaan</button>
                    <button type="button" class="btn btn-secondary" id="closeModalFooterBtn">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Edit Pekerjaan -->
    <div class="modal fade" id="editDailyModal" tabindex="-1" aria-labelledby="editDailyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDailyModalLabel">Edit Daily Activity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editDailyForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="daily_id">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="datetime-local" name="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Plan Today</label>
                            <div id="editPlanTodayEditor" style="height: 150px;"></div>
                            <input type="hidden" name="plan_today">
                        </div>
                        <div class="form-group">
                            <label>Plan Tomorrow</label>
                            <div id="editPlanTomorrowEditor" style="height: 150px;"></div>
                            <input type="hidden" name="plan_tomorrow">
                        </div>
                        <div class="form-group">
                            <label>Problem</label>
                            <div id="editProblemEditor" style="height: 150px;"></div>
                            <input type="hidden" name="problem">
                        </div>
                        <div class="form-group">
                            <label>Upload File (kosongkan jika tidak diubah)</label>
                            <input type="file" name="upload_file" class="form-control-file">
                            <div id="currentFile" class="mt-2"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="saveEditPekerjaanBtn" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" id="closeModalEditFooterBtn">Tutup</button>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Default tanggal hari ini
            let today = new Date().toISOString().split('T')[0];
            $('#filterTanggal').val(today);

            // Fetch and render Daily Activities by date
            function fetchDailyActivities(tanggal = today) {
                $('#dailyCardList').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);

                $.ajax({
                    url: "{{ route('daily.getList') }}",
                    method: "GET",
                    data: {
                        tanggal: tanggal
                    }, // kirim tanggal ke backend
                    success: function(data) {
                        let html = '';
                        if (data.length > 0) {
                            data.forEach(function(item) {
                                html += `
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">${item.user.name}</h5>
                                    <small class="text-muted">${new Date(item.tanggal).toLocaleString()}</small>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-primary editBtn" data-id="${item.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}">Delete</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Plan Today:</strong> ${item.plan_today}</p>
                                <p><strong>Plan Tomorrow:</strong> ${item.plan_tomorrow || '-'}</p>
                                <p><strong>Problem:</strong> ${item.problem || '-'}</p>
                                ${item.upload_file ? `
                                                                                    <p><strong>File:</strong> <a href="/storage/${item.upload_file}" target="_blank">Download</a></p>
                                                                                ` : ''}
                            </div>
                        </div>
                        `;
                            });
                        } else {
                            html =
                                '<p class="text-center text-muted">Tidak ada data untuk tanggal ini.</p>';
                        }
                        $('#dailyCardList').html(html);
                    },
                    error: function() {
                        $('#dailyCardList').html(
                            '<p class="text-danger text-center">Gagal memuat data.</p>');
                    }
                });
            }

            // Load data untuk hari ini saat pertama kali
            fetchDailyActivities();

            // Reload data saat tanggal diganti
            $('#filterTanggal').on('change', function() {
                let tanggalDipilih = $(this).val();
                fetchDailyActivities(tanggalDipilih);
            });

            let quillPlanToday, quillPlanTomorrow, quillProblem;

            function initQuillEditors() {
                quillPlanToday = new Quill('#planTodayEditor', {
                    theme: 'snow',
                    placeholder: 'Tulis Plan Today...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['clean']
                        ]
                    }
                });

                quillPlanTomorrow = new Quill('#planTomorrowEditor', {
                    theme: 'snow',
                    placeholder: 'Tulis Plan Tomorrow...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['clean']
                        ]
                    }
                });

                quillProblem = new Quill('#problemEditor', {
                    theme: 'snow',
                    placeholder: 'Tulis Problem...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['clean']
                        ]
                    }
                });
            }


            // Init Quill for Add Modal
            function initAddQuillEditors() {
                if (!quillPlanToday) {
                    quillPlanToday = new Quill('#planTodayEditor', {
                        theme: 'snow'
                    });
                }
                if (!quillPlanTomorrow) {
                    quillPlanTomorrow = new Quill('#planTomorrowEditor', {
                        theme: 'snow'
                    });
                }
                if (!quillProblem) {
                    quillProblem = new Quill('#problemEditor', {
                        theme: 'snow'
                    });
                }
            }

            let quillEditPlanToday, quillEditPlanTomorrow, quillEditProblem;

            function initEditQuillEditors() {
                if (!quillEditPlanToday) {
                    quillEditPlanToday = new Quill('#editPlanTodayEditor', {
                        theme: 'snow'
                    });
                }
                if (!quillEditPlanTomorrow) {
                    quillEditPlanTomorrow = new Quill('#editPlanTomorrowEditor', {
                        theme: 'snow'
                    });
                }
                if (!quillEditProblem) {
                    quillEditProblem = new Quill('#editProblemEditor', {
                        theme: 'snow'
                    });
                }
            }


            // Open Add Modal
            $('#openModalBtn').on('click', function() {
                $('#tambahDailyModal').modal('show');
                setTimeout(() => {
                    initAddQuillEditors();
                }, 300);
            });

            // Save New Daily
            $('#savePekerjaanBtn').on('click', function() {
                $('input[name="plan_today"]').val(quillPlanToday.root.innerHTML);
                $('input[name="plan_tomorrow"]').val(quillPlanTomorrow.root.innerHTML);
                $('input[name="problem"]').val(quillProblem.root.innerHTML);

                let form = $('#addDailyForm')[0];
                let formData = new FormData(form);
                formData.append('user_id', {{ auth()->id() }});

                $.ajax({
                    url: "{{ route('daily.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        Swal.fire('Berhasil', 'Daily berhasil ditambahkan.', 'success');
                        $('#tambahDailyModal').modal('hide');
                        fetchDailyActivities();
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON.errors;
                        let msg = '';
                        for (const key in err) {
                            msg += `${err[key]}<br>`;
                        }
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            });

            // Open Edit Modal
            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: `/daily/edit/${id}`,
                    method: "GET",
                    success: function(item) {
                        $('#editDailyForm input[name="daily_id"]').val(item.id);
                        $('#editDailyForm input[name="tanggal"]').val(item.tanggal.replace(' ',
                            'T'));
                        setTimeout(() => {
                            initEditQuillEditors();
                            quillEditPlanToday.root.innerHTML = item.plan_today;
                            quillEditPlanTomorrow.root.innerHTML = item.plan_tomorrow ||
                                '';
                            quillEditProblem.root.innerHTML = item.problem || '';
                        }, 300);

                        if (item.upload_file) {
                            $('#currentFile').html(`
                    <p>File saat ini: <a href="/storage/${item.upload_file}" target="_blank">Download</a></p>
                `);
                        } else {
                            $('#currentFile').html(`<p class="text-muted">Tidak ada file.</p>`);
                        }

                        $('#editDailyModal').modal('show');
                    }
                });
            });

            // Save Edited Daily
            $('#saveEditPekerjaanBtn').on('click', function() {
                let id = $('#editDailyForm input[name="daily_id"]').val();

                $('input[name="plan_today"]').val(quillEditPlanToday.root.innerHTML);
                $('input[name="plan_tomorrow"]').val(quillEditPlanTomorrow.root.innerHTML);
                $('input[name="problem"]').val(quillEditProblem.root.innerHTML);

                let form = $('#editDailyForm')[0];
                let formData = new FormData(form);

                $.ajax({
                    url: `/daily/update/${id}`,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        Swal.fire('Berhasil', 'Daily berhasil diperbarui.', 'success');
                        $('#editDailyModal').modal('hide');
                        fetchDailyActivities();
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON.errors;
                        let msg = '';
                        for (const key in err) {
                            msg += `${err[key]}<br>`;
                        }
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            });

            // Delete Daily
            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin ingin hapus?',
                    text: "Data tidak dapat dikembalikan setelah dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/daily/delete/${id}`,
                            method: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                Swal.fire('Berhasil', 'Daily berhasil dihapus.',
                                    'success');
                                fetchDailyActivities();
                            },
                            error: function() {
                                Swal.fire('Gagal', 'Gagal menghapus Daily.', 'error');
                            }
                        });
                    }
                });
            });

            // Close modals
            $('#closeModalFooterBtn').on('click', function() {
                $('#tambahDailyModal').modal('hide');
            });
            $('#closeModalEditFooterBtn').on('click', function() {
                $('#editDailyModal').modal('hide');
            });

            $('#tambahDailyModal').on('hidden.bs.modal', function() {
                if (quillPlanToday) quillPlanToday.setContents([]);
                if (quillPlanTomorrow) quillPlanTomorrow.setContents([]);
                if (quillProblem) quillProblem.setContents([]);
            });

            $('#editDailyModal').on('hidden.bs.modal', function() {
                if (quillEditPlanToday) quillEditPlanToday.setContents([]);
                if (quillEditPlanTomorrow) quillEditPlanTomorrow.setContents([]);
                if (quillEditProblem) quillEditProblem.setContents([]);
            });


        });
    </script>
@endsection
