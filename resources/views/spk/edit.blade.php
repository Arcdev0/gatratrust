@extends('layout.app')

@section('title', 'Edit SPK')

@section('content')
    <div class="page-wrap">
        <div class="main-content">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <h5>Edit SPK</h5>
                            </div>
                        </div>
                        <div class="col-lg-4 text-right">
                            <a href="{{ route('spk.show', $spk) }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('spk.update', $spk) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('spk._form')
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
