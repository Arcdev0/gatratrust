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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Gagal menyimpan data:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('spk.store') }}" method="POST">
                            @include('spk._form', ['spk' => null])
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi gagal',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        @endif
    </script>
@endsection
