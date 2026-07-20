<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $type === 'construction' ? 'SI-JAKI | Under Construction' : 'SI-JAKI | Under Maintenance' }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/logo.png')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css')}}">
</head>
<body>
    <main class="auth-creative-wrapper">
        <div class="auth-creative-inner">
            <div class="creative-card-wrapper">
                <div class="card my-4 overflow-hidden" style="z-index: 1">
                    <div class="row flex-1 g-0">
                        <div class="col-lg-6 h-100 my-auto">
                            <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-50 start-50 d-none d-lg-block">
                                <img src="{{ asset('logo/logo.png')}}" alt="" class="img-fluid">
                            </div>
                            <div class="creative-card-body card-body p-sm-5">
                                @if($type === 'construction')
                                    <h2 class="fs-20 fw-bolder mb-4 text-info">UNDER CONSTRUCTION</h2>
                                    <h4 class="fs-16 fw-bold mb-2">SI-JAKI is Under Construction</h4>
                                    <p class="fs-14 fw-medium text-muted">We apologize for the inconvenience. The website is currently under construction and improvement.</p>
                                @else
                                    <h2 class="fs-20 fw-bolder mb-4 text-danger">UNDER MAINTENANCE</h2>
                                    <h4 class="fs-16 fw-bold mb-2">Oops! SI-JAKI is Taking a Break</h4>
                                    <p class="fs-14 fw-medium text-muted">We apologize for the inconvenience. The website is currently undergoing maintenance.</p>
                                @endif

                                <div class="mt-4 text-center">
                                    <h3 class="fs-15 fw-bold">Will be back in:</h3>
                                    <div class="d-flex justify-content-center gap-3 mt-3">
                                        <div class="p-3 bg-light rounded text-center">
                                            <h2 id="days" class="mb-0 fw-bold text-primary">00</h2>
                                            <small>Days</small>
                                        </div>
                                        <div class="p-3 bg-light rounded text-center">
                                            <h2 id="hours" class="mb-0 fw-bold text-primary">00</h2>
                                            <small>Hours</small>
                                        </div>
                                        <div class="p-3 bg-light rounded text-center">
                                            <h2 id="minutes" class="mb-0 fw-bold text-primary">00</h2>
                                            <small>Minutes</small>
                                        </div>
                                        <div class="p-3 bg-light rounded text-center">
                                            <h2 id="seconds" class="mb-0 fw-bold text-primary">00</h2>
                                            <small>Seconds</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 bg-primary">
                            <div class="h-100 d-flex align-items-center justify-content-center">
                                @if($type === 'construction')
                                    <img src="{{ asset('logo/under-construction.gif')}}" alt="" class="img-fluid">
                                @else
                                    <img src="{{ asset('logo/maintenance.gif')}}" alt="" class="img-fluid">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{ asset('assets/js/common-init.min.js')}}"></script>
    
    <script>
        // Set target date from server data
        const endTime = new Date("{{ $endTime }}").getTime();

        // Update timer every second
        const timer = setInterval(function() {
            // Get current time
            const now = new Date().getTime();

            // Calculate time difference
            const distance = endTime - now;

            // Calculate days, hours, minutes, seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Display result
            document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
            document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');

            // If timer finished
            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("days").innerHTML = "00";
                document.getElementById("hours").innerHTML = "00";
                document.getElementById("minutes").innerHTML = "00";
                document.getElementById("seconds").innerHTML = "00";

                // Refresh page to check if maintenance is finished
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            }
        }, 1000);
    </script>
</body>
</html>