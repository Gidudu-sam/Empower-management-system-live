<?php
$notifications = $notifications ?? [];
$member = $member ?? [];
?>

<div class="portal-content-padded">
    <?php if (!empty($notifications)): ?>
    <div class="portal-section-header">
        <div>
            <h2 class="portal-section-title">All Notifications</h2>
        </div>
        <span class="portal-section-subtitle"><?= count($notifications) ?> total</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php foreach ($notifications as $notif): 
            $isUnread = ($notif['is_read'] ?? 0) == 0;
            $type = $notif['notification_type'] ?? 'info';
            $icon = [
                'info' => 'bi-info-circle-fill',
                'success' => 'bi-check-circle-fill',
                'warning' => 'bi-exclamation-triangle-fill',
                'danger' => 'bi-x-circle-fill',
            ][$type] ?? 'bi-bell-fill';
            
            $title = htmlspecialchars($notif['title'] ?? 'Notification');
            $message = htmlspecialchars($notif['message'] ?? '');
            $createdAt = isset($notif['created_at']) ? date('d M Y, g:i A', strtotime($notif['created_at'])) : 'Just now';
        ?>
        <div class="portal-card portal-card--content" style="display: flex; gap: 14px; <?= $isUnread ? 'border-left: 4px solid #FF7E06;' : '' ?>">
            <div style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; background: <?= $type === 'success' ? '#ECFDF5' : ($type === 'warning' ? '#FFF7ED' : ($type === 'danger' ? '#FEE2E2' : '#EEF2FF')) ?>; color: <?= $type === 'success' ? '#059669' : ($type === 'warning' ? '#FF7E06' : ($type === 'danger' ? '#DC2626' : '#1B2B6B')) ?>;">
                <i class="bi <?= $icon ?>"></i>
            </div>
            <div style="flex: 1;">
                <div class="portal-list__title"><?= $title ?></div>
                <div style="font-size: 13px; color: #6B7280; line-height: 1.5; margin-bottom: 6px;"><?= $message ?></div>
                <div style="font-size: 11px; color: #9CA3AF;"><?= $createdAt ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="portal-empty" style="padding: 80px 20px;">
        <div class="portal-empty__icon"><i class="bi bi-bell"></i></div>
        <div class="portal-empty__text">
            <div style="font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 8px;">No notifications yet</div>
            <div style="font-size: 13px; color: #6B7280;">You'll be notified about deposits, loan updates, and other important activities here.</div>
        </div>
    </div>
    <?php endif; ?>
</div>

