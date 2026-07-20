<!-- [ Footer ] start -->
<style>
    .footer {
        position: fixed;
        left: 280px;
        right: 0;
        bottom: 0;
        height: 56px;
        z-index: 999;
        background: #ffffff;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        box-shadow: 0 -4px 18px rgba(15, 23, 42, 0.04);
    }

    .footer p {
        line-height: 1.4;
    }

    .footer a {
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .footer a:hover {
        color: #2563eb;
    }

    .nxl-content {
        padding-bottom: 80px !important;
    }

    body.minimized .footer,
    body[data-sidebar-size="sm"] .footer,
    body.nxl-sidebar-mini .footer {
        left: 80px;
    }

    @media (max-width: 991.98px) {
        .footer {
            left: 0;
            height: auto;
            min-height: 56px;
            padding: 10px 16px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .nxl-content {
            padding-bottom: 90px !important;
        }
    }
</style>

<footer class="footer">
    <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
        <span class="d-none d-md-inline">
            Copyright © 2024 -
            <script>
                document.write(new Date().getFullYear());
            </script>
        </span>

        <span class="d-none d-md-inline">
            | Sistem Informasi Jejak Pembinaan Perguruan Tinggi | Version 2.2
        </span>

        <span class="d-md-none">
            Version 2.2
        </span>
    </p>

    <div class="d-flex align-items-center gap-2">
        <a href="#" class="fs-11 fw-semibold text-uppercase d-none d-md-inline">Bantuan</a>
        <a href="#" class="fs-11 fw-semibold text-uppercase d-md-none">Help</a>

        <span class="text-muted d-none d-md-inline">|</span>

        <a href="{{ url('/pengembang') }}" class="fs-11 fw-semibold text-uppercase d-none d-md-inline">Pengembang</a>
        <a href="{{ url('/pengembang') }}" class="fs-11 fw-semibold text-uppercase d-md-none">Developer</a>

    </div>
</footer>
<!-- [ Footer ] end -->