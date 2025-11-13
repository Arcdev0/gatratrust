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
</style>
@section('content')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Tambah PAK</h3>
            <a href="{{ route('pak.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pak.store') }}" method="POST">
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
                                <input type="text" class="form-control @error('location_project') is-invalid @enderror"
                                    id="location_project" name="location_project" value="{{ old('location_project') }}"
                                    required>
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
                                    name="date" value="{{ old('date') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee <span class="text-danger">*</span></label>
                                <select multiple id="employee" name="employee[]" class="form-control employee-select">
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ is_array(old('employee')) && in_array($emp->id, old('employee')) ? 'selected' : '' }}>
                                            {{ $emp->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="employee-selected-list" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered text-center align-middle table-sm" id="pak-table">
                            <thead class="text-white">
                                <tr>
                                    <th style="width:40px;">NO</th>
                                    <th>Operational Needs</th>
                                    <th>Description</th>
                                    <th style="width:90px;">Unit Qty</th>
                                    <th style="width:140px;">Unit Cost</th>
                                    <th style="width:140px;">Total Cost</th>
                                    <th style="width:140px;">MAX COST</th>
                                    <th style="width:60px;">%</th>
                                    <th style="width:100px;">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- ========== SECTION A ========== -->
                                <tr class="section-row">
                                    <td colspan="9" class="section-title" style="text-align: left">A. Honorarium</td>
                                </tr>

                                <!-- Item Row -->
                                <tr class="item-row">
                                    <td class="numbering">1</td>
                                    <td><input type="text" class="form-control" name="operational_needs[]"></td>
                                    <td><input type="text" class="form-control" name="description[]"></td>
                                    <td><input type="number" class="form-control unit_qty" name="unit_qty[]"></td>
                                    <td>
                                        <input type="text" class="form-control unit_cost_display" placeholder="Rp 0">
                                        <input type="hidden" class="unit_cost" name="unit_cost[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total_cost_display" readonly
                                            placeholder="Rp 0">
                                        <input type="hidden" class="total_cost" name="total_cost[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control max_cost_display" readonly
                                            placeholder="Rp 0">
                                        <input type="hidden" class="max_cost" name="max_cost[]">
                                    </td>
                                    <td><input type="text" class="form-control percent" name="percent[]" readonly></td>
                                    <td>
                                        <input type="text" class="form-control status-field" name="status[]" readonly>
                                    </td>
                                </tr>

                                <tr class="section-row">
                                    <td colspan="9" class="section-title" style="text-align: left">TOTAL I (MAX 70%)</td>
                                </tr>

                                <tr class="section-row">
                                    <td colspan="9" class="section-title" style="text-align: left">B. Operational</td>
                                </tr>
                                <tr class="item-row">
                                    <td class="numbering">1</td>
                                    <td><input type="text" class="form-control" name="operational_needs[]"></td>
                                    <td><input type="text" class="form-control" name="description[]"></td>
                                    <td><input type="number" class="form-control unit_qty" name="unit_qty[]"></td>
                                    <td>
                                        <input type="text" class="form-control unit_cost_display" placeholder="Rp 0">
                                        <input type="hidden" class="unit_cost" name="unit_cost[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total_cost_display" readonly
                                            placeholder="Rp 0">
                                        <input type="hidden" class="total_cost" name="total_cost[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control max_cost_display" readonly
                                            placeholder="Rp 0">
                                        <input type="hidden" class="max_cost" name="max_cost[]">
                                    </td>
                                    <td><input type="text" class="form-control percent" name="percent[]" readonly></td>
                                    <td>
                                        <input type="text" class="form-control status-field" name="status[]" readonly>
                                    </td>
                                </tr>
                                <tr class="section-row">
                                    <td colspan="9" class="section-title" style="text-align: left">TOTAL I (MAX 10%)</td>
                                </tr>
                                <tr class="section-row">
                                    <td colspan="9" class="section-title" style="text-align: left">C. Consumable</td>
                                </tr>
                                <tr class="item-row">
                                    <td class="numbering">1</td>
                                    <td><input type="text" class="form-control" name="operational_needs[]"></td>
                                    <td><input type="text" class="form-control" name="description[]"></td>
                                    <td><input type="number" class="form-control unit_qty" name="unit_qty[]"></td>
                                    <td>
                                        <input type="text" class="form-control unit_cost_display" placeholder="Rp 0">
                                        <input type="hidden" class="unit_cost" name="unit_cost[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total_cost_display" readonly
                                            placeholder="Rp 0">
                                        <input type="hidden" class="total_cost" name="total_cost[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control max_cost_display" readonly
                                            placeholder="Rp 0">
                                        <input type="hidden" class="max_cost" name="max_cost[]">
                                    </td>
                                    <td><input type="text" class="form-control percent" name="percent[]" readonly></td>
                                    <td>
                                        <input type="text" class="form-control status-field" name="status[]" readonly>
                                    </td>
                                </tr>
                                <tr class="section-row">
                                    <td colspan="9" class="section-title" style="text-align: left">TOTAL I (MAX 5%)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="items-container">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan PAK
                            </button>
                            <a href="{{ route('pak.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let itemIndex = 1;

            // Format angka ke Rupiah
            function formatRupiah(angka) {
                let number_string = angka.toString().replace(/[^,\d]/g, '');
                let split = number_string.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return 'Rp ' + rupiah;
            }

            // Parse Rupiah ke angka
            function parseRupiah(rupiah) {
                return parseInt(rupiah.replace(/[^\d]/g, '')) || 0;
            }

            // Format Project Value dan recalculate semua row
            $('#project_value_display').on('keyup', function () {
                let value = $(this).val().replace(/[^\d]/g, '');
                $(this).val(formatRupiah(value));
                $('#project_value').val(value);

                // Recalculate semua row ketika project value berubah
                recalculateAllRows();
            });

            // Format Unit Cost
            $(document).on('keyup', '.unit_cost_display', function () {
                let row = $(this).closest('tr');
                let value = $(this).val().replace(/[^\d]/g, '');
                $(this).val(formatRupiah(value));
                row.find('.unit_cost').val(value);
                calculateRow(row);
            });

            // Calculate row ketika qty berubah
            $(document).on('input', '.unit_qty', function () {
                let row = $(this).closest('tr');
                calculateRow(row);
            });

            // Function untuk calculate per row
            function calculateRow(row) {
                let qty = parseFloat(row.find('.unit_qty').val() || 0);
                let unitCost = parseFloat(row.find('.unit_cost').val() || 0);
                let projectValue = parseFloat($('#project_value').val() || 0);

                // Total Cost = Unit Qty × Unit Cost
                let totalCost = qty * unitCost;

                // Set total cost
                row.find('.total_cost').val(totalCost);
                row.find('.total_cost_display').val(formatRupiah(totalCost));

                // Max Cost = Total Cost (sama dengan total cost karena max adalah yang dibolehkan)
                let maxCost = totalCost;
                row.find('.max_cost').val(maxCost);
                row.find('.max_cost_display').val(formatRupiah(maxCost));

                // Calculate percentage terhadap project value
                let percent = (projectValue > 0) ? (totalCost / projectValue) * 100 : 0;
                row.find('.percent').val(percent.toFixed(2) + '%');

                // Set status: OVER jika total cost > project value
                let statusField = row.find('.status-field');
                if (totalCost > projectValue && projectValue > 0) {
                    statusField.val('OVER');
                    statusField.css('color', 'red');
                    statusField.css('font-weight', 'bold');
                } else {
                    statusField.val('OK');
                    statusField.css('color', 'green');
                    statusField.css('font-weight', 'bold');
                }
            }

            // Recalculate semua row
            function recalculateAllRows() {
                $('.item-row').each(function () {
                    calculateRow($(this));
                });
            }

            // Initialize Select2
            $('#employee').select2({
                placeholder: "Pilih satu atau lebih karyawan",
                width: "100%",
            });

            // Add item functionality (jika diperlukan)
            $('#add-item').click(function () {
                // Implementasi add item jika diperlukan
            });

            // Remove item
            $(document).on('click', '.remove-item', function () {
                $(this).closest('.item-row').remove();
                updateItemNumbers();
            });

            // Update item numbers
            function updateItemNumbers() {
                $('.item-row').each(function (index) {
                    $(this).find('.numbering').text(index + 1);
                });
            }

            // Initial calculation untuk semua row yang ada
            recalculateAllRows();
        });
    </script>
@endsection
