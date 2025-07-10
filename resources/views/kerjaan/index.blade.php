@extends('layout.app')
@section('title', 'Input Project')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Input Project</h3>
                    <button id="addListProsesBtn" class="btn btn-success" data-toggle="modal" data-target="#addProsesModal">
                        <i class="fas fa-plus"></i> Tambah Input Project
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                        <table class="table table-bordered" id="listProsesTable">
                            <thead style="background-color: #f2f2f2;">
                                <tr>
                                    <th>Nama Proses</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addProsesModal" tabindex="-1" role="dialog" aria-labelledby="addProsesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="addProsesForm" action="{{ route('listproses.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProsesModalLabel">Tambah Proses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_proses">Nama Proses</label>
                            <input type="text" class="form-control" name="nama_proses" id="nama_proses" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editProsesModal" tabindex="-1" role="dialog" aria-labelledby="editProsesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editProsesForm">
                <input type="hidden" name="id" id="editId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProsesModalLabel">Edit Proses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editNamaProses">Nama Proses</label>
                            <input type="text" class="form-control" name="nama_proses" id="editNamaProses" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
