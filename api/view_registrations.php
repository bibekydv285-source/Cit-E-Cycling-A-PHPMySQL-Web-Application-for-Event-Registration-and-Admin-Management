<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}
include 'dbconnect.php';

// Handle delete
$deleteMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $chk = $conn->prepare("SELECT firstname, surname FROM interest WHERE id = :id");
        $chk->bindParam(':id', $deleteId, PDO::PARAM_INT);
        $chk->execute();
        $person = $chk->fetch(PDO::FETCH_ASSOC);

        if ($person) {
            $del = $conn->prepare("DELETE FROM interest WHERE id = :id");
            $del->bindParam(':id', $deleteId, PDO::PARAM_INT);
            $del->execute();
            $deleteMsg = '
            <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-white rounded shadow-sm">
                <div style="width:42px;height:42px;min-width:42px;background-color:#6abf7b;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="#fff" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="fw-bold">Deleted Successfully!</div>
                    <div class="text-muted small"><strong>' . htmlspecialchars($person['firstname'] . ' ' . $person['surname']) . '</strong> has been removed from the registrations list.</div>
                </div>
            </div>';
        }
    } catch (PDOException $e) {
        $deleteMsg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrations of Interest – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f4f8; }
        .badge-accepted  { background-color: #198754; }
        .badge-declined  { background-color: #dc3545; }

        /* Club pill colours */
        .club-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .club-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

        /* Colours per club ID */
        .club-1 { background: #fff0f3; color: #c0392b; }
        .club-1 .club-dot { background: #e94560; }
        .club-2 { background: #e8f4fd; color: #1565c0; }
        .club-2 .club-dot { background: #1e88e5; }
        .club-3 { background: #fffde7; color: #856400; }
        .club-3 .club-dot { background: #f9a825; }
        .club-4 { background: #e8f5e9; color: #2e7d32; }
        .club-4 .club-dot { background: #43a047; }
        .club-none { background: #f0f4f8; color: #6c757d; font-style: italic; font-weight: 400; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4" style="background-color:#0f3460;">
        <div class="container">
            <a class="navbar-brand fw-bold" href=".">🚴 Cit-E Cycling</a>
            <div class="d-flex gap-2">
                <a href="admin_menu.php" class="btn btn-outline-light btn-sm">← Admin Menu</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="fw-bold mb-1">Registrations of Interest</h2>
        <p class="text-muted mb-4">All users who submitted an interest registration form.</p>

        <?= $deleteMsg ?>

<?php
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ── JOIN club table so we get the club name and location ──────────────
    $stmt = $conn->query("
        SELECT i.id, i.firstname, i.surname, i.email, i.terms,
               i.club_id,
               c.name     AS club_name,
               c.location AS club_location
        FROM interest i
        LEFT JOIN club c ON i.club_id = c.id
        ORDER BY i.id DESC
    ");
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($registrations)):
?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <div class="display-4 mb-3">📭</div>
                <h5 class="fw-bold">No registrations yet</h5>
                <p class="text-muted">Nobody has submitted an interest registration form so far.</p>
            </div>
        </div>
<?php else:
        $total    = count($registrations);
        $accepted = count(array_filter($registrations, fn($r) => $r['terms'] == 1));

        // Count how many chose each club (for the breakdown stat)
        $withClub = count(array_filter($registrations, fn($r) => !empty($r['club_id'])));
?>

        <!-- ── Summary stats ───────────────────────────────────────────── -->
        <div class="row g-3 mb-4">
            <div class="col-sm-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fw-bold fs-3"><?= $total ?></div>
                    <div class="text-muted small">Total Registrations</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fw-bold fs-3 text-success"><?= $accepted ?></div>
                    <div class="text-muted small">Terms Accepted</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fw-bold fs-3 text-danger"><?= $total - $accepted ?></div>
                    <div class="text-muted small">Terms Not Accepted</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fw-bold fs-3" style="color:#0f3460;"><?= $withClub ?></div>
                    <div class="text-muted small">With Club Preference</div>
                </div>
            </div>
        </div>

        <!-- ── Registrations table ─────────────────────────────────────── -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Surname</th>
                                <th>Email</th>
                                <th>Club</th>
                                <th class="text-center">Terms</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $r): ?>
                            <tr>
                                <td class="text-muted small"><?= (int)$r['id'] ?></td>
                                <td><?= htmlspecialchars($r['firstname']) ?></td>
                                <td><strong><?= htmlspecialchars($r['surname']) ?></strong></td>
                                <td class="text-muted small">
                                    <a href="mailto:<?= htmlspecialchars($r['email']) ?>"
                                       class="text-decoration-none text-muted">
                                        <?= htmlspecialchars($r['email']) ?>
                                    </a>
                                </td>

                                <!-- ── Club column ── -->
                                <td>
                                    <?php if (!empty($r['club_id']) && $r['club_name']): ?>
                                        <span class="club-pill club-<?= (int)$r['club_id'] ?>">
                                            <span class="club-dot"></span>
                                            <?= htmlspecialchars($r['club_name']) ?>
                                        </span>
                                        <div class="text-muted" style="font-size:0.72rem; margin-top:2px; padding-left:2px;">
                                            📍 <?= htmlspecialchars($r['club_location']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="club-pill club-none">No club</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php if ($r['terms'] == 1): ?>
                                        <span class="badge badge-accepted">✓ Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-declined">✗ No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="<?= (int)$r['id'] ?>"
                                        data-name="<?= htmlspecialchars($r['firstname'] . ' ' . $r['surname']) ?>"
                                        title="Delete">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <p class="text-muted small mt-2"><?= $total ?> registration(s) total.</p>

<?php endif; ?>

<?php
} catch (PDOException $e) {
    echo '<div class="alert alert-danger"><strong>Database error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">⚠️ Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete the registration for
                       <strong id="modalPersonName"></strong>? This cannot be undone.</p>
                </div>
                <div class="modal-footer border-0">
                    <form method="POST" action="view_registrations.php">
                        <input type="hidden" name="delete_id" id="modalDeleteId">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">🗑️ Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('modalDeleteId').value        = btn.getAttribute('data-id');
            document.getElementById('modalPersonName').textContent = btn.getAttribute('data-name');
        });
    </script>
</body>
</html>