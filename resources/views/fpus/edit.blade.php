@extends('layout.app')

@section('title', 'Edit FPU')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        {{-- HEADER --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="mb-0 text-primary font-weight-bold">Edit FPU</h3>
                <div class="text-muted small">
                    {{ $fpu->fpu_no }} •
                    <span class="badge badge-secondary text-uppercase">{{ $fpu->status }}</span>
                </div>
            </div>
            <a href="{{ route('fpus.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
        </div>

        <div class="row">
            {{-- LEFT --}}
            <div class="col-lg-8">

                {{-- HEADER FORM --}}
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Header</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">Tanggal *</label>
                                <input type="date" id="request_date" class="form-control"
                                    value="{{ $fpu->request_date->toDateString() }}"
                                    {{ $fpu->status !== 'draft' ? 'disabled' : '' }}>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">No Project (Opsional)</label>
                                <select class="form-control" id="project_id"
                                    {{ $fpu->status !== 'draft' ? 'disabled' : '' }}>
                                    <option value="">-- pilih project --</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}"
                                            {{ $fpu->project_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->no_project }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">Requester</label>
                                <input type="text" id="requester_name" class="form-control"
                                    value="{{ $fpu->requester_name }}" {{ $fpu->status !== 'draft' ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">Purpose</label>
                                <select class="form-control" id="purpose"
                                    {{ $fpu->status !== 'draft' ? 'disabled' : '' }}>
                                    <option value="">-- pilih --</option>
                                    @foreach (\App\Models\Fpu::PURPOSES as $p)
                                        <option value="{{ $p }}" {{ $fpu->purpose === $p ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $p)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-8">
                                <label class="font-weight-semibold">Notes</label>
                                <textarea class="form-control" id="notes" rows="3" {{ $fpu->status !== 'draft' ? 'disabled' : '' }}>{{ $fpu->notes }}</textarea>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- LINES --}}
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Detail Pengeluaran</strong>
                        @if ($fpu->status === 'draft')
                            <button class="btn btn-sm btn-primary" id="btnAddLine">
                                <i class="fas fa-plus mr-1"></i>Tambah Baris
                            </button>
                        @endif
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:60px;">No</th>
                                        <th>Description</th>
                                        <th style="width:180px;" class="text-right">Amount</th>
                                        <th style="width:90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="linesBody">
                                    @foreach ($fpu->lines as $i => $line)
                                        <tr>
                                            <td class="text-center line-no">{{ $i + 1 }}</td>
                                            <td>
                                                <input type="text" class="form-control line-desc"
                                                    value="{{ $line->description }}"
                                                    {{ $fpu->status !== 'draft' ? 'disabled' : '' }}>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    class="form-control text-right line-amount" value="{{ $line->amount }}"
                                                    {{ $fpu->status !== 'draft' ? 'disabled' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                @if ($fpu->status === 'draft')
                                                    <button class="btn btn-sm btn-danger btnRemoveLine">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th colspan="2" class="text-right">TOTAL</th>
                                        <th class="text-right" id="grandTotal">
                                            {{ number_format($fpu->total_amount, 2) }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Aksi</strong>
                    </div>
                    <div class="card-body">

                        @if ($fpu->status === 'draft')
                            <button class="btn btn-primary btn-block mb-2" id="btnUpdateFpu">
                                <i class="fas fa-save mr-1"></i>Save Draft
                            </button>
                            <button class="btn btn-success btn-block" id="btnSubmitFpu">
                                <i class="fas fa-paper-plane mr-1"></i>Submit
                            </button>
                        @else
                            <div class="alert alert-info mb-0">
                                FPU sudah <b>{{ strtoupper($fpu->status) }}</b> dan tidak bisa diedit.
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        $(function() {

            $('#project_id, #purpose').select2({
                width: '100%'
            });

            function calcTotal() {
                let t = 0;
                $('.line-amount').each(function() {
                    t += parseFloat($(this).val() || 0);
                });
                $('#grandTotal').text(t.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }));
            }

            $(document).on('keyup change', '.line-amount', calcTotal);

            $('#btnAddLine').on('click', function() {
                $('#linesBody').append(`
            <tr>
                <td class="text-center line-no">0</td>
                <td><input type="text" class="form-control line-desc"></td>
                <td><input type="number" step="0.01" class="form-control text-right line-amount"></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger btnRemoveLine">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
                renumber();
            });

            $(document).on('click', '.btnRemoveLine', function() {
                $(this).closest('tr').remove();
                renumber();
                calcTotal();
            });

            function renumber() {
                $('#linesBody tr').each(function(i) {
                    $(this).find('.line-no').text(i + 1);
                });
            }

            function collectLines() {
                let lines = [];
                $('#linesBody tr').each(function() {
                    lines.push({
                        description: $(this).find('.line-desc').val(),
                        amount: $(this).find('.line-amount').val()
                    });
                });
                return lines;
            }

            $('#btnUpdateFpu').on('click', function() {
                saveFpu(false);
            });

            $('#btnSubmitFpu').on('click', function() {
                saveFpu(true);
            });

            function saveFpu(submit) {
                Swal.fire({
                    title: submit ? 'Submit FPU?' : 'Simpan draft?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya'
                }).then(res => {
                    if (!res.isConfirmed) return;

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('fpus.update', $fpu->id) }}",
                        method: 'PUT',
                        data: {
                            project_id: $('#project_id').val(),
                            request_date: $('#request_date').val(),
                            requester_name: $('#requester_name').val(),
                            purpose: $('#purpose').val(),
                            notes: $('#notes').val(),
                            lines: collectLines()
                        },
                        success: function() {
                            if (submit) {
                                $.post("{{ route('fpus.submit', $fpu->id) }}", {}, () => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Success', 'Draft disimpan', 'success');
                            }
                        }
                    });
                });
            }

        });
    </script>
@endsection
