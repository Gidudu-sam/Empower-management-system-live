<?php $base = APP_URL . '/index.php'; ?>

<style>
body {
    background: #1e3a8a;
}

.portal-main {
    padding: 0;
    max-width: 500px;
    margin: 0 auto;
}

/* Profile Header */
.profile-header {
    padding: 24px 20px;
    color: white;
}

.profile-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #FF7E06;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 16px;
}

.profile-name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 2px;
}

.profile-subtitle {
    font-size: 12px;
    opacity: 0.7;
}

/* Menu Items */
.menu-list {
    padding: 0;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    color: white;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    transition: background 0.2s;
}

.menu-item:hover {
    background: rgba(255,255,255,0.05);
}

.menu-item:active {
    background: rgba(255,255,255,0.1);
}

.menu-icon {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.menu-label {
    flex: 1;
    font-size: 15px;
    font-weight: 500;
}

/* Logout */
.menu-item.logout {
    margin-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.1);
    border-bottom: none;
}
</style>

<?php
$member = $member ?? [];
$memberName = htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
$userInitial = strtoupper(substr($member['first_name'] ?? 'M', 0, 1));
?>

<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-avatar"><?= $userInitial ?></div>
    <div class="profile-name"><?= $memberName ?></div>
    <div class="profile-subtitle">Member portal</div>
</div>

<!-- Menu List -->
<div class="menu-list">
    <a href="<?= $base ?>?page=portal-home" class="menu-item">
        <i class="bi bi-house-door menu-icon"></i>
        <span class="menu-label">Home</span>
    </a>
    
    <a href="<?= $base ?>?page=portal-profile" class="menu-item">
        <i class="bi bi-person menu-icon"></i>
        <span class="menu-label">My Profile</span>
    </a>
    
    <a href="<?= $base ?>?page=portal-savings" class="menu-item">
        <i class="bi bi-wallet2 menu-icon"></i>
        <span class="menu-label">My Savings</span>
    </a>
    
    <a href="<?= $base ?>?page=portal-loans" class="menu-item">
        <i class="bi bi-cash-coin menu-icon"></i>
        <span class="menu-label">My Loans</span>
    </a>
    
    <a href="<?= $base ?>?page=portal-repayments" class="menu-item">
        <i class="bi bi-arrow-repeat menu-icon"></i>
        <span class="menu-label">My Repayments</span>
    </a>
    
    <a href="<?= $base ?>?page=portal-fees" class="menu-item">
        <i class="bi bi-tag menu-icon"></i>
        <span class="menu-label">My Fees</span>
    </a>
    
    <a href="<?= $base ?>?page=portal-statement" class="menu-item">
        <i class="bi bi-file-earmark-text menu-icon"></i>
        <span class="menu-label">My Statement</span>
    </a>
    
    <a href="<?= $base ?>?page=logout" class="menu-item logout">
        <i class="bi bi-box-arrow-right menu-icon"></i>
        <span class="menu-label">Log out</span>
    </a>
</div>
