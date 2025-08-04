@extends('layout.app')
@section('title', 'Daily Activity')

@section('content')
    <style>
        .comment-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        .timeline-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
        }

        .month-box {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 5px;
        }

        .month-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            font-size: 12px;
        }

        .day-box {
            width: 20px;
            height: 20px;
            margin: 1px;
            text-align: center;
            line-height: 20px;
            border-radius: 3px;
            font-size: 10px;
            border: 1px solid #ccc;
            background-color: #f8f9fa;
        }

        .day-plan-orange {
            background-color: orange;
            color: white;
            border-color: orange;
        }

        .day-plan-green {
            background-color: green;
            color: white;
            border-color: green;
        }

        .daily-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
        }

        .desc-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            height: 100%;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            {{-- Bagian tengah: Timeline Tahunan & Daily Activity --}}
            <div class="col-md-9">
                {{-- Timeline Tahunan --}}
                <div class="timeline-box mb-3">
                    <h5 class="text-primary font-weight-bold" id="timelineTitle">Timeline Tahunan</h5>
                    <div class="row g-2 mt-2" id="timelineContainer">
                        <!-- Box bulan akan dibuat otomatis oleh jQuery -->
                    </div>
                </div>

                {{-- Daily Activity --}}
                <div class="daily-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-primary font-weight-bold">Daily Activity</h5>
                        <button id="openModalBtn" class="btn btn-success">
                            Tambah Daily Activity
                        </button>
                    </div>
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

            {{-- Bagian kanan: Deskripsi Aktivitas --}}
            <div class="col-md-3">
                <div class="desc-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-primary font-weight-bold">Deskripsi Aktivitas</h5>
                        <button id="openModalBtnDesc" class="btn btn-sm btn-success">
                            +
                        </button>
                    </div>
                    <div id="activityDescription" class="mt-3">
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
                            <label>Today’s Achievements</label>
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
                            <label>Today’s Achievements</label>
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

    <div class="modal fade" id="komentarModal" tabindex="-1" aria-labelledby="komentarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Komentar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="komentarInput" class="form-label">Tulis Komentar</label>
                        <textarea class="form-control" id="komentarInput" rows="3" placeholder="Tulis komentar di sini..."></textarea>
                    </div>
                    <button class="btn btn-primary mb-3" id="btnTambahKomentar">Tambah Komentar</button>
                    <hr>
                    <div id="listKomentar" style="max-height: 400px; overflow-y: auto; padding-right: 10px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityModalLabel">Tambah Aktivitas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="activityForm">
                        <input type="hidden" id="planIndex">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="startDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="endDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="desc" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="desc" rows="2" required></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isAction">
                            <label class="form-check-label" for="isAction">Is Action?</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger d-none" id="deleteActivityBtn"
                        data-id="">Hapus</button>
                    <button type="button" class="btn btn-secondary" class="close" data-dismiss="modal"
                        aria-label="Close">Batal</button>
                    <button type="button" id="saveActivityBtn" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>
        $(document).ready(function() {
            const timelineContainer = $("#timelineContainer");
            const activityDescription = $("#activityDescription");
            const year = new Date().getFullYear();
            $("#timelineTitle").text(`Timeline Tahunan ${year}`);

            const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
            let plans = [];

            function getDaysInMonth(month, year) {
                return new Date(year, month + 1, 0).getDate();
            }

            function loadPlans() {
                $.get(`/timeline/get?tahun=${year}`, function(data) {
                    plans = data;
                    console.log("Isi timeline", plans);
                    renderTimeline();
                    renderPlans();
                });
            }

            function renderTimeline() {
                timelineContainer.empty();
                for (let month = 0; month < 12; month++) {
                    let daysInMonth = getDaysInMonth(month, year);
                    let monthBox = $(`
            <div class="col-2 mb-2">
                <div class="month-box">
                    <div class="month-title">${monthNames[month]}</div>
                    <div class="d-flex flex-wrap days-container"></div>
                </div>
            </div>
        `);
                    let daysContainer = monthBox.find(".days-container");

                    for (let day = 1; day <= daysInMonth; day++) {
                        let classPlan = "";
                        let planFound = plans.find(p => {
                            let startParts = p.start_date.split('-');
                            let endParts = p.end_date.split('-');

                            // Buat date tanpa jam
                            let start = new Date(parseInt(startParts[0]), parseInt(startParts[1]) - 1,
                                parseInt(startParts[2]));
                            let end = new Date(parseInt(endParts[0]), parseInt(endParts[1]) - 1, parseInt(
                                endParts[2]));

                            let current = new Date(year, month, day);
                            return current >= start && current <= end;
                        });

                        if (planFound) {
                            classPlan = planFound.is_action === 1 ? "day-plan-green" : "day-plan-orange";
                        }

                        daysContainer.append(`<div class="day-box ${classPlan}">${day}</div>`);
                    }

                    timelineContainer.append(monthBox);
                }
            }

            function renderPlans() {
                if (!plans || plans.length === 0) {
                    activityDescription.html(`
            <p class="text-muted text-center mt-3">
                Belum ada aktivitas terjadwal di tahun ini.
            </p>
        `);
                    return;
                }

                let html = `<div class="list-group">`;
                plans.forEach(plan => {
                    let start = new Date(plan.start_date);
                    let end = new Date(plan.end_date);
                    html += `
            <a href="javascript:void(0)" 
               class="list-group-item list-group-item-action planItem" 
               data-id="${plan.id}">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1 font-weight-bold">
                        ${start.getDate()}-${end.getDate()} ${monthNames[start.getMonth()]} ${year}
                    </h6>
                    <small class="text-${plan.is_action ? 'success' : 'warning'}">
                        ${plan.is_action ? 'Action' : 'Non-Action'}
                    </small>
                </div>
                <p class="mb-1">${plan.description}</p>
            </a>
        `;
                });
                html += `</div>`;
                activityDescription.html(html);
            }

            $("#openModalBtnDesc").on("click", function() {
                $("#activityModalLabel").text("Tambah Aktivitas");
                $("#activityForm")[0].reset();
                $("#planIndex").val("");
                $("#deleteActivityBtn").addClass("d-none").attr("data-id", ""); // hide delete
                $("#activityModal").modal("show");
            });

            $("#saveActivityBtn").on("click", function() {
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
                const desc = $("#desc").val();
                const isAction = $("#isAction").is(":checked") ? 1 : 0;
                const planId = $("#planIndex").val();

                if (!startDate || !endDate || !desc) {
                    Swal.fire("Gagal!", "Mohon isi semua field!", "error");
                    return;
                }

                const payload = {
                    tahun: year,
                    start_date: startDate,
                    end_date: endDate,
                    description: desc,
                    is_action: isAction,
                    _token: '{{ csrf_token() }}'
                };

                if (planId) {
                    // Edit (PUT)
                    $.ajax({
                        url: `/timeline/update/${planId}`,
                        type: 'PUT',
                        data: payload,
                        success: function(res) {
                            Swal.fire("Berhasil!", res.message, "success");
                            $("#activityModal").modal("hide");
                            loadPlans();
                        }
                    });
                } else {
                    // Tambah (POST)
                    $.post(`/timeline/add`, payload, function(res) {
                        Swal.fire("Berhasil!", res.message, "success");
                        $("#activityModal").modal("hide");
                        loadPlans();
                    });
                }
            });

            $(document).on("click", ".planItem", function() {
                const id = $(this).data("id");
                const plan = plans.find(p => p.id === id);

                $("#activityModalLabel").text("Edit Aktivitas");
                $("#startDate").val(plan.start_date);
                $("#endDate").val(plan.end_date);
                $("#desc").val(plan.description);
                $("#isAction").prop("checked", plan.is_action === 1);
                $("#planIndex").val(plan.id);

                // Show delete button saat edit
                $("#deleteActivityBtn").removeClass("d-none").attr("data-id", plan.id);

                $("#activityModal").modal("show");
            });

            $("#deleteActivityBtn").on("click", function() {
                const id = $(this).data("id");

                if (!id) return;

                Swal.fire({
                    title: "Yakin ingin menghapus?",
                    text: "Data yang dihapus tidak bisa dikembalikan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/timeline/delete/${id}`,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                Swal.fire("Berhasil!", "Data berhasil dihapus.",
                                    "success");
                                $("#activityModal").modal("hide");
                                loadPlans();
                            },
                            error: function() {
                                Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.",
                                    "error");
                            }
                        });
                    }
                });
            });

            loadPlans();
        });
    </script>


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
                    },
                    success: function(response) {
                        let html = '';
                        let authUserId = response.auth_user_id;
                        let data = response.data;

                        if (data.length > 0) {
                            data.forEach(function(item) {
                                let actionButtons = '';
                                if (authUserId === item.user_id) {
                                    actionButtons = `
                            <button class="btn btn-sm btn-primary editBtn" data-id="${item.id}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}"><i class="fas fa-trash"></i></button>
                        `;
                                }

                                html += `
                        <div class="card mb-3" data-id="${item.id}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">${item.user.name}</h5>
                                    <small class="text-muted">${new Date(item.tanggal).toLocaleString()}</small>
                                </div>
                                <div>
                                    ${actionButtons}
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Today’s Achievements:</strong> ${item.plan_today}</p>
                                <p><strong>Plan Tomorrow:</strong> ${item.plan_tomorrow || '-'}</p>
                                <p><strong>Problem:</strong> ${item.problem || '-'}</p>
                                ${item.upload_file ? `
                                                                                                                                                                                                                                                <p><strong>File:</strong> <a href="/storage/${item.upload_file}" target="_blank">Download</a></p>
                                                                                                                                                                                                                                            ` : ''}
                            </div>
                            <div class="card-footer d-flex justify-content-start">
                                <button class="btn btn-light btn-sm commentBtn" data-id="${item.id}">
                                    💬 <span class="comment-count">${item.comments_count || 0}</span>
                                </button>
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

            fetchDailyActivities();

            $('#filterTanggal').on('change', function() {
                let tanggalDipilih = $(this).val();
                fetchDailyActivities(tanggalDipilih);
            });

            let currentDailyId = null;

            $(document).on('click', '.commentBtn', function() {
                currentDailyId = $(this).data('id');
                $('#komentarInput').val('');
                $('#listKomentar').html('<p class="text-muted">Memuat komentar...</p>');
                $('#komentarModal').modal('show');

                // Load komentar
                loadKomentar(currentDailyId);
            });

            function loadKomentar(id) {
                $.get(`/daily/${id}/comments`, function(res) {
                    if (res.length === 0) {
                        $('#listKomentar').html('<p class="text-muted">Belum ada komentar.</p>');
                    } else {
                        let html = '';
                        res.forEach(k => {
                            const isOwnComment = k.user.id === {{ auth()->id() }};
                            html += `
                    <div class="card mb-3 comment-card mx-auto" style="max-width: 700px;" data-id="${k.id}">
                        <div class="card-body d-flex">
                            <img src="/template/img/user_main.jpg" alt="User" class="comment-avatar mr-3" width="52">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-0">${k.user.name}</h6>
                                        <div class="comment-meta">${new Date(k.created_at).toLocaleString()}</div>
                                    </div>
                                    ${isOwnComment ? `
                                                                                                                                                                                                                                                <button class="btn btn-sm btn-outline-danger btn-delete-komentar" data-id="${k.id}">&times;</button>
                                                                                                                                                                                                                                            ` : ''}
                                </div>
                                <p class="mt-2 mb-0">${k.comment}</p>
                            </div>
                        </div>
                    </div>
                `;
                        });
                        $('#listKomentar').html(html);
                    }
                });
            }

            $('#btnTambahKomentar').click(function() {
                let isiKomentar = $('#komentarInput').val().trim();
                if (!isiKomentar) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops!',
                        text: 'Komentar tidak boleh kosong.'
                    });
                    return;
                }

                $.post(`/daily/${currentDailyId}/comments`, {
                        _token: "{{ csrf_token() }}",
                        comment: isiKomentar
                    })
                    .done(function() {
                        $('#komentarInput').val('');
                        loadKomentar(currentDailyId); // Reload list komentar

                        // Update count komentar pada card yang sesuai
                        let card = $(`.card[data-id="${currentDailyId}"]`);
                        let countSpan = card.find('.comment-count');
                        let count = parseInt(countSpan.text());
                        countSpan.text(count + 1);

                        Swal.fire({
                            icon: 'success',
                            title: 'Komentar ditambahkan!',
                            showConfirmButton: false,
                            timer: 1000
                        });
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Komentar tidak berhasil ditambahkan.'
                        });
                    });
            });

            $(document).on('click', '.btn-delete-komentar', function() {
                const commentId = $(this).data('id');

                Swal.fire({
                    title: 'Hapus komentar ini?',
                    text: "Tindakan ini tidak bisa dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/daily/comments/${commentId}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                loadKomentar(currentDailyId);

                                // Update count komentar pada card yang sesuai
                                let card = $(`.card[data-id="${currentDailyId}"]`);
                                let countSpan = card.find('.comment-count');
                                let count = parseInt(countSpan.text());
                                if (count > 0) countSpan.text(count - 1);

                                Swal.fire(
                                    'Dihapus!',
                                    'Komentar telah dihapus.',
                                    'success'
                                );
                            },
                            error: function(err) {
                                Swal.fire(
                                    'Gagal!',
                                    'Komentar gagal dihapus.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });


            let quillPlanToday, quillPlanTomorrow, quillProblem;

            function initQuillEditors() {
                quillPlanToday = new Quill('#planTodayEditor', {
                    theme: 'snow',
                    placeholder: 'Tulis Today’s Achievements...',
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
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
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
