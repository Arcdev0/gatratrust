<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login | ThemeKit - Admin Template</title>
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
<body>

<div class="auth-wrapper">
    <div class="container-fluid h-100">
        <div class="row flex-row h-100 bg-white">
            <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                <div class="lavalite-bg" style="background-image: url('{{ asset('img/auth/login-bg.jpg') }}')">
                    <div class="lavalite-overlay"></div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                <div class="authentication-form mx-auto">
                    <div class="logo-centered">
                        <a href="{{ url('/') }}"><img src="{{ asset('src/img/brand.svg') }}" alt="Logo"></a>
                    </div>
                    <h3>Sign In to ThemeKit</h3>
                    <p>Happy to see you again!</p>

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
                        <div class="form-group">
                            <input type="text"  class="form-control"  placeholder="Username" name="name" required />
                            <i class="ik ik-user"></i>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                            <i class="ik ik-lock"></i>
                        </div>
                        <div class="row">
                            <div class="col text-left">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="remember">
                                    <span class="custom-control-label">&nbsp;Remember Me</span>
                                </label>
                            </div>
                            <div class="col text-right">
                                <a href="#">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="sign-btn text-center">
                            <button class="btn btn-theme" type="submit">Sign In</button>
                        </div>
                    </form>
                    <div class="register">
                        <p>Don't have an account? <a href="#">Create an account</a></p>
                    </div>
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
