<?php
require_once 'includes/header.php';
requireLogin();

$systems = SystemProject::getActive($pdo);
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $systemId    = (int)($_POST['system_id'] ?? 0);
    $type        = $_POST['ticket_type'] ?? '';
    $priority    = $_POST['priority'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (!$title || !$systemId || !$type || !$priority || !$description) {
        $error = 'Please fill in all required fields.';
    } elseif (!Ticket::isValidType($type) || !Ticket::isValidPriority($priority)) {
        $error = 'Invalid type or priority selected.';
    } elseif (!SystemProject::isActiveAndValid($pdo, $systemId)) {
        $error = 'Invalid system selected.';
    } else {
        $ticketId = Ticket::create($pdo, [
            'title'       => $title,
            'description' => $description,
            'system_id'   => $systemId,
            'ticket_type' => $type,
            'priority'    => $priority,
            'submitted_by' => $_SESSION['user_id'],
        ]);
        redirect(BASE_URL . '/view_ticket.php?id=' . $ticketId . '&submitted=1');
    }
}

$backUrl = BASE_URL . '/' . (isDeveloper() ? 'dashboard.php' : 'my_tickets.php');
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">

        <div class="d-flex align-items-center mb-4">
            <a href="<?= $backUrl ?>" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Submit a Ticket</h4>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>

        <?php if (empty($systems)): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle me-2"></i>
            No active systems are available. Please contact the developer to set up systems before submitting a ticket.
        </div>
        <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               placeholder="Brief description of the issue or request" required
                               value="<?= e($_POST['title'] ?? '') ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">System / Project <span class="text-danger">*</span></label>
                            <select name="system_id" class="form-select" required>
                                <option value="">Select system…</option>
                                <?php foreach ($systems as $sys): ?>
                                <option value="<?= $sys['id'] ?>" <?= ($_POST['system_id'] ?? '') == $sys['id'] ? 'selected' : '' ?>>
                                    <?= e($sys['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Request Type <span class="text-danger">*</span></label>
                            <select name="ticket_type" class="form-select" required>
                                <option value="">Select type…</option>
                                <?php foreach (Ticket::TYPES() as $t): ?>
                                <option value="<?= $t ?>" <?= ($_POST['ticket_type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Priority <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 flex-wrap mt-1">
                            <?php
                            $priorityConfig = [
                                'Low'      => ['bg-success',           'bi-arrow-down-circle', 'For minor issues with no time constraint'],
                                'Medium'   => ['bg-primary',           'bi-dash-circle',       'Standard requests'],
                                'High'     => ['bg-warning text-dark', 'bi-arrow-up-circle',   'Affects workflow significantly'],
                                'Critical' => ['bg-danger',            'bi-exclamation-circle','System is broken or unusable'],
                            ];
                            $selectedPriority = $_POST['priority'] ?? 'Medium';
                            foreach ($priorityConfig as $p => [$cls, $icon, $hint]):
                            ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="priority"
                                       id="p_<?= $p ?>" value="<?= $p ?>"
                                       <?= $selectedPriority === $p ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="p_<?= $p ?>">
                                    <span class="badge <?= $cls ?>">
                                        <i class="bi <?= $icon ?> me-1"></i><?= $p ?>
                                    </span>
                                    <small class="text-muted d-block"><?= $hint ?></small>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="7"
                                  placeholder="Describe the issue in detail. For bugs, include steps to reproduce and what you expected to happen."
                                  required><?= e($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Submit Ticket
                        </button>
                        <a href="<?= $backUrl ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
