<?php
$member = $member ?? [];
$memberNumber = htmlspecialchars($member['member_number'] ?? 'N/A');
$status = htmlspecialchars($member['status'] ?? 'Active');
$fullName = htmlspecialchars(trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')));
$gender = htmlspecialchars($member['gender'] ?? 'N/A');
$phone = htmlspecialchars($member['phone'] ?? 'N/A');
$email = htmlspecialchars($member['email'] ?? 'N/A');
$nationalId = htmlspecialchars($member['national_id'] ?? 'N/A');
$joinDate = !empty($member['join_date']) ? date('d M Y', strtotime($member['join_date'])) : 'N/A';
?>

<div class="portal-card--content portal-content--with-header">
    <!-- Profile Header -->
    <div style="display: flex; align-items: center; gap: 16px; padding-bottom: 24px; margin-bottom: 24px; border-bottom: 1px solid var(--portal-border);">
        <div class="portal-avatar portal-avatar--lg portal-avatar--gradient">
            <?= strtoupper(substr($member['first_name'] ?? 'M', 0, 1)) ?>
        </div>
        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: var(--portal-text-primary); margin: 0 0 4px 0;">
                <?= $fullName ?>
            </h2>
            <p style="font-size: 13px; color: var(--portal-text-secondary); margin: 0;">
                Member #<?= $memberNumber ?>
            </p>
        </div>
    </div>

    <!-- Profile Fields -->
    <div class="portal-grid portal-grid--md-2" style="gap: 24px 32px;">
        <div>
            <div class="portal-label">Status</div>
            <div class="portal-value" style="font-size: 15px;">
                <span class="portal-badge portal-badge--success"><?= $status ?></span>
            </div>
        </div>

        <div>
            <div class="portal-label">Gender</div>
            <div class="portal-value" style="font-size: 15px;"><?= $gender ?></div>
        </div>

        <div>
            <div class="portal-label">Phone Number</div>
            <div class="portal-value" style="font-size: 15px;"><?= $phone ?></div>
        </div>

        <div>
            <div class="portal-label">Email Address</div>
            <div class="portal-value" style="font-size: 15px;"><?= $email ?></div>
        </div>

        <div>
            <div class="portal-label">National ID</div>
            <div class="portal-value" style="font-size: 15px;"><?= $nationalId ?></div>
        </div>

        <div>
            <div class="portal-label">Join Date</div>
            <div class="portal-value" style="font-size: 15px;"><?= $joinDate ?></div>
        </div>
    </div>
</div>
