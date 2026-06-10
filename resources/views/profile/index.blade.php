@extends('layout.app')

@section('title', 'Profile')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user bg-blue"></i>
                        <div class="d-inline">
                            <h5>Profile</h5>
                            <span>Kelola informasi akun dan password Anda.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Informasi Akun</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Username</label>
                                <input id="name" type="text" class="form-control" value="{{ auth()->user()->name }}"
                                    readonly>
                                <small class="form-text text-muted">Hubungi admin jika username perlu diubah.</small>
                            </div>

                            <div class="form-group">
                                <label for="company">Company</label>
                                <input id="company" type="text"
                                    class="form-control @error('company') is-invalid @enderror" name="company"
                                    value="{{ old('company', auth()->user()->company) }}" required>
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label for="email">Email</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Ganti Password</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                Kosongkan bagian ini jika Anda tidak ingin mengganti password.
                            </div>

                            <div class="form-group">
                                <label for="current_password">Password Lama</label>
                                <input id="current_password" type="password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    name="current_password" autocomplete="current-password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password">Password Baru</label>
                                <input id="new_password" type="password"
                                    class="form-control @error('new_password') is-invalid @enderror" name="new_password"
                                    autocomplete="new-password">
                                <small class="form-text text-muted">Password baru minimal 8 karakter.</small>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                                <input id="new_password_confirmation" type="password" class="form-control"
                                    name="new_password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="ik ik-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
