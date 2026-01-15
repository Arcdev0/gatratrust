@extends('layout.app')

@section('title', 'COA')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        {{-- BUTTON TOP --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0 text-primary fw-bold">COA</h3>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahCOA">
                <i class="fas fa-plus me-2"></i>Tambah
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">Daftar Account</h5>
                <div class="table-responsive">
                    <div class="container" id="containerListCOA">
                        {{-- AJAX will render here --}}
                    </div>
                </div>

            </div>
        </div>

        {{-- ========================= MODAL TAMBAH ========================= --}}
        <div class="modal fade" id="modalTambahCOA" tabindex="-1" aria-labelledby="modalTambahCOATitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahCOATitle">Add New Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="mt-2">
                            <label for="groupAccount" class="form-label fw-semibold">Group Account</label>
                            <select class="form-control" id="groupAccount">
                                <option value="" selected>Select Group Account</option>
                                @foreach ($groupAccounts as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="errGroupAccount" class="text-danger mt-1 d-none">Silahkan pilih group account</div>
                        </div>

                        <div class="mt-3">
                            <label for="codeAccountID" class="form-label fw-semibold">Code Account ID*</label>
                            <input type="text" class="form-control" id="codeAccountID" placeholder="Input Account ID"
                                required>
                            <div id="errCodeAccountID" class="text-danger mt-1 d-none">Silahkan masukkan Code Account</div>
                        </div>

                        <div class="mt-3">
                            <label for="nameAccount" class="form-label fw-semibold">Nama*</label>
                            <input type="text" class="form-control" id="nameAccount" placeholder="Input Name" required>
                            <div id="errNameAccount" class="text-danger mt-1 d-none">Silahkan masukkan Nama</div>
                        </div>

                        <div class="mt-3">
                            <label for="descriptionAccount" class="form-label fw-semibold">Description</label>
                            <input type="text" class="form-control" id="descriptionAccount"
                                placeholder="Input Description">
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Set as Group</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="setGroup">
                                <label class="form-check-label" for="setGroup">Yes</label>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="defaultPosisi" class="form-label fw-semibold">Default Posisi*</label>
                            <select class="form-control" id="defaultPosisi" required>
                                <option value="" disabled selected>Select Default Position</option>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                            <div id="errDefaultPosisi" class="text-danger mt-1 d-none">Silahkan pilih posisi</div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveCOA" class="btn btn-primary">Save COA</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ========================= END MODAL TAMBAH ========================= --}}

        {{-- ========================= MODAL UPDATE ========================= --}}
        <div class="modal fade" id="modalUpdateCOA" tabindex="-1" aria-labelledby="modalUpdateCOATitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdateCOATitle">Update Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="mt-2">
                            <label for="editGroupAccount" class="form-label fw-semibold">Group Account</label>
                            <select class="form-control" id="editGroupAccount">
                                <option value="" selected>Select Group Account</option>
                                @foreach ($groupAccounts as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="errEditGroupAccount" class="text-danger mt-1 d-none">Silahkan pilih group account
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="editCodeAccountID" class="form-label fw-semibold">Code Account ID*</label>
                            <input type="text" class="form-control" id="editCodeAccountID" required>
                            <div id="errEditCodeAccountID" class="text-danger mt-1 d-none">Silahkan masukkan Code Account
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="editNameAccount" class="form-label fw-semibold">Nama*</label>
                            <input type="text" class="form-control" id="editNameAccount" required>
                            <div id="errEditNameAccount" class="text-danger mt-1 d-none">Silahkan masukkan Nama</div>
                        </div>

                        <div class="mt-3">
                            <label for="editDescriptionAccount" class="form-label fw-semibold">Description</label>
                            <input type="text" class="form-control" id="editDescriptionAccount">
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Set as Group</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editSetGroup">
                                <label class="form-check-label" for="editSetGroup">Yes</label>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="editDefaultPosisi" class="form-label fw-semibold">Default Posisi*</label>
                            <select class="form-control" id="editDefaultPosisi" required>
                                <option value="" disabled selected>Select Default Position</option>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                            <div id="errEditDefaultPosisi" class="text-danger mt-1 d-none">Silahkan pilih posisi</div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="updateCOA" class="btn btn-primary">Update COA</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ========================= END MODAL UPDATE ========================= --}}

    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {

            // CSRF untuk semua AJAX (lebih enak daripada header manual tiap request)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Select2 (pastikan select2 sudah include)
            $('#groupAccount').select2({
                placeholder: "Select Group Account",
                width: "100%",
                dropdownParent: $('#modalTambahCOA')
            });

            $('#editGroupAccount').select2({
                placeholder: "Select Group Account",
                width: "100%",
                dropdownParent: $('#modalUpdateCOA')
            });

            function showMessage(type, text) {
                return Swal.fire({
                    icon: type,
                    title: text,
                    showConfirmButton: true,
                    timer: 1500
                });
            }

            // Load daftar COA via AJAX
            function loadCOAList() {
                const loadSpin = `
            <div class="d-flex justify-content-center align-items-center my-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `;
                $('#containerListCOA').html(loadSpin);

                $.ajax({
                    url: "{{ route('coa.list') }}",
                    method: "GET",
                    success: function(res) {
                        $('#containerListCOA').html(res.html);

                        // destroy bila sebelumnya sudah ada
                        if ($.fn.DataTable.isDataTable('#tableCOA')) {
                            $('#tableCOA').DataTable().destroy();
                        }

                        $('#tableCOA').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            info: true,
                            lengthChange: true
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        $('#containerListCOA').html(
                            '<div class="text-danger">Gagal memuat data. Silakan coba lagi.</div>');
                    }
                });
            }

            loadCOAList();

            // Generate next code saat pilih group
            $('#groupAccount').on('change', function() {
                const parentId = $(this).val() || null;

                $.ajax({
                    url: "{{ route('coa.next-code') }}",
                    method: "POST",
                    data: {
                        accountId: parentId
                    },
                    success: function(res) {
                        $('#codeAccountID').val(res.next_account_code || '');
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            });

            // Tambah COA
            $('#saveCOA').on('click', function() {
                const codeAccountID = $('#codeAccountID').val();
                const groupAccount = $('#groupAccount').val();
                const nameAccount = $('#nameAccount').val();
                const description = $('#descriptionAccount').val();
                const setGroup = $('#setGroup').is(':checked') ? 1 : 0;
                const defaultPosisi = $('#defaultPosisi').val();

                // reset error
                $('#errCodeAccountID,#errNameAccount,#errDefaultPosisi').addClass('d-none');

                if (!codeAccountID || !nameAccount || !defaultPosisi) {
                    if (!codeAccountID) $('#errCodeAccountID').removeClass('d-none');
                    if (!nameAccount) $('#errNameAccount').removeClass('d-none');
                    if (!defaultPosisi) $('#errDefaultPosisi').removeClass('d-none');
                    return;
                }

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Anda akan menambahkan COA baru",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#5D87FF',
                    cancelButtonColor: '#49BEFF',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu, sedang menyimpan data.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: "{{ route('coa.store') }}",
                        method: "POST",
                        data: {
                            code_account_id: codeAccountID,
                            group_account: groupAccount,
                            name: nameAccount,
                            description: description,
                            set_group: setGroup,
                            default_position: defaultPosisi,
                        },
                        success: function(res) {
                            Swal.close();
                            $('#modalTambahCOA').modal('hide');
                            showMessage("success", "COA berhasil ditambahkan");
                            loadCOAList();
                        },
                        error: function(xhr) {
                            Swal.close();
                            const msg = xhr.responseJSON?.message ||
                                'Terjadi kesalahan.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan!',
                                text: msg
                            });
                        }
                    });
                });
            });

            // Open modal edit
            $(document).on('click', '.editCOA', function() {
                const coaId = $(this).data('id');

                $.ajax({
                    url: "{{ url('coa') }}/" + coaId,
                    method: "GET",
                    success: function(res) {
                        $('#editCodeAccountID').val(res.code_account_id || '');
                        $('#editGroupAccount').val(res.parent_id).trigger('change');
                        $('#editNameAccount').val(res.name || '');
                        $('#editDescriptionAccount').val(res.description || '');
                        $('#editSetGroup').prop('checked', !!res.set_as_group);
                        $('#editDefaultPosisi').val(res.default_posisi || '').trigger('change');

                        $('#updateCOA').data('id', coaId);

                        const modal = new bootstrap.Modal(document.getElementById(
                            'modalUpdateCOA'));
                        modal.show();
                    },
                    error: function() {
                        showMessage("error", "Terjadi kesalahan saat mengambil data COA");
                    }
                });
            });

            // Update COA
            $('#updateCOA').on('click', function() {
                const coaId = $(this).data('id');

                const codeAccountID = $('#editCodeAccountID').val();
                const groupAccount = $('#editGroupAccount').val();
                const nameAccount = $('#editNameAccount').val();
                const description = $('#editDescriptionAccount').val();
                const setGroup = $('#editSetGroup').is(':checked') ? 1 : 0;
                const defaultPosisi = $('#editDefaultPosisi').val();

                // reset error
                $('#errEditCodeAccountID,#errEditNameAccount,#errEditDefaultPosisi').addClass('d-none');

                if (!codeAccountID || !nameAccount || !defaultPosisi) {
                    if (!codeAccountID) $('#errEditCodeAccountID').removeClass('d-none');
                    if (!nameAccount) $('#errEditNameAccount').removeClass('d-none');
                    if (!defaultPosisi) $('#errEditDefaultPosisi').removeClass('d-none');
                    return;
                }

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Anda akan memperbarui COA ini",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#5D87FF',
                    cancelButtonColor: '#49BEFF',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu, sedang memperbarui data.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: "{{ url('coa') }}/" + coaId,
                        method: "PUT",
                        data: {
                            code_account_id: codeAccountID,
                            group_account: groupAccount,
                            name: nameAccount,
                            description: description,
                            set_as_group: setGroup,
                            default_posisi: defaultPosisi,
                        },
                        success: function(res) {
                            Swal.close();
                            $('#modalUpdateCOA').modal('hide');
                            showMessage("success", "COA berhasil diperbarui");
                            loadCOAList();
                        },
                        error: function(xhr) {
                            Swal.close();
                            const msg = xhr.responseJSON?.message ||
                                'Terjadi kesalahan.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan!',
                                text: msg
                            });
                        }
                    });
                });
            });

            // Delete COA
            $(document).on('click', '.btndeleteCOA', function(e) {
                e.preventDefault();
                const coaId = $(this).data('id');

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#5D87FF',
                    cancelButtonColor: '#49BEFF',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu, sedang menghapus data.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: "{{ url('coa') }}/" + coaId,
                        method: "DELETE",
                        success: function(res) {
                            Swal.close();
                            showMessage("success", "COA berhasil dihapus");
                            loadCOAList();
                        },
                        error: function(xhr) {
                            Swal.close();
                            const msg = xhr.responseJSON?.message ||
                                'Gagal menghapus COA';
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan!',
                                text: msg
                            });
                        }
                    });
                });
            });

            // Reset modal tambah
            $('#modalTambahCOA').on('hidden.bs.modal', function() {
                $('#groupAccount').val('').trigger('change');
                $('#codeAccountID').val('');
                $('#nameAccount').val('');
                $('#descriptionAccount').val('');
                $('#defaultPosisi').val('').trigger('change');
                $('#setGroup').prop('checked', false);

                $('#errGroupAccount,#errCodeAccountID,#errNameAccount,#errDefaultPosisi').addClass(
                    'd-none');
            });

            // Reset modal update
            $('#modalUpdateCOA').on('hidden.bs.modal', function() {
                $('#editGroupAccount').val('').trigger('change');
                $('#editCodeAccountID').val('');
                $('#editNameAccount').val('');
                $('#editDescriptionAccount').val('');
                $('#editDefaultPosisi').val('').trigger('change');
                $('#editSetGroup').prop('checked', false);

                $('#errEditGroupAccount,#errEditCodeAccountID,#errEditNameAccount,#errEditDefaultPosisi')
                    .addClass('d-none');
            });

        });
    </script>
@endsection
