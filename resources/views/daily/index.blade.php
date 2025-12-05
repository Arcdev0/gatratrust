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
        .readonly-row input[type=text] {
            background: #f6f6f6 !important;
            pointer-events: none !important;
            /* tidak bisa diklik */
        }

        .readonly-row .status-radio {
            pointer-events: auto !important;
            /* status tetap bisa diganti */
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
                            <label>Plan Tomorrow</label>
                            <small class="form-text text-muted">
                                Daftar di bawah ini akan terisi otomatis dari Today’s Achievements yang statusnya
                                <strong>Belum</strong>.
                            </small>

                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-sm" id="tomorrowTable">
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
                                        <!-- Auto-generate dari Today yang Belum -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- hidden inputs untuk dikirim ke server -->
                            <div id="tomorrowHiddenInputs"></div>
                        </div>
                        {{-- <div class="form-group">
                            <label>Problem</label>
                            <div id="problemEditor" style="height: 150px;"></div>
                            <input type="hidden" name="problem">
                        </div> --}}
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
                        @method('PUT')
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
        $(document).ready(function() {


            let projectProcesses = {};
            let carryOverItems = [];
            let doneProcessesByProject = {};
            let completedProjects = [];
            let dailyProjects = [];
            let projectMap = {};
            let prosesMap = {};
            let projectDataLoaded = false;


            // Default tanggal hari ini
            let today = new Date().toISOString().split('T')[0];
            $('#filterTanggal').val(today);

            function parseJsonArrayIfPossible(value) {
                if (!value) return null;

                // Kalau sudah array langsung pakai
                if (Array.isArray(value)) return value;

                if (typeof value !== 'string') return null;

                const trimmed = value.trim();

                // --- CASE 1: JSON ARRAY "[ {...}, ... ]" ---
                if (trimmed.startsWith('[')) {
                    try {
                        const parsed = JSON.parse(trimmed);
                        return Array.isArray(parsed) ? parsed : null;
                    } catch (e) {
                        return null;
                    }
                }

                // --- CASE 2: JSON OBJECT "{ "0": {...}, ... }" ---
                if (trimmed.startsWith('{')) {
                    try {
                        const parsed = JSON.parse(trimmed);
                        // kalau hasilnya object biasa → ambil values-nya jadi array
                        if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                            return Object.values(parsed);
                        }
                        return null;
                    } catch (e) {
                        return null;
                    }
                }

                // Bukan JSON yang kita kenal → anggap data teks lama
                return null;
            }

            function renderPlanTable(rows) {
                if (!rows || !rows.length) return '-';

                let body = '';
                rows.forEach((row, i) => {
                    const jenisText = row.jenis === 'project' ? 'Project' : 'Umum';

                    // project: pakai map [id => no_project]
                    let projectCol = '-';
                    if (row.project_id) {
                        projectCol = projectMap[row.project_id] || row.project_id;
                    }

                    // proses / pekerjaan: pakai map [id => nama_proses], fallback ke pekerjaan_umum
                    let pekerjaanCol = '-';
                    if (row.proses_id) {
                        pekerjaanCol = prosesMap[row.proses_id] || row.proses_id;
                    } else if (row.pekerjaan_umum) {
                        pekerjaanCol = row.pekerjaan_umum;
                    }

                    const ketCol = row.keterangan ?? '';
                    const statusRaw = row.status;

                    const isOk = (
                        statusRaw === true ||
                        statusRaw === 1 ||
                        statusRaw === '1' ||
                        statusRaw === 'ok'
                    );

                    const statusBadge = isOk ?
                        '<span class="badge badge-success">OK</span>' :
                        '<span class="badge badge-secondary">Belum</span>';

                    body += `
                            <tr>
                                <td class="text-center align-middle">${i + 1}</td>
                                <td class="align-middle">${jenisText}</td>
                                <td class="align-middle">${projectCol}</td>
                                <td class="align-middle">${pekerjaanCol}</td>
                                <td class="align-middle">${ketCol}</td>
                                <td class="align-middle text-center">${statusBadge}</td>
                            </tr>
                        `;
                });

                return `
                        <div class="table-responsive mt-1">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th style="width: 80px;">Jenis</th>
                                        <th style="width: 120px;">Project</th>
                                        <th>Pekerjaan / Proses</th>
                                        <th>Keterangan</th>
                                        <th style="width: 80px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${body}
                                </tbody>
                            </table>
                        </div>
                    `;
            }



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

                        // 🔹 UPDATE MAP DI SINI
                        projectMap = response.project_map || {};
                        prosesMap = response.proses_map || {};

                        if (data.length > 0) {
                            data.forEach(function(item) {
                                let actionButtons = '';
                                if (authUserId === item.user_id) {
                                    actionButtons = `
                            <button class="btn btn-sm btn-primary editBtn" data-id="${item.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                                }

                                const planTodayArray = parseJsonArrayIfPossible(item
                                    .plan_today);
                                const planTomorrowArray = parseJsonArrayIfPossible(item
                                    .plan_tomorrow);

                                let planTodayHtml = planTodayArray ?
                                    renderPlanTable(planTodayArray) :
                                    (item.plan_today || '-');

                                let planTomorrowHtml = planTomorrowArray ?
                                    renderPlanTable(planTomorrowArray) :
                                    (item.plan_tomorrow || '-');

                                html += `
                        <div class="card mb-3" data-id="${item.id}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">${item.user.name}</h5>
                                    <small class="text-muted">${new Date(item.tanggal).toLocaleString()}</small>
                                </div>
                                <div>${actionButtons}</div>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Today’s Achievements:</strong></p>
                                ${planTodayHtml}

                                <p class="mt-3 mb-1"><strong>Plan Tomorrow:</strong></p>
                                ${planTomorrowHtml}

                                ${item.upload_file ? `
                                            <p class="mt-3 mb-0"><strong>File:</strong> 
                                                <a href="/storage/${item.upload_file}" target="_blank">Download</a>
                                            </p>` : ''}
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
                            '<p class="text-danger text-center">Gagal memuat data.</p>'
                        );
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

            let todayIndex = 0;

            // ========================
            // 1. Tambah row (Today/Pending)
            // ========================

            function addAchievementRow(targetTbodySelector = '#achievementTable tbody', prefill = null) {
                const index = todayIndex++;
                const projectOptions = buildProjectOptions(); // 🔥 ambil options dari data AJAX

                // normalisasi status
                let status = 'ok';
                if (prefill) {
                    const s = prefill.status;
                    if (s === 'belum' || s === '0' || s === 0 || s === false) {
                        status = 'belum';
                    }
                }

                let isUmum = prefill && prefill.jenis === 'umum';

                let newRow = `
        <tr>
            <td class="text-center align-middle row-number-today"></td>

            <!-- JENIS PEKERJAAN -->
            <td>
                <select name="achievements[${index}][jenis]" 
                        class="form-control form-control-sm jenis-select">
                    <option value="project" ${isUmum ? '' : 'selected'}>Pekerjaan Project</option>
                    <option value="umum" ${isUmum ? 'selected' : ''}>Pekerjaan Umum</option>
                </select>
            </td>

            <!-- NO PROJECT (hanya dipakai kalau jenis = project) -->
            <td class="col-project">
                <select name="achievements[${index}][project_id]"
                        class="form-control form-control-sm project-select">
                    ${projectOptions}
                </select>
            </td>

            <!-- PEKERJAAN -->
            <td>
                <div class="pekerjaan-project">
                    <select name="achievements[${index}][proses_id]" 
                            class="form-control form-control-sm pekerjaan-select">
                        <option value="">-- Pilih Proses --</option>
                    </select>
                </div>

                <div class="pekerjaan-umum d-none">
                    <input type="text" 
                        name="achievements[${index}][pekerjaan_umum]" 
                        class="form-control form-control-sm pekerjaan-umum-input"
                        placeholder="Contoh: Menyapu lantai, Perbaiki kran WC">
                </div>
            </td>

            <!-- KETERANGAN -->
            <td>
                <textarea name="achievements[${index}][keterangan]"
                        class="form-control form-control-sm keterangan-textarea"
                        rows="2"
                        placeholder="Keterangan..."></textarea>
            </td>

            <!-- STATUS -->
            <td class="align-middle">
                <div class="form-check form-check-inline">
                    <input class="form-check-input status-radio"
                        type="radio"
                        name="achievements[${index}][status]"
                        value="ok"
                        ${status === 'ok' ? 'checked' : ''}>
                    <label class="form-check-label">OK</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input status-radio"
                        type="radio"
                        name="achievements[${index}][status]"
                        value="belum"
                        ${status === 'belum' ? 'checked' : ''}>
                    <label class="form-check-label">Belum</label>
                </div>
            </td>

            <!-- AKSI -->
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-danger btn-remove-today-row">
                    &times;
                </button>
            </td>
        </tr>
    `;

                const $tbody = $(targetTbodySelector);
                $tbody.append(newRow);

                const $row = $tbody.find('tr').last();

                // Prefill jika ada
                if (prefill) {
                    $row.find('.jenis-select').val(prefill.jenis || 'project').trigger('change');

                    if (prefill.project_id) {
                        $row.find('.project-select').val(prefill.project_id).trigger('change');
                    }

                    if (prefill.jenis === 'project' && prefill.proses_id) {
                        setTimeout(() => {
                            $row.find('.pekerjaan-select').val(prefill.proses_id);
                        }, 0);
                    }

                    if (prefill.jenis === 'umum') {
                        $row.find('.pekerjaan-umum-input').val(prefill.pekerjaan_umum || '');
                    }

                    $row.find('.keterangan-textarea').val(prefill.keterangan || '');
                } else {
                    $row.find('.jenis-select').trigger('change');
                }

                updateTodayRowNumbers();
                rebuildTomorrowFromToday();
            }



            // ========================
            // 2. Tambah row ke Pending Section
            // ========================
            function addAchievementRowToPending(prefill) {
                // buat row baru di TABLE TODAY dulu
                addAchievementRow('#achievementTable tbody', prefill);

                // ambil row terakhir
                const $row = $('#achievementTable tbody tr').last();

                // pindahkan DOM row ke tabel pending
                $row.appendTo('#pendingTable tbody');

                // pending = dipastikan status BELUM
                $row.find('.status-radio[value="belum"]').prop('checked', true);

                // tandai sebagai readonly (jangan bisa di-edit user)
                $row.addClass('readonly-row');

                // hapus tombol aksi di pending
                $row.find('.btn-remove-today-row').remove();

                updateRowNumbers();
                rebuildTomorrowFromToday();
            }

            // ========================
            // 3. Update nomor row
            // ========================
            function updateRowNumbers() {
                // nomor di pending
                $('#pendingTable tbody tr').each(function(i) {
                    $(this).find('.row-number-today').text(i + 1);
                });

                // nomor di today
                $('#achievementTable tbody tr').each(function(i) {
                    $(this).find('.row-number-today').text(i + 1);
                });
            }

            function updateTodayRowNumbers() {
                $('#achievementTable tbody tr').each(function(i) {
                    $(this).find('.row-number-today').text(i + 1);
                });
            }



            // ====== AUTO GENERATE PLAN TOMORROW DARI TODAY YANG "BELUM" ======
            function rebuildTomorrowFromToday() {
                const tbody = $('#tomorrowTable tbody');
                const hidden = $('#tomorrowHiddenInputs');

                tbody.empty();
                hidden.empty();

                let rowNumber = 1;
                let idx = 0; // index untuk tomorrows[idx][...]

                $('#pendingTable tbody tr, #achievementTable tbody tr').each(function() {
                    const row = $(this);

                    const statusVal = row.find('.status-radio:checked').val();
                    if (statusVal !== 'belum') {
                        return; // hanya ambil yang BELUM
                    }

                    const jenis = row.find('.jenis-select').val(); // "project" / "umum"
                    let jenisText = (jenis === 'project') ? 'Project' : 'Umum';

                    let projectText = '-';
                    let projectId = null;
                    let pekerjaanText = '';
                    let prosesId = null;
                    let pekerjaanUmum = null;

                    if (jenis === 'project') {
                        const $projectSelect = row.find('.project-select option:selected');
                        projectId = row.find('.project-select').val();
                        projectText = $projectSelect.data('no-project') || $projectSelect
                            .text();

                        const $jobSelect = row.find('.pekerjaan-select option:selected');
                        prosesId = row.find('.pekerjaan-select').val();
                        pekerjaanText = $jobSelect.text();
                    } else {
                        // PEKERJAAN UMUM
                        pekerjaanUmum = row.find('.pekerjaan-umum-input').val();
                        pekerjaanText = pekerjaanUmum;
                        projectText = '-';
                        projectId = null;
                        prosesId = null;
                    }

                    const keteranganText = row.find('.keterangan-textarea').val();

                    let keteranganTomorrow = "";
                    if (keteranganText && keteranganText.trim() !== "") {
                        keteranganTomorrow = "Akan dilanjutkan: " + keteranganText;
                    } else {
                        keteranganTomorrow = "Akan dilanjutkan besok";
                    }

                    // tampilkan di tabel Plan Tomorrow
                    let displayRow = `
                            <tr>
                                <td class="text-center align-middle">${rowNumber++}</td>
                                <td class="align-middle">${jenisText}</td>
                                <td class="align-middle">${projectText || '-'}</td>
                                <td class="align-middle">${pekerjaanText || '-'}</td>
                                <td class="align-middle">${keteranganTomorrow}</td>
                            </tr>
                        `;
                    tbody.append(displayRow);

                    // hidden inputs untuk kirim ke backend
                    hidden.append(`
                            <input type="hidden" name="tomorrows[${idx}][jenis]" value="${jenis}">
                            <input type="hidden" name="tomorrows[${idx}][project_id]" value="${projectId ? projectId : ''}">
                            <input type="hidden" name="tomorrows[${idx}][proses_id]" value="${prosesId ? prosesId : ''}">
                            <input type="hidden" name="tomorrows[${idx}][pekerjaan_umum]" value="${pekerjaanUmum ? pekerjaanUmum.replace(/"/g, '&quot;') : ''}">
                            <input type="hidden" name="tomorrows[${idx}][keterangan]" value="${keteranganTomorrow.replace(/"/g, '&quot;')}">
                            <input type="hidden" name="tomorrows[${idx}][status]" value="0">
                        `);

                    idx++;
                });
            }

            function buildProjectOptions() {
                let html = '<option value="">-- Pilih Project --</option>';

                if (!dailyProjects || !dailyProjects.length) {
                    return html;
                }

                dailyProjects.forEach(function(p) {
                    // skip project yang sudah completed semua prosesnya
                    if (completedProjects && completedProjects.includes(p.id)) {
                        return;
                    }

                    html += `
            <option value="${p.id}" data-no-project="${p.no_project}">
                ${p.no_project}
            </option>
        `;
                });

                return html;
            }



            // ====== EVENT BINDING ======
            $(document).ready(function() {

                $('#openModalBtn').on('click', function() {
                    if (!projectDataLoaded) {
                        $.get("{{ route('daily.projectData') }}", function(res) {

                            dailyProjects = res.projects || [];
                            projectProcesses = res.projectProcesses || {};
                            doneProcessesByProject = res.doneProcessesByProject || {};
                            completedProjects = res.completedProjects || [];
                            carryOverItems = res.carryOverItems || [];

                            projectDataLoaded = true;
                            $('#tambahDailyModal').modal('show');
                        });
                    } else {
                        $('#tambahDailyModal').modal('show');
                    }
                });

                // saat modal ditampilkan
                $('#tambahDailyModal').on('shown.bs.modal', function() {
                    const $pendingTbody = $('#pendingTable tbody');
                    const $todayTbody = $('#achievementTable tbody');

                    $pendingTbody.empty();
                    $todayTbody.empty();
                    todayIndex = 0;

                    if (carryOverItems && carryOverItems.length > 0) {
                        $('#pendingSection').show();
                        carryOverItems.forEach(function(item) {
                            addAchievementRowToPending(item);
                        });
                    } else {
                        $('#pendingSection').hide();
                    }

                    // sediakan 1 baris baru untuk today
                    // addAchievementRow('#achievementTable tbody');

                    updateRowNumbers();
                    rebuildTomorrowFromToday();
                });

                // Tambah row Today
                $(document).on('click', '#addAchievementRow', function() {
                    addAchievementRow('#achievementTable tbody');
                });

                // Hapus row Today
                $(document).on('click', '.btn-remove-today-row', function() {
                    $(this).closest('tr').remove();
                    updateTodayRowNumbers();
                    rebuildTomorrowFromToday();
                });

                // Kalau status OK / Belum berubah → update Plan Tomorrow
                $(document).on('change', '.status-radio', function() {
                    rebuildTomorrowFromToday();
                });

                // Kalau project / pekerjaan / keterangan di Today diubah → update Plan Tomorrow
                $(document).on('change', '.project-select, .pekerjaan-select', function() {
                    rebuildTomorrowFromToday();
                });


                $(document).on('keyup', '.keterangan-textarea', function() {
                    rebuildTomorrowFromToday();
                });

                // Ketika jenis pekerjaan diubah
                $(document).on('change', '.jenis-select', function() {
                    const row = $(this).closest('tr');
                    const jenis = $(this).val();

                    if (jenis === 'umum') {
                        // Pekerjaan UMUM: nonaktifkan project & pekerjaan-select
                        row.find('.col-project select').prop('disabled', true);
                        row.find('.pekerjaan-project select').prop('disabled', true)
                            .closest(
                                '.pekerjaan-project').addClass('d-none');

                        // Aktifkan input text umum
                        row.find('.pekerjaan-umum-input').prop('disabled', false);
                        row.find('.pekerjaan-umum').removeClass('d-none');

                    } else {
                        // Pekerjaan PROJECT: aktifkan project & pekerjaan-select
                        row.find('.col-project select').prop('disabled', false);
                        row.find('.pekerjaan-project select').prop('disabled', false)
                            .closest(
                                '.pekerjaan-project').removeClass('d-none');

                        // Nonaktifkan input umum
                        row.find('.pekerjaan-umum-input').prop('disabled', true);
                        row.find('.pekerjaan-umum').addClass('d-none');
                    }

                    rebuildTomorrowFromToday();
                });

                // ketika NO PROJECT berubah → isi ulang PROSES
                $(document).on('change', '.project-select', function() {
                    const row = $(this).closest('tr');
                    const projectId = $(this).val();

                    const pekerjaanSelect = row.find('.pekerjaan-select');
                    pekerjaanSelect.empty();
                    pekerjaanSelect.append(
                        '<option value="">-- Pilih Proses --</option>');

                    if (!projectId || !projectProcesses[projectId]) {
                        rebuildTomorrowFromToday();
                        return;
                    }

                    const doneForProject = doneProcessesByProject[projectId] || [];

                    projectProcesses[projectId].forEach(p => {
                        // kalau proses sudah selesai → disable
                        const disabled = doneForProject.includes(p.id) ?
                            'disabled' : '';
                        pekerjaanSelect.append(
                            `<option value="${p.id}" ${disabled}>${p.urutan}. ${p.nama}</option>`
                        );
                    });

                    rebuildTomorrowFromToday();
                });


                // Save New Daily
                $('#savePekerjaanBtn').on('click', function() {
                    // $('input[name="plan_today"]').val(quillPlanToday.root.innerHTML);
                    // $('input[name="plan_tomorrow"]').val(quillPlanTomorrow.root.innerHTML);
                    // $('input[name="problem"]').val(quillProblem.root.innerHTML);

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
                            Swal.fire('Berhasil',
                                'Daily berhasil ditambahkan.', 'success'
                            );
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

                // Tombol Tutup
                $('#closeModalFooterBtn').on('click', function() {
                    $('#tambahDailyModal').modal('hide');
                });


            });




            let editIndex = 0;

            function addEditAchievementRow(prefill = null) {
                const index = editIndex++;

                // normalisasi nilai jenis & status
                const jenis = (prefill && prefill.jenis === 'umum') ? 'umum' : 'project';
                let s = prefill ? prefill.status : 'ok';
                const statusNorm =
                    (s === true || s === 1 || s === '1' || s === 'ok') ?
                    'ok' :
                    'belum';

                // build HTML option project berdasarkan data dari AJAX
                const selectedProjectId = prefill ? (prefill.project_id ?? null) : null;
                const projectOptionsHtml = buildProjectOptionsHtmlForEdit(selectedProjectId);

                let newRow = `
        <tr>
            <td class="text-center align-middle edit-row-number"></td>

            <!-- JENIS PEKERJAAN -->
            <td>
                <select name="achievements[${index}][jenis]" 
                        class="form-control form-control-sm jenis-select">
                    <option value="project" ${jenis === 'project' ? 'selected' : ''}>Pekerjaan Project</option>
                    <option value="umum" ${jenis === 'umum' ? 'selected' : ''}>Pekerjaan Umum</option>
                </select>
            </td>

            <!-- NO PROJECT -->
            <td class="col-project">
                <select name="achievements[${index}][project_id]"
                        class="form-control form-control-sm project-select">
                    ${projectOptionsHtml}
                </select>
            </td>

            <!-- PEKERJAAN -->
            <td>
                <div class="pekerjaan-project">
                    <select name="achievements[${index}][proses_id]" 
                            class="form-control form-control-sm pekerjaan-select">
                        <option value="">-- Pilih Proses --</option>
                    </select>
                </div>

                <div class="pekerjaan-umum d-none">
                    <input type="text" 
                        name="achievements[${index}][pekerjaan_umum]" 
                        class="form-control form-control-sm pekerjaan-umum-input"
                        placeholder="Contoh: Menyapu lantai, Perbaiki kran WC">
                </div>
            </td>

            <!-- KETERANGAN -->
            <td>
                <textarea name="achievements[${index}][keterangan]"
                        class="form-control form-control-sm keterangan-textarea"
                        rows="2"
                        placeholder="Keterangan..."></textarea>
            </td>

            <!-- STATUS -->
            <td class="align-middle">
                <div class="form-check form-check-inline">
                    <input class="form-check-input status-radio"
                        type="radio"
                        name="achievements[${index}][status]"
                        value="ok"
                        ${statusNorm === 'ok' ? 'checked' : ''}>
                    <label class="form-check-label">OK</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input status-radio"
                        type="radio"
                        name="achievements[${index}][status]"
                        value="belum"
                        ${statusNorm === 'belum' ? 'checked' : ''}>
                    <label class="form-check-label">Belum</label>
                </div>
            </td>

            <!-- AKSI -->
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-danger btn-remove-edit-row">
                    &times;
                </button>
            </td>
        </tr>
    `;

                const $tbody = $('#editAchievementTable tbody');
                $tbody.append(newRow);

                const $row = $tbody.find('tr').last();

                // Trigger jenis (supaya show/hide kolom umum/project)
                $row.find('.jenis-select').val(jenis).trigger('change');

                // Prefill detail project & proses
                if (jenis === 'project') {
                    if (selectedProjectId) {
                        $row.find('.project-select').val(String(selectedProjectId)).trigger('change');
                    }

                    if (prefill && prefill.proses_id) {
                        // tunggu handler .project-select mengisi daftar proses
                        setTimeout(() => {
                            $row.find('.pekerjaan-select').val(String(prefill.proses_id));
                        }, 0);
                    }
                } else {
                    // pekerjaan umum
                    $row.find('.pekerjaan-umum-input').val(prefill && prefill.pekerjaan_umum ? prefill
                        .pekerjaan_umum : '');
                }

                // keterangan
                if (prefill && prefill.keterangan) {
                    $row.find('.keterangan-textarea').val(prefill.keterangan);
                }

                updateEditRowNumbers();
                rebuildEditTomorrowFromToday();
            }

            function updateEditRowNumbers() {
                $('#editAchievementTable tbody tr').each(function(i) {
                    $(this).find('.edit-row-number').text(i + 1);
                });
            }

            function rebuildEditTomorrowFromToday() {
                const tbody = $('#editTomorrowTable tbody');
                const hidden = $('#editTomorrowHiddenInputs');

                tbody.empty();
                hidden.empty();

                let rowNumber = 1;
                let idx = 0;

                $('#editAchievementTable tbody tr').each(function() {
                    const row = $(this);
                    const statusVal = row.find('.status-radio:checked').val();

                    if (statusVal !== 'belum') {
                        return;
                    }

                    const jenis = row.find('.jenis-select').val();
                    const jenisText = (jenis === 'project') ? 'Project' : 'Umum';

                    let projectId = null;
                    let projectText = '-';
                    let prosesId = null;
                    let pekerjaanText = '';
                    let pekerjaanUmum = null;

                    if (jenis === 'project') {
                        const $projectOpt = row.find('.project-select option:selected');
                        projectId = row.find('.project-select').val();
                        projectText = $projectOpt.data('no-project') || $projectOpt.text() || '-';

                        const $prosesOpt = row.find('.pekerjaan-select option:selected');
                        prosesId = row.find('.pekerjaan-select').val();
                        pekerjaanText = $prosesOpt.text() || '';
                    } else {
                        pekerjaanUmum = row.find('.pekerjaan-umum-input').val();
                        pekerjaanText = pekerjaanUmum || '';
                    }

                    const ket = row.find('.keterangan-textarea').val() || '';
                    const ketTomorrow = ket.trim() !== '' ?
                        'Akan dilanjutkan: ' + ket :
                        'Akan dilanjutkan besok';

                    // tampil di tabel Plan Tomorrow
                    tbody.append(`
            <tr>
                <td class="text-center align-middle">${rowNumber++}</td>
                <td class="align-middle">${jenisText}</td>
                <td class="align-middle">${projectText || '-'}</td>
                <td class="align-middle">${pekerjaanText || '-'}</td>
                <td class="align-middle">${ketTomorrow}</td>
            </tr>
        `);

                    // hidden input untuk kirim ke backend
                    hidden.append(`
            <input type="hidden" name="tomorrows[${idx}][jenis]" value="${jenis}">
            <input type="hidden" name="tomorrows[${idx}][project_id]" value="${projectId || ''}">
            <input type="hidden" name="tomorrows[${idx}][proses_id]" value="${prosesId || ''}">
            <input type="hidden" name="tomorrows[${idx}][pekerjaan_umum]" value="${(pekerjaanUmum || '').replace(/"/g, '&quot;')}">
            <input type="hidden" name="tomorrows[${idx}][keterangan]" value="${ketTomorrow.replace(/"/g, '&quot;')}">
            <input type="hidden" name="tomorrows[${idx}][status]" value="0">
        `);

                    idx++;
                });
            }

            function openEditModal(id) {
                $.ajax({
                    url: `/daily/edit/${id}`,
                    method: 'GET',
                    success: function(item) {
                        // isi ID & tanggal
                        $('#editDailyForm input[name="daily_id"]').val(item.id);
                        $('#editDailyForm input[name="tanggal"]').val(
                            item.tanggal ? item.tanggal.replace(' ', 'T') : ''
                        );

                        // file saat ini
                        if (item.upload_file) {
                            $('#currentFile').html(`
                    <p>File saat ini: 
                        <a href="/storage/${item.upload_file}" target="_blank">Download</a>
                    </p>
                `);
                        } else {
                            $('#currentFile').html(`<p class="text-muted">Tidak ada file.</p>`);
                        }

                        // siapkan tabel Today (versi EDIT)
                        const $tbody = $('#editAchievementTable tbody');
                        $tbody.empty();
                        editIndex = 0;

                        const todayArray = parseJsonArrayIfPossible(item.plan_today);

                        if (todayArray && todayArray.length) {
                            todayArray.forEach(rawRow => {
                                // normalisasi status ke 'ok' / 'belum'
                                let s = rawRow.status;
                                let statusNorm =
                                    (s === true || s === 1 || s === '1' || s === 'ok') ?
                                    'ok' :
                                    'belum';

                                const row = {
                                    jenis: rawRow.jenis || 'project',
                                    project_id: rawRow.project_id ?? null,
                                    proses_id: rawRow.proses_id ?? null,
                                    pekerjaan_umum: rawRow.pekerjaan_umum ?? '',
                                    keterangan: rawRow.keterangan ?? '',
                                    status: statusNorm,
                                };

                                // fungsi ini harus mirip addAchievementRow tapi untuk modal edit
                                addEditAchievementRow(row);
                            });
                        } else {
                            // fallback: data lama (plain text) → jadikan 1 baris pekerjaan umum
                            addEditAchievementRow({
                                jenis: 'umum',
                                project_id: null,
                                proses_id: null,
                                pekerjaan_umum: '',
                                keterangan: item.plan_today || '',
                                status: 'ok',
                            });
                        }

                        // generate Plan Tomorrow versi edit
                        rebuildEditTomorrowFromToday();

                        $('#editDailyModal').modal('show');
                    }
                });
            }

            $(document).on('click', '.editBtn', function() {
                const id = $(this).data('id');
                if (!projectDataLoaded) {
                    $.get("{{ route('daily.projectData') }}", function(res) {
                        dailyProjects = res.projects || [];
                        projectProcesses = res.projectProcesses || {};
                        doneProcessesByProject = res.doneProcessesByProject || {};
                        completedProjects = res.completedProjects || [];
                        carryOverItems = res.carryOverItems || [];

                        projectDataLoaded = true;
                        openEditModal(id); // baru buka modal edit
                    });
                } else {
                    // data project sudah ada → langsung buka modal edit
                    openEditModal(id);
                }
            });

            // Tambah row baru di modal Edit
            $(document).on('click', '#editAddAchievementRow', function() {
                addEditAchievementRow();
            });

            // Hapus row di modal Edit
            $(document).on('click', '.btn-remove-edit-row', function() {
                $(this).closest('tr').remove();
                updateEditRowNumbers();
                rebuildEditTomorrowFromToday();
            });

            // Perubahan status / project / proses / keterangan di Edit
            $(document).on('change',
                '#editAchievementTable .status-radio, #editAchievementTable .project-select, #editAchievementTable .pekerjaan-select, #editAchievementTable .jenis-select',
                function() {
                    // kalau jenis ganti, atur tampilan project vs umum
                    if ($(this).hasClass('jenis-select')) {
                        const row = $(this).closest('tr');
                        const jenis = $(this).val();

                        if (jenis === 'umum') {
                            row.find('.col-project select').prop('disabled', true);
                            row.find('.pekerjaan-project select')
                                .prop('disabled', true)
                                .closest('.pekerjaan-project')
                                .addClass('d-none');

                            row.find('.pekerjaan-umum-input').prop('disabled', false);
                            row.find('.pekerjaan-umum').removeClass('d-none');
                        } else {
                            row.find('.col-project select').prop('disabled', false);
                            row.find('.pekerjaan-project select')
                                .prop('disabled', false)
                                .closest('.pekerjaan-project')
                                .removeClass('d-none');

                            row.find('.pekerjaan-umum-input').prop('disabled', true);
                            row.find('.pekerjaan-umum').addClass('d-none');
                        }
                    }

                    rebuildEditTomorrowFromToday();
                });

            $(document).on('keyup', '#editAchievementTable .keterangan-textarea', function() {
                rebuildEditTomorrowFromToday();
            });

            // ketika NO PROJECT berubah di Edit → isi ulang PROSES
            $(document).on('change', '#editAchievementTable .project-select', function() {
                const row = $(this).closest('tr');
                const projectId = $(this).val();

                const pekerjaanSelect = row.find('.pekerjaan-select');
                pekerjaanSelect.empty();
                pekerjaanSelect.append('<option value="">-- Pilih Proses --</option>');

                if (!projectId || !projectProcesses[projectId]) {
                    rebuildEditTomorrowFromToday();
                    return;
                }

                const doneForProject = doneProcessesByProject[projectId] || [];

                projectProcesses[projectId].forEach(p => {
                    const isDone = doneForProject.includes(p.id);
                    const disabled = isDone ? 'disabled' : '';

                    pekerjaanSelect.append(
                        `<option value="${p.id}" ${disabled}>${p.urutan}. ${p.nama}</option>`
                    );
                });

                rebuildEditTomorrowFromToday();
            });

            // Save Edited Daily
            $('#saveEditPekerjaanBtn').on('click', function() {
                const id = $('#editDailyForm input[name="daily_id"]').val();

                // pastikan Plan Tomorrow sudah rebuild → hidden input keisi
                rebuildEditTomorrowFromToday();

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
                        let err = xhr.responseJSON.errors || {};
                        let msg = '';
                        for (const key in err) {
                            msg += `${err[key]}<br>`;
                        }
                        Swal.fire('Gagal', msg || 'Terjadi kesalahan.', 'error');
                    }
                });
            });

            // Tombol Tutup modal Edit
            $('#closeModalEditFooterBtn').on('click', function() {
                $('#editDailyModal').modal('hide');
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

            // $('#tambahDailyModal').on('hidden.bs.modal', function() {
            //     if (quillPlanToday) quillPlanToday.setContents([]);
            //     if (quillPlanTomorrow) quillPlanTomorrow.setContents([]);
            //     if (quillProblem) quillProblem.setContents([]);
            // });

            $('#editDailyModal').on('hidden.bs.modal', function() {
                if (quillEditPlanToday) quillEditPlanToday.setContents([]);
                if (quillEditPlanTomorrow) quillEditPlanTomorrow.setContents([]);
                if (quillEditProblem) quillEditProblem.setContents([]);
            });


        });
    </script>
@endsection
