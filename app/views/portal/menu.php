<?php $base = APP_URL . '/index.php'; ?>

<?php
$member = $member ?? [];
$firstName = $member['first_name'] ?? '';
$lastName = $member['last_name'] ?? '';
$memberName = htmlspecialchars(trim($firstName . ' ' . $lastName));
$memberNumber = htmlspecialchars($member['member_number'] ?? 'N/A');
$userInitial = strtoupper(substr($firstName ?: 'M', 0, 1));
$status = htmlspecialchars($member['status'] ?? 'Active');
$notificationCount = $notificationCount ?? 0;
?>

<!-- Header -->
<div style="padding: 20px 20px 30px; display: flex; align-items: center; gap: 16px; border-bottom: 1px solid #F3F4F6; background: white;">
    <div class="portal-avatar portal-avatar--lg portal-avatar--gradient"><?= $userInitial ?></div>
    <div style="flex: 1;">
        <div style="font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px;"><?= $memberName ?></div>
        <div style="font-size: 13px; color: #6B7280; margin-bottom: 2px;">Member #<?= $memberNumber ?></div>
        <span class="portal-badge portal-badge--success"><?= $status ?></span>
    </div>
</div>

<!-- Quick Actions -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; padding: 24px 20px; border-bottom: 8px solid #F9FAFB; background: white;">
    <a href="<?= $base ?>?page=portal-profile" style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #111827;">
        <div style="width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 8px; background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #1B2B6B;">
            <i class="bi bi-person-fill"></i>
        </div>
        <div style="font-size: 12px; font-weight: 500; text-align: center;">My Profile</div>
    </a>
    
    <a href="<?= $base ?>?page=portal-repayments" style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #111827;">
        <div style="width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 8px; background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%); color: #FF7E06;">
            <i class="bi bi-arrow-repeat"></i>
        </div>
        <div style="font-size: 12px; font-weight: 500; text-align: center;">Repayments</div>
    </a>
    
    <a href="<?= $base ?>?page=portal-fees" style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #111827;">
        <div style="width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 8px; background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); color: #059669;">
            <i class="bi bi-tag-fill"></i>
        </div>
        <div style="font-size: 12px; font-weight: 500; text-align: center;">My Fees</div>
    </a>
    
    <a href="<?= $base ?>?page=portal-share-certificate" style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #111827;">
        <div style="width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 8px; background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%); color: #0284C7;">
            <i class="bi bi-award-fill"></i>
        </div>
        <div style="font-size: 12px; font-weight: 500; text-align: center;">My Shares</div>
    </a>
    
    <a href="<?= $base ?>?page=portal-statement" style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #111827;">
        <div style="width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 8px; background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #1B2B6B;">
            <i class="bi bi-file-earmark-text-fill"></i>
        </div>
        <div style="font-size: 12px; font-weight: 500; text-align: center;">Statements</div>
    </a>
    
    <a href="<?= $base ?>?page=portal-change-password" style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #111827;">
        <div style="width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 8px; background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%); color: #FF7E06;">
            <i class="bi bi-key-fill"></i>
        </div>
        <div style="font-size: 12px; font-weight: 500; text-align: center;">Password</div>
    </a>
</div>

<!-- Menu List -->
<div style="padding: 0 20px; background: white;">
    <a href="<?= $base ?>?page=portal-notifications" style="display: flex; align-items: center; gap: 14px; padding: 16px 0; text-decoration: none; color: #111827; border-bottom: 1px solid #F3F4F6;">
        <i class="bi bi-bell-fill" style="width: 24px; text-align: center; color: #6B7280; font-size: 20px;"></i>
        <div style="flex: 1;">
            <div style="font-size: 15px; font-weight: 500;">
                Notifications
                <?php if ($notificationCount > 0): ?>
                <span class="portal-badge portal-badge--danger" style="margin-left: 8px;"><?= $notificationCount ?></span>
                <?php endif; ?>
            </div>
        </div>
        <i class="bi bi-chevron-right" style="color: #D1D5DB; font-size: 16px;"></i>
    </a>
</div>

<!-- Logout -->
<div style="padding: 24px 20px 32px; border-top: 8px solid #F9FAFB; background: white;">
    <a href="<?= $base ?>?page=logout" style="width: 100%; padding: 16px; background: white; border: 1.5px solid #FEE2E2; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none; color: #EF4444; font-weight: 600; font-size: 15px;">
        <i class="bi bi-box-arrow-right" style="font-size: 20px;"></i>
        <span>Log Out</span>
    </a>
</div>

<!-- Version -->
<div style="text-align: center; padding: 20px; color: #9CA3AF; font-size: 11px; background: white;">
    Empower Investment Club v2.0
</div>

