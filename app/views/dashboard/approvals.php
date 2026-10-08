<?php
$activeTab = $_GET['tab'] ?? 'pending';
$statusFilter = match($activeTab) {
    'approved' => 'approved',
    'rejected' => 'rejected',
    default => 'pending'
};

$typeColors = [
    'Internal Voucher' => 'primary',
    'Member Adjustment' => 'warning',
    'Investment' => 'success',
    'Opening Balance' => 'info',
    'Loan' => 'danger',
    'Loan Application' => 'secondary',
];

$typeIcons = [
    'Internal Voucher' => 'receipt',
    'Member Adjustment' => 'calculator',
    'Investment' => 'graph-up-arrow',
    'Opening Balance' => 'database',
    'Loan' => 'cash-coin',
    'Loan Application' => 'file-earmark-text',
];

// Filter approvals by tab
$filteredApprovals = array_filter($allApprovals, function($item) use ($statusFilter) {
    return ($item['status'] ?? 'pending') === $statusFilter;
});

$totalPending = count(array_filter($allApprovals, fn($i) => ($i['status'] ?? 'pending') === 'pending'));
$totalApproved = count(array_filter($allApprovals, fn($i) => ($i['status'] ?? 'pending') === 'approved'));
$totalRejected = count(array_filter($allApprovals, fn($i) => ($i['status'] ?? 'pending') === 'rejected'));
?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mt-4 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold" style="color:var(--brand-navy);">
            <i class="bi bi-clipboard-check me-2" style="color:#F47920;"></i>Approvals Management
        </h1>
        <p class="text-muted mb-0 small">Review and manage all approval requests</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body">
                <div class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px; font-weight: 600;">Pending Review</div>
                <div class="h2 mb-0 fw-bold" style="color: #333;"><?= $totalPending ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body">
                <div class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px; font-weight: 600;">Approved</div>
                <div class="h2 mb-0 fw-bold" style="color: #333;"><?= $totalApproved ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body">
                <div class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px; font-weight: 600;">Rejected</div>
                <div class="h2 mb-0 fw-bold" style="color: #333;"><?= $totalRejected ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body">
                <div class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px; font-weight: 600;">Total Items</div>
                <div class="h2 mb-0 fw-bold" style="color: #333;"><?= count($allApprovals) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs and Table -->
<div class="card shadow-sm border-0">
    <!-- Tab Navigation -->
    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $activeTab === 'pending' ? 'active' : '' ?>" 
                   href="?page=pending-approvals&tab=pending"
                   style="<?= $activeTab === 'pending' ? 'border-bottom: 3px solid #F47920 !important;' : '' ?>">
                    <i class="bi bi-clock-history me-2"></i>
                    <span class="fw-semibold">Pending</span>
                    <?php if ($totalPending > 0): ?>
                    <span class="badge bg-warning text-dark ms-2"><?= $totalPending ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $activeTab === 'approved' ? 'active' : '' ?>" 
                   href="?page=pending-approvals&tab=approved"
                   style="<?= $activeTab === 'approved' ? 'border-bottom: 3px solid #F47920 !important;' : '' ?>">
                    <i class="bi bi-check-circle me-2"></i>
                    <span class="fw-semibold">Approved</span>
                    <?php if ($totalApproved > 0): ?>
                    <span class="badge bg-success ms-2"><?= $totalApproved ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $activeTab === 'rejected' ? 'active' : '' ?>" 
                   href="?page=pending-approvals&tab=rejected"
                   style="<?= $activeTab === 'rejected' ? 'border-bottom: 3px solid #F47920 !important;' : '' ?>">
                    <i class="bi bi-x-circle me-2"></i>
                    <span class="fw-semibold">Rejected</span>
                    <?php if ($totalRejected > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $totalRejected ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- Filter Bar -->
    <div class="card-body border-bottom bg-light py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by reference or description...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="Internal Voucher">Internal Voucher</option>
                    <option value="Loan">Loan</option>
                    <option value="Loan Application">Loan Application</option>
                    <option value="Investment">Investment</option>
                    <option value="Member Adjustment">Member Adjustment</option>
                    <option value="Opening Balance">Opening Balance</option>
                </select>
            </div>
            <div class="col-md-7 text-end">
                <small class="text-muted">
                    Showing <span id="resultCount"><?= count($filteredApprovals) ?></span> 
                    <?= $activeTab === 'pending' ? 'pending' : ($activeTab === 'approved' ? 'approved' : 'rejected') ?> item(s)
                </small>
            </div>
        </div>
    </div>

    <!-- Table Content -->
    <div class="card-body p-0">
        <?php if (empty($filteredApprovals)): ?>
        <div class="text-center py-5">
            <?php if ($activeTab === 'pending'): ?>
                <i class="bi bi-check-circle text-success" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 text-muted">All Caught Up!</h5>
                <p class="text-muted mb-0">No pending approvals at this time</p>
            <?php elseif ($activeTab === 'approved'): ?>
                <i class="bi bi-clipboard-check text-info" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 text-muted">No Approved Items</h5>
                <p class="text-muted mb-0">Approved items will appear here</p>
            <?php else: ?>
                <i class="bi bi-shield-check text-secondary" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 text-muted">No Rejected Items</h5>
                <p class="text-muted mb-0">Rejected items will appear here</p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="approvalsTable">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 180px;">
                            <small class="text-muted fw-semibold">TYPE</small>
                        </th>
                        <th class="py-3" style="width: 150px;">
                            <small class="text-muted fw-semibold">REFERENCE</small>
                        </th>
                        <th class="py-3">
                            <small class="text-muted fw-semibold">DESCRIPTION</small>
                        </th>
                        <th class="py-3 text-end" style="width: 140px;">
                            <small class="text-muted fw-semibold">AMOUNT</small>
                        </th>
                        <th class="py-3" style="width: 140px;">
                            <small class="text-muted fw-semibold">SUBMITTED BY</small>
                        </th>
                        <th class="py-3" style="width: 150px;">
                            <small class="text-muted fw-semibold">DATE</small>
                        </th>
                        <?php if ($activeTab !== 'pending'): ?>
                        <th class="py-3" style="width: 140px;">
                            <small class="text-muted fw-semibold">ACTIONED BY</small>
                        </th>
                        <?php endif; ?>
                        <th class="text-center pe-4 py-3" style="width: 100px;">
                            <small class="text-muted fw-semibold">ACTION</small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredApprovals as $item): ?>
                    <tr data-type="<?= htmlspecialchars($item['type']) ?>" data-ref="<?= htmlspecialchars($item['reference']) ?>" data-desc="<?= htmlspecialchars($item['description']) ?>">
                        <td class="ps-4">
                            <span class="badge bg-<?= $typeColors[$item['type']] ?? 'secondary' ?> d-flex align-items-center gap-1 justify-content-center py-2">
                                <i class="bi bi-<?= $typeIcons[$item['type']] ?? 'file-earmark' ?>"></i>
                                <span class="small"><?= htmlspecialchars($item['type']) ?></span>
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($item['reference']) ?></span>
                        </td>
                        <td>
                            <div class="text-muted small" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($item['description']) ?>">
                                <?= htmlspecialchars($item['description']) ?>
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark">Shs <?= number_format($item['amount'], 0) ?></span>
                        </td>
                        <td>
                            <span class="small text-muted"><?= htmlspecialchars($item['submitted_by']) ?></span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= date('d M Y', strtotime($item['submitted_at'])) ?>
                                <br>
                                <i class="bi bi-clock me-1"></i>
                                <?= date('H:i', strtotime($item['submitted_at'])) ?>
                            </small>
                        </td>
                        <?php if ($activeTab !== 'pending'): ?>
                        <td>
                            <span class="small text-muted"><?= htmlspecialchars($item['actioned_by'] ?? 'System') ?></span>
                        </td>
                        <?php endif; ?>
                        <td class="text-center pe-4">
                            <a href="<?= $item['url'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript for filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const table = document.getElementById('approvalsTable');
    const resultCount = document.getElementById('resultCount');
    
    function filterTable() {
        if (!table) return;
        
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const type = row.dataset.type;
            const ref = row.dataset.ref.toLowerCase();
            const desc = row.dataset.desc.toLowerCase();
            
            const matchesSearch = ref.includes(searchTerm) || desc.includes(searchTerm);
            const matchesType = !selectedType || type === selectedType;
            
            if (matchesSearch && matchesType) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        if (resultCount) {
            resultCount.textContent = visibleCount;
        }
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', filterTable);
    }
});
</script>
