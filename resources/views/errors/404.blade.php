<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page Not Found | 404</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/logo.png')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css')}}">
</head>
<body>

<body>
    <main class="auth-cover-wrapper">
        <div class="auth-cover-content-inner">
            <div class="auth-cover-content-wrapper">
                <div class="auth-img">
                    <img src="{{ asset('logo/lldikti3.png')}}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="auth-cover-sidebar-inner">
            <div class="auth-cover-card-wrapper">
                <div class="auth-cover-card p-sm-5">
                    <div class="wd-50 mb-5">
                        <img src="{{ asset('logo/logo.png')}}" alt="" class="img-fluid">
                    </div>
                    <h4 class="fw-bold mb-2">Page not found</h4>
                    <p class="fs-12 fw-medium text-muted">Maaf, halaman yang Anda cari tidak dapat ditemukan. Silakan periksa URL atau coba halaman lain.</p>
                    <h2 class="fw-bolder mb-4" style="font-size: 120px">4<span class="text-danger">0</span>4</h2>
                    <div class="mt-5">
                        <a href="{{ route('dashboard')}}" class="btn btn-light-brand w-100">Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{ asset('assets/js/common-init.min.js')}}"></script>
</body>

</html>