<?php
/** Roles & permissions (read-only matrix). Authorization still uses Session::hasRole(). */
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Roles &amp; Permissions</h2>
            <p class="text-muted mb-0">Role definitions and assigned permissions from the database.</p>
        </div>
        <a href="<?= APP_URL ?>/index.php?page=settings-users" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-people me-1"></i> Manage Users
        </a>
    </div>

    <div class="alert alert-info">
        Live access checks currently use role names via <code>Session::hasRole()</code>.
        This page shows the <code>roles</code> / <code>permissions</code> / <code>role_permissions</code> tables for administration visibility.
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Roles</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>ID</th><th>Name</th><th>Label</th></tr></thead>
                            <tbody>
                            <?php foreach (($roles ?? []) as $role): ?>
                                <tr>
                                    <td><?= (int)$role['id'] ?></td>
                                    <td><code><?= htmlspecialchars($role['name'] ?? '') ?></code></td>
                                    <td><?= htmlspecialchars($role['display_name'] ?? $role['label'] ?? $role['name'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Permissions by Role</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Permission</th>
                                    <?php foreach (($roles ?? []) as $role): ?>
                                        <th class="text-center"><small><?= htmlspecialchars($role['name'] ?? '') ?></small></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach (($permissions ?? []) as $perm): ?>
                                <tr>
                                    <td>
                                        <code><?= htmlspecialchars($perm['name'] ?? '') ?></code>
                                        <?php if (!empty($perm['module'])): ?>
                                            <div class="small text-muted"><?= htmlspecialchars($perm['module']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <?php foreach (($roles ?? []) as $role): ?>
                                        <?php $has = in_array((int)$perm['id'], $rolePerms[(int)$role['id']] ?? [], true); ?>
                                        <td class="text-center"><?= $has ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<span class="text-muted">—</span>' ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($permissions)): ?>
                                <tr><td colspan="<?= 1 + count($roles ?? []) ?>" class="text-muted p-3">No permissions rows found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
