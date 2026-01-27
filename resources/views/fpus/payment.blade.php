@extends('layout.app')

@section('title', 'FPU Payment')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="mb-0 text-primary font-weight-bold">FPU Payment</h3>
                <div class="text-muted small">Upload bukti pembayaran per line (Finance only)</div>
            </div>
            <div class="d-flex">
                <a href="{{ route('fpus.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>

        {{-- HEADER INFO --}}
        <div class="card mb-3">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td style="width:160px;" class="text-muted">FPU No</td>
                        <td class="font-weight-bold">{{ $fpu->fpu_no }}</td>

                        <td style="width:160px;" class="text-muted">Status</td>
                        <td>
                            <span
                                class="badge badge-{{ $fpu->status === \App\Models\Fpu::STATUS_PAID ? 'success' : 'primary' }}"
                                id="badgeFpuStatus">
                                {{ $fpu->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Request Date</td>
                        <td>{{ optional($fpu->request_date)->format('d-m-Y') }}</td>

                        <td class="text-muted">Project</td>
                        <td>{{ optional($fpu->project)->no_project ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Wallet</td>
                        <td>{{ optional($fpu->walletCoa)->code_account_id }} - {{ optional($fpu->walletCoa)->name ?? '-' }}
                        </td>

                        <td class="text-muted">Total</td>
                        <td class="font-weight-bold">Rp {{ number_format((float) $fpu->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Notes</td>
                        <td colspan="3">{{ $fpu->notes ?? '-' }}</td>
                    </tr>
                </table>

                @if (!in_array($fpu->status, [\App\Models\Fpu::STATUS_APPROVED, \App\Models\Fpu::STATUS_PAID], true))
                    <div class="alert alert-warning mt-3 mb-0">
                        Upload bukti terkunci. FPU harus <b>APPROVED</b> dulu.
                    </div>
                @endif
            </div>
        </div>

        {{-- LINES + ATTACHMENTS --}}
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Lines</strong>
                <div class="text-muted small">
                    Upload bukti bisa lebih dari 1 file per line (max 5MB/file).
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="tblProofLines">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:70px;">No</th>
                                <th>Description</th>
                                <th style="width:170px;" class="text-right">Amount</th>
                                <th style="width:140px;">Proof</th>
                                <th style="width:320px;">Attachments</th>
                                <th style="width:140px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fpu->lines as $line)
                                <tr id="lineRow-{{ $line->id }}" data-line-id="{{ $line->id }}"
                                    data-line-no="{{ $line->line_no }}" data-amount="{{ $line->amount }}"
                                    data-desc="{{ e($line->description) }}">
                                    <td>{{ $line->line_no }}</td>
                                    <td>{{ $line->description }}</td>
                                    <td class="text-right">Rp {{ number_format((float) $line->amount, 0, ',', '.') }}</td>

                                    <td>
                                        <span class="badge {{ $line->has_proof ? 'badge-success' : 'badge-warning' }}"
                                            id="badgeProof-{{ $line->id }}">
                                            {{ $line->has_proof ? 'Ada' : 'Belum' }}
                                        </span>
                                        <div class="text-muted small mt-1" id="paidAt-{{ $line->id }}">
                                            @if ($line->paid_at)
                                                Paid: {{ $line->paid_at->format('d-m-Y H:i') }}
                                            @endif
                                        </div>
                                    </td>

                                    <td id="attList-{{ $line->id }}">
                                        @if ($line->attachments->count())
                                            @foreach ($line->attachments as $att)
                                                <div class="d-flex justify-content-between align-items-center mb-1 attItem"
                                                    data-att-id="{{ $att->id }}">
                                                    <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank">
                                                        📎 {{ $att->file_name }}
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-xs btn-outline-danger btnDeleteAtt"
                                                        data-attachment-id="{{ $att->id }}"
                                                        data-line-id="{{ $line->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary btnUploadProof"
                                            data-line-id="{{ $line->id }}"
                                            @if (!in_array($fpu->status, [\App\Models\Fpu::STATUS_APPROVED, \App\Models\Fpu::STATUS_PAID], true)) disabled @endif>
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    {{-- MODAL UPLOAD --}}
    <div class="modal fade" id="modalUploadProof" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                        <div class="text-muted small" id="upLineInfo">-</div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="up_line_id">

                    <div class="form-group">
                        <label class="font-weight-semibold">File Bukti (bisa lebih dari 1)</label>
                        <input type="file" class="form-control" id="up_files" multiple>
                        <small class="text-muted">Max 5MB per file.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btnDoUploadProof">
                        <i class="fas fa-cloud-upload-alt mr-1"></i>Upload
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // open modal upload
            $(document).on('click', '.btnUploadProof', function() {
                const lineId = $(this).data('line-id');
                const $row = $('#lineRow-' + lineId);

                const lineNo = $row.data('line-no');
                const desc = $row.data('desc');
                const amt = $row.data('amount');

                $('#up_line_id').val(lineId);
                $('#up_files').val(null);
                $('#upLineInfo').text(`Line #${lineNo} • ${desc} • Rp ${formatIDR(amt)}`);

                $('#modalUploadProof').modal('show');
            });

            // do upload
            $('#btnDoUploadProof').on('click', function() {
                const lineId = $('#up_line_id').val();
                const files = $('#up_files')[0].files;

                if (!files || files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih file dulu'
                    });
                    return;
                }

                const fd = new FormData();
                for (let i = 0; i < files.length; i++) fd.append('files[]', files[i]);

                Swal.fire({
                    title: 'Upload bukti?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, upload',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((r) => {
                    if (!r.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: "{{ url('fpus/lines') }}/" + lineId + "/attachments",
                        method: "POST",
                        data: fd,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            Swal.close();
                            $('#modalUploadProof').modal('hide');

                            updateLineRowFromResponse(res.data.line);

                            if (res.data.fpu?.status) $('#badgeFpuStatus').text(res.data
                                .fpu.status);

                            Swal.fire({
                                icon: 'success',
                                title: res.message || 'Upload berhasil',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan'
                            });
                        }
                    });
                });
            });

            // delete attachment
            $(document).on('click', '.btnDeleteAtt', function() {
                const attId = $(this).data('attachment-id');
                const lineId = $(this).data('line-id');

                Swal.fire({
                    title: 'Hapus attachment?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((r) => {
                    if (!r.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: "{{ url('fpus/attachments') }}/" + attId,
                        method: "DELETE",
                        success: function() {
                            Swal.close();

                            $('#attList-' + lineId + ' .attItem[data-att-id="' + attId +
                                '"]').remove();

                            if ($('#attList-' + lineId + ' .attItem').length === 0) {
                                $('#attList-' + lineId).html(
                                    '<span class="text-muted small">-</span>');
                                $('#badgeProof-' + lineId).removeClass('badge-success')
                                    .addClass('badge-warning').text('Belum');
                                $('#paidAt-' + lineId).text('');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil dihapus',
                                timer: 900,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan'
                            });
                        }
                    });
                });
            });

            function updateLineRowFromResponse(line) {
                const lineId = line.id;

                if (line.has_proof) {
                    $('#badgeProof-' + lineId).removeClass('badge-warning').addClass('badge-success').text('Ada');
                }

                if (line.paid_at) {
                    $('#paidAt-' + lineId).text('Paid: ' + formatDateTime(line.paid_at));
                }

                if (line.attachments && line.attachments.length) {
                    let html = '';
                    line.attachments.forEach(a => {
                        const url = a.file_url || ("{{ asset('storage') }}/" + a.file_path);
                        html += `
                    <div class="d-flex justify-content-between align-items-center mb-1 attItem" data-att-id="${a.id}">
                        <a href="${url}" target="_blank">📎 ${escapeHtml(a.file_name)}</a>
                        <button type="button" class="btn btn-xs btn-outline-danger btnDeleteAtt"
                            data-attachment-id="${a.id}" data-line-id="${lineId}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                    });
                    $('#attList-' + lineId).html(html);
                }
            }

            function formatIDR(n) {
                return new Intl.NumberFormat('id-ID').format(parseFloat(n || 0));
            }

            function formatDateTime(iso) {
                const d = new Date(iso);
                const pad = (x) => String(x).padStart(2, '0');
                return `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
            }

            function escapeHtml(str) {
                return String(str || '')
                    .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;').replaceAll("'", "&#039;");
            }

        });
    </script>
@endsection
