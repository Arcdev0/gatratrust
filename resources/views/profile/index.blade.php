
@extends('layout.app')

@section('title', 'Profile')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-green text-white">
                    <h5 class="mb-0">Profil Saya</h5>
                </div>
                <div class="card-body">
                    {{-- Pesan sukses/error --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        {{-- Form Update Profile --}}
                        <div class="col-md-6 border-right">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Company</label>
                                    <input type="text" name="company" class="form-control" value="{{ Auth::user()->company }}" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                        {{-- Form Ganti Password --}}
                        <div class="col-md-6">
                          
                                <div class="form-group">
                                    <label>Password Lama</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success">Ganti Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection