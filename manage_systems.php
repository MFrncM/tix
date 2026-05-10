<?php
require_once 'includes/header.php';
requireDeveloper();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$name) {
            $error = 'System name is required.';
        } elseif (SystemProject::nameExists($pdo, $name)) {
            $error = 'A system with that name already exists.';
        } else {
            SystemProject::add($pdo, $name, $description ?: null);
            $success = 'System "' . e($name) . '" added successfully.';
        }

    } elseif ($action === 'toggle') {
        $sysId = (int)($_POST['system_id'] ?? 0);
        if ($sysId) {
            SystemProject::toggle($pdo, $sysId);
        }
        redirect(BASE_URL . '/manage_systems.php');
    }
}

$systems = SystemProject::getAll($pdo);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-grid me-2"></i>Manage Systems</h4>
</div>

<?php if ($error): ?>
<div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success py-2"><i class="bi bi-check-circle me-2"></i><?= $success ?></div>
<?php endif; ?>

<div class="row g-4">

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">System Name</th>
                            <th>Description</th>
                            <th>Tickets</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($systems)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-grid fs-3 d-block mb-2 opacity-50"></i>
                                No systems yet. Add one to get started.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($systems as $sys): ?>
                        <tr>
                            <td class="ps-3 fw-medium"><?= e($sys['name']) ?></td>
                            <td class="text-muted small"><?= e($sys['description'] ?: '—') ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $sys['ticket_count'] ?></span></td>
                            <td>
                                <span class="badge <?= $sys['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $sys['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="system_id" value="<?= $sys['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-<?= $sys['is_active'] ? 'danger' : 'success' ?>">
                                        <?= $sys['is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i>Add New System</h6>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">System Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="e.g. HR Portal" required
                               value="<?= e($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="3"
                                  placeholder="Brief description of this system…"><?= e($_POST['description'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-circle me-1"></i>Add System
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
