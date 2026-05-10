<?php
require_once 'includes/header.php';
requireLogin();

$tickets = Ticket::getAll($pdo, ['submitted_by' => $_SESSION['user_id']]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-ticket me-2"></i>My Tickets</h4>
    <a href="<?= BASE_URL ?>/submit_ticket.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Submit New Ticket
    </a>
</div>

<div class="card border-0 shadow-sm">
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
                        <th>Submitted</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                            No tickets yet. <a href="<?= BASE_URL ?>/submit_ticket.php">Submit your first ticket</a>.
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
