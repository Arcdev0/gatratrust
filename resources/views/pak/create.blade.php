@extends('layout.app')

@section('title', 'Tambah Proposal Anggaran Kerja')

<style>
    .select2-selection__choice {
        padding-left: 1.6em !important;
        position: relative;
        display: inline-flex;
        align-items: center;
        height: 1.6em;
        line-height: 1.6em;
    }

    .btn-add-row {
        font-size: 11px;
        padding: 3px 10px;
    }

    .btn-remove-row {
        font-size: 11px;
        padding: 2px 6px;
    }

    .section-total-row {
        background-color: #f0f0f0;
        font-weight: bold;
    }


    .status-ok {
        color: #007A33 !important;
        /* hijau */
        font-weight: bold;
    }

    .status-over {
        color: #d9534f !important;
        /* merah */
        font-weight: bold;
    }
</style>

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Tambah PAK</h3>
            <a href="{{ route('pak.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="pakForm" action="{{ route('pak.store') }}" method="POST">
                    @csrf

                    <!-- Informasi Project -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_name">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('project_name') is-invalid @enderror"
                                    id="project_name" name="project_name" value="{{ old('project_name') }}" required>
                                @error('project_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_number">Project Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('project_number') is-invalid @enderror"
                                    id="project_number" name="project_number" value="{{ old('project_number') }}" required>
                                @error('project_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_value">Project Value <span class="text-danger">*</span></label>
                                <input type="text" id="project_value_display" class="form-control" placeholder="Rp 0"
                                    required>
                                <input type="hidden" id="project_value" name="project_value">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_project">Location Project <span class="text-danger">*</span></label>

                                <select class="form-control @error('location_project') is-invalid @enderror"
                                    id="location_project" name="location_project" required>

                                    <option value="">-- Pilih Lokasi --</option>

                                    <option value="dalam_kota"
                                        {{ old('location_project') == 'dalam_kota' ? 'selected' : '' }}>
                                        Batam
                                    </option>

                                    <option value="luar_kota"
                                        {{ old('location_project') == 'luar_kota' ? 'selected' : '' }}>
                                        Luar Batam
                                    </option>

                                </select>

                                @error('location_project')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee <span class="text-danger">*</span></label>
                                <select multiple id="employee" name="employee[]" class="form-control employee-select"
                                    required>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ is_array(old('employee')) && in_array($emp->id, old('employee')) ? 'selected' : '' }}>
                                            {{ $emp->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered text-center align-middle table-sm" id="pak-table">
                            <thead class="text-white" style="background-color: #007A33;">
                                <tr style="color:white; font-weight:bold;">
                                    <th style="width:40px;">NO</th>
                                    <th style="width:180px;">Operational Needs</th>
                                    <th>Description</th>
                                    <th style="width:90px;">Qty</th>
                                    <th style="width:150px;">Unit Cost</th>
                                    <th style="width:150px;">Total Cost</th>
                                    <th style="width:150px;">Max Cost</th>
                                    <th style="width:120px;">%</th>
                                    <th style="width:80px;">Status</th>
                                    <th style="width:40px;">#</th>
                                </tr>
                            </thead>

                            <tbody id="pak-dynamic-body">

                                @foreach ($categories as $cat)
                                    <!-- SECTION HEADER -->
                                    <tr class="section-header">
                                        <td colspan="10" style="text-align:left; background:#e9ecef; font-weight:bold;">
                                            {{ $cat->code }}. {{ $cat->name }}
                                        </td>
                                    </tr>

                                    <!-- ITEM TEMPLATE (INDEX = 0) -->
                                    <tr class="item-row" data-section="{{ $cat->code }}"
                                        data-category="{{ $cat->id }}" data-index="0">

                                        <td class="numbering">1</td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                name="items[{{ $cat->id }}][0][operational_needs]" required>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                name="items[{{ $cat->id }}][0][description]">
                                        </td>

                                        <td>
                                            <input type="number" class="form-control form-control-sm unit_qty"
                                                name="items[{{ $cat->id }}][0][qty]" value="0" min="0"
                                                required>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm unit_cost_display"
                                                placeholder="Rp 0">
                                            <input type="hidden" class="unit_cost"
                                                name="items[{{ $cat->id }}][0][unit_cost]" value="0">
                                        </td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm total_cost_display"
                                                readonly placeholder="Rp 0">
                                            <input type="hidden" class="total_cost"
                                                name="items[{{ $cat->id }}][0][total_cost]" value="0">
                                        </td>

                                        <td>
                                            <input type="hidden" class="max_cost"
                                                name="items[{{ $cat->id }}][0][max_cost]" value="0">
                                        </td>

                                        <td>
                                            <input type="hidden" class="percent"
                                                name="items[{{ $cat->id }}][0][percent]" value="0">
                                        </td>

                                        <td>
                                            <input type="hidden" class="status-field"
                                                name="items[{{ $cat->id }}][0][status]" value="OK">
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                style="display:none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- SECTION TOTAL ROW -->
                                    <tr class="section-total-row" data-section="{{ $cat->code }}"
                                        data-category="{{ $cat->id }}"
                                        data-max-percentage="{{ $cat->max_percentage }}">

                                        <td colspan="2" style="text-align:left; font-weight:bold;">
                                            TOTAL {{ $cat->order }} (MAX {{ $cat->max_percentage }}%)
                                        </td>

                                        <td colspan="2"></td>

                                        <td>
                                            <span class="section-max-display">Rp 0</span>
                                        </td>

                                        <td>
                                            <span class="section-total-display">Rp 0</span>
                                        </td>

                                        <td>
                                            <span class="section-percent-display">0%</span>
                                        </td>

                                        <td>
                                            <span class="section-status-display status-ok">OK</span>
                                        </td>

                                        <td colspan="2">
                                            <button type="button" class="btn btn-sm btn-primary btn-add-row"
                                                data-category="{{ $cat->id }}" data-section="{{ $cat->code }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- GRAND TOTAL -->
                                <tr>
                                    <td colspan="5">
                                        <div class="col-md-4" style="text-align:left; font-weight:bold;">
                                            Grand Total
                                        </div>
                                    </td>
                                    <td colspan="5">
                                        <div class="col-md-4">
                                            <div id="grand-total-display" style="font-weight:bold;">
                                                Rp 0
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- PROJECT VALUE & COST SUMMARY -->
                    <div class="card mt-3">
                        <div class="card-body">

                            <h5 class="mb-3"><strong>Project Financial Summary</strong></h5>

                            <div class="row">

                                <!-- PPH23 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PPH 23 (Optional)</label>

                                        <!-- Input tampilan -->
                                        <input type="text" class="form-control rupiah-display" id="pph23_display"
                                            value="{{ old('pph23') ? 'Rp ' . number_format(old('pph23')) : '' }}">

                                        <!-- Hidden value -->
                                        <input type="hidden" name="pph23" id="pph23"
                                            value="{{ old('pph23') }}">
                                    </div>
                                </div>

                                <!-- PPN 11% -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PPN 11% (Optional)</label>

                                        <input type="text" class="form-control rupiah-display" id="ppn11_display"
                                            value="{{ old('ppn11') ? 'Rp ' . number_format(old('ppn11')) : '' }}">

                                        <input type="hidden" name="ppn11" id="ppn11"
                                            value="{{ old('ppn11') }}">
                                    </div>
                                </div>

                            </div>

                            <hr>

                            <!-- PROJECT COST -->
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PROJECT COST</label>

                                        <input type="text" class="form-control rupiah-display"
                                            id="project_cost_display"
                                            value="{{ old('project_cost') ? 'Rp ' . number_format(old('project_cost')) : '' }}">

                                        <input type="hidden" name="project_cost" id="project_cost"
                                            value="{{ old('project_cost') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- SUMMARY TABLE -->
                            <div class="mt-3">
                                <table class="table table-bordered text-center align-middle">
                                    <tr style="background: #d9d9d9; font-weight: bold;">
                                        <td class="text-start">TOTAL PROJECT COST</td>
                                        <td style="width: 180px;">
                                            <span id="display_total_cost">Rp 0</span>
                                        </td>
                                        <td style="width: 80px;">
                                            <span id="cost_percentage">0%</span>
                                        </td>
                                    </tr>

                                    <tr style="background: #d9d9d9; font-weight: bold;">
                                        <td class="text-start">ESTIMATED PROFIT (min 15%)</td>
                                        <td id="profit_cell" style="background: red; color: white;">
                                            <span id="estimated_profit">Rp 0</span>
                                        </td>
                                        <td id="profit_percent_cell" style="background: red; color: white;">
                                            <span id="profit_percentage">0%</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan PAK
                        </button>
                        <a href="{{ route('pak.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            // Initialize Select2
            $('#employee').select2({
                placeholder: "Pilih satu atau lebih karyawan",
                width: "100%",
            });

            // Format Date
            function formatDate(dateStr) {
                if (!dateStr) return '';
                return dateStr.split('T')[0];
            }


            // ==================================================
            // Helper rupiah
            // ==================================================
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function parseRupiah(v) {
                if (v === null || v === undefined) return 0;
                return parseInt(String(v).replace(/[^\d]/g, '')) || 0;
            }

            // ==================================================
            // Ambil projectValue dengan fallback
            // ==================================================
            function getProjectValue() {
                // prioritas: hidden #project_value -> #project_value_display -> #project_cost (hidden/display)
                let v = parseRupiah($('#project_value').val());
                if (v > 0) return v;

                v = parseRupiah($('#project_value_display').val());
                if (v > 0) return v;

                v = parseRupiah($('#project_cost').val());
                if (v > 0) return v;

                v = parseRupiah($('#project_cost_display').val && $('#project_cost_display').val());
                return v || 0;
            }

            function replaceIndexInName(oldName, newIndex) {
                // handle pattern seperti: items[<cat>][<idx>][fieldname] atau any similar with two numbers
                // kita mengganti bagian index (angka kedua)
                return oldName.replace(/^(.+?\[\d+\]\[)\d+(\].*?)$/, '$1' + newIndex + '$2');
            }

            function reindexSectionNames(categoryId) {
                let $rows = $('tr.item-row[data-category="' + categoryId + '"]');
                $rows.each(function(i) {
                    // set data-index pada row
                    $(this).attr('data-index', i);

                    // update semua input/select/textarea name di row
                    $(this).find('input[name], select[name], textarea[name]').each(function() {
                        let oldName = $(this).attr('name');
                        if (!oldName) return;

                        let newName = replaceIndexInName(oldName, i);
                        $(this).attr('name', newName);
                    });
                });
            }

            function reindexAllSections() {
                $('.section-total-row').each(function() {
                    let categoryId = $(this).data('category');
                    reindexSectionNames(categoryId);
                });
            }

            // ==================================================
            // Recalc satu row
            // ==================================================
            function recalcRow($row) {
                let qty = Number($row.find('.unit_qty').val()) || 0;
                // beberapa template pakai unit_cost_display, beberapa pakai unit_cost_display (kamu pakai unit_cost_display)
                let unitCostDisplay = $row.find('.unit_cost_display').length ? $row.find('.unit_cost_display')
                    .val() : $row.find('.unit-cost').val();
                let unitCost = parseRupiah(unitCostDisplay);
                // simpan ke hidden .unit_cost jika ada
                if ($row.find('.unit_cost').length) $row.find('.unit_cost').val(unitCost);

                let total = qty * unitCost;
                if ($row.find('.total_cost').length) $row.find('.total_cost').val(total);
                if ($row.find('.total_cost_display').length) $row.find('.total_cost_display').val(formatRupiah(
                    total));
            }

            // ==================================================
            // Recalc semua section dan grand total
            // ==================================================
            function recalcAll() {
                let projectValue = getProjectValue();
                let grandTotal = 0;

                // untuk setiap section-total-row (dinamis)
                $('tr.section-total-row').each(function() {
                    let $secRow = $(this);
                    let sectionCode = String($secRow.data('section'));
                    let maxPercent = parseFloat($secRow.data('max-percentage')) || 0;

                    // semua item rows yang punya data-section sama
                    let $rows = $('tr.item-row[data-section="' + sectionCode + '"]');
                    let sectionTotal = 0;

                    $rows.each(function() {
                        recalcRow($(this));
                        // total stored either in hidden .total_cost or in cell value
                        let t = 0;
                        if ($(this).find('.total_cost').length) t = parseRupiah($(this).find(
                            '.total_cost').val());
                        else if ($(this).find('.total_cost_display').length) t = parseRupiah($(this)
                            .find('.total_cost_display').val());
                        sectionTotal += Number(t);
                    });

                    grandTotal += sectionTotal;

                    // hitung allowed & percent
                    let allowed = Math.round(projectValue * (maxPercent / 100));
                    let percentOfProject = projectValue ? (sectionTotal / projectValue * 100) : 0;
                    let statusText = (allowed > 0 && sectionTotal > allowed) ? 'OVER' : 'OK';

                    // update UI
                    $secRow.find('.section-total-display').text(formatRupiah(sectionTotal));
                    $secRow.find('.section-max-display').text(formatRupiah(allowed));
                    $secRow.find('.section-percent-display').text(percentOfProject.toFixed(2) + ' %');

                    let $status = $secRow.find('.section-status-display');
                    $status.text(statusText);
                    $status.removeClass('status-ok status-over');
                    if (statusText === 'OVER') $status.addClass('status-over');
                    else $status.addClass('status-ok');
                });

                $('#grand-total-display').text(formatRupiah(grandTotal));

                // juga update overall financial summary jika ada elemen tersebut
                if ($('#display_total_cost').length) $('#display_total_cost').text(formatRupiah(grandTotal));

                // profit check (min 15%)
                let pv = projectValue;
                if (pv > 0 && $('#estimated_profit').length) {
                    let profitNow = pv - grandTotal;
                    let profitPercent = (profitNow / pv) * 100;
                    $('#estimated_profit').text(formatRupiah(profitNow));
                    $('#profit_percentage').text(isFinite(profitPercent) ? profitPercent.toFixed(1) + '%' : '0%');

                    if (profitPercent < 15) {
                        $('#profit_cell, #profit_percentage_cell').css({
                            background: 'red',
                            color: 'white'
                        });
                    } else {
                        $('#profit_cell, #profit_percentage_cell').css({
                            background: '#28a745',
                            color: 'white'
                        });
                    }
                }
            }

            // ==================================================
            // renumber rows
            // ==================================================
            function renumberSection(sectionCode) {
                let i = 1;
                $('tr.item-row[data-section="' + sectionCode + '"]').each(function() {
                    $(this).find('.numbering').text(i++);
                });
            }

            // ==================================================
            // add row handler (robust fallback if no firstRow found)
            // ==================================================
            $(document).on('click', '.btn-add-row', function() {
                let categoryId = $(this).data('category');
                let sectionCode = $(this).data('section');

                // hitung existing rows di kategori ini
                let rowCount = $('tr.item-row[data-category="' + categoryId + '"]').length;
                let nextIndex = rowCount; // index baru (0-based)

                // clone the first row template (fallback)
                let $firstRow = $('tr.item-row[data-category="' + categoryId + '"]').first();
                let $new = $firstRow.clone(true, true); // true to copy data+events (safe)

                // perbarui atribut data-index
                $new.attr('data-index', nextIndex);

                // update semua input/select/textarea di row cloned
                $new.find('input, select, textarea').each(function() {
                    let $el = $(this);
                    let oldName = $el.attr('name');

                    // jika punya name, ganti hanya INDEX (angka kedua dalam items[CAT][INDEX][field])
                    if (oldName) {
                        // replace angka index yang diikuti oleh "][" (target index, bukan category id)
                        let newName = oldName.replace(/\[(\d+)\](?=\]\[)/, '[' + nextIndex + ']');
                        $el.attr('name', newName);
                    }

                    // reset value dengan pintar:
                    // - hidden numeric fields harus default 0
                    // - visible text/number kosong
                    if ($el.hasClass('unit_cost') || $el.hasClass('total_cost') || $el.hasClass(
                            'max_cost') || $el.hasClass('percent')) {
                        $el.val(0);
                    } else {
                        // jika input jenis display (mis. unit_cost_display, total_cost_display) set jadi format 0
                        if ($el.hasClass('unit_cost_display') || $el.hasClass(
                                'total_cost_display')) {
                            $el.val(formatRupiah(0));
                        } else {
                            $el.val('');
                        }
                    }

                    // jika ada atribut id yang mengandung index (jarang) — update juga (opsional)
                    if ($el.attr('id')) {
                        let newId = $el.attr('id').replace(/\d+$/, '') + nextIndex;
                        $el.attr('id', newId);
                    }
                });

                // show remove button pada row clone
                $new.find('.btn-remove-row').show();

                // insert sebelum section total row
                let $secTotalRow = $('tr.section-total-row[data-category="' + categoryId + '"]');
                $secTotalRow.before($new);
                reindexSectionNames(categoryId);
                renumberSection(sectionCode);
                recalcAll();
            });



            // ==================================================
            // remove row handler
            // ==================================================
            $(document).on('click', '.btn-remove-row', function() {
                let $row = $(this).closest('tr.item-row');
                let sectionCode = $row.data('section') + '';
                let $rows = $('tr.item-row[data-section="' + sectionCode + '"]');

                if ($rows.length <= 1) {
                    $row.find('input').val('');
                    recalcAll();
                    return;
                }
                $row.remove();
                reindexSectionNames(sectionCode ? $('[data-section="' + sectionCode + '"]').data(
                    'category') : categoryId); // atau panggil reindexAllSections();
                renumberSection(sectionCode);
                recalcAll();
            });

            // ==================================================
            // listeners for input changes
            // ==================================================
            $(document).on('input', '.unit_qty, .unit_cost_display', function() {
                let $r = $(this).closest('tr.item-row');
                recalcRow($r);
                recalcAll();
            });

            $(document).on('blur', '.unit_cost_display', function() {
                let v = parseRupiah($(this).val());
                $(this).val(formatRupiah(v));
                let $r = $(this).closest('tr.item-row');
                recalcRow($r);
                recalcAll();
            });

            // call recalc on init and whenever project_value_display blurred (so hidden updated)
            $(document).ready(function() {
                // If you have project_value_display input, attach blur to update hidden if needed
                $('#project_value_display').on('blur', function() {
                    let v = parseRupiah($(this).val());
                    $('#project_value').val(v);
                    $(this).val(formatRupiah(v));
                    recalcAll();
                });

                // initial hide remove buttons except first in each section
                $('tr.item-row').each(function() {
                    let sectionCode = $(this).data('section') + '';
                    let $rows = $('tr.item-row[data-section="' + sectionCode + '"]');
                    if ($rows.index(this) === 0) $(this).find('.btn-remove-row').hide();
                });

                recalcAll();
            });



            // Auto format Rupiah display + update hidden value
            $(".rupiah-display").on("keyup change", function() {
                let inputDisplay = $(this);

                // ambil id ketemu _display → ganti ke hidden
                let hiddenId = inputDisplay.attr("id").replace("_display", "");
                let inputHidden = $("#" + hiddenId);

                let angka = parseRupiah(inputDisplay.val());

                // Set angka murni ke hidden
                inputHidden.val(angka);

                // Update tampilan
                inputDisplay.val(formatRupiah(angka));

                // Hitung ulang summary
                hitungSummary();
            });

            function hitungSummary() {

                let pph23 = parseRupiah($("#pph23").val());
                let ppn11 = parseRupiah($("#ppn11").val());
                let projectCost = parseRupiah($("#project_cost").val());
                let projectValue = parseRupiah($("#project_value").val() || 0);

                // TOTAL COST = pph23 + ppn11 + project_cost
                let totalCost = pph23 + ppn11 + projectCost;

                // COST PERCENT
                let costPercent = projectValue > 0 ?
                    ((totalCost / projectValue) * 100).toFixed(0) :
                    0;

                // PROFIT SEKARANG
                let profitNow = projectValue - totalCost;

                // MINIMUM PROFIT 15%
                let minProfit = projectValue * 0.15;

                // PROFIT PERCENT
                let profitPercent = projectValue > 0 ?
                    ((profitNow / projectValue) * 100).toFixed(0) :
                    0;

                // Tampil ke tabel
                $("#display_total_cost").text(formatRupiah(totalCost));
                $("#cost_percentage").text(costPercent + "%");

                $("#estimated_profit").text(formatRupiah(profitNow));
                $("#profit_percentage").text(profitPercent + "%");

                // WARNA OTOMATIS: merah jika profit < 15%
                if (profitNow < minProfit) {
                    $("#profit_cell, #profit_percent_cell").css({
                        background: "red",
                        color: "white"
                    });
                } else {
                    $("#profit_cell, #profit_percent_cell").css({
                        background: "#28a745",
                        color: "white"
                    });
                }
            }

            // Initial load
            hitungSummary();



            $('#pakForm').on('submit', function(e) {
                e.preventDefault();

                let valid = true;
                let missingCategory = null;

                // Loop semua section (dibaca dari row TOTAL)
                $('.section-total-row').each(function() {

                    let categoryId = $(this).data('category');

                    // Cari semua input operational di kategori ini
                    let hasItem = $(`.item-row[data-category="${categoryId}"]`)
                        .find('input[name^="items[' + categoryId +
                            ']"][name$="[operational_needs]"]')
                        .filter(function() {
                            return $(this).val().trim() !== "";
                        }).length > 0;

                    if (!hasItem) {
                        valid = false;
                        missingCategory = categoryId;
                    }
                });

                if (!valid) {
                    Swal.fire('Error!', 'Setiap section harus memiliki minimal 1 item yang diisi', 'error');
                    return;
                }

                // lanjut proses submit seperti biasa
                const formEl = this;
                const $form = $(formEl);
                const btn = $form.find('button[type="submit"]');
                const btnOriginalText = btn.html();

                reindexAllSections();

                let formData = new FormData(formEl);

                Swal.fire({
                    title: 'Simpan PAK?',
                    text: 'Pastikan data sudah benar',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                    );

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message ||
                                        'PAK berhasil disimpan.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('pak.index') }}";
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: (response && response.message) ?
                                        response.message : 'Terjadi kesalahan.',
                                    icon: 'error'
                                });
                                btn.prop('disabled', false).html(btnOriginalText);
                            }
                        },
                        error: function(xhr) {
                            let msg = 'Terjadi kesalahan pada server.';
                            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: msg,
                                icon: 'error'
                            });
                            btn.prop('disabled', false).html(btnOriginalText);
                        }
                    });

                });
            });
        });
    </script>
@endsection
