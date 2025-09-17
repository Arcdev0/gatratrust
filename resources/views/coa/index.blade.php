@extends('layout.app')

@section('title', 'COA')

@section('content')


    <!---Container Fluid-->
    <div class="container-fluid" id="container-wrapper">

        <!-- Modal tambah COA -->
        <div class="modal fade" id="modalTambahCOA" tabindex="-1" role="dialog" aria-labelledby="modalTambahCOATitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahCOATitle">Add New Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="mt-3">
                            <label for="groupAccount" class="form-label fw-bold">Group Account</label>
                            <select class="form-control" id="groupAccount" required>
                                <option value="" selected>Select Group Account</option>
                                @foreach ($groupAccounts as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="errGroupAccount" class="text-danger mt-1 d-none">Silahkan pilih group account</div>
                        </div>
                        <div class="mt-3">
                            <label for="codeAccountID" class="form-label fw-bold">Code Account ID*</label>
                            <input type="text" class="form-control" id="codeAccountID" placeholder="Input Account ID"
                                required>
                            <div id="errCodeAccountID" class="text-danger mt-1 d-none">
                            Silahkan Masukkan Code Account
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="nameAccount" class="form-label fw-bold">Nama*</label>
                            <input type="text" class="form-control" id="nameAccount" placeholder="Input Name" required>
                            <div id="errNameAccount" class="text-danger mt-1 d-none">  Silahkan Masukkan Nama</div>
                        </div>
                        <div class="mt-3">
                            <label for="descriptionAccount" class="form-label fw-bold">Description</label>
                            <input type="text" class="form-control" id="descriptionAccount"
                                placeholder="Input Description">
                        </div>
                        <div class="mt-3">
                            <label for="setGroup" class="form-label fw-bold">Set as Group</label>
                            <div>
                                <input type="checkbox" id="setGroup" name="setGroup"> Yes
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="defaultPosisi" class="form-label fw-bold">Default Posisi*</label>
                            <select class="form-control" id="defaultPosisi" required>
                                <option value="" disabled selected>Select Default Position</option>
                                <option value="Debit">Debit</option>
                                <option value="Credit">Credit</option>
                            </select>
                            <div id="errDefaultPosisi" class="text-danger mt-1 d-none">Silahkan Pilih Posisi</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="button" id="saveCOA" class="btn btn-primary">Save COA</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal tambah COA -->


        <!-- Modal Update COA -->
        <div class="modal fade" id="modalUpdateCOA" tabindex="-1" role="dialog" aria-labelledby="modalUpdateCOATitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdateCOATitle">Update Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-3">
                            <label for="editGroupAccount" class="form-label fw-bold">Group Account</label>
                            <select class="form-control" id="editGroupAccount" required>
                                <option value="" selected>Select Group Account</option>
                                @foreach ($groupAccounts as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="errEditGroupAccount" class="text-danger mt-1 d-none">Please select a group account
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="editCodeAccountID" class="form-label fw-bold">Code Account ID*</label>
                            <input type="text" class="form-control" id="editCodeAccountID"
                                placeholder="Input Account ID" required>
                            <div id="errEditCodeAccountID" class="text-danger mt-1 d-none">Silahkan Masukkan Code Account</div>
                        </div>
                        <div class="mt-3">
                            <label for="editNameAccount" class="form-label fw-bold">Name*</label>
                            <input type="text" class="form-control" id="editNameAccount" placeholder="Input Name"
                                required>
                            <div id="errEditNameAccount" class="text-danger mt-1 d-none">Silahkan Masukkan Nama</div>
                        </div>
                        <div class="mt-3">
                            <label for="editDescriptionAccount" class="form-label fw-bold">Description</label>
                            <input type="text" class="form-control" id="editDescriptionAccount"
                                placeholder="Input Description">
                        </div>
                        <div class="mt-3">
                            <label for="editSetGroup" class="form-label fw-bold">Set as Group</label>
                            <div>
                                <input type="checkbox" id="editSetGroup" name="editSetGroup"> Yes
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="editDefaultPosisi" class="form-label fw-bold">Default Posisi*</label>
                            <select class="form-control" id="editDefaultPosisi" required>
                                <option value="" disabled selected>Select Default Position</option>
                                <option value="Debit">Debit</option>
                                <option value="Credit">Credit</option>
                            </select>
                            <div id="errEditDefaultPosisi" class="text-danger mt-1 d-none">This field is required</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="button" id="updateCOA" class="btn btn-primary">Update COA</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Update COA -->


        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h3 class="text-primary font-weight-bold">COA</h3>
            {{-- <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
      </ol> --}}
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex mb-2 mr-3 float-right">
                            {{-- <button class="btn btn-primary" id="btnModalTambahCostumer">Tambah</button> --}}
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#modalTambahCOA" id="modalTambahBook"><span class="pr-2"><i
                                        class="fas fa-plus"></i></span>Tambah</button>
                        </div>

                        <h4 class="pt-5 ml-3">Daftar Account</h4>
                        <div class="ml-3 table-responsive" id="containerListCOA">
                            {{-- <ul>
                                <li>
                                    <a href="">1.0.00 - ASET</a>
                                    <button class="btn-danger" style="font-size: 12px;" onclick="removeItem(this)"><i
                                            class="fas fa-trash"></i></button>
                                    <ul>
                                        <li>
                                            <a href="">1.1.00 - Sub Bab A</a>
                                            <button class="btn-danger" style="font-size: 12px;"
                                                onclick="removeItem(this)"><i class="fas fa-trash"></i></button>
                                            <ul>
                                                <li>
                                                    <a href="">1.1.1 - Sub Sub Bab A.1</a>
                                                    <button class="btn-danger" style="font-size: 12px;"
                                                        onclick="removeItem(this)"><i class="fas fa-trash"></i></button>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="">1.2.00 - Sub Bab B</a>
                                            <button class="btn-danger" style="font-size: 12px;"
                                                onclick="removeItem(this)"><i class="fas fa-trash"></i></button>
                                            <ul>
                                                <li>
                                                    <a href="">1.2.1 - Sub Sub Bab B.1</a>
                                                    <button class="btn-danger" style="font-size: 12px;"
                                                        onclick="removeItem(this)"><i class="fas fa-trash"></i></button>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!---Container Fluid-->

@endsection
@section('script')
    <script>
    $(document).ready(function() {
        $('#editGroupAccount').select2({
            placeholder: "Select Group Account",
            minimumResultsForSearch: 0,
            width: "100%",
            dropdownParent: $('#modalUpdateCOA')
        });
        $('#groupAccount').select2({
            placeholder: "Select Group Account",
            minimumResultsForSearch: 0,
            width: "100%",
            dropdownParent: $('#modalTambahCOA')
        });

        // Helper SweetAlert
        function showMessage(type, text) {
            return Swal.fire({
                icon: type,
                title: text,
                showConfirmButton: true,
                timer: 1500
            });
        }

        // Load daftar COA
        function loadCOAList() {
            const loadSpin = `
                <div class="d-flex justify-content-center align-items-center mt-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            `;
            $('#containerListCOA').html(loadSpin);

            $.ajax({
                url: "{{ route('getlistcoa') }}",
                method: 'GET',
                success: function(response) {
                    $('#containerListCOA').html(response.html);
                    $('#tableCOA').DataTable({
                        paging: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        lengthChange: true
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error loading COA list:", error);
                    $('#containerListCOA').html('<p>Gagal memuat data. Silakan coba lagi.</p>');
                }
            });
        }

        loadCOAList();

        // Generate next code
        $('#groupAccount').on('change', function() {
            const selectedSubChildId = $(this).val();
            $.ajax({
                url: "{{ route('getNextAccountCode') }}",
                method: 'GET',
                data: { accountId: selectedSubChildId },
                success: function(response) {
                    $('#codeAccountID').val(response.next_account_code);
                },
                error: function(error) {
                    console.log('Error fetching account code:', error);
                }
            });
        });

        // Tambah COA
        $('#saveCOA').on('click', function() {
            var codeAccountID = $('#codeAccountID').val();
            var groupAccount = $('#groupAccount').val();
            var nameAccount = $('#nameAccount').val();
            var descriptionAccount = $('#descriptionAccount').val();
            var setGroup = $('#setGroup').is(':checked') ? 1 : 0;
            var defaultPosisi = $('#defaultPosisi').val();

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
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu, sedang menyimpan data.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/coa/store',
                        method: 'POST',
                        data: {
                            code_account_id: codeAccountID,
                            group_account: groupAccount,
                            name: nameAccount,
                            description: descriptionAccount,
                            set_group: setGroup,
                            default_position: defaultPosisi,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                $('#modalTambahCOA').modal('hide');
                                showMessage("success", "COA Berhasil ditambahkan").then(() => {
                                    window.location.href = "{{ route('coa.index') }}"; // 🔹 redirect
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                title: 'Terjadi Kesalahan!',
                                text: xhr.responseJSON.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Edit COA
        $(document).on('click', '.editCOA', function(e) {
            var coaId = $(this).data('id');
            $.ajax({
                url: '/coa/' + coaId,
                method: 'GET',
                success: function(response) {
                    $('#editCodeAccountID').val(response.code_account_id);
                    $('#editGroupAccount').val(response.parent_id).trigger('change');
                    $('#editNameAccount').val(response.name);
                    $('#editDescriptionAccount').val(response.description);
                    $('#editSetGroup').prop('checked', response.set_as_group);
                    $('#editDefaultPosisi').val(response.default_posisi);
                    $('#modalUpdateCOA').modal('show');
                    $('#updateCOA').data('id', coaId);
                },
                error: function() {
                    showMessage("error", "Terjadi kesalahan saat mengambil data COA");
                }
            });
        });

        // Update COA
        $('#updateCOA').on('click', function() {
            var coaId = $(this).data('id');
            var codeAccountID = $('#editCodeAccountID').val();
            var groupAccount = $('#editGroupAccount').val();
            var nameAccount = $('#editNameAccount').val();
            var descriptionAccount = $('#editDescriptionAccount').val();
            var setGroup = $('#editSetGroup').is(':checked') ? 1 : 0;
            var defaultPosisi = $('#editDefaultPosisi').val();

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
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu, sedang memperbarui data.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/coa/update/' + coaId,
                        method: 'PUT',
                        data: {
                            code_account_id: codeAccountID,
                            group_account: groupAccount,
                            name: nameAccount,
                            description: descriptionAccount,
                            set_as_group: setGroup,
                            default_posisi: defaultPosisi,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                $('#modalUpdateCOA').modal('hide');
                                showMessage("success", "COA berhasil diperbarui").then(() => {
                                    window.location.href = "{{ route('coa.index') }}"; // 🔹 redirect
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                title: 'Terjadi Kesalahan!',
                                text: xhr.responseJSON.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Delete COA
        $(document).on('click', '.btndeleteCOA', function(e) {
            e.preventDefault();
            var coaId = $(this).data('id');

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
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu, sedang menghapus data.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/coa/delete/' + coaId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                showMessage("success", "COA berhasil dihapus").then(() => {
                                    window.location.href = "{{ route('coa.index') }}"; // 🔹 redirect
                                });
                            } else {
                                showMessage("error", "Gagal menghapus COA");
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                title: 'Terjadi Kesalahan!',
                                text: xhr.responseJSON.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Reset modal Tambah
        $('#modalTambahCOA').on('hidden.bs.modal', function() {
            $('#groupAccount,#codeAccountID,#nameAccount,#descriptionAccount,#defaultPosisi').val('');
            $('#setGroup').prop('checked', false);
            $('#errGroupAccount,#errCodeAccountID,#errNameAccount,#errDefaultPosisi').addClass('d-none');
        });

        // Reset modal Update
        $('#modalUpdateCOA').on('hidden.bs.modal', function() {
            $('#editCodeAccountID,#editNameAccount,#editDescriptionAccount,#editDefaultPosisi').val('');
            $('#errEditGroupAccount,#errEditCodeAccountID,#errEditNameAccount,#errEditDefaultPosisi').addClass('d-none');
        });
    });
</script>
@endsection