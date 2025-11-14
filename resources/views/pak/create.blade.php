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
                            <input type="text" id="project_value_display" class="form-control" placeholder="Rp 0" required>
                            <input type="hidden" id="project_value" name="project_value">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location_project">Location Project <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('location_project') is-invalid @enderror"
                                id="location_project" name="location_project" value="{{ old('location_project') }}" required>
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
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                                name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select multiple id="employee" name="employee[]" class="form-control employee-select" required>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ is_array(old('employee')) && in_array($emp->id, old('employee')) ? 'selected' : '' }}>
                                        {{ $emp->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center align-middle table-sm" id="pak-table">
                        <thead class="text-white" style="background-color: #007A33">
                            <tr style="color: white; font-weight: bold;">
                                <th style="width:40px;">NO</th>
                                <th>Operational Needs</th>
                                <th>Description</th>
                                <th style="width:90px;">Unit Qty</th>
                                <th style="width:140px;">Unit Cost</th>
                                <th style="width:140px;">Total Cost</th>
                                <th style="width:140px;">MAX COST</th>
                                <th style="width:60px;">%</th>
                                <th style="width:100px;">Status</th>
                                <th style="width:60px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- ========== SECTION A - HONORARIUM ========== -->
                            <tr class="section-header">
                                <td colspan="10" style="text-align: left; background-color: #e9ecef; font-weight: bold;">
                                    A. Honorarium
                                </td>
                            </tr>

                            <tr class="item-row" data-section="A" data-category="honorarium">
                                <td class="numbering">1</td>
                                <td><input type="text" class="form-control form-control-sm" name="operational_needs[]" required></td>
                                <td><input type="text" class="form-control form-control-sm" name="description[]"></td>
                                <td><input type="number" class="form-control form-control-sm unit_qty" name="unit_qty[]" value="0" min="0" required></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm unit_cost_display" placeholder="Rp 0" required>
                                    <input type="hidden" class="unit_cost" name="unit_cost[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm total_cost_display" readonly placeholder="Rp 0">
                                    <input type="hidden" class="total_cost" name="total_cost[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm max_cost_display" readonly placeholder="Rp 0">
                                    <input type="hidden" class="max_cost" name="max_cost[]">
                                </td>
                                <td><input type="text" class="form-control form-control-sm percent" name="percent[]" readonly></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm status-field" name="status[]" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-row" style="display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr class="section-total-row" data-section="A">
                                <td colspan="5" style="text-align: left;">
                                    TOTAL I (MAX 70%)
                                </td>
                                <td class="section-total-display" style="font-weight: bold;">Rp 0</td>
                                <td colspan="3"></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm btn-add-row" data-section="A">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </td>
                            </tr>

                            <!-- ========== SECTION B - OPERATIONAL ========== -->
                            <tr class="section-header">
                                <td colspan="10" style="text-align: left; background-color: #e9ecef; font-weight: bold;">
                                    B. Operational
                                </td>
                            </tr>

                            <tr class="item-row" data-section="B" data-category="operational">
                                <td class="numbering">1</td>
                                <td><input type="text" class="form-control form-control-sm" name="operational_needs[]" required></td>
                                <td><input type="text" class="form-control form-control-sm" name="description[]"></td>
                                <td><input type="number" class="form-control form-control-sm unit_qty" name="unit_qty[]" value="0" min="0" required></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm unit_cost_display" placeholder="Rp 0" required>
                                    <input type="hidden" class="unit_cost" name="unit_cost[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm total_cost_display" readonly placeholder="Rp 0">
                                    <input type="hidden" class="total_cost" name="total_cost[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm max_cost_display" readonly placeholder="Rp 0">
                                    <input type="hidden" class="max_cost" name="max_cost[]">
                                </td>
                                <td><input type="text" class="form-control form-control-sm percent" name="percent[]" readonly></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm status-field" name="status[]" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-row" style="display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr class="section-total-row" data-section="B">
                                <td colspan="5" style="text-align: left;">
                                    TOTAL II (MAX 10%)
                                </td>
                                <td class="section-total-display" style="font-weight: bold;">Rp 0</td>
                                <td colspan="3"></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm btn-add-row" data-section="B">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </td>
                            </tr>

                            <!-- ========== SECTION C - CONSUMABLE ========== -->
                            <tr class="section-header">
                                <td colspan="10" style="text-align: left; background-color: #e9ecef; font-weight: bold;">
                                    C. Consumable
                                </td>
                            </tr>

                            <tr class="item-row" data-section="C" data-category="consumable">
                                <td class="numbering">1</td>
                                <td><input type="text" class="form-control form-control-sm" name="operational_needs[]" required></td>
                                <td><input type="text" class="form-control form-control-sm" name="description[]"></td>
                                <td><input type="number" class="form-control form-control-sm unit_qty" name="unit_qty[]" value="0" min="0" required></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm unit_cost_display" placeholder="Rp 0" required>
                                    <input type="hidden" class="unit_cost" name="unit_cost[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm total_cost_display" readonly placeholder="Rp 0">
                                    <input type="hidden" class="total_cost" name="total_cost[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm max_cost_display" readonly placeholder="Rp 0">
                                    <input type="hidden" class="max_cost" name="max_cost[]">
                                </td>
                                <td><input type="text" class="form-control form-control-sm percent" name="percent[]" readonly></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm status-field" name="status[]" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-row" style="display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr class="section-total-row" data-section="C">
                                <td colspan="5" style="text-align: left;">
                                    TOTAL III (MAX 5%)
                                </td>
                                <td class="section-total-display" style="font-weight: bold;">Rp 0</td>
                                <td colspan="3"></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm btn-add-row" data-section="C">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </td>
                            </tr>

                            <!-- ========== GRAND TOTAL ========== -->
                            <tr style="background-color: #007A33; color: white; font-weight: bold;">
                                <td colspan="5" style="text-align: left;">GRAND TOTAL</td>
                                <td id="grand-total-display">Rp 0</td>
                                <td colspan="4"></td>
                            </tr>
                        </tbody>
                    </table>
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
    
    // Format angka ke Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }

    // Parse Rupiah ke angka
    function parseRupiah(rupiah) {
        return parseInt(rupiah.toString().replace(/[^\d]/g, '')) || 0;
    }

    // Format Date
    function formatDate(dateStr) {
        if (!dateStr) return '';
        return dateStr.split('T')[0];
    }

    // Initialize Select2
    $('#employee').select2({
        placeholder: "Pilih satu atau lebih karyawan",
        width: "100%",
    });

    $('#copyFrom').select2({
        placeholder: "-- Pilih PAK Lama --",
        width: '100%'
    });



    // Fill first row of section
    function fillFirstRow(section, item) {
        let row = $('.item-row[data-section="' + section + '"]').first();
        row.find('input[name="operational_needs[]"]').val(item.operational_needs);
        row.find('input[name="description[]"]').val(item.description);
        row.find('.unit_qty').val(item.unit_qty);
        row.find('.unit_cost').val(item.unit_cost);
        row.find('.unit_cost_display').val(formatRupiah(item.unit_cost));
        calculateRow(row);
    }

    // Add item row to section
    function addItemRow(section, item) {
        let lastRow = $('.item-row[data-section="' + section + '"]').last();
        let newRow = lastRow.clone();
        
        newRow.find('input[name="operational_needs[]"]').val(item.operational_needs);
        newRow.find('input[name="description[]"]').val(item.description);
        newRow.find('.unit_qty').val(item.unit_qty);
        newRow.find('.unit_cost').val(item.unit_cost);
        newRow.find('.unit_cost_display').val(formatRupiah(item.unit_cost));
        
        let totalRow = $('.section-total-row[data-section="' + section + '"]');
        newRow.insertBefore(totalRow);
        
        calculateRow(newRow);
        updateSectionNumbering(section);
        updateRemoveButtons(section);
    }

    // Reset Copy
    $('#resetCopy').on('click', function() {
        $('#copyFrom').val('').trigger('change');
        
        $('#project_name').val('');
        $('#project_number').val('');
        $('#project_value').val('');
        $('#project_value_display').val('');
        $('#location_project').val('');
        $('#date').val('{{ date("Y-m-d") }}');
        $('#employee').val([]).trigger('change');

        // Reset items to one row per section
        $('.item-row').each(function() {
            $(this).find('input[type="text"]').not('.unit_cost, .total_cost, .max_cost').val('');
            $(this).find('input[type="number"]').val('0');
            $(this).find('.unit_cost, .total_cost, .max_cost').val('');
            $(this).find('.unit_cost_display, .total_cost_display, .max_cost_display').val('');
            $(this).find('.percent, .status-field').val('');
        });

        // Remove extra rows, keep only first row per section
        $('.item-row[data-section="A"]:not(:first)').remove();
        $('.item-row[data-section="B"]:not(:first)').remove();
        $('.item-row[data-section="C"]:not(:first)').remove();

        updateRemoveButtons('A');
        updateRemoveButtons('B');
        updateRemoveButtons('C');
        recalculateAllRows();
    });

    // Format Project Value
    $('#project_value_display').on('keyup', function() {
        let value = parseRupiah($(this).val());
        $(this).val(formatRupiah(value));
        $('#project_value').val(value);
        recalculateAllRows();
    });

    // Format Unit Cost
    $(document).on('keyup', '.unit_cost_display', function() {
        let row = $(this).closest('tr');
        let value = parseRupiah($(this).val());
        $(this).val(formatRupiah(value));
        row.find('.unit_cost').val(value);
        calculateRow(row);
        let section = row.data('section');
        calculateSectionTotal(section);
    });

    // Calculate row ketika qty berubah
    $(document).on('input', '.unit_qty', function() {
        let row = $(this).closest('tr');
        calculateRow(row);
        let section = row.data('section');
        calculateSectionTotal(section);
    });

    // Function untuk calculate per row
    function calculateRow(row) {
        let qty = parseFloat(row.find('.unit_qty').val() || 0);
        let unitCost = parseFloat(row.find('.unit_cost').val() || 0);
        let projectValue = parseFloat($('#project_value').val() || 0);

        let totalCost = qty * unitCost;
        let maxCost = totalCost;

        row.find('.total_cost').val(totalCost);
        row.find('.total_cost_display').val(formatRupiah(totalCost));
        row.find('.max_cost').val(maxCost);
        row.find('.max_cost_display').val(formatRupiah(maxCost));

        let percent = (projectValue > 0) ? (totalCost / projectValue) * 100 : 0;
        row.find('.percent').val(percent.toFixed(2) + '%');

        let statusField = row.find('.status-field');
        if (totalCost > projectValue && projectValue > 0) {
            statusField.val('OVER').css({'color': 'red', 'font-weight': 'bold'});
        } else {
            statusField.val('OK').css({'color': 'green', 'font-weight': 'bold'});
        }
    }

    // Calculate section total
    function calculateSectionTotal(section) {
        let sectionTotal = 0;
        $('.item-row[data-section="' + section + '"]').each(function() {
            let totalCost = parseFloat($(this).find('.total_cost').val() || 0);
            sectionTotal += totalCost;
        });

        $('.section-total-row[data-section="' + section + '"] .section-total-display').text(formatRupiah(sectionTotal));
        calculateGrandTotal();
    }

    // Calculate grand total
    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.item-row').each(function() {
            let totalCost = parseFloat($(this).find('.total_cost').val() || 0);
            grandTotal += totalCost;
        });
        $('#grand-total-display').text(formatRupiah(grandTotal));
    }

    // Recalculate semua row
    function recalculateAllRows() {
        $('.item-row').each(function() {
            calculateRow($(this));
        });
        calculateSectionTotal('A');
        calculateSectionTotal('B');
        calculateSectionTotal('C');
    }

    // Add row button
    $(document).on('click', '.btn-add-row', function() {
        let section = $(this).data('section');
        let category = '';
        
        if (section === 'A') category = 'honorarium';
        else if (section === 'B') category = 'operational';
        else if (section === 'C') category = 'consumable';
        
        let lastRow = $('.item-row[data-section="' + section + '"]').last();
        let newRow = lastRow.clone();
        
        newRow.find('input[type="text"]').not('.unit_cost, .total_cost, .max_cost').val('');
        newRow.find('input[type="number"]').val('0');
        newRow.find('.unit_cost, .total_cost, .max_cost').val('');
        newRow.find('.unit_cost_display, .total_cost_display, .max_cost_display').val('');
        newRow.find('.percent, .status-field').val('');
        
        let totalRow = $('.section-total-row[data-section="' + section + '"]');
        newRow.insertBefore(totalRow);
        
        updateSectionNumbering(section);
        updateRemoveButtons(section);
        calculateSectionTotal(section);
    });

    // Remove row button
    $(document).on('click', '.btn-remove-row', function() {
        let row = $(this).closest('tr');
        let section = row.data('section');
        let rowCount = $('.item-row[data-section="' + section + '"]').length;
        
        if (rowCount > 1) {
            row.remove();
            updateSectionNumbering(section);
            updateRemoveButtons(section);
            calculateSectionTotal(section);
        } else {
            Swal.fire('Peringatan!', 'Minimal harus ada 1 item di section ini!', 'warning');
        }
    });

    // Update numbering per section
    function updateSectionNumbering(section) {
        $('.item-row[data-section="' + section + '"]').each(function(index) {
            $(this).find('.numbering').text(index + 1);
        });
    }

    // Update visibility tombol remove
    function updateRemoveButtons(section) {
        let rowCount = $('.item-row[data-section="' + section + '"]').length;
        
        if (rowCount > 1) {
            $('.item-row[data-section="' + section + '"] .btn-remove-row').show();
        } else {
            $('.item-row[data-section="' + section + '"] .btn-remove-row').hide();
        }
    }

    // Submit form dengan AJAX (mengikuti pola QuotationController)
    $('#pakForm').on('submit', function(e) {
        e.preventDefault();

        // Validasi minimal 1 item per section
        let hasItemA = $('.item-row[data-section="A"]').find('input[name="operational_needs[]"]').filter(function() {
            return $(this).val().trim() !== '';
        }).length > 0;

        let hasItemB = $('.item-row[data-section="B"]').find('input[name="operational_needs[]"]').filter(function() {
            return $(this).val().trim() !== '';
        }).length > 0;

        let hasItemC = $('.item-row[data-section="C"]').find('input[name="operational_needs[]"]').filter(function() {
            return $(this).val().trim() !== '';
        }).length > 0;

        if (!hasItemA || !hasItemB || !hasItemC) {
            Swal.fire('Error!', 'Setiap section harus memiliki minimal 1 item yang diisi', 'error');
            return;
        }

        const formEl = this;
        const $form = $(formEl);
        const btn = $form.find('button[type="submit"]');
        const btnOriginalText = btn.html();

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
                            text: response.message || 'PAK berhasil disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = "{{ route('pak.index') }}";
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: (response && response.message) ? response.message : 'Terjadi kesalahan.',
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

    // Initial setup
    updateRemoveButtons('A');
    updateRemoveButtons('B');
    updateRemoveButtons('C');
    recalculateAllRows();
});
</script>
@endsection