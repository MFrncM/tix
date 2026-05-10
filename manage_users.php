<?php
require_once 'includes/header.php';
requireAdmin();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_role') {
        $targetId = (int)($_POST['user_id'] ?? 0);
        $newRole  = $_POST['role'] ?? '';

        if (!$targetId || !in_array($newRole, User::ROLES())) {
            $error = 'Invalid request.';
        } elseif ($targetId === (int)$_SESSION['user_id'] && $newRole !== 'admin') {
            $error = 'You cannot remove your own admin role.';
        } else {
            $adminCount = User::countByRole($pdo, 'admin');
            $target     = User::findById($pdo, $targetId);
            if ($target && $target['role'] === 'admin' && $newRole !== 'admin' && $adminCount <= 1) {
                $error = 'Cannot demote the last administrator.';
            } else {
                User::updateRole($pdo, $targetId, $newRole);
                $success = 'User role updated successfully.';
            }
        }

    } elseif ($action === 'delete_user') {
        $targetId = (int)($_POST['user_id'] ?? 0);

        if (!$targetId) {
            $error = 'Invalid request.';
        } elseif ($targetId === (int)$_SESSION['user_id']) {
            $error = 'You cannot delete your own account.';
        } else {
            $target = User::findById($pdo, $targetId);
            if ($target && $target['role'] === 'admin' && User::countByRole($pdo, 'admin') <= 1) {
                $error = 'Cannot delete the last administrator.';
            } else {
                User::delete($pdo, $targetId);
                $success = 'User deleted successfully.';
            }
        }
    }
}

$users = User::getAll($pdo);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Manage Users</h4>
    <span class="badge bg-danger px-3 py-2" style="font-size:0.8rem;">Admin Only</span>
</div>

<?php if ($error): ?>
<div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success py-2"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tickets</th>
                        <th>Registered</th>
                        <th>Change Role</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-3 d-block mb-2 opacity-50"></i>No users found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <?php $isSelf = (int)$u['id'] === (int)$_SESSION['user_id']; ?>
                    <tr <?= $isSelf ? 'class="table-primary"' : '' ?>>
                        <td class="ps-3 text-muted small"><?= $u['id'] ?></td>
                        <td class="fw-medium">
                            <?= e($u['name']) ?>
                            <?php if ($isSelf): ?>
                            <span class="badge bg-secondary ms-1" style="font-size:0.6rem;">You</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?= e($u['email']) ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                            <?php elseif ($u['role'] === 'developer'): ?>
                            <span class="badge bg-warning text-dark">Developer</span>
                            <?php else: ?>
                            <span class="badge bg-info text-dark">User</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= (int)$u['ticket_count'] ?></span></td>
                        <td class="small text-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <?php if (!$isSelf): ?>
                            <form method="POST" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="role" class="form-select form-select-sm" style="width:auto;">
                                    <?php foreach (User::ROLES() as $role): ?>
                                    <option value="<?= $role ?>" <?= $u['role'] === $role ? 'selected' : '' ?>>
                                        <?= ucfirst($role) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-save"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$isSelf): ?>
                            <form method="POST" onsubmit="return confirm('Delete this user? Their tickets will remain in the system.');">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <small class="text-muted">
        <i class="bi bi-info-circle me-1"></i>
        Your own account is highlighted. You cannot delete yourself or demote the last administrator.
    </small>
</div>

<?php require_once 'includes/footer.php'; ?>
