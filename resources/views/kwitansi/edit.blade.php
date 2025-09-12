@extends('layout.app')

@section('title', 'Edit Kwitansi')

@section('content')
<div class="container-fluid">
    <h3 class="text-primary font-weight-bold">Edit Kwitansi</h3>
    <div class="card mb-3">
        <div class="card-body">
            <form id="kwitansiForm" method="POST" action="{{ route('kwitansi.update', $kwitansi->id) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">No. Invoice</label>
                        <select name="invoice_id" id="invoice_id" class="form-control" required>
                            <option value="">-- Pilih Invoice --</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" {{ $kwitansi->invoice_id == $inv->id ? 'selected' : '' }}>
                                    {{ $inv->invoice_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ $kwitansi->payment_date }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Dibayarkan</label>
                        <input type="text" id="amountPaidDisplay" class="form-control text-end" value="{{ 'Rp. ' . number_format($kwitansi->amount_paid, 0, ',', '.') }}">
                        <input type="hidden" name="amount_paid" id="amount_paid" value="{{ $kwitansi->amount_paid }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="note" class="form-control" rows="3">{{ $kwitansi->note }}</textarea>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">Back</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#invoice_id').select2({
            placeholder: "-- Pilih Invoice --",
            width: '100%'
        });

        function formatRupiah(angka) {
            return 'Rp. ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        function parseRupiah(str) {
            return parseFloat(str.replace(/[^0-9]/g, '')) || 0;
        }

        $('#amountPaidDisplay').on('input', function() {
            let val = parseRupiah($(this).val());
            $(this).val(formatRupiah(val));
            $('#amount_paid').val(val);
        });

        $('#kwitansiForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();

            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Perubahan kwitansi ini akan disimpan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Harap tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('kwitansi.index') }}";
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Terjadi kesalahan'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Server error'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection