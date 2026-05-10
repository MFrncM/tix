<?php
require_once 'includes/header.php';
requireLogin();

$id      = (int)($_GET['id'] ?? 0);
$backUrl = BASE_URL . '/' . (isDeveloper() ? 'dashboard.php' : 'my_tickets.php');

if (!$id) {
    redirect($backUrl);
}

$ticket = Ticket::getById($pdo, $id);
if (!$ticket) {
    redirect($backUrl);
}

if (!isDeveloper() && $ticket['submitted_by'] != $_SESSION['user_id']) {
    redirect(BASE_URL . '/my_tickets.php');
}

$error   = '';
$success = isset($_GET['submitted']) ? 'Your ticket has been submitted successfully!' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_comment') {
        $comment = trim($_POST['comment'] ?? '');
        if ($comment) {
            TicketComment::add($pdo, $id, $_SESSION['user_id'], $comment);
            redirect(BASE_URL . '/view_ticket.php?id=' . $id . '#comments');
        } else {
            $error = 'Comment cannot be empty.';
        }

    } elseif ($action === 'update_ticket' && isDeveloper()) {
        $newStatus   = $_POST['status']   ?? '';
        $newPriority = $_POST['priority'] ?? '';

        if (Ticket::isValidStatus($newStatus) && Ticket::isValidPriority($newPriority)) {
            if ($newStatus !== $ticket['status']) {
                TicketComment::add($pdo, $id, $_SESSION['user_id'],
                    "Status changed from {$ticket['status']} to {$newStatus}."
                );
            }
            Ticket::update($pdo, $id, $newStatus, $newPriority);
            redirect(BASE_URL . '/view_ticket.php?id=' . $id);
        }
    }
}

$ticket   = Ticket::getById($pdo, $id);
$comments = TicketComment::getByTicket($pdo, $id);
?>

<div class="d-flex align-items-start mb-4">
    <a href="<?= $backUrl ?>" class="btn btn-sm btn-outline-secondary me-3 mt-1">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h4 class="fw-bold mb-0"><?= e($ticket['title']) ?></h4>
            <?= statusBadge($ticket['status']) ?>
            <?= priorityBadge($ticket['priority']) ?>
        </div>
        <small class="text-muted">
            #<?= $ticket['id'] ?> &middot;
            <?= e($ticket['system_name']) ?> &middot;
            Submitted by <?= e($ticket['submitter_name']) ?> on <?= date('F d, Y \a\t g:i A', strtotime($ticket['created_at'])) ?>
        </small>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success py-2"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
<?php endif; ?>

<div class="row g-4">

    <div class="col-lg-8">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="text-uppercase small text-muted fw-bold mb-3">Description</h6>
                <div class="ticket-description"><?= nl2br(e($ticket['description'])) ?></div>
            </div>
        </div>

        <div id="comments">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-chat-dots me-2"></i>Activity &amp; Comments
                <span class="badge bg-light text-dark border ms-1"><?= count($comments) ?></span>
            </h6>

            <?php if (empty($comments)): ?>
            <p class="text-muted small">No comments yet.</p>
            <?php else: ?>
            <?php foreach ($comments as $comment):
                $isDev = $comment['author_role'] === 'developer';
            ?>
            <div class="comment-block <?= $isDev ? 'dev-comment' : '' ?> mb-3">
                <div class="d-flex align-items-center mb-1 gap-2">
                    <span class="fw-medium"><?= e($comment['author_name']) ?></span>
                    <?php if ($isDev): ?>
                    <span class="badge bg-warning text-dark" style="font-size:0.65rem;">Developer</span>
                    <?php endif; ?>
                    <span class="text-muted small"><?= date('M d, Y g:i A', strtotime($comment['created_at'])) ?></span>
                </div>
                <div class="comment-body">
                    <?= nl2br(e($comment['comment'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="fw-medium mb-3"><i class="bi bi-chat me-1"></i>Add a Comment</h6>
                    <form method="POST" action="<?= BASE_URL ?>/view_ticket.php?id=<?= $id ?>#comments">
                        <input type="hidden" name="action" value="add_comment">
                        <div class="mb-3">
                            <textarea name="comment" class="form-control" rows="4"
                                      placeholder="Add a comment or update…" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send me-1"></i>Post Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="text-uppercase small text-muted fw-bold mb-3">Details</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7"><?= statusBadge($ticket['status']) ?></dd>
                    <dt class="col-5 text-muted">Priority</dt>
                    <dd class="col-7"><?= priorityBadge($ticket['priority']) ?></dd>
                    <dt class="col-5 text-muted">Type</dt>
                    <dd class="col-7"><?= e($ticket['ticket_type']) ?></dd>
                    <dt class="col-5 text-muted">System</dt>
                    <dd class="col-7"><span class="badge bg-light text-dark border"><?= e($ticket['system_name']) ?></span></dd>
                    <dt class="col-5 text-muted">Submitted</dt>
                    <dd class="col-7"><?= date('M d, Y', strtotime($ticket['created_at'])) ?></dd>
                    <dt class="col-5 text-muted">Last Updated</dt>
                    <dd class="col-7 mb-0"><?= date('M d, Y', strtotime($ticket['updated_at'])) ?></dd>
                </dl>
            </div>
        </div>

        <?php if (isDeveloper()): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-uppercase small text-muted fw-bold mb-3">Update Ticket</h6>
                <form method="POST">
                    <input type="hidden" name="action" value="update_ticket">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <?php foreach (Ticket::STATUSES() as $s): ?>
                            <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Priority</label>
                        <select name="priority" class="form-select form-select-sm">
                            <?php foreach (Ticket::PRIORITIES() as $p): ?>
                            <option value="<?= $p ?>" <?= $ticket['priority'] === $p ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-save me-1"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
