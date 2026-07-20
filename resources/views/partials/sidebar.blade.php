<style>
    /* Logo sidebar normal: sedikit lebih kecil */
    .nxl-navigation .m-header .logo-lg {
        max-height: 38px !important; /* sebelumnya 45px */
        width: auto !important;
        object-fit: contain;
        transition: all 0.3s ease;
    }

    .nxl-navigation .m-header .logo-sm {
        max-height: 24px !important; /* logo kecil saat sidebar hide */
        width: auto !important;
        object-fit: contain;
        transition: all 0.3s ease;
        display: none !important;
    }

    /* Saat sidebar di-hide / minimized */
    body.minimized .nxl-navigation .logo-lg,
    body.nxl-sidebar-mini .nxl-navigation .logo-lg,
    body[data-sidebar-size="sm"] .nxl-navigation .logo-lg,
    .nxl-navigation.minimized .logo-lg {
        display: none !important;
    }

    body.minimized .nxl-navigation .logo-sm,
    body.nxl-sidebar-mini .nxl-navigation .logo-sm,
    body[data-sidebar-size="sm"] .nxl-navigation .logo-sm,
    .nxl-navigation.minimized .logo-sm {
        display: block !important;
        max-height: 24px !important;
    }

    /* Header logo saat sidebar kecil */
    body.minimized .nxl-navigation .m-header,
    body.nxl-sidebar-mini .nxl-navigation .m-header,
    body[data-sidebar-size="sm"] .nxl-navigation .m-header,
    .nxl-navigation.minimized .m-header {
        padding: 8px 4px !important;
        justify-content: center !important;
    }

    body.minimized .nxl-navigation .b-brand,
    body.nxl-sidebar-mini .nxl-navigation .b-brand,
    body[data-sidebar-size="sm"] .nxl-navigation .b-brand,
    .nxl-navigation.minimized .b-brand {
        justify-content: center !important;
    }
</style>
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <!-- Header Logo -->
      <div class="m-header" style="display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px;">
    <a href="{{ route('dashboard')}}" class="b-brand" style="text-decoration: none; display: flex; align-items: center; justify-content: center; width: 100%;">
        
        <img src="{{ asset('logo/sijaki.png') }}" alt="SIJAKI Logo" class="logo-lg">
        
        <img src="{{ asset('logo/logo.png') }}" alt="SIJAKI Logo" class="logo-sm">
        
    </a>
</div>

        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Menu Utama</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Beranda</span>
                    </a>
                </li>

                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-file-text"></i></span>
                        <span class="nxl-mtext">Laporan Pembinaan PT</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('user.laporan-ptn.index')}}">PTN</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('user.laporan-pts.index')}}">PTS</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item">
                    <a href="{{ route('histori-pt.index')}}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-clock"></i></span>
                        <span class="nxl-mtext">Histori PT</span>
                    </a>
                </li>

                @if(auth()->user()->akses == 'Dev' || auth()->user()->akses == 'Admin')
                    <li class="nxl-item nxl-caption">
                        <label>Admin SI-JAKI</label>
                    </li>
                    <li class="nxl-item">
                        <a href="{{ route('manage-pt.index')}}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Manajemen PT</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <a href="{{ route('manage-users.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Manajemen Pengguna</span>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->akses == 'Dev')
                    <li class="nxl-item nxl-caption">
                        <label>Developer SI-JAKI</label>
                    </li>
                    <li class="nxl-item">
                        <a href="{{ route('admin.maintenance.index')}}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-loader"></i></span>
                            <span class="nxl-mtext">Maintenance</span>
                        </a>
                    </li>
                @endif
                
                <li class="nxl-item nxl-caption">
                    <label>Pengaturan</label>
                </li>
                <li class="nxl-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-log-out"></i></span>
                            <span class="nxl-mtext">Keluar</span>
                        </a>
                    </form>
                </li>
            </ul>
            
            <div class="card text-center">
                <div class="card-body">
                    <i class="feather-chevrons-down fs-4" style="color: var(--primary-blue);"></i>
                    <h6 class="mt-4 text-dark fw-bolder">SI-JAKI</h6>
                    <p class="fs-11 my-3 text-dark">Sistem Informasi Jejak Pembinaan Perguruan Tinggi.</p>
                </div>
            </div>
        </div>
    </div>
</nav>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.querySelector(".nxl-navigation");

        if (!sidebar) return;

        function showSmallLogoWhenSidebarHide() {
            if (sidebar.offsetWidth <= 100) {
                sidebar.classList.add("minimized");
            } else {
                sidebar.classList.remove("minimized");
            }
        }

        showSmallLogoWhenSidebarHide();

        window.addEventListener("resize", showSmallLogoWhenSidebarHide);

        document.addEventListener("click", function () {
            setTimeout(showSmallLogoWhenSidebarHide, 100);
            setTimeout(showSmallLogoWhenSidebarHide, 300);
        });

        if (typeof ResizeObserver !== "undefined") {
            const resizeObserver = new ResizeObserver(showSmallLogoWhenSidebarHide);
            resizeObserver.observe(sidebar);
        }
    });
</script>