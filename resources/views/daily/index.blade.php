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

        .readonly-row select,
        .readonly-row input,
        .readonly-row textarea {
            pointer-events: none !important;
            background: #f6f6f6 !important;
        }

        /* status tetap bisa */
        .readonly-row .status {
            pointer-events: auto !important;
            background: #fff !important;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            {{-- Bagian tengah: Timeline Tahunan & Daily Activity --}}
            <div class="col-md-9">
                {{-- Timeline Tahunan --}}
                <div class="timeline-box mb-3 d-none d-md-block">
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

                    <!-- CARD TASK PENDING -->
                    <div class="card mb-3" id="pendingTaskCard">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 font-weight-bold">Task Pending</h6>
                                <small class="text-muted">
                                    Daftar pekerjaan yang statusnya <strong>Belum</strong> dari Daily sebelumnya.
                                </small>
                            </div>
                            <span class="badge badge-warning"><span id="pendingCountBadge">0</span> Pending</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <div class="container">
                                    <table class="table table-sm table-bordered mt-3" id="pendingTaskTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 40px;">No</th>
                                                <th style="width: 120px;">Tanggal</th>
                                                <th style="width: 120px;">Project</th>
                                                <th style="width: 150px;">PIC</th>
                                                <th>Jenis & Pekerjaan / Proses</th>
                                                <th>Keterangan</th>
                                                <th style="width: 80px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>


                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- END CARD PENDING -->

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
            <div class="col-md-3 d-none d-md-block">
                <div class="desc-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-primary font-weight-bold">Deskripsi Timeline</h5>
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
        <div class="modal-dialog" style="max-width: 1140px;">
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

                        <div class="form-group" id="pendingSection" style="display:none;">
                            <label>Pending Task</label>
                            <small class="form-text text-muted">
                                Task di bawah ini otomatis diambil dari daily sebelumnya yang statusnya
                                <strong>Belum</strong>.
                                Status & keterangan masih bisa diubah.
                            </small>

                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-sm" id="pendingTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;">No</th>
                                            <th style="width: 180px;">Jenis</th>
                                            <th style="width: 180px;">No Project</th>
                                            <th style="width: 180px;">Pekerjaan</th>
                                            <th>Keterangan</th>
                                            <th style="width: 140px;">Status</th>
                                            {{-- <th style="width: 60px;">Aksi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Row pending dari kemarin -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Today’s Achievements</label>

                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-success" id="addAchievementRow">
                                    + Tambah Item
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="achievementTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;">No</th>
                                            <th style="width: 180px;">Jenis</th>
                                            <th style="width: 180px;">No Project</th>
                                            <th style="width: 180px;">Pekerjaan</th>
                                            <th>Keterangan</th>
                                            <th style="width: 140px;">Status</th>
                                            <th style="width: 60px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Row akan di-generate via JS -->
                                    </tbody>
                                </table>
                            </div>
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
    <div class="modal fade" id="editDailyModal" tabindex="-1" aria-labelledby="editDailyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="max-width: 1140px;">
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
                        @method('POST')
                        <input type="hidden" name="daily_id">

                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="datetime-local" name="tanggal" class="form-control" required>
                        </div>

                        {{-- TODAY’S ACHIEVEMENTS (EDIT) --}}
                        <div class="form-group">
                            <label>Today’s Achievements</label>

                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-success" id="editAddAchievementRow">
                                    + Tambah Item
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="editAchievementTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;">No</th>
                                            <th style="width: 180px;">Jenis</th>
                                            <th style="width: 180px;">No Project</th>
                                            <th style="width: 180px;">Pekerjaan</th>
                                            <th>Keterangan</th>
                                            <th style="width: 140px;">Status</th>
                                            <th style="width: 60px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- diisi via JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- PLAN TOMORROW (EDIT, AUTO DARI TODAY=BELUM) --}}
                        <div class="form-group">
                            <label>Plan Tomorrow</label>
                            <small class="form-text text-muted">
                                Daftar di bawah ini akan terisi otomatis dari Today’s Achievements yang statusnya
                                <strong>Belum</strong>.
                            </small>

                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-sm" id="editTomorrowTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;">No</th>
                                            <th style="width: 180px;">Jenis</th>
                                            <th style="width: 180px;">No Project</th>
                                            <th style="width: 180px;">Pekerjaan</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- auto generate --}}
                                    </tbody>
                                </table>
                            </div>

                            <div id="editTomorrowHiddenInputs"></div>
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

                let sortedPlans = [...plans].sort((a, b) => a.is_action - b.is_action);

                let html = `<div class="list-group">`;
                sortedPlans.forEach(plan => {
                    let start = new Date(plan.start_date);
                    let end = new Date(plan.end_date);

                    let planYear = start.getFullYear();

                    let dateLabel;
                    if (start.getTime() === end.getTime()) {
                        dateLabel = `${start.getDate()} ${monthNames[start.getMonth()]} ${planYear}`;
                    } else {
                        dateLabel =
                            `${start.getDate()}-${end.getDate()} ${monthNames[start.getMonth()]} ${planYear}`;
                    }

                    html += `
            <a href="javascript:void(0)" 
               class="list-group-item list-group-item-action planItem" 
               data-id="${plan.id}">
                <div class="d-flex w-100 justify-content-between">
                   <h6 class="mb-1 font-weight-bold">
                        ${dateLabel}
                    </h6>
                    <small class="text-${plan.is_action ? 'success' : 'warning'}">
                        ${plan.is_action ? 'Completed' : 'Planned '}
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
                $("#deleteActivityBtn").addClass("d-none").attr("data-id", "");
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
        $(function() {
            // =========================
            // CONFIG & STATE
            // =========================
            const routes = {
                pending: "{{ route('newdaily.pending') }}",
                cards: "{{ route('newdaily.cards') }}",
                myOpenTasks: "{{ route('newdaily.my_open_tasks') }}",
                store: "{{ route('newdaily.store') }}",
                projectData: "{{ route('newdaily.projectData') }}",
                edit: (id) => `/new-daily/${id}/edit`,
                update: (id) => `/new-daily/${id}`,
                destroy: (id) => `/new-daily/${id}`,
            };

            const today = new Date().toISOString().slice(0, 10);
            $('#filterTanggal').val(today);

            // =========================
            // HELPERS
            // =========================
            function showLoadingCards() {
                $('#dailyCardList').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `);
            }

            function statusBadge(status) {
                if (status === 'done' || status === 'ok') return '<span class="badge badge-success">OK</span>';
                return '<span class="badge badge-secondary">Belum</span>';
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", "&#039;");
            }

            // =========================
            // PENDING TABLE (TOP CARD)
            // =========================
            function renderPending(tasks) {
                const $tbody = $('#pendingTaskTable tbody');
                $tbody.empty();

                const count = tasks?.length || 0;
                $('#pendingCountBadge').text(count).toggle(count > 0);

                if (!count) {
                    $tbody.append(
                        `<tr><td colspan="7" class="text-center text-muted">Tidak ada task pending.</td></tr>`);
                    return;
                }

                tasks.forEach((t, i) => {
                    const tanggal = t.tanggal ? t.tanggal : '-';
                    const pic = t.pic || t.pic_name || '-';
                    const projectNo = t.project_no || '-';
                    const jenisText = t.jenis === 'project' ? 'Project' : 'Umum';
                    const pekerjaan = t.pekerjaan || t.judul_umum || '-';
                    const ket = t.keterangan || '-';

                    $tbody.append(`
                <tr>
                    <td class="text-center align-middle">${i + 1}</td>
                    <td class="align-middle">${escapeHtml(tanggal)}</td>
                    <td class="align-middle">${escapeHtml(projectNo)}</td>
                    <td class="align-middle">${escapeHtml(pic)}</td>
                    <td class="align-middle">
                        <div class="font-weight-bold">${escapeHtml(pekerjaan)}</div>
                        <div><small class="text-muted">${jenisText}</small></div>
                    </td>
                    <td class="align-middle">${escapeHtml(ket)}</td>
                    <td class="text-center align-middle"><span class="badge badge-secondary">Belum</span></td>
                </tr>
            `);
                });
            }

            function loadPending(tanggal) {
                $.get(routes.pending, {
                    tanggal
                }, function(res) {
                    renderPending(res.data || []);
                });
            }

            // =========================
            // DAILY CARDS (BOTTOM)
            // =========================
            function renderCards(res) {
                const authUserId = res.auth_user_id;
                const dailies = res.data || [];

                if (!dailies.length) {
                    $('#dailyCardList').html(
                        '<p class="text-center text-muted">Tidak ada data untuk tanggal ini.</p>');
                    return;
                }

                let html = '';
                dailies.forEach(d => {
                    const canEdit = (Number(authUserId) === Number(d.user_id));

                    const buttons = canEdit ? `
            <button class="btn btn-sm btn-secondary editBtn" data-id="${d.id}">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger deleteBtn" data-id="${d.id}">
                <i class="fas fa-trash"></i>
            </button>
            ` : '';

                    const dateText = d.tanggal ?
                        new Date(d.tanggal).toLocaleString('id-ID', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) :
                        '-';

                    const logs = d.task_logs || d.taskLogs || [];
                    const rows = logs.map((lg, idx) => {
                        const task = lg.task || {};
                        const jenis = task.jenis || '-';
                        const projectNo = (task.project && task.project.no_project) ? task.project
                            .no_project : '-';

                        let pekerjaan = '-';
                        if (jenis === 'umum') {
                            pekerjaan = task.judul_umum || '-';
                        } else {
                            const pr = task.proses_rel || task.prosesRel;
                            const namaKerjaan = pr?.kerjaan?.nama_kerjaan;
                            const namaProses = pr?.proses?.nama_proses;
                            const urutan = pr?.urutan;

                            if (namaProses || namaKerjaan) {
                                pekerjaan =
                                    `${urutan ? urutan + '. ' : ''}${namaProses || ''}${(namaProses && namaKerjaan) ? ' — ' : ''}${namaKerjaan || ''}`
                                    .trim();
                            } else {
                                pekerjaan = task.proses_id ? `Proses #${task.proses_id}` :
                                    'Project Task';
                            }
                        }

                        return `
                    <tr>
                        <td class="text-center">${idx+1}</td>
                        <td>${escapeHtml(jenis)}</td>
                        <td>${escapeHtml(projectNo)}</td>
                        <td>${escapeHtml(pekerjaan)}</td>
                        <td>${escapeHtml(lg.keterangan || '')}</td>
                        <td class="text-center">${statusBadge(lg.status_hari_ini)}</td>
                    </tr>
                `;
                    }).join('');

                    const logTable = `
                <div class="table-responsive mt-1">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:40px;">No</th>
                                <th style="width:90px;">Jenis</th>
                                <th style="width:120px;">Project</th>
                                <th>Pekerjaan</th>
                                <th>Keterangan</th>
                                <th style="width:80px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>${rows || `<tr><td colspan="6" class="text-center text-muted">Tidak ada item.</td></tr>`}</tbody>
                    </table>
                </div>
            `;

                    html += `
                <div class="card mb-3" data-id="${d.id}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">${escapeHtml(d.user?.name || '-')}</h5>
                            <small class="text-muted">${dateText}</small>
                        </div>
                        <div>${buttons}</div>
                    </div>
                    <div class="card-body">
                        ${logTable}
                        ${(d.upload_file) ? `
                                                                                        <p class="mt-3 mb-0"><strong>File:</strong>
                                                                                            <a href="/storage/${d.upload_file}" target="_blank">Download</a>
                                                                                        </p>
                                                                                    ` : ''}
                    </div>
                </div>
            `;
                });

                $('#dailyCardList').html(html);
            }

            function loadCards(tanggal) {
                showLoadingCards();
                $.get(routes.cards, {
                    tanggal
                }, function(res) {
                    renderCards(res);
                }).fail(function() {
                    $('#dailyCardList').html('<p class="text-danger text-center">Gagal memuat data.</p>');
                });
            }

            // =========================
            // RELOAD ALL
            // =========================
            function reloadAll() {
                const tanggal = $('#filterTanggal').val() || today;
                loadPending(tanggal);
                loadCards(tanggal);
            }

            reloadAll();
            $('#filterTanggal').on('change', reloadAll);

            // =========================
            // MODAL ADD: Project Select2 + Proses by Project
            // =========================
            let projectDataLoaded = false;
            let dailyProjects = [];
            let projectProcesses = {}; // {project_id: [ {id, urutan, nama_proses, nama_kerjaan}, ... ]}

            function buildProjectOptions(selectedId = null) {
                let html = `<option value="">-- Pilih Project --</option>`;
                dailyProjects.forEach(p => {
                    const sel = (String(p.id) === String(selectedId)) ? 'selected' : '';
                    html += `<option value="${p.id}" ${sel}>${escapeHtml(p.no_project)}</option>`;
                });
                return html;
            }

            function buildProcessOptions(projectId, selectedProsesId = null) {
                let html = `<option value="">-- Pilih Proses --</option>`;
                const list = projectProcesses[projectId] || [];
                list.forEach(pr => {
                    const sel = (String(pr.id) === String(selectedProsesId)) ? 'selected' : '';
                    const label =
                        `${pr.urutan ? pr.urutan + '. ' : ''}${pr.nama_proses || ''}${(pr.nama_proses && pr.nama_kerjaan) ? ' — ' : ''}${pr.nama_kerjaan || ''}`
                        .trim();
                    html += `<option value="${pr.id}" ${sel}>${escapeHtml(label)}</option>`;
                });
                return html;
            }


            // =========================
            // FRONTEND LOCK: Proses tidak boleh dipilih dobel untuk project yang sama
            // (berlaku di Pending + Today)
            // =========================
            function getUsedProsesIds(projectId, $excludeTr = null) {
                const used = new Set();

                $('#pendingTable tbody tr, #achievementTable tbody tr').each(function() {
                    const $tr = $(this);
                    if ($excludeTr && $excludeTr.length && $tr[0] === $excludeTr[0]) return;

                    const jenis = $tr.find('.jenis').val();
                    if (jenis !== 'project') return;

                    const pid = $tr.find('.project_id').val();
                    if (!pid || String(pid) !== String(projectId)) return;

                    const prosesId = $tr.find('.proses_id').val();
                    if (prosesId) used.add(String(prosesId));
                });

                return used;
            }

            function refreshProcessDropdownForRow($tr) {
                const jenis = $tr.find('.jenis').val();
                if (jenis !== 'project') return;

                const projectId = $tr.find('.project_id').val();
                if (!projectId) {
                    $tr.find('.proses_id').html('<option value="">-- Pilih Proses --</option>');
                    return;
                }

                // proses yang sudah dipakai oleh row lain utk project yg sama
                const used = getUsedProsesIds(projectId, $tr);

                // simpan selection sekarang
                const currentSelected = String($tr.find('.proses_id').val() || '');

                let html = `<option value="">-- Pilih Proses --</option>`;
                const list = projectProcesses[projectId] || [];

                list.forEach(pr => {
                    const val = String(pr.id);
                    const label =
                        `${pr.urutan ? pr.urutan + '. ' : ''}${pr.nama_proses || ''}${(pr.nama_proses && pr.nama_kerjaan) ? ' — ' : ''}${pr.nama_kerjaan || ''}`
                        .trim();

                    const isUsedElsewhere = used.has(val) && currentSelected !== val;
                    const disabled = isUsedElsewhere ? 'disabled' : '';
                    const selected = (currentSelected === val) ? 'selected' : '';

                    html += `<option value="${val}" ${selected} ${disabled}>${escapeHtml(label)}</option>`;
                });

                $tr.find('.proses_id').html(html);
            }

            function refreshAllProcessLocks() {
                $('#pendingTable tbody tr, #achievementTable tbody tr').each(function() {
                    refreshProcessDropdownForRow($(this));
                });
            }


            function initSelect2ForRow($tr) {
                $tr.find('.project-select2').select2({
                    dropdownParent: $('#tambahDailyModal'),
                    width: '100%',
                    placeholder: '-- Pilih Project --'
                });
            }

            function renumberRows() {
                $('#pendingTable tbody tr').each(function(i) {
                    $(this).find('.row-no').text(i + 1);
                });
                $('#achievementTable tbody tr').each(function(i) {
                    $(this).find('.row-no').text(i + 1);
                });
            }

            function resetModal() {
                $('#pendingTable tbody').empty();
                $('#achievementTable tbody').empty();
                $('#pendingSection').hide();

                // OPTIONAL: hide kolom aksi pending jika thead masih ada
                $('#pendingTable thead th.pending-aksi, #pendingTable tbody td.pending-aksi').hide();
            }

            // PENTING:
            // - Pending row: TIDAK ada tombol remove
            // - Pending row: jenis/project/proses/judul locked (readonly)
            // - Pending row: status & keterangan BOLEH diubah
            function pendingRowTemplate(prefill = {}) {
                const jenis = prefill.jenis || 'project';
                const isUmum = jenis === 'umum';

                return `
            <tr data-task-id="${prefill.task_id || ''}" class="readonly-row">
                <td class="text-center align-middle row-no"></td>

                <td>
                    <select class="form-control form-control-sm jenis" disabled>
                        <option value="project" ${!isUmum ? 'selected':''}>Project</option>
                        <option value="umum" ${isUmum ? 'selected':''}>Umum</option>
                    </select>
                </td>

                <td>
                    <select class="form-control form-control-sm project_id project-select2" ${isUmum ? 'disabled':''}>
                        ${buildProjectOptions(prefill.project_id)}
                    </select>
                </td>

                <td>
                    <div class="wrap-proses ${isUmum ? 'd-none' : ''}">
                        <select class="form-control form-control-sm proses_id" ${isUmum ? 'disabled':''}>
                            ${buildProcessOptions(prefill.project_id, prefill.proses_id)}
                        </select>
                    </div>

                    <div class="wrap-umum ${isUmum ? '' : 'd-none'}">
                        <input type="text"
                               class="form-control form-control-sm judul_umum"
                               value="${escapeHtml(prefill.judul_umum || '')}">
                    </div>
                </td>

                <td>
                    <textarea class="form-control form-control-sm keterangan" rows="2"
                              placeholder="Keterangan...">${escapeHtml(prefill.keterangan || '')}</textarea>
                </td>

                <td class="text-center align-middle">
                    <select class="form-control form-control-sm status">
                        <option value="lanjut" ${(prefill.status_hari_ini || 'lanjut') === 'lanjut' ? 'selected':''}>Belum</option>
                        <option value="done" ${(prefill.status_hari_ini) === 'done' ? 'selected':''}>OK</option>
                    </select>
                </td>
            </tr>
        `;
            }

            function todayRowTemplate() {
                return `
            <tr>
                <td class="text-center align-middle row-no"></td>

                <td>
                    <select class="form-control form-control-sm jenis">
                        <option value="project" selected>Project</option>
                        <option value="umum">Umum</option>
                    </select>
                </td>

                <td>
                    <select class="form-control form-control-sm project_id project-select2">
                        ${buildProjectOptions(null)}
                    </select>
                </td>

                <td>
                    <div class="wrap-proses">
                        <select class="form-control form-control-sm proses_id">
                            <option value="">-- Pilih Proses --</option>
                        </select>
                    </div>

                    <div class="wrap-umum d-none">
                        <input type="text" class="form-control form-control-sm judul_umum"
                               placeholder="Contoh: Menyapu lantai">
                    </div>
                </td>

                <td>
                    <textarea class="form-control form-control-sm keterangan" rows="2"
                              placeholder="Keterangan..."></textarea>
                </td>

                <td class="text-center align-middle">
                    <select class="form-control form-control-sm status">
                        <option value="lanjut" selected>Belum</option>
                        <option value="done">OK</option>
                    </select>
                </td>

                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger btn-remove">&times;</button>
                </td>
            </tr>
        `;
            }

            function loadCarryOverIntoModal() {
                $.get(routes.myOpenTasks, function(res) {
                    const tasks = res.data || [];

                    if (!tasks.length) {
                        $('#pendingSection').hide();
                        return;
                    }

                    $('#pendingSection').show();

                    tasks.forEach(t => {
                        $('#pendingTable tbody').append(pendingRowTemplate({
                            task_id: t.id,
                            jenis: t.jenis,
                            project_id: t.project_id,
                            proses_id: t.proses_id,
                            judul_umum: t.judul_umum,
                            keterangan: t.latest_log?.keterangan || '',
                            status_hari_ini: 'lanjut'
                        }));

                        const $tr = $('#pendingTable tbody tr').last();
                        initSelect2ForRow($tr);

                        // kunci item (jenis/project/proses/judul), tapi status & keterangan tetap editable
                        $tr.find('.project_id, .proses_id, .judul_umum').prop('disabled', true);
                        $tr.find('.status, .keterangan').prop('disabled', false);
                    });

                    renumberRows();
                });
            }

            // OPEN MODAL: load projectData once
            $('#openModalBtn').on('click', function() {
                const openNow = () => {
                    resetModal();
                    loadCarryOverIntoModal();
                    $('#tambahDailyModal').modal('show');
                };

                if (!projectDataLoaded) {
                    $.get(routes.projectData, function(res) {
                        dailyProjects = res.projects || [];
                        projectProcesses = res.projectProcesses || {};
                        projectDataLoaded = true;
                        openNow();
                    }).fail(function() {
                        Swal.fire('Gagal', 'Gagal memuat data project.', 'error');
                    });
                } else {
                    openNow();
                }
            });

            // modal shown -> default 1 row today
            $('#tambahDailyModal').on('shown.bs.modal', function() {
                if ($('#achievementTable tbody tr').length === 0) {
                    $('#achievementTable tbody').append(todayRowTemplate());
                    initSelect2ForRow($('#achievementTable tbody tr').last());
                    renumberRows();
                }
            });

            // add row today
            $('#addAchievementRow').on('click', function() {
                $('#achievementTable tbody').append(todayRowTemplate());
                initSelect2ForRow($('#achievementTable tbody tr').last());
                renumberRows();
            });

            // remove row today
            $(document).on('click', '#tambahDailyModal .btn-remove', function() {
                $(this).closest('tr').remove();
                renumberRows();
                renumberRows();
                refreshAllProcessLocks();
            });

            // toggle jenis in TODAY rows only (pending rows locked)
            $(document).on('change', '#achievementTable .jenis', function() {
                const $tr = $(this).closest('tr');
                const jenis = $(this).val();
                const isUmum = (jenis === 'umum');

                $tr.find('.wrap-proses').toggleClass('d-none', isUmum);
                $tr.find('.wrap-umum').toggleClass('d-none', !isUmum);

                $tr.find('.project_id, .proses_id').prop('disabled', isUmum);
                $tr.find('.judul_umum').prop('disabled', !isUmum);
            });

            // when project changed -> reload proses (TODAY rows)
            $(document).on('change', '#achievementTable .project_id', function() {
                const $tr = $(this).closest('tr');
                const projectId = $(this).val();
                $tr.find('.proses_id').html(buildProcessOptions(projectId));
            });

            function collectItemsFromModal() {
                const items = [];

                function collect(selector) {
                    $(selector).find('tr').each(function() {
                        const $tr = $(this);

                        const taskId = $tr.data('task-id') || null;
                        const jenis = $tr.find('.jenis').val();
                        const status_hari_ini = $tr.find('.status').val();
                        const keterangan = $tr.find('.keterangan').val() || null;

                        const project_id = $tr.find('.project_id').val() || null;
                        const proses_id = $tr.find('.proses_id').val() || null;
                        const judul_umum = $tr.find('.judul_umum').val() || null;

                        items.push({
                            task_id: taskId ? Number(taskId) : null,
                            jenis,
                            project_id: (jenis === 'project' && project_id) ? Number(project_id) :
                                null,
                            proses_id: (jenis === 'project' && proses_id) ? Number(proses_id) :
                                null,
                            judul_umum: (jenis === 'umum') ? judul_umum : null,
                            keterangan,
                            status_hari_ini,
                        });
                    });
                }

                collect('#pendingTable tbody');
                collect('#achievementTable tbody');

                return items;
            }

            $(document).on('change', '#pendingTable .project_id, #achievementTable .project_id', function() {
                const $tr = $(this).closest('tr');
                refreshProcessDropdownForRow($tr);
                refreshAllProcessLocks();
            });

            // SAVE ADD
            $('#savePekerjaanBtn').on('click', function() {
                const tanggal = $('#addDailyForm input[name="tanggal"]').val();
                const items = collectItemsFromModal();

                if (!tanggal) {
                    Swal.fire('Gagal', 'Tanggal wajib diisi.', 'error');
                    return;
                }

                if (!items.length) {
                    Swal.fire('Gagal', 'Minimal 1 item pekerjaan.', 'error');
                    return;
                }

                // validasi ringan
                for (const it of items) {
                    if (it.jenis === 'project') {
                        if (!it.project_id) {
                            Swal.fire('Gagal', 'Project wajib dipilih.', 'error');
                            return;
                        }
                        if (!it.proses_id) {
                            Swal.fire('Gagal', 'Proses wajib dipilih.', 'error');
                            return;
                        }
                    } else {
                        if (!it.judul_umum || it.judul_umum.trim() === '') {
                            Swal.fire('Gagal', 'Judul pekerjaan umum wajib diisi.', 'error');
                            return;
                        }
                    }
                }

                const formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('tanggal', tanggal);

                const upload = $('#addDailyForm input[name="upload_file"]')[0]?.files?.[0];
                if (upload) formData.append('upload_file', upload);

                formData.append('items', JSON.stringify(items));

                $.ajax({
                    url: routes.store,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        Swal.fire('Berhasil', 'Daily berhasil ditambahkan.', 'success');
                        $('#tambahDailyModal').modal('hide');
                        reloadAll();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.',
                            'error');
                    }
                });
            });


            // =========================
            // EDIT MODAL
            // =========================
            function editRowTemplate(prefill = {}) {
                const jenis = prefill.jenis || 'project';
                const isUmum = (jenis === 'umum');
                const logId = prefill.log_id || '';
                const taskId = prefill.task_id || '';

                return `
    <tr data-log-id="${logId}" data-task-id="${taskId}">
      <td class="text-center row-no"></td>

      <td>
        <select class="form-control form-control-sm jenis">
          <option value="project" ${!isUmum ? 'selected':''}>Project</option>
          <option value="umum" ${isUmum ? 'selected':''}>Umum</option>
        </select>
      </td>

      <td>
        <select class="form-control form-control-sm project_id project-select2" ${isUmum ? 'disabled':''}>
          ${buildProjectOptions(prefill.project_id)}
        </select>
      </td>

      <td>
        <div class="wrap-proses ${isUmum ? 'd-none' : ''}">
          <select class="form-control form-control-sm proses_id" ${isUmum ? 'disabled':''}>
            ${buildProcessOptions(prefill.project_id, prefill.proses_id)}
          </select>
        </div>

        <div class="wrap-umum ${isUmum ? '' : 'd-none'}">
          <input type="text" class="form-control form-control-sm judul_umum"
                 value="${escapeHtml(prefill.judul_umum || '')}">
        </div>
      </td>

      <td>
        <textarea class="form-control form-control-sm keterangan" rows="2">${escapeHtml(prefill.keterangan || '')}</textarea>
      </td>

      <td class="text-center align-middle">
        <select class="form-control form-control-sm status">
          <option value="lanjut" ${(prefill.status_hari_ini || 'lanjut') === 'lanjut' ? 'selected':''}>Belum</option>
          <option value="done" ${(prefill.status_hari_ini) === 'done' ? 'selected':''}>OK</option>
        </select>
      </td>

      <td class="text-center align-middle">
        <button type="button" class="btn btn-sm btn-danger btn-remove-edit">&times;</button>
      </td>
    </tr>
  `;
            }

            function renumberEditRows() {
                $('#editTable tbody tr').each(function(i) {
                    $(this).find('.row-no').text(i + 1);
                });
            }

            function openEditModal(id) {
                $.get(routes.edit(id), function(res) {
                    const d = res.data || res; // tergantung response kamu

                    // set hidden id + tanggal
                    $('#editDailyForm input[name="daily_id"]').val(d.id);
                    $('#editDailyForm input[name="tanggal"]').val(
                        d.tanggal ? String(d.tanggal).replace(' ', 'T') : ''
                    );

                    // file saat ini
                    if (d.upload_file) {
                        $('#currentFile').html(`
        <p>File saat ini:
          <a href="/storage/${d.upload_file}" target="_blank">Download</a>
        </p>
      `);
                    } else {
                        $('#currentFile').html(`<p class="text-muted">Tidak ada file.</p>`);
                    }

                    // isi table edit (dari taskLogs)
                    const logs = d.task_logs || d.taskLogs || [];
                    const $tbody = $('#editAchievementTable tbody');
                    $tbody.empty();

                    logs.forEach((lg, idx) => {
                        const task = lg.task || {};
                        const jenis = task.jenis || 'project';
                        const isUmum = jenis === 'umum';

                        // project & proses options (butuh dailyProjects & projectProcesses sudah load)
                        const projectOpt = buildProjectOptions(task.project_id || null);
                        const prosesOpt = buildProcessOptions(task.project_id || null, task
                            .proses_id || null);

                        $tbody.append(`
        <tr data-log-id="${lg.id}" data-task-id="${task.id}">
          <td class="text-center align-middle row-no">${idx + 1}</td>

          <td>
            <select class="form-control form-control-sm jenis">
              <option value="project" ${!isUmum ? 'selected':''}>Project</option>
              <option value="umum" ${isUmum ? 'selected':''}>Umum</option>
            </select>
          </td>

          <td>
            <select class="form-control form-control-sm project_id project-select2" ${isUmum ? 'disabled':''}>
              ${projectOpt}
            </select>
          </td>

          <td>
            <div class="wrap-proses ${isUmum ? 'd-none':''}">
              <select class="form-control form-control-sm proses_id" ${isUmum ? 'disabled':''}>
                ${prosesOpt}
              </select>
            </div>

            <div class="wrap-umum ${isUmum ? '' : 'd-none'}">
              <input type="text" class="form-control form-control-sm judul_umum"
                     value="${escapeHtml(task.judul_umum || '')}">
            </div>
          </td>

          <td>
            <textarea class="form-control form-control-sm keterangan" rows="2">${escapeHtml(lg.keterangan || '')}</textarea>
          </td>

          <td class="text-center align-middle">
            <select class="form-control form-control-sm status">
              <option value="lanjut" ${(lg.status_hari_ini || 'lanjut') === 'lanjut' ? 'selected':''}>Belum</option>
              <option value="done" ${lg.status_hari_ini === 'done' ? 'selected':''}>OK</option>
            </select>
          </td>

          <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-danger btn-remove-edit">&times;</button>
          </td>
        </tr>
      `);

                        // init select2 untuk row yang barusan
                        const $tr = $tbody.find('tr').last();
                        $tr.find('.project-select2').select2({
                            dropdownParent: $('#editDailyModal'),
                            width: '100%'
                        });
                    });

                    $('#editDailyModal').modal('show');
                });
            }


            // click edit button (pastikan ada tombol edit di card)
            $(document).on('click', '.editBtn', function() {
                const id = $(this).data('id');

                const openNow = () => {
                    openEditModal(id);
                };

                if (!projectDataLoaded) {
                    $.get(routes.projectData, function(res) {
                        dailyProjects = res.projects || [];
                        projectProcesses = res.projectProcesses || {};
                        projectDataLoaded = true;
                        openNow();
                    }).fail(function() {
                        Swal.fire('Gagal', 'Gagal memuat data project.', 'error');
                    });
                } else {
                    openNow();
                }
            });


            // toggle jenis in edit
            // change jenis edit
            $(document).on('change', '#editAchievementTable .jenis', function() {
                const $tr = $(this).closest('tr');
                const isUmum = ($(this).val() === 'umum');

                $tr.find('.wrap-proses').toggleClass('d-none', isUmum);
                $tr.find('.wrap-umum').toggleClass('d-none', !isUmum);

                $tr.find('.project_id, .proses_id').prop('disabled', isUmum);
                $tr.find('.judul_umum').prop('disabled', !isUmum);
            });


            // project change -> proses list
            // change project edit -> rebuild proses
            $(document).on('change', '#editAchievementTable .project_id', function() {
                const $tr = $(this).closest('tr');
                const projectId = $(this).val();
                $tr.find('.proses_id').html(buildProcessOptions(projectId));
            });

            // remove row edit
            $(document).on('click', '.btn-remove-edit', function() {
                $(this).closest('tr').remove();
            });

            function collectEditItems() {
                const items = [];
                $('#editTable tbody tr').each(function() {
                    const $tr = $(this);

                    items.push({
                        log_id: $tr.data('log-id') ? Number($tr.data('log-id')) : null,
                        task_id: $tr.data('task-id') ? Number($tr.data('task-id')) : null,
                        jenis: $tr.find('.jenis').val(),
                        project_id: $tr.find('.jenis').val() === 'project' ? Number($tr.find(
                            '.project_id').val() || 0) : null,
                        proses_id: $tr.find('.jenis').val() === 'project' ? Number($tr.find(
                            '.proses_id').val() || 0) : null,
                        judul_umum: $tr.find('.jenis').val() === 'umum' ? ($tr.find('.judul_umum')
                            .val() || null) : null,
                        keterangan: $tr.find('.keterangan').val() || null,
                        status_hari_ini: $tr.find('.status').val()
                    });
                });
                return items;
            }

            // SAVE EDIT
            $('#saveEditPekerjaanBtn').on('click', function() {
                const dailyId = $('#editDailyForm input[name="daily_id"]').val();
                const tanggal = $('#editDailyForm input[name="tanggal"]').val();

                const items = [];
                $('#editAchievementTable tbody tr').each(function() {
                    const $tr = $(this);
                    items.push({
                        log_id: $tr.data('log-id') ? Number($tr.data('log-id')) : null,
                        task_id: $tr.data('task-id') ? Number($tr.data('task-id')) : null,
                        jenis: $tr.find('.jenis').val(),
                        project_id: $tr.find('.jenis').val() === 'project' ? Number($tr
                            .find('.project_id').val() || 0) : null,
                        proses_id: $tr.find('.jenis').val() === 'project' ? Number($tr.find(
                            '.proses_id').val() || 0) : null,
                        judul_umum: $tr.find('.jenis').val() === 'umum' ? ($tr.find(
                            '.judul_umum').val() || null) : null,
                        keterangan: $tr.find('.keterangan').val() || null,
                        status_hari_ini: $tr.find('.status').val()
                    });
                });

                const formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('tanggal', tanggal);

                const upload = $('#editDailyForm input[name="upload_file"]')[0]?.files?.[0];
                if (upload) formData.append('upload_file', upload);

                formData.append('items', JSON.stringify(items));

                $.ajax({
                    url: routes.update(dailyId),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        Swal.fire('Berhasil', 'Daily berhasil diupdate', 'success');
                        $('#editDailyModal').modal('hide');
                        reloadAll();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan',
                            'error');
                    }
                });
            });




            // DELETE DAILY CARD
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ingin hapus?',
                    icon: 'warning',
                    showCancelButton: true,
                }).then((r) => {
                    if (!r.isConfirmed) return;

                    $.ajax({
                        url: routes.destroy(id),
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            Swal.fire('Berhasil', 'Daily berhasil dihapus.', 'success');
                            reloadAll();
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Gagal menghapus Daily.', 'error');
                        }
                    });
                });
            });

        });
    </script>




    <script>
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
    </script>
@endsection
