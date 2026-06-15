<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>Gatra Trust - Sedang Dalam Pemeliharaan</title>

    <link rel="icon" href="{{ asset('template/img/Logo_gatra.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/icon-kit/dist/css/iconkit.min.css') }}">

    <style>
        :root {
            --gatra-green: #1da34b;
            --gatra-green-dark: #086145;
            --gatra-text: #2e3451;
            --gatra-muted: #6c757d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--gatra-text);
            font-family: "Nunito Sans", Arial, sans-serif;
            background:
                linear-gradient(135deg, rgba(46, 52, 81, .78), rgba(8, 97, 69, .9)),
                url("{{ asset('template/img/auth/login3-bg.png') }}") center / cover fixed;
        }

        .maintenance-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .maintenance-card {
            width: 100%;
            max-width: 650px;
            padding: 48px;
            overflow: hidden;
            position: relative;
            text-align: center;
            background: rgba(255, 255, 255, .98);
            border-radius: 18px;
            box-shadow: 0 24px 70px rgba(14, 30, 37, .28);
        }

        .maintenance-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--gatra-green), var(--gatra-green-dark));
        }

        .brand-logo {
            width: 88px;
            height: auto;
            margin-bottom: 24px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            padding: 8px 16px;
            color: var(--gatra-green-dark);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: rgba(29, 163, 75, .12);
            border-radius: 999px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--gatra-green);
            border-radius: 50%;
            animation: pulse 1.8s infinite;
        }

        h1 {
            margin-bottom: 16px;
            font-size: 34px;
            font-weight: 800;
            line-height: 1.25;
        }

        .description {
            max-width: 500px;
            margin: 0 auto 30px;
            color: var(--gatra-muted);
            font-size: 17px;
            line-height: 1.7;
        }

        .information {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 20px;
            color: var(--gatra-green-dark);
            font-weight: 600;
            background: #f2faf5;
            border: 1px solid #d9f0e1;
            border-radius: 12px;
        }

        .information i {
            font-size: 22px;
        }

        .footer-text {
            margin-top: 28px;
            margin-bottom: 0;
            color: #9aa0a6;
            font-size: 13px;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(29, 163, 75, .45);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(29, 163, 75, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(29, 163, 75, 0);
            }
        }

        @media (max-width: 576px) {
            .maintenance-card {
                padding: 38px 22px;
            }

            h1 {
                font-size: 27px;
            }

            .description {
                font-size: 15px;
            }

            .information {
                align-items: flex-start;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <main class="maintenance-wrapper">
        <section class="maintenance-card" aria-labelledby="maintenance-title">
            {{-- <img class="brand-logo" src="{{ asset('template/img/LOGO_Gatra1.png') }}" alt="Logo Gatra Trust"> --}}

            <div class="status-badge">
                <span class="status-dot" aria-hidden="true"></span>
                Sedang dalam pemeliharaan
            </div>

            <h1 id="maintenance-title">Gatra Trust sedang meningkatkan sistem</h1>

            <p class="description">
                Aplikasi sementara tidak dapat digunakan karena sedang dalam proses pemeliharaan.
                Silakan kembali beberapa saat lagi.
            </p>

            <div class="information">
                <i class="ik ik-shield" aria-hidden="true"></i>
                <span>Data Anda tetap aman selama proses pemeliharaan berlangsung.</span>
            </div>

            <p class="footer-text">&copy; {{ date('Y') }} Gatra Trust. Terima kasih atas pengertiannya.</p>
        </section>
    </main>
</body>

</html>
