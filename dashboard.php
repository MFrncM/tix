<?php
require_once 'includes/header.php';
requireDeveloper();

$stats = Ticket::getStats($pdo);

$filters = [
    'system_id' => $_GET['system']   ?? '',
    'status'    => $_GET['status']   ?? '',
    'priority'  => $_GET['priority'] ?? '',
];
$tickets = Ticket::getAll($pdo, array_filter($filters));
$systems = SystemProject::getActive($pdo);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i>Developer Dashboard</h4>
    <a href="<?= BASE_URL ?>/submit_ticket.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>New Ticket
    </a>
</div>

<!-- Stats -->
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

<!-- Filters + Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">System</label>
                <select name="system" class="form-select form-select-sm">
                    <option value="">All Systems</option>
                    <?php foreach ($systems as $sys): ?>
                    <option value="<?= $sys['id'] ?>" <?= ($filters['system_id'] == $sys['id']) ? 'selected' : '' ?>>
                        <?= e($sys['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <?php foreach (Ticket::STATUSES() as $s): ?>
                    <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Priority</label>
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priorities</option>
                    <?php foreach (Ticket::PRIORITIES() as $p): ?>
                    <option value="<?= $p ?>" <?= $filters['priority'] === $p ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <?php if (array_filter($filters)): ?>
                <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Title</th>
                        <th>System</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                            No tickets found for the selected filters.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td class="ps-3 text-muted small">#<?= $t['id'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/view_ticket.php?id=<?= $t['id'] ?>" class="text-decoration-none fw-medium text-dark">
                                <?= e($t['title']) ?>
                            </a>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= e($t['system_name']) ?></span></td>
                        <td class="small text-muted"><?= e($t['ticket_type']) ?></td>
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
