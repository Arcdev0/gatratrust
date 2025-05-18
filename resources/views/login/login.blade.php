<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Gatra Trust</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">

    {{-- CSS Files --}}
    <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/ionicons/dist/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/icon-kit/dist/css/iconkit.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/css/theme.min.css') }}">

    <script src="{{ asset('template/src/js/vendor/modernizr-2.8.3.min.js') }}"></script>
</head>

<body style="background: linear-gradient(135deg,rgb(18, 214, 77) 0%,rgb(29, 163, 11) 100%); min-height: 100vh;">

    <div class="auth-wrapper">
        <div class="container-fluid h-100">
            <div class="row flex-row h-100 bg-white">
                <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                    <div class="lavalite-bg" style="background-image: url('{{ asset('img/auth/login-bg.jpg') }}')">
                        <div class="lavalite-overlay"></div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                    <div class="authentication-form mx-auto shadow-lg rounded p-4"
                        style="background: #fff; max-width: 370px;">
                        <div class="logo-centered mb-3">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('img/26585b26-8704-499c-a3f1-3b56af4ab2de.png') }}" alt="Logo"
                                    style="width: 80px;">
                            </a>
                        </div>
                        <h3 class="text-center mb-2" style="font-weight: 700; color: #1da34b;">Gatra Trust</h3>
                        <p class="text-center mb-4" style="color: #888;">Welcome To Gatra Trust</p>
                        {{-- Alert Error --}}
                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3 position-relative">
                                <i class="ik ik-user position-absolute"
                                    style="left: 15px; top: 50%; transform: translateY(-50%); color: #1da34b;"></i>
                                <input type="text" class="form-control rounded-pill" placeholder="Username" name="name"
                                    required style="padding-left: 40px;">
                            </div>

                            <div class="form-group mb-3 position-relative">
                                <i class="ik ik-lock position-absolute"
                                    style="left: 15px; top: 50%; transform: translateY(-50%); color: #1da34b;"></i>
                                <input type="password" class="form-control rounded-pill" placeholder="Password"
                                    name="password" required style="padding-left: 40px;">
                            </div>
                            <div class="row mb-3">
                                <div class="col text-left">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="remember">
                                        <span class="custom-control-label">&nbsp;Remember Me</span>
                                    </label>
                                </div>
                            </div>
                            <div class="sign-btn text-center">
                                <button class="btn btn-theme btn-block rounded-pill" type="submit"
                                    style="background: #1da34b; color: #fff; font-weight: 600; transition: background 0.2s;">Sign
                                    In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS Files --}}
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>window.jQuery || document.write('<script src="{{ asset('template/src/js/vendor/jquery-3.3.1.min.js') }}"><\/script>')</script>
    <script src="{{ asset('template/plugins/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('template/plugins/screenfull/dist/screenfull.js') }}"></script>
    <script src="{{ asset('template/dist/js/theme.js') }}"></script>

</body>

</html>
