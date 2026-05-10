<?php
require_once 'includes/header.php';
requireAdmin();

$stats       = Ticket::getStats($pdo);
$adminCount  = User::countByRole($pdo, 'admin');
$devCount    = User::countByRole($pdo, 'developer');
$userCount   = User::countByRole($pdo, 'user');
$totalUsers  = $adminCount + $devCount + $userCount;

$recentTickets = Ticket::getAll($pdo, []);
$recentTickets = array_slice($recentTickets, 0, 10);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Dashboard</h4>
    <span class="badge bg-danger px-3 py-2" style="font-size:0.8rem;">Master Control</span>
</div>

<!-- Ticket Stats -->
<h6 class="text-uppercase small text-muted fw-bold mb-3">Ticket Overview</h6>
<div class="row g-3 mb-4">
    <?php
    $statsConfig = [
        ['label' => 'Open',        'key' => 'open_count',       'icon' => 'bi-folder2-open', 'color' => 'primary'],
        ['label' => 'In Progress', 'key' => 'inprogress_count', 'icon' => 'bi-arrow-repeat',  'color' => 'warning'],
        ['label' => 'Resolved',    'key' => 'resolved_count',   'icon' => 'bi-check-circle',  'color' => 'success'],
        ['label' => 'Closed',      'key' => 'closed_count',     'icon' => 'bi-archive',       'color' => 'secondary'],
    ];
    foreach ($statsConfig as $sc):
    ?>
    <div class="col-md-3 col-6">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-<?= $sc['color'] ?>-subtle text-<?= $sc['color'] ?> rounded-3 me-3">
                        <i class="bi <?= $sc['icon'] ?> fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small"><?= $sc['label'] ?></div>
                        <div class="fs-3 fw-bold lh-1"><?= (int)($stats[$sc['key']] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- User Stats + Quick Links -->
<div class="row g-4 mb-4">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-uppercase small text-muted fw-bold mb-3">User Breakdown</h6>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span><span class="badge bg-danger me-2">Admin</span>Administrators</span>
                    <strong><?= $adminCount ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span><span class="badge bg-warning text-dark me-2">Dev</span>Developers</span>
                    <strong><?= $devCount ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span><span class="badge bg-info text-dark me-2">User</span>Regular Users</span>
                    <strong><?= $userCount ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3 mt-1 border-top">
                    <span class="fw-medium">Total Users</span>
                    <strong class="fs-5"><?= $totalUsers ?></strong>
                </div>
                <a href="<?= BASE_URL ?>/manage_users.php" class="btn btn-outline-danger btn-sm w-100 mt-3">
                    <i class="bi bi-people me-1"></i>Manage Users
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-uppercase small text-muted fw-bold mb-3">System Management</h6>
                <p class="text-muted small">Add, activate, or deactivate systems that users file tickets against.</p>
                <a href="<?= BASE_URL ?>/manage_systems.php" class="btn btn-outline-primary btn-sm w-100 mt-2">
                    <i class="bi bi-grid me-1"></i>Manage Systems
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-uppercase small text-muted fw-bold mb-3">Ticket Management</h6>
                <p class="text-muted small">View all tickets across the system, filter by status, system, or priority.</p>
                <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                    <i class="bi bi-speedometer2 me-1"></i>All Tickets
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Recent Tickets -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Recent Tickets</h6>
        <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Title</th>
                        <th>System</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentTickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                            No tickets in the system yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentTickets as $t): ?>
                    <tr>
                        <td class="ps-3 text-muted small">#<?= $t['id'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/view_ticket.php?id=<?= $t['id'] ?>" class="text-decoration-none fw-medium text-dark">
                                <?= e($t['title']) ?>
                            </a>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= e($t['system_name']) ?></span></td>
                        <td><?= priorityBadge($t['priority']) ?></td>
                        <td><?= statusBadge($t['status']) ?></td>
                        <td class="small"><?= e($t['submitter_name']) ?></td>
                        <td class="small text-muted"><?= date('M d, Y', strtotime($t['created_at'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/view_ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
