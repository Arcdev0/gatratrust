@extends('layout.app')

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
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="font-weight-bold">Detail Project</h3>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5>Informasi Project</h5>
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


                        <div class="container my-4">
                            <h5 class="mt-4">Timeline {{ $project->kerjaan->nama_kerjaan }}</h5>
                            <div class="timeline-container">
                                <!-- Desktop Version -->
                                <div class="timeline-horizontal">
                                    @foreach ($steps as $index => $step)
                                        @php
                                            $status = $stepStatuses[$index];
                                        @endphp

                                        <div class="timeline-step">
                                            <div class="circle {{ $status == 'done' ? 'done' : 'pending' }}"
                                                onclick="showStepModal('{{ $step }}')"
                                                data-step="{{ $step }}">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <p class="step-label">{{ $step }}</p>
                                        </div>

                                        @if ($index < count($steps) - 1)
                                            <div class="arrow-connector">
                                                <div class="arrow-line {{ $status == 'done' ? 'done' : '' }}"></div>
                                                <div class="arrow-head {{ $status == 'done' ? 'done' : '' }}"></div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>


                                <!-- Mobile Version -->
                                <div class="timeline-vertical">
                                    @foreach ($steps as $index => $step)
                                        @php
                                            $status = $stepStatuses[$index];
                                        @endphp
                                        <div class="timeline-step">
                                            <div class="circle {{ $status == 'done' ? 'done' : 'pending' }}"
                                                data-step="{{ $step }}"
                                                onclick="showStepModal('{{ $step }}')">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <p class="step-label">{{ $step }}</p>
                                            @if ($index < count($steps) - 1)
                                                <div class="arrow-line {{ $status == 'done' ? 'done' : '' }}"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="timelineModal" tabindex="-1" role="dialog"
                            aria-labelledby="timelineModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Proses <strong id="modalStepName"></strong></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="fileInputContainer">
                                            <!-- Input akan ditambahkan di sini -->
                                        </div>
                                        <button type="button" id="addFileBtn" class="btn btn-sm btn-outline-primary mt-2">
                                            + Tambah File
                                        </button>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        <button class="btn btn-primary">Simpan</button>
                                        <button class="btn btn-success" id="markAsDoneBtn">Tandai Selesai</button>
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
        let fileInputCounter = 0;

        function showStepModal(stepName) {
            $('#modalStepName').text(stepName);
            $('#fileInputContainer').empty(); // reset input
            fileInputCounter = 0;
            $('#timelineModal').modal('show');
        }

        $(document).ready(function() {
            $('.circle').click(function() {
                var step = $(this).data('step');
                $('#modalStepName').text(step);
                $('#timelineModal').modal('show');
            });

            $('#addFileBtn').on('click', function() {
                fileInputCounter++;

                const inputGroup = $(`
                <div class="form-group">
                    <input type="text" class="form-control mb-2" name="fileLabel[]" placeholder="Contoh : Sertifikat">
                    <input type="file" class="form-control mb-3" name="fileInput[]">
                </div>
            `);

                $('#fileInputContainer').append(inputGroup);
            });
        });
    </script>
@endsection
