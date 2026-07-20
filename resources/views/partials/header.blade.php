<style>
    .dropdown-header .user-info-container {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .dropdown-header .text-wrap {
        word-wrap: break-word;
        white-space: normal;
        overflow-wrap: break-word;
        text-align: left;
    }
</style>

<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-sidebar fs-5"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-chevron-right fs-5"></i>
                </a>
            </div>
            </div>
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <div class="dropdown nxl-h-item nxl-header-search">
                    <a href="javascript:void(0);" class="nxl-head-link me-0" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <i class="feather-search"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-search-dropdown">
                        <div class="input-group search-form">
                            <span class="input-group-text">
                                <i class="feather-search fs-6 text-muted"></i>
                            </span>
                            <input type="text" class="form-control search-input-field" placeholder="Cari pengguna, laporan, dokumen..." />
                            <span class="input-group-text">
                                <button type="button" class="btn-close"></button>
                            </span>
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        </div>
                </div>
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="{{ asset('logo/logo-lldikti3.png') }}" alt="user-image" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <div class="user-info-container">
                                    <h6 class="text-dark mb-1 text-wrap">{{ Auth::user()->pokja }}</h6>
                                    <span class="fs-12 fw-medium text-muted text-wrap">{{ Auth::user()->name }}</span>
                                </div>
                            </div>
                        </div>
                        {{-- <a href="{{ route('manage-users.index')}}" class="dropdown-item">
                            <i class="feather-settings"></i>
                            <span>Pengaturan Akun</span>
                        </a> --}}
                        {{-- <div class="dropdown-divider"></div> --}}
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="feather-log-out"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
</header>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="{{ asset('js/global-search.js') }}"></script>