@extends('layout.app')

@section('title', 'Detail Project')

@section('content')
    <style>
        /* Base Styles */
        .timeline-container {
            width: 100%;
            margin: 20px 0;
        }

        /* Desktop Timeline (Horizontal) */
        .timeline-horizontal {
            display: flex;
            align-items: center;
            overflow-x: auto;
            padding: 30px 0px;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 100px;
            flex-shrink: 0;
            padding: 0 15px;
        }

        .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            position: relative;
            z-index: 2;
        }

        .circle.done {
            background-color: #4CAF50;
        }

        .circle.pending {
            background-color: #e0e0e0;
        }

        .circle:hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .step-label {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
            width: 100%;
            word-wrap: break-word;
            color: #333;
        }

        /* Arrow Connectors */
        .arrow-connector {
            position: relative;
            width: 50px;
            /* bisa disesuaikan */
            height: 50px;
            flex-shrink: 0;
        }

        .arrow-line {
            width: 95%;
            height: 2px;
            background-color: #e0e0e0;
            position: absolute;
            left: 0;
            z-index: 1;
        }

        .arrow-line.done {
            background-color: #4CAF50;
        }

        .arrow-head {
            position: absolute;
            right: 0;
            margin-top: 1px;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-left: 8px solid #e0e0e0;
        }

        .arrow-head.done {
            border-left-color: #4CAF50;
        }

        /* Mobile Timeline (Vertical) */
        @media (max-width: 768px) {
            .timeline-horizontal {
                display: none;
            }

            .timeline-vertical {
                display: block;
                padding: 20px;
            }

            .timeline-step {
                flex-direction: row;
                align-items: center;
                min-width: 100%;
                padding: 10px 0;
                margin-bottom: 15px;
            }

            .step-label {
                margin-top: 0;
                margin-left: 15px;
                text-align: left;
                flex: 1;
            }

            .arrow-line {
                position: absolute;
                top: 50px;
                left: 20px;
                width: 2px;
                height: calc(100% - 40px);
            }

            .arrow-head {
                display: none;
            }

            .timeline-step:last-child .arrow-line {
                display: none;
            }
        }

        @media (min-width: 769px) {
            .timeline-vertical {
                display: none;
            }
        }

        .comment-card {
            background-color: #f0f0f0;
            /* abu-abu terang */
            border: 1px solid #ccc;
            /* garis tepi abu-abu */
            border-radius: 8px;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        #agenda-proyek {
            margin: 20px;
            border: 1px solid #ccc;
        }

        #agenda-proyek h2 {
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin: 0;
        }

        .timeline-container {
            display: flex;
        }

        /* Fixed left column */
        .left-column {
            flex: 0 0 250px;
            border-right: 1px solid #ddd;
        }

        .left-column table {
            border-collapse: collapse;
            width: 100%;
        }

        .left-column th,
        .left-column td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            background-color: #f9f9f9;
        }

        /* Scrollable timeline */
        .right-timeline {
            overflow-x: auto;
            width: 100%;
        }

        .right-timeline table {
            border-collapse: collapse;
            min-width: 1200px;
        }

        .right-timeline th,
        .right-timeline td {
            border: 1px solid #ddd;
            text-align: center;
            min-width: 20px;
            padding: 2px;
        }

        .right-timeline .month-header {
            background-color: #ddd;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }

        .right-timeline .date-header {
            background-color: #f0f0f0;
            font-weight: normal;
        }

        .timeline-bar {
            height: 25.5px;
            border-radius: 5px;
        }

        /* Status colors */
        .plan {
            background-color: orange;
        }

        /* 🟧 Plan */
        .action-in {
            background-color: green;
        }

        /* 🟩 Action in Plan */
        .action-early {
            background-color: rgba(0, 162, 255, 0.973);
        }

        /* 🟦 Action before Plan */
        .action-late {
            background-color: red;
        }

        /* 🟥 Action after Plan */

        /* Legend Styles */
        .legend {
            display: flex;
            margin-top: 10px;
            gap: 20px;
            font-size: 14px;
            justify-content: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
        }

        .legend-plan {
            background-color: orange;
        }

        .legend-action-in {
            background-color: green;
        }

        .legend-action-early {
            background-color: rgba(0, 162, 255, 0.973);
        }

        .legend-action-late {
            background-color: red;
        }

        /* Mobile View */
        @media (max-width: 768px) {
            #agenda-proyek {
                margin: 10px;
                border: none;
            }

            #agenda-proyek h2 {
                font-size: 18px;
                padding: 8px;
            }

            .timeline-container {
                display: block;
                /* Stack vertically */
            }

            .left-column {
                border-right: none;
                margin-bottom: 10px;
            }

            .left-column table,
            .right-timeline table {
                width: 100%;
                min-width: auto;
            }

            .right-timeline {
                overflow-x: scroll;
            }

            .right-timeline table {
                min-width: 600px;
                /* still scrollable horizontally */
            }

            .right-timeline th,
            .right-timeline td {
                font-size: 12px;
                min-width: 15px;
                padding: 1px;
            }

            /* Timeline bar smaller */
            .timeline-bar {
                height: 15px;
            }

            /* Legend stacked vertically */
            .legend {
                flex-direction: column;
                align-items: flex-start;
                font-size: 12px;
                gap: 5px;
            }

            .legend-item {
                gap: 8px;
            }

            .legend-color {
                width: 15px;
                height: 15px;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="font-weight-bold">Detail Project</h3>
                    <a href="{{ route('projects.tampilan') }}" class="btn btn-primary">
                        Back to Project
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5>Informasi Project ({{ $project->client->company ?? '-' }})</h5>
                        <div class="row my-3">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th width="30%">No. Project</th>
                                        <td>{{ $project->no_project }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Project</th>
                                        <td>{{ $project->nama_project }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Company</th>
                                        <td>{{ $project->client->company ?? '-' }}</td>
                                    </tr> --}}
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th>Jenis Kerjaan</th>
                                        <td>{{ $project->kerjaan->nama_kerjaan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Oleh</th>
                                        <td>{{ $project->creator->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="agenda-proyek">
                                    <h2>TIMELINE PROJECT</h2>
                                    <div class="timeline-container">
                                        <!-- Fixed title column -->
                                        <div class="left-column">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th style="height:50px;">Deskripsi Pekerjaan</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="title-column"></tbody>
                                            </table>
                                        </div>

                                        <!-- Scrollable timeline -->
                                        <div class="right-timeline">
                                            <table>
                                                <thead>
                                                    <tr id="month-row"></tr>
                                                    <tr id="date-row"></tr>
                                                </thead>
                                                <tbody id="timeline-body"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Legend -->
                                    <div class="legend">
                                        <div class="legend-item">
                                            <div class="legend-color legend-plan"></div> Plan (Rencana)
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color legend-action-in"></div> Action (Dalam Plan)
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color legend-action-early"></div> Early Start (Action Sebelum
                                            Plan)
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color legend-action-late"></div> Late Finish (Action Setelah
                                            Plan)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="container my-4">
                            <h5 class="mt-4">Proses {{ $project->kerjaan->nama_kerjaan }}</h5>
                            <div class="timeline-container">
                                <!-- Desktop Version -->
                                <div class="timeline-horizontal">
                                    @php
                                        $stepKeys = array_keys($steps);
                                        $stepCount = count($stepKeys);
                                    @endphp

                                    @foreach ($stepKeys as $i => $key)
                                        @php
                                            $step = $steps[$key];
                                            $status = $stepStatuses[$key];
                                            $listProsesId = $stepProcessIds[$key];
                                            $urutan = $stepUrutan[$key];
                                        @endphp

                                        <div class="timeline-step">
                                            <div class="circle {{ $status === 'done' ? 'done' : 'pending' }}"
                                                onclick="showStepModal('{{ $step }}', '{{ $listProsesId }}', '{{ $status }}', '{{ $urutan }}')"
                                                data-step="{{ $step }}" data-id="{{ $listProsesId }}"
                                                data-urutan="{{ $urutan }}">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <p class="step-label">{{ $step }}</p>
                                        </div>

                                        @if ($i < $stepCount - 1)
                                            @php
                                                $nextKey = $stepKeys[$i + 1];
                                                $nextStatus = $stepStatuses[$nextKey];
                                                $isArrowDone = $status === 'done' && $nextStatus === 'done';
                                            @endphp
                                            <div class="arrow-connector">
                                                <div class="arrow-line {{ $isArrowDone ? 'done' : '' }}"></div>
                                                <div class="arrow-head {{ $isArrowDone ? 'done' : '' }}"></div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Mobile Version -->
                                <div class="timeline-vertical">
                                    @foreach ($stepKeys as $i => $key)
                                        @php
                                            $step = $steps[$key];
                                            $status = $stepStatuses[$key];
                                            $listProsesId = $stepProcessIds[$key];
                                            $urutan = $stepUrutan[$key];
                                        @endphp

                                        <div class="timeline-step">
                                            <div class="circle {{ $status === 'done' ? 'done' : 'pending' }}"
                                                onclick="showStepModal('{{ $step }}', '{{ $listProsesId }}', '{{ $status }}', '{{ $urutan }}')"
                                                data-step="{{ $step }}" data-id="{{ $listProsesId }}"
                                                data-urutan="{{ $urutan }}">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <p class="step-label">{{ $step }}</p>

                                            @if ($i < $stepCount - 1)
                                                @php
                                                    $nextKey = $stepKeys[$i + 1];
                                                    $nextStatus = $stepStatuses[$nextKey];
                                                    $isArrowDone = $status === 'done' && $nextStatus === 'done';
                                                @endphp
                                                <div class="arrow-line {{ $isArrowDone ? 'done' : '' }}"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="timelineModal" tabindex="-1" role="dialog"
                            aria-labelledby="timelineModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <!-- ganti jadi modal-lg -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Proses <strong id="modalStepName"></strong></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="modalListProsesId" />
                                        <input type="hidden" id="modalUrutanInput" name="urutan">
                                        <div class="row">
                                            <!-- Kolom Kiri: Data File yang sudah diupload -->

                                            <div
                                                class="@if (Auth::user()->role_id == 1) col-md-6 border-end @else col-md-12 @endif">
                                                <h6>Data Terunggah</h6>
                                                <div id="dataFile">
                                                    <p class="text-muted">Belum ada data yang di-upload</p>
                                                </div>
                                            </div>

                                            <!-- Kolom Kanan: Form upload -->
                                            @if (Auth::user()->role_id == 1)
                                                <div class="col-md-6">
                                                    <h6>Upload File Baru</h6>
                                                    <div id="fileInputContainer">
                                                        <!-- Input akan ditambahkan di sini -->
                                                    </div>
                                                    <button type="button" id="addFileBtn"
                                                        class="btn btn-sm btn-outline-primary mt-2">
                                                        + Tambah File
                                                    </button>

                                                </div>
                                            @endif
                                        </div>
                                        <!-- Form Start and End Action -->
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <label for="startActionDate" class="form-label">Tanggal Mulai</label>
                                                <input type="date" class="form-control" id="startActionDate"
                                                    name="start_action">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="endActionDate" class="form-label">Tanggal Selesai</label>
                                                <input type="date" class="form-control bulanDefault"
                                                    id="endActionDate" name="end_action">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="container pt-2 pb-5">
                                                <!-- Form Komentar -->
                                                <div class="mb-3">
                                                    <label for="komentar" class="form-label">Tulis Komentar</label>
                                                    <textarea class="form-control" id="komentar" rows="3" placeholder="Tulis komentar kamu di sini..."></textarea>
                                                </div>
                                                <button class="btn btn-primary mb-4" id="btnTambahKomentar">Tambah
                                                    Komentar</button>

                                                <!-- List Komentar -->
                                                <div id="listKomentar"
                                                    style="max-height: 300px; overflow-y: auto; padding-right: 10px;">
                                                    <!-- Komentar Dummy -->
                                                    {{-- <div class="card mb-3 comment-card">
                                                        <div class="card-body d-flex">
                                                            <img src="{{ asset('template/img/user_main.jpg') }}"
                                                                alt="User" class="comment-avatar mr-5">
                                                            <div class="flex-grow-1">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-start">
                                                                    <div>
                                                                        <h6 class="mb-0">Jane Doe <span
                                                                                class="badge bg-secondary">Admin</span></h6>
                                                                        <div class="comment-meta">31 Mei 2025, 12:30</div>
                                                                    </div>
                                                                    <div>
                                                                        <button
                                                                            class="btn btn-sm btn-outline-danger btn-sm">&times;</button>
                                                                    </div>
                                                                </div>
                                                                <p class="mt-2 mb-0">Ini adalah komentar dummy untuk contoh
                                                                    tampilan.</p>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        @if (Auth::user()->role_id == 1)
                                            <button class="btn btn-primary btn-sm" id="saveFile">Simpan</button>
                                            <button class="btn btn-success btn-sm" id="markAsDoneBtn">Tandai
                                                Selesai</button>
                                            <button class="btn btn-secondary btn-sm" id="unmarkAsDoneBtn">Cabut Tanda
                                                Selesai</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const timelineData = @json($timelineData);
            const startDate = new Date("{{ \Carbon\Carbon::parse($project->start)->format('Y-m-d') }}");
            const endDate = new Date("{{ \Carbon\Carbon::parse($project->end)->format('Y-m-d') }}");
            const $monthRow = $("#month-row");
            const $dateRow = $("#date-row");

            let dates = [];
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli",
                "Agustus", "September", "Oktober", "November", "Desember"
            ];

            let currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                dates.push(new Date(currentDate));
                currentDate.setDate(currentDate.getDate() + 1);
            }

            // Group dates by month
            let monthMap = {};
            dates.forEach(date => {
                const monthKey = `${date.getFullYear()}-${date.getMonth()}`;
                if (!monthMap[monthKey]) {
                    monthMap[monthKey] = [];
                }
                monthMap[monthKey].push(date);
            });

            // Render month row
            $.each(monthMap, function(key, days) {
                const month = monthNames[days[0].getMonth()];
                const year = days[0].getFullYear();
                const colspan = days.length;
                $monthRow.append(`<th class="month-header" colspan="${colspan}">${month} ${year}</th>`);
            });

            // Render date row
            dates.forEach(date => {
                const day = date.getDate();
                $dateRow.append(`<th class="date-header">${day}</th>`);
            });

            // Render titles & timeline bars
            timelineData.forEach(item => {
                $("#title-column").append(`<tr><td>${item.title}</td></tr>`);

                let $row = $("<tr></tr>");
                dates.forEach(date => {
                    let isoDate = date.toISOString().split('T')[0];
                    let $cell = $("<td></td>");

                    const isInPlan = isoDate >= item.start_plan && isoDate <= item.end_plan;
                    const isInAction = isoDate >= item.start_action && isoDate <= item.end_action;

                    if (isInAction && isoDate < item.start_plan) {
                        $cell.append(
                            `<div class="timeline-bar action-early"></div>`);
                    } else if (isInAction && isoDate > item.end_plan) {
                        $cell.append(
                            `<div class="timeline-bar action-late"></div>`);
                    } else if (isInAction && isInPlan) {
                        $cell.append(`<div class="timeline-bar action-in"></div>`);
                    } else if (isInPlan) {
                        $cell.append(`<div class="timeline-bar plan"></div>`);
                    }

                    $row.append($cell);
                });
                $("#timeline-body").append($row);
            });
        });
    </script>

    <script>
        function showStepModal(step, prosesId, status, urutan) {
            $('#modalStepName').text(step);
            $('#modalListProsesId').val(prosesId);
            $('#modalUrutan').text(urutan);
            $('#modalUrutanInput').val(urutan);

            $('#fileInputContainer').empty();

            const userRoleId = {{ Auth::user()->role_id }};

            if (status === 'done') {
                $('#markAsDoneBtn').hide();
                $('#unmarkAsDoneBtn').show();
            } else {
                $('#markAsDoneBtn').show();
                $('#unmarkAsDoneBtn').hide();
            }

            if (userRoleId !== 1) {
                $('#saveFile').hide();
                $('#markAsDoneBtn').hide();
                $('#unmarkAsDoneBtn').hide();
                $('#addFileBtn').hide();
                $('#fileInputContainer').addClass('d-none');
            } else {
                $('#saveFile').show();
                $('#markAsDoneBtn').show();
                $('#unmarkAsDoneBtn').show();
                $('#addFileBtn').show();
                $('#fileInputContainer').removeClass('d-none');
            }
        }

        $(document).ready(function() {
            const path = window.location.pathname;
            const match = path.match(/\/(\d+)$/);
            const id = match ? match[1] : null;

            let currentStep = null;

            function loadKomentar(projectId, listProsesId, urutanId) {
                $.ajax({
                    url: `/project-detail/comments`,
                    method: 'GET',
                    data: {
                        project_id: projectId,
                        list_proses_id: listProsesId,
                        urutan_id: urutanId
                    },
                    success: function(data) {
                        let html = '';

                        const userRoleId = {{ Auth::user()->role_id }};
                        const isAdmin = userRoleId === 1;

                        if (data.length === 0) {
                            html = `
                    <div class="text-center text-muted mt-3">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada komentar.</p>
                    </div>`;
                        } else {
                            data.forEach(k => {
                                html += `
                    <div class="card mb-3 comment-card">
                        <div class="card-body d-flex">
                            <img src="/template/img/user_main.jpg" alt="User" class="comment-avatar mr-5">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                       <h6 class="mb-0">${k.user_name ?? 'Unknown User'}
                                            ${k.role_name ? `
                                                                <span class="badge ${k.role_name === 'Client' ? 'bg-white text-dark border' : (k.role_name === 'Admin' ? 'bg-success text-white' : 'bg-secondary')}">
                                                                    ${k.role_name}
                                                                </span>` : ''}
                                        </h6>
                                        <div class="comment-meta">${formatDate(k.created_at)}</div>
                                    </div>
                                    <div>
                                        ${isAdmin ? `<button class="btn btn-sm btn-outline-danger btn-delete-komentar" data-id="${k.id}">&times;</button>` : ''}
                                    </div>
                                </div>
                                <p class="mt-2 mb-0">${k.comment}</p>
                            </div>
                        </div>
                    </div>`;
                            });
                        }

                        $('#listKomentar').html(html);
                    },
                    error: function() {
                        $('#listKomentar').html('<p class="text-danger">Gagal memuat komentar.</p>');
                    }
                });
            }

            function formatDate(datetimeStr) {
                const d = new Date(datetimeStr);
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function getTodayDate() {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            }

            $('.circle').click(function() {
                var step = $(this).data('step');
                $('#modalStepName').text(step);
                currentStep = $(this).data('step');

                const userRoleId = parseInt(@json(Auth::user()->role_id));
                const isAdmin = userRoleId === 1;

                let projectId = id;
                let list_proses_id = $('#modalListProsesId').val();
                let urutan = $('#modalUrutanInput').val();

                loadKomentar(projectId, list_proses_id, urutan);

                $.ajax({
                    url: '/project/' + projectId + '/uploaded-files',
                    type: 'GET',
                    data: {
                        list_proses_id: list_proses_id,
                        urutan_id: urutan
                    },
                    success: function(response) {
                        if (response.length === 0) {
                            $('#dataFile').html(
                                '<p class="text-muted">Belum ada data yang di-upload</p>');
                        } else {
                            let html = '<ul class="list-group">';
                            response.forEach(function(file) {
                                let uploadedAt = new Date(file.uploaded_at);
                                let formattedDate = uploadedAt.toLocaleString('id-ID', {
                                    dateStyle: 'medium',
                                    timeStyle: 'short'
                                });


                                const isRestrictedProcess = list_proses_id ==
                                    4;

                                // Tentukan apakah harus menampilkan tombol eye
                                const showEyeButton = !isRestrictedProcess || (
                                    isRestrictedProcess && isAdmin);

                                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-start flex-column">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <span>
                                <strong>${file.name}</strong>
                            </span>
                            <div>`;

                                // Tampilkan tombol eye jika memenuhi kondisi
                                if (showEyeButton) {
                                    html += `
                                <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-primary me-2" title="Lihat File">
                                    <i class="fas fa-eye"></i>
                                </a>`;
                                }

                                if (isAdmin) {
                                    html += `
                                <button class="btn btn-sm btn-outline-danger btn-delete-file" data-id="${file.id}" title="Hapus File">
                                    <i class="fas fa-trash"></i>
                                </button>`;
                                }

                                html += `
                            </div>
                        </div>
                        <small class="text-muted">Diunggah pada: ${formattedDate}</small>
                    </li>`;
                            });

                            html += '</ul>';
                            $('#dataFile').html(html);



                        }


                        if (response.length > 0) {
                            let startDate = response[0].start_action.split(' ')[
                                0]; // Ambil bagian tanggal saja
                            let endDate = response[0].end_action.split(' ')[
                                0]; // Ambil bagian tanggal saja

                            $('#startActionDate').val(startDate);
                            $('#endActionDate').val(endDate);
                        } else {
                            $('#startActionDate').val('');
                            $('#endActionDate').val('');
                        }

                    },
                    error: function(xhr) {
                        $('#dataFile').html(
                            '<p class="text-danger">Gagal memuat data file.</p>');
                    }
                });

                $('#startActionDate').on('change', function() {
                    let startDateAction = $(this).val();
                    $('#endActionDate').attr('min', startDateAction);
                    let endDateAction = $('#endActionDate').val();
                    if (endDateAction && endDateAction < startDateAction) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanggal tidak valid',
                            text: 'Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.',
                        });
                        $('#endActionDate').val('');
                    }
                });

                $('#endActionDate').on('change', function() {
                    let startDateAction = $('#startActionDate').val();
                    let endDateAction = $(this).val();

                    if (startDateAction && endDateAction < startDateAction) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanggal tidak valid',
                            text: 'Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.',
                        });
                        $(this).val('');
                    }
                });


                if (!isAdmin) {
                    $('#startActionDate').closest('.form-group').hide();
                    $('#endActionDate').closest('.form-group').hide();
                } else {
                    $('#startActionDate').closest('.form-group').show();
                    $('#endActionDate').closest('.form-group').show();
                }

                $('#timelineModal').modal('show');
            });

            $('#addFileBtn').on('click', function() {
                const inputGroup = $(`
        <div class="form-group position-relative border rounded p-3 mb-2 bg-light">
             <button type="button" class="btn btn-sm btn-danger float-right mb-2 remove-file-btn">
                &times;
            </button>
            <input type="text" class="form-control mb-2" name="fileLabel[]" placeholder="Contoh : Sertifikat">
            <input type="file" class="form-control mb-2" name="fileInput[]">
        </div>

    `);

                $('#fileInputContainer').append(inputGroup);
            });

            // Event delegasi untuk hapus input file
            $('#fileInputContainer').on('click', '.remove-file-btn', function() {
                $(this).closest('.form-group').remove();
            });


            $('#saveFile').on('click', function() {
                const formData = new FormData();
                const urutan = $('#modalUrutanInput').val();
                const list_proses_id = $('#modalListProsesId').val();
                const start_action = $('#startActionDate').val();
                const end_action = $('#endActionDate').val();

                let adaFileKosong = false;

                // Ambil semua input label dan file
                $('#fileInputContainer .form-group').each(function(index, element) {
                    const label = $(element).find('input[type="text"]').val();
                    const fileInput = $(element).find('input[type="file"]')[0];
                    const file = fileInput.files[0];

                    // Validasi: pastikan file tidak kosong
                    if (!file) {
                        adaFileKosong = true;
                    }

                    formData.append(`fileLabel[${index}]`, label ?? '');
                    formData.append(`fileInput[${index}]`, file ?? '');
                });

                if (adaFileKosong) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Semua input file harus diisi sebelum mengunggah.',
                    });
                    return;
                }

                formData.append('list_proses_id', list_proses_id);
                formData.append('project_id', id);
                formData.append('urutan_id', urutan);
                formData.append('start_action', start_action);
                formData.append('end_action', end_action);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Kirim ke server via AJAX
                $.ajax({
                    url: '/projects/upload-step-files',
                    type: 'POST',
                    data: formData,
                    processData: false, // jangan ubah FormData ke query string
                    contentType: false, // biarkan browser otomatis set boundary multipart
                    timeout: 0, // tambahkan timeout 0 agar file besar tidak putus
                    beforeSend: function() {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Mengunggah...',
                            text: 'Mohon tunggu sementara file diunggah.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'File berhasil disimpan',
                        }).then(() => {
                            // $('#timelineModal').modal('hide');
                            location
                                .reload();
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengunggah file. Coba periksa ukuran file atau koneksi.',
                        });
                    }
                });
            });

            $('#btnTambahKomentar').on('click', function() {
                let $btn = $(this);
                let projectId = id; // asumsi id global sudah terdefinisi
                let list_proses_id = $('#modalListProsesId').val();
                let urutan_id = $('#modalUrutanInput').val();
                let komentar = $('#komentar').val().trim();

                if (!komentar) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Komentar kosong',
                        text: 'Silakan isi komentar terlebih dahulu.'
                    });
                    return;
                }

                // Ubah tombol jadi loading
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengirim...'
                );

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/project-detail/comments/store',
                    method: 'POST',
                    data: {
                        project_id: projectId,
                        list_proses_id: list_proses_id,
                        urutan_id: urutan_id,
                        comment: komentar
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        });
                        $('#komentar').val('');
                        loadKomentar(projectId, list_proses_id, urutan_id);
                    },
                    error: function(xhr) {
                        let errMsg = xhr.responseJSON?.error || xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat menyimpan komentar.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errMsg
                        });
                    },
                    complete: function() {
                        // Kembalikan tombol ke normal
                        $btn.prop('disabled', false).html('Tambah Komentar');
                    }
                });
            });

            $(document).on('click', '.btn-delete-komentar', function() {
                const komentarId = $(this).data('id');

                let projectId = id;
                let list_proses_id = $('#modalListProsesId').val();
                let urutan_id = $('#modalUrutanInput').val();

                Swal.fire({
                    title: 'Yakin ingin menghapus komentar ini?',
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: `/project-detail/comments/${komentarId}`,
                            method: 'DELETE',
                            success: function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Komentar telah dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadKomentar(projectId, list_proses_id, urutan_id);
                            },
                            error: function() {
                                Swal.fire('Gagal', 'Komentar gagal dihapus', 'error');
                            }
                        });
                    }
                });
            });



            $(document).on('click', '.btn-delete-file', function() {
                let fileId = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "File yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/project/delete-file/' + fileId,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'File berhasil dihapus!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Refresh data (klik ulang step)
                                $('.circle[data-step="' + $('#modalStepName').text() +
                                    '"]').click();
                            },
                            error: function(err) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat menghapus file.',
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#markAsDoneBtn', function() {
                let projectId = id;
                let list_proses_id = $('#modalListProsesId').val();
                let urutan = $('#modalUrutanInput').val();


                // Tampilkan konfirmasi sebelum mengubah status
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menandai proses ini sudah selesai?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tandai Selesai',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim request AJAX
                        $.ajax({
                            url: '/project/' + projectId + '/mark-step-done',
                            type: 'POST',
                            data: {
                                list_proses_id: list_proses_id,
                                urutan_id: urutan,
                                _token: $('meta[name="csrf-token"]').attr(
                                    'content') // CSRF token untuk Laravel
                            },
                            beforeSend: function() {
                                // Tampilkan loading indicator
                                $('#markAsDoneBtn').prop('disabled', true).html(
                                    '<i class="fas fa-spinner fa-spin"></i> Memproses...'
                                );
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Status step berhasil diubah menjadi selesai',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Refresh halaman atau update UI
                                        location
                                            .reload(); // Opsi 1: Refresh halaman

                                        // Opsi 2: Update UI tanpa refresh (jika prefer)
                                        // $('.circle[data-id="' + list_proses_id + '"]').removeClass('pending').addClass('done');
                                        // $('#timelineModal').modal('hide');
                                    });
                                } else {
                                    Swal.fire('Gagal', response.message ||
                                        'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error',
                                    'Terjadi kesalahan saat mengubah status',
                                    'error');
                            },
                            complete: function() {
                                $('#markAsDoneBtn').prop('disabled', false).html(
                                    'Tandai Selesai');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#unmarkAsDoneBtn', function() {
                let projectId = id;
                let list_proses_id = $('#modalListProsesId').val();
                let urutan = $('#modalUrutanInput').val();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin membatalkan status selesai pada proses ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan Selesai',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/project/' + projectId + '/unmark-step-done',
                            type: 'POST',
                            data: {
                                kerjaan_list_proses_id: list_proses_id,
                                urutan_id: urutan,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function() {
                                $('#unmarkAsDoneBtn').prop('disabled', true).html(
                                    '<i class="fas fa-spinner fa-spin"></i> Memproses...'
                                );
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Status step berhasil dibatalkan dari selesai',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal', response.message ||
                                        'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error',
                                    'Terjadi kesalahan saat membatalkan status',
                                    'error');
                            },
                            complete: function() {
                                $('#unmarkAsDoneBtn').prop('disabled', false).html(
                                    'Batalkan Selesai');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
