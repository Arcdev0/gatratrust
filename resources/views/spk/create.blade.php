@extends('layout.app')

@section('title', 'Tambah SPK')

@section('content')
    <div class="page-wrap">
        <div class="main-content">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <h5>Tambah SPK</h5>
                            </div>
                        </div>
                        <div class="col-lg-4 text-right">
                            <a href="{{ route('spk.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('spk.store') }}" method="POST">
                            @include('spk._form')
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
