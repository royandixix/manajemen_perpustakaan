<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$namaAdmin = $_SESSION['admin']['nama'] ?? 'Admin';
?>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
<link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/fonts/icomoon/style.css">

<link rel="stylesheet" href="css/css/owl.carousel.min.css">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="css/css/bootstrap.min.css">

<!-- Style -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link rel="stylesheet" href="css/css/style.css">
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    /* ---------------- Base ---------------- */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f7fa;
        margin: 0;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 10% 20%, rgba(13, 110, 253, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 90% 80%, rgba(51, 153, 255, 0.05) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }

    /* ---------------- Navbar ---------------- */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1030;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.25);
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .navbar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.1) 100%);
        pointer-events: none;
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.4rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
        letter-spacing: 1px;
    }

    .navbar-brand .material-symbols-outlined {
        font-size: 1.8rem;
        animation: rotate 3s ease-in-out infinite;
    }

    @keyframes rotate {

        0%,
        100% {
            transform: rotate(0deg);
        }

        50% {
            transform: rotate(10deg);
        }
    }

    .navbar .dropdown-menu {
        min-width: 220px;
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        padding: 0.5rem;
        margin-top: 0.5rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .navbar .dropdown-item {
        border-radius: 8px;
        padding: 0.7rem 1rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .navbar .dropdown-item:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateX(5px);
    }

    .nav-link {
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        transform: translateY(-2px);
    }

    /* ---------------- Sidebar ---------------- */
    #sidebar {
        width: 250px;
        min-height: 100vh;
        background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        padding-top: 70px;
        position: fixed;
        top: 0;
        left: 0;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
        z-index: 1020;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto;
    }

    #sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    }

    #sidebar.hide {
        transform: translateX(-260px);
    }

    #sidebar h5 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }
    }

    #sidebar .nav-link {
        border-radius: 12px;
        margin-bottom: 8px;
        color: #4a5568;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    #sidebar .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.4s ease;
        z-index: -1;
    }

    #sidebar .nav-link.active::before,
    #sidebar .nav-link:hover::before {
        left: 0;
    }

    #sidebar .nav-link.active {
        color: #fff;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.35);
        transform: translateX(8px) scale(1.02);
    }

    #sidebar .nav-link:hover {
        color: #fff;
        transform: translateX(8px) scale(1.02);
    }

    #sidebar .nav-link .material-symbols-outlined {
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    #sidebar .nav-link:hover .material-symbols-outlined {
        transform: rotateY(360deg);
    }

    #sidebar .nav-link.active .material-symbols-outlined {
        animation: bounce 1s ease infinite;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    /* ---------------- Main Content ---------------- */
    #main-content {
        margin-left: 250px;
        padding: 25px;
        padding-top: 100px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
    }

    #main-content.fullwidth {
        margin-left: 0;
    }

    /* ---------------- Cards ---------------- */
    .card {
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        cursor: default;
        border: none;
        position: relative;
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .card:hover::before {
        transform: scaleX(1);
    }

    .card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
    }

    .counter {
        font-size: 2.5rem;
        font-weight: bold;
        transition: all 0.3s;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* ---------------- Responsive ---------------- */
    @media (max-width: 991px) {
        #sidebar {
            left: -260px;
        }

        #sidebar.show {
            left: 0;
            box-shadow: 8px 0 40px rgba(0, 0, 0, 0.2);
        }

        #main-content {
            margin-left: 0;
            padding-top: 120px;
        }
    }

    /* Smooth scrollbar */
    #sidebar::-webkit-scrollbar {
        width: 6px;
    }

    #sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    #sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #764ba2 0%, #667eea 100%);
    }

    /* Navbar Toggle Animation */
    .navbar-toggler {
        border: none;
        padding: 0.5rem;
        transition: all 0.3s ease;
    }

    .navbar-toggler:focus {
        box-shadow: none;
    }

    .navbar-toggler:hover {
        transform: rotate(90deg);
    }

    /* Ripple Effect */
    .ripple {
        position: relative;
        overflow: hidden;
    }

    .ripple::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;


        /* kode baru di tambahkan */
        list-style: none;
        font-synthesis-weight: none;

        display: block;
        direction: ltr;
        filter: blur();
    }

    .ripple:active::after {
        width: 300px;
        height: 300px;
    }
</style>

<!-- ---------------- Navbar ---------------- -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center ripple" href="index.php">
            <span class="material-symbols-outlined me-2">library_books</span> PerpustakaanKu
        </a>
        <button class="navbar-toggler" type="button" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center ripple" href="#" role="button" data-bs-toggle="dropdown">
                        <span class="material-symbols-outlined me-1">account_circle</span>
                        <?= htmlspecialchars($namaAdmin, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><span class="material-symbols-outlined me-2" style="font-size: 1.2rem;">person</span>Profil Saya</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="../logout.php"><span class="material-symbols-outlined me-2" style="font-size: 1.2rem;">logout</span>Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ---------------- Sidebar ---------------- -->
<div id="sidebar" class="bg-white border-end shadow-sm position-fixed">
    <h5 class="text-center fw-bold mb-3 pt-3">Admin Panel</h5>
    <ul class="nav flex-column px-3">
        <li class="nav-item mb-1">
            <a class="nav-link ripple <?= $current_page == 'index.php' ? 'active' : '' ?>" href="../index/index.php">
                <span class="material-symbols-outlined">dashboard</span> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link ripple <?= $current_page == 'buku.php' ? 'active' : '' ?>" href="../buku/buku.php">
                <span class="material-symbols-outlined">menu_book</span> Buku
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link ripple <?= $current_page == 'anggota.php' ? 'active' : '' ?>" href="../anggota/anggota.php">
                <span class="material-symbols-outlined">group</span> Anggota
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link ripple <?= $current_page == 'peminjaman.php' ? 'active' : '' ?>" href="../peminjaman/peminjaman.php">
                <span class="material-symbols-outlined">library_add</span> Peminjaman
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link ripple <?= $current_page == 'pengembalian.php' ? 'active' : '' ?>" href="../pengembalian/pengembalian.php">
                <span class="material-symbols-outlined">assignment_return</span> Pengembalian
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link ripple <?= $current_page == 'laporan.php' ? 'active' : '' ?>" href="../laporan/laporan.php">
                <span class="material-symbols-outlined">insights</span> Laporan
            </a>
        </li>
    </ul>
</div>

<!-- ---------------- Bootstrap JS ---------------- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- ===== SCRIPTS ===== -->
<script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
    lucide.createIcons();

    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("show");
        document.getElementById("sidebar-overlay").classList.toggle("show");
    }
</script>

<!-- ---------------- Enhanced JS ---------------- -->
<script>
    // Sidebar toggle dengan animasi smooth
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggle = document.getElementById('sidebarToggle');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('show');

        // Tambah efek blur ke background saat sidebar terbuka di mobile
        if (window.innerWidth < 992) {
            if (sidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
                const overlay = document.createElement('div');
                overlay.id = 'sidebar-overlay';
                overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1019;
                animation: fadeIn 0.3s ease;
            `;
                document.body.appendChild(overlay);

                // Close sidebar saat klik overlay
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    overlay.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => {
                        overlay.remove();
                        document.body.style.overflow = '';
                    }, 300);
                });
            }
        }
    });

    // Animasi fadeIn/fadeOut untuk overlay
    const style = document.createElement('style');
    style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
    document.head.appendChild(style);

    // Auto close sidebar saat navigasi di mobile
    if (window.innerWidth < 992) {
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('show');
                const overlay = document.getElementById('sidebar-overlay');
                if (overlay) {
                    overlay.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => {
                        overlay.remove();
                        document.body.style.overflow = '';
                    }, 300);
                }
            });
        });
    }

    // Main content width adjustment
    function adjustMain() {
        if (window.innerWidth >= 992) {
            mainContent.classList.remove('fullwidth');
        } else {
            mainContent.classList.add('fullwidth');
        }
    }
    window.addEventListener('resize', adjustMain);
    adjustMain();

    // Dropdown hover untuk desktop
    const dropdowns = document.querySelectorAll('.navbar .dropdown');
    dropdowns.forEach(dd => {
        dd.addEventListener('mouseenter', () => {
            if (window.innerWidth >= 992) {
                const menu = dd.querySelector('.dropdown-menu');
                menu.classList.add('show');
            }
        });
        dd.addEventListener('mouseleave', () => {
            if (window.innerWidth >= 992) {
                const menu = dd.querySelector('.dropdown-menu');
                menu.classList.remove('show');
            }
        });
    });

    // Smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Counter Animation untuk dashboard cards
    function animateCounter(element, target, duration = 1000) {
        let start = 0;
        const increment = target / (duration / 16);

        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start);
            }
        }, 16);
    }

    // Trigger counter animation saat card masuk viewport
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target.querySelector('.counter');
                if (counter && !counter.classList.contains('animated')) {
                    counter.classList.add('animated');
                    const target = parseInt(counter.textContent);
                    counter.textContent = '0';
                    animateCounter(counter, target);
                }
            }
        });
    }, observerOptions);

    // Observe semua cards
    setTimeout(() => {
        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });
    }, 100);

    // Navbar shadow on scroll
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            navbar.style.boxShadow = '0 12px 40px rgba(102, 126, 234, 0.35)';
        } else {
            navbar.style.boxShadow = '0 8px 32px rgba(102, 126, 234, 0.25)';
        }

        lastScroll = currentScroll;
    });

    // Particle effect di background (subtle)
    const createParticle = () => {
        const particle = document.createElement('div');
        particle.style.cssText = `
        position: fixed;
        width: 3px;
        height: 3px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.5), transparent);
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
        left: ${Math.random() * 100}vw;
        top: ${Math.random() * 100}vh;
        animation: float ${5 + Math.random() * 10}s ease-in-out infinite;
    `;
        document.body.appendChild(particle);

        setTimeout(() => particle.remove(), 15000);
    };

    // Animasi float untuk particle
    const floatStyle = document.createElement('style');
    floatStyle.textContent = `
    @keyframes float {
        0%, 100% { transform: translate(0, 0); opacity: 0; }
        10% { opacity: 0.3; }
        90% { opacity: 0.3; }
        50% { transform: translate(${Math.random() * 100 - 50}px, ${Math.random() * 100 - 50}px); }
    }
`;
    document.head.appendChild(floatStyle);

    // Generate particles setiap 3 detik
    setInterval(createParticle, 3000);

    // Loading animation untuk page transitions
    window.addEventListener('beforeunload', () => {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.3s ease';
    });

    // Page load animation
    window.addEventListener('load', () => {
        document.body.style.opacity = '0';
        setTimeout(() => {
            document.body.style.transition = 'opacity 0.5s ease';
            document.body.style.opacity = '1';
        }, 100);
    });
</script>