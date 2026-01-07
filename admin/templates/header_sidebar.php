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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* ============ Base Styles ============ */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
        background: #f8fafc;
        overflow-x: hidden;
        min-height: 100vh;
    }

    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background: 
            radial-gradient(circle at 20% 10%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 90%, rgba(139, 92, 246, 0.08) 0%, transparent 50%),
            linear-gradient(180deg, transparent 0%, rgba(248, 250, 252, 0.8) 100%);
        pointer-events: none;
        z-index: 0;
    }

    /* ============ Navbar ============ */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1030;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px) saturate(180%);
        border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 10px 40px rgba(99, 102, 241, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .navbar.scrolled {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
        background: rgba(255, 255, 255, 0.98);
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.35rem;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .navbar-brand:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
    }

    .navbar-brand .material-symbols-outlined {
        font-size: 1.8rem;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: iconFloat 3s ease-in-out infinite;
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-3px) rotate(5deg); }
    }

    .navbar .nav-link {
        color: #475569;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .navbar .nav-link:hover {
        background: rgba(99, 102, 241, 0.08);
        color: #6366f1;
    }

    .navbar .dropdown-menu {
        min-width: 240px;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 0.5rem;
        margin-top: 0.75rem;
        animation: dropdownSlide 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.98);
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-12px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .navbar .dropdown-item {
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        font-weight: 500;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .navbar .dropdown-item:hover {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        transform: translateX(4px);
    }

    .navbar .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .navbar-toggler {
        border: none;
        padding: 0.5rem;
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .navbar-toggler:hover {
        background: rgba(99, 102, 241, 0.08);
    }

    /* ============ Sidebar ============ */
    #sidebar {
        width: 280px;
        min-height: 100vh;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        padding-top: 80px;
        position: fixed;
        top: 0;
        left: 0;
        box-shadow: 1px 0 0 rgba(0, 0, 0, 0.05), 4px 0 24px rgba(99, 102, 241, 0.08);
        z-index: 1020;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto;
        overflow-x: hidden;
    }

    #sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
    }

    #sidebar.hide {
        transform: translateX(-300px);
    }

    #sidebar h5 {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: -0.02em;
        margin-bottom: 1.5rem;
        position: relative;
    }

    #sidebar h5::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, transparent, #6366f1, transparent);
        border-radius: 2px;
    }

    #sidebar .nav-link {
        border-radius: 12px;
        margin-bottom: 6px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.875rem 1.25rem;
        font-weight: 600;
        font-size: 0.925rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    #sidebar .nav-link::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: -1;
    }

    #sidebar .nav-link:hover::before {
        opacity: 0.08;
    }

    #sidebar .nav-link.active::before {
        opacity: 1;
    }

    #sidebar .nav-link.active {
        color: #fff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4), 0 2px 4px rgba(0, 0, 0, 0.1);
        transform: translateX(4px);
    }

    #sidebar .nav-link:hover:not(.active) {
        color: #6366f1;
        transform: translateX(4px);
        background: rgba(99, 102, 241, 0.04);
    }

    #sidebar .nav-link .material-symbols-outlined {
        font-size: 1.4rem;
        transition: all 0.3s ease;
    }

    #sidebar .nav-link:hover .material-symbols-outlined,
    #sidebar .nav-link.active .material-symbols-outlined {
        transform: scale(1.1);
    }

    /* ============ Main Content ============ */
    #main-content {
        margin-left: 280px;
        padding: 2rem;
        padding-top: 100px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
        min-height: 100vh;
    }

    #main-content.fullwidth {
        margin-left: 0;
    }

    /* ============ Cards ============ */
    .card {
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        position: relative;
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15), 0 4px 8px rgba(0, 0, 0, 0.08);
    }

    .card:hover::before {
        transform: scaleX(1);
    }

    .counter {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.03em;
    }

    /* ============ Scrollbar ============ */
    #sidebar::-webkit-scrollbar {
        width: 6px;
    }

    #sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    #sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 10px;
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #8b5cf6 0%, #6366f1 100%);
    }

    /* ============ Overlay ============ */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1019;
    }

    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    /* ============ Responsive ============ */
    @media (max-width: 991px) {
        #sidebar {
            transform: translateX(-300px);
        }

        #sidebar.show {
            transform: translateX(0);
        }

        #main-content {
            margin-left: 0;
            padding: 1.5rem;
            padding-top: 90px;
        }
    }

    @media (max-width: 576px) {
        #main-content {
            padding: 1rem;
            padding-top: 80px;
        }

        .navbar-brand {
            font-size: 1.15rem;
        }

        .counter {
            font-size: 2rem;
        }
    }

    /* ============ Animations ============ */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeIn 0.5s ease backwards;
    }

    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.3s; }
    .card:nth-child(4) { animation-delay: 0.4s; }

    /* ============ Utilities ============ */
    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:active {
        transform: translateY(0);
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="index.php">
            <span class="material-symbols-outlined">library_books</span>
            PerpustakaanKu
        </a>
        <button class="navbar-toggler" type="button" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <span class="material-symbols-outlined">account_circle</span>
                        <?= htmlspecialchars($namaAdmin, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <span class="material-symbols-outlined">person</span>
                                Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="../logout.php">
                                <span class="material-symbols-outlined">logout</span>
                                Keluar
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div id="sidebar">
    <h5 class="text-center">Admin Panel</h5>
    <ul class="nav flex-column px-3">
        <li class="nav-item">
            <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="../index/index.php">
                <span class="material-symbols-outlined">dashboard</span> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current_page == 'buku.php' ? 'active' : '' ?>" href="../buku/buku.php">
                <span class="material-symbols-outlined">menu_book</span> Buku
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current_page == 'anggota.php' ? 'active' : '' ?>" href="../anggota/anggota.php">
                <span class="material-symbols-outlined">group</span> Anggota
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current_page == 'peminjaman.php' ? 'active' : '' ?>" href="../peminjaman/peminjaman.php">
                <span class="material-symbols-outlined">library_add</span> Peminjaman
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current_page == 'pengembalian.php' ? 'active' : '' ?>" href="../pengembalian/pengembalian.php">
                <span class="material-symbols-outlined">assignment_return</span> Pengembalian
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current_page == 'laporan.php' ? 'active' : '' ?>" href="../laporan/laporan.php">
                <span class="material-symbols-outlined">insights</span> Laporan
            </a>
        </li>
    </ul>
</div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Sidebar Toggle
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('main-content');
const sidebarToggle = document.getElementById('sidebarToggle');
const overlay = document.getElementById('sidebar-overlay');

function toggleSidebar() {
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    if (window.innerWidth < 992) {
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    }
}

sidebarToggle?.addEventListener('click', toggleSidebar);
overlay?.addEventListener('click', toggleSidebar);

// Close sidebar when clicking nav links on mobile
if (window.innerWidth < 992) {
    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            setTimeout(toggleSidebar, 100);
        });
    });
}

// Adjust main content on resize
function adjustLayout() {
    if (window.innerWidth >= 992) {
        mainContent?.classList.remove('fullwidth');
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    } else {
        mainContent?.classList.add('fullwidth');
    }
}

window.addEventListener('resize', adjustLayout);
adjustLayout();

// Navbar scroll effect
let lastScroll = 0;
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    lastScroll = currentScroll;
});

// Counter Animation
function animateCounter(element, target, duration = 1500) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Intersection Observer for counter animation
const observerOptions = {
    threshold: 0.3,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counter = entry.target.querySelector('.counter');
            if (counter && !counter.classList.contains('animated')) {
                counter.classList.add('animated');
                const target = parseInt(counter.textContent) || 0;
                counter.textContent = '0';
                animateCounter(counter, target);
            }
        }
    });
}, observerOptions);

// Observe cards when they exist
setTimeout(() => {
    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
}, 100);

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Page load animation
window.addEventListener('load', () => {
    document.body.style.opacity = '1';
});
</script>