<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= APP_URL ?>/public/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= APP_URL ?>/public/images/logo.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= APP_URL ?>/public/images/logo.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/app.css?v=<?= time() ?>">
    <meta name="theme-color" content="#1B2B6B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <style>
    /* Enhanced Member Portal Styles */
    :root {
        --portal-primary: #1e3a8a;
        --portal-accent: #FF7E06;
        --portal-bg: #FFFFFF;
    }
    
    body.portal-body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--portal-bg);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Mobile-First Portal Container */
    .portal-wrapper {
        min-height: 100vh;
        padding-bottom: 70px;
    }
    
    /* Top Header - Hide on home page */
    .portal-header {
        background: white;
        padding: 16px 20px;
        position: sticky;
        top: 0;
        z-index: 1000;
        display: none; /* Hidden by default, show on other pages */
    }
    
    body[data-page="portal-savings"] .portal-header,
    body[data-page="portal-loans"] .portal-header,
    body[data-page="portal-statement"] .portal-header {
        display: block;
    }
    
    .portal-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .portal-page-title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }
    
    .portal-avatar-small {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--portal-primary), var(--portal-accent));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
    }
    
    /* Main Content Area */
    .portal-main {
        max-width: 500px;
        margin: 0 auto;
        padding: 0;
    }
    
    /* Bottom Navigation - iOS Style */
    .portal-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #E5E7EB;
        padding: 8px 0 max(8px, env(safe-area-inset-bottom));
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    
    .portal-nav-items {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 500px;
        margin: 0 auto;
        padding: 0 8px;
    }
    
    .portal-nav-item {
        flex: 1;
        text-align: center;
        text-decoration: none;
        color: #9CA3AF;
        padding: 8px 4px;
        border-radius: 12px;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    
    .portal-nav-item i {
        font-size: 20px;
        transition: all 0.3s ease;
    }
    
    .portal-nav-item span {
        font-size: 11px;
        font-weight: 500;
    }
    
    .portal-nav-item.active {
        color: #111827;
    }
    
    .portal-nav-item:active {
        transform: scale(0.95);
    }
    
    /* Desktop Sidebar (hidden on mobile) */
    @media (min-width: 992px) {
        .portal-wrapper {
            padding-bottom: 0;
        }
        
        .portal-bottom-nav {
            display: none;
        }
        
        #layoutSidenav {
            display: flex !important;
        }
    }
    
    @media (max-width: 991px) {
        #layoutSidenav {
            display: none !important;
        }
    }
    </style>
</head>
<body class="portal-body">
<?php
// The portal previously had its own lightweight top-navbar-only shell,
// visually disconnected from the rest of the app (no logo, no sidebar,
// generic Bootstrap navbar). This reuses the exact same sb-sidenav /
// sb-topnav / layoutSidenav shell and CSS (public/css/app.css) that every
// staff role already uses, so a member sees the same Empower chrome, logo
// and brand mark as staff -- just with a member-only nav list. No backend,
// route, or authorization logic here; purely the visual shell.
$portalNav = [
    ['page' => 'portal-home',       'icon' => 'bi-house-door-fill', 'label' => 'Home'],
    ['page' => 'portal-savings',    'icon' => 'bi-piggy-bank-fill', 'label' => 'Savings'],
    ['page' => 'portal-loans',      'icon' => 'bi-cash-coin',       'label' => 'Loans'],
    ['page' => 'portal-statement',  'icon' => 'bi-file-earmark-text-fill', 'label' => 'Statement'],
    ['page' => 'portal-menu',       'icon' => 'bi-grid-fill',       'label' => 'More'],
];
$currentPage  = $_GET['page'] ?? 'portal-home';
// loan-view.php drills into a single loan from My Loans -- keep that item
// highlighted as active rather than showing nothing selected.
if ($currentPage === 'portal-loan-view') { $currentPage = 'portal-loans'; }
if (in_array($currentPage, ['portal-profile', 'portal-repayments', 'portal-fees'])) { $currentPage = 'portal-menu'; }

$memberName   = htmlspecialchars(Session::get('user_name', 'Member'));
$memberNumber = isset($member['member_number']) ? htmlspecialchars($member['member_number']) : '';
$userInitial  = strtoupper(substr(Session::get('user_name', 'M'), 0, 1));

$activeNavEntry = null;
foreach ($portalNav as $nav) {
    if ($nav['page'] === $currentPage) { $activeNavEntry = $nav; break; }
}
$pageLabel = $activeNavEntry['label'] ?? ucfirst(str_replace(['portal-', '-'], ['', ' '], $currentPage));
?>

<!-- Mobile Header - Only shown on specific pages -->
<div class="portal-header">
    <div class="portal-header-content">
        <div class="portal-page-title"><?= $pageLabel ?></div>
        <div class="portal-avatar-small" onclick="window.location='<?= APP_URL ?>/index.php?page=portal-profile'">
            <?= $userInitial ?>
        </div>
    </div>
</div>

<!-- Desktop Sidebar (hidden on mobile) -->
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav sb-sidenav-dark">
            <div style="padding:1.25rem 1.25rem .75rem;display:flex;align-items:center;gap:.65rem;">
                <img src="<?= APP_URL ?>/public/images/logo.png" alt="Empower Logo" style="width:36px;height:36px;border-radius:.4rem;object-fit:contain;">
                <div style="line-height:1.15;overflow:hidden;">
                    <div style="font-size:.82rem;font-weight:700;color:#fff;white-space:nowrap;">Empower</div>
                    <div style="font-size:.54rem;font-weight:500;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.1em;">Member Portal</div>
                </div>
            </div>

            <div class="sb-sidenav-menu">
                <div class="nav">
                    <a class="nav-link <?= $currentPage === 'portal-home' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-home">
                        <div class="sb-nav-link-icon"><i class="bi bi-house-door-fill"></i></div> Home
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-profile' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-profile">
                        <div class="sb-nav-link-icon"><i class="bi bi-person"></i></div> My Profile
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-savings' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-savings">
                        <div class="sb-nav-link-icon"><i class="bi bi-piggy-bank-fill"></i></div> My Savings
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-loans' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-loans">
                        <div class="sb-nav-link-icon"><i class="bi bi-cash-coin"></i></div> My Loans
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-repayments' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-repayments">
                        <div class="sb-nav-link-icon"><i class="bi bi-receipt"></i></div> My Repayments
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-fees' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-fees">
                        <div class="sb-nav-link-icon"><i class="bi bi-tag"></i></div> My Fees
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-statement' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-statement">
                        <div class="sb-nav-link-icon"><i class="bi bi-file-earmark-text-fill"></i></div> My Statement
                    </a>
                </div>
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <strong><?= $memberName ?></strong>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <?= $content ?>
            </div>
        </main>
    </div>
</div>

<!-- Mobile Portal Wrapper -->
<div class="portal-wrapper">
    <div class="portal-main">
        <?= $content ?>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="portal-bottom-nav">
    <div class="portal-nav-items">
        <?php foreach ($portalNav as $nav): ?>
        <a href="<?= APP_URL ?>/index.php?page=<?= $nav['page'] ?>" 
           class="portal-nav-item <?= $currentPage === $nav['page'] ? 'active' : '' ?>">
            <i class="bi <?= $nav['icon'] ?>"></i>
            <span><?= htmlspecialchars($nav['label']) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
<script>
// Smooth scroll behavior
document.querySelectorAll('.portal-nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        // Add haptic feedback simulation on mobile
        if ('vibrate' in navigator) {
            navigator.vibrate(10);
        }
    });
});

// Prevent pull-to-refresh on mobile
let lastTouchY = 0;
let preventPullToRefresh = false;

document.addEventListener('touchstart', function(e) {
    if (e.touches.length !== 1) return;
    lastTouchY = e.touches[0].clientY;
    preventPullToRefresh = window.pageYOffset === 0;
}, {passive: true});

document.addEventListener('touchmove', function(e) {
    const touchY = e.touches[0].clientY;
    const touchYDelta = touchY - lastTouchY;
    lastTouchY = touchY;

    if (preventPullToRefresh && touchYDelta > 0) {
        e.preventDefault();
        return;
    }
}, {passive: false});
</script>
</body>
</html>
