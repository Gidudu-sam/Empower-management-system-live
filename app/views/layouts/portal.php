<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: https:; connect-src 'self';">
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
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/portal.css?v=<?= time() ?>">
    <meta name="theme-color" content="#1B2B6B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <style>
    /* Portal Layout-Specific Styles */
    
    /* Top Header - Mobile only */
    .portal-header {
        background: white;
        padding: 12px 20px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        display: block;
        border-bottom: 1px solid var(--portal-border);
        min-height: 60px;
        display: flex;
        align-items: center;
    }
    
    /* Hide default header on home and menu pages - they use custom headers */
    body[data-page="portal-home"] .portal-header,
    body[data-page="portal-menu"] .portal-header {
        display: none !important;
    }
    
    /* Home page custom mobile header */
    .portal-home-mobile-header {
        background: white;
        padding: 16px 20px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        display: none;
        border-bottom: 1px solid var(--portal-border);
    }
    
    @media (max-width: 991px) {
        body[data-page="portal-home"] .portal-home-mobile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        body[data-page="portal-home"] .portal-main {
            padding-top: 68px;
        }
    }
    
    .portal-brand {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }
    
    /* Add padding to content when header is visible */
    body:not([data-page="portal-home"]):not([data-page="portal-menu"]) .portal-main {
        padding-top: 76px;
    }
    
    .portal-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        gap: 16px;
    }
    
    .portal-back-btn {
        width: 36px;
        height: 36px;
        border-radius: var(--portal-radius-full);
        background: #F3F4F6;
        color: var(--portal-text-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
        border: none;
        transition: all var(--portal-transition-base);
        flex-shrink: 0;
    }
    
    .portal-back-btn:active {
        background: #E5E7EB;
        transform: scale(0.95);
    }
    
    .portal-page-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--portal-text-primary);
        flex: 1;
        text-align: left;
    }
    
    /* Bottom Navigation - iOS Style */
    .portal-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid var(--portal-border-dark);
        padding: 8px 0 max(8px, env(safe-area-inset-bottom));
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    
    .portal-nav-items {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 100%;
        margin: 0 auto;
        padding: 0 8px;
    }
    
    .portal-nav-item {
        flex: 1;
        text-align: center;
        text-decoration: none;
        color: var(--portal-text-tertiary);
        padding: 8px 4px;
        border-radius: var(--portal-radius-md);
        transition: all var(--portal-transition-base);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    
    .portal-nav-item i {
        font-size: 20px;
        transition: all var(--portal-transition-slow);
    }
    
    .portal-nav-item span {
        font-size: 11px;
        font-weight: 500;
    }
    
    .portal-nav-item.active {
        color: var(--portal-primary);
    }
    
    .portal-nav-item.active i {
        transform: scale(1.1);
    }
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
    
    /* Desktop Sidebar - Exact Design Match */
    @media (min-width: 992px) {
        .portal-wrapper {
            display: none !important;
            padding-bottom: 0;
        }
        
        .portal-bottom-nav {
            display: none !important;
        }
        
        .portal-header {
            display: none !important;
        }
        
        #layoutSidenav {
            display: flex !important;
        }
        
        /* Sidebar exact styling */
        .sb-sidenav {
            background: #1E293B !important;
            border-right: none !important;
            width: 260px !important;
        }
        
        .sb-sidenav-dark {
            background: #1E293B !important;
        }
        
        /* Navigation menu */
        .sb-sidenav-menu {
            padding: 8px 0 !important;
        }
        
        .sb-sidenav-menu .nav {
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
        }
        
        /* Navigation links */
        .nav-link {
            color: rgba(255,255,255,0.7) !important;
            padding: 12px 16px !important;
            margin: 0 !important;
            border-radius: 0 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.2s !important;
            border-left: 3px solid transparent !important;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.05) !important;
            color: #FFFFFF !important;
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.1) !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
            border-left-color: #FF7E06 !important;
        }
        
        .sb-nav-link-icon {
            color: inherit !important;
            margin-right: 12px !important;
            font-size: 16px !important;
            width: 20px !important;
            text-align: center !important;
        }
        
        /* Footer / Logout */
        .sb-sidenav-footer {
            background: transparent !important;
            padding: 16px !important;
            border-top: 1px solid rgba(255,255,255,0.1) !important;
            margin-top: auto !important;
        }
        
        .sb-sidenav-footer .small {
            display: none !important;
        }
        
        .sb-sidenav-footer strong {
            display: none !important;
        }
        
        /* Add logout link styling if it's in footer */
        .sb-sidenav-footer a,
        .sb-sidenav-footer button {
            color: #FF7E06 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        
        /* Main content */
        #layoutSidenav_content {
            background: #F5F7FA !important;
            margin-left: 260px;
            margin-top: 0 !important; /* Remove top navigation spacing - portal has no top nav */
            width: calc(100% - 260px);
            min-height: 100vh;
        }
        
        #layoutSidenav_content main {
            padding: 24px !important; /* Reduced from 32px for tighter spacing */
            width: 100%;
            max-width: none;
            margin: 0;
        }
        
        body {
            background: #F5F7FA;
        }
    }
    
    @media (max-width: 991px) {
        #layoutSidenav {
            display: none !important;
        }
        
        .portal-wrapper {
            display: block;
        }
    }
    </style>
</head>
<body class="portal-body" data-page="<?= $currentPage ?>">
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
if (in_array($currentPage, ['portal-profile', 'portal-repayments', 'portal-fees', 'portal-share-certificate'])) { $currentPage = 'portal-menu'; }

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

<!-- Home Page Mobile Header - Only shown on home page mobile view -->
<div class="portal-home-mobile-header">
    <div class="portal-brand">Empower</div>
    <div class="portal-avatar-small" onclick="window.location='<?= APP_URL ?>/index.php?page=portal-profile'">
        <?= $userInitial ?>
    </div>
</div>

<!-- Desktop Sidebar (hidden on mobile) -->
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav sb-sidenav-dark">
            <div style="padding:20px 20px;display:flex;align-items:center;gap:12px;border-bottom:1px solid rgba(255,255,255,0.1);">
                <div style="width:44px;height:44px;border-radius:50%;background:#FF7E06;color:white;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;flex-shrink:0;">
                    <?= $userInitial ?>
                </div>
                <div style="flex:1;">
                    <div style="color:#FFF;font-size:15px;font-weight:600;margin-bottom:2px;"><?= explode(' ', $memberName)[0] ?></div>
                    <div style="color:rgba(255,255,255,0.5);font-size:11px;">Member portal</div>
                </div>
            </div>

            <div class="sb-sidenav-menu">
                <div style="padding:8px 0 4px 20px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.4);margin-top:16px;">Overview</div>
                <div class="nav">
                    <a class="nav-link <?= $currentPage === 'portal-home' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-home">
                        <div class="sb-nav-link-icon"><i class="bi bi-house-door-fill"></i></div> Home
                    </a>
                </div>
                
                <div style="padding:8px 0 4px 20px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.4);margin-top:20px;">My Finances</div>
                <div class="nav">
                    <a class="nav-link <?= $currentPage === 'portal-savings' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-savings">
                        <div class="sb-nav-link-icon"><i class="bi bi-piggy-bank-fill"></i></div> My Savings
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-share-certificate' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-share-certificate">
                        <div class="sb-nav-link-icon"><i class="bi bi-award-fill"></i></div> My Shares
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
                        <div class="sb-nav-link-icon"><i class="bi bi-file-earmark-text-fill"></i></div> My Statements
                    </a>
                </div>
                
                <div style="padding:8px 0 4px 20px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.4);margin-top:20px;">Account & Support</div>
                <div class="nav">
                    <a class="nav-link <?= $currentPage === 'portal-profile' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-profile">
                        <div class="sb-nav-link-icon"><i class="bi bi-person"></i></div> My Profile
                    </a>
                    <a class="nav-link <?= $currentPage === 'portal-notifications' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php?page=portal-notifications">
                        <div class="sb-nav-link-icon"><i class="bi bi-bell-fill"></i></div> Notifications
                    </a>
                </div>
            </div>

            <div class="sb-sidenav-footer">
                <a href="<?= APP_URL ?>/index.php?page=logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Log out</span>
                </a>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <?= $content ?>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= APP_URL ?>/public/js/portal-chart.js"></script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
<script>
// Back button handler
function handleBackClick() {
    const currentPage = '<?= $currentPage ?>';
    const pagesFromMore = ['portal-profile', 'portal-repayments', 'portal-fees', 'portal-change-password', 
                           'portal-share-certificate', 'portal-notifications'];
    
    // If page was accessed from More menu, go back to More
    if (pagesFromMore.includes(currentPage)) {
        window.location.href = '<?= APP_URL ?>/index.php?page=portal-menu';
    } 
    // For main pages (Savings, Loans, Statement), go to Home
    else if (['portal-savings', 'portal-loans', 'portal-statement'].includes(currentPage)) {
        window.location.href = '<?= APP_URL ?>/index.php?page=portal-home';
    }
    // For loan detail view, go back to loans
    else if (currentPage === 'portal-loan-view') {
        window.location.href = '<?= APP_URL ?>/index.php?page=portal-loans';
    }
    // Default: browser back
    else if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '<?= APP_URL ?>/index.php?page=portal-home';
    }
    
    // Haptic feedback
    if ('vibrate' in navigator) {
        navigator.vibrate(10);
    }
}

// Add data-page attribute to body for CSS targeting (already set in HTML)
// document.body.setAttribute('data-page', '<?= $currentPage ?>');

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
