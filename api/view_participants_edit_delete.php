<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}
include 'dbconnect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Participants – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #f0f4f8; }</style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4" style="background-color:#0f3460;">
        <div class="container">
            <a class="navbar-brand fw-bold" href=".">🚴 Cit-E Cycling</a>
            <div class="d-flex gap-2">
                <a href="/admin-menu" class="btn btn-outline-light btn-sm">← Admin Menu</a>
                <a href="/logout" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="fw-bold mb-1">All Participants</h2>
        <p class="text-muted mb-4">Click Edit to update scores, or Delete to remove a participant.</p>

<?php
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("
        SELECT p.id, p.firstname, p.surname, p.email, p.power_output, p.distance, c.name AS club_name
        FROM participant p
        LEFT JOIN club c ON p.club_id = c.id
        ORDER BY p.surname ASC
    ");
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Club</th>
                                <th>Power (W)</th>
                                <th>Distance (km)</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $p): ?>
                            <tr>
                                <td class="text-muted small"><?= $p['id'] ?></td>
                                <td><strong><?= htmlspecialchars($p['firstname'] . ' ' . $p['surname']) ?></strong></td>
                                <td class="text-muted small"><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= htmlspecialchars($p['club_name'] ?? 'No Club') ?></td>
                                <td><?= htmlspecialchars($p['power_output']) ?></td>
                                <td><?= htmlspecialchars($p['distance']) ?></td>
                                <td class="text-center">
                                    <a href="/edit-participant?id=<?= $p['id'] ?>" title="Edit" class="btn btn-sm btn-warning me-1">✏️</a>
                                    <a href="/delete?id=<?= $p['id'] ?>&name=<?= urlencode($p['firstname'] . ' ' . $p['surname']) ?>" title="Delete" class="btn btn-sm btn-danger">🗑️ </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <p class="text-muted small mt-2"><?= count($participants) ?> participants total.</p>

<?php
} catch (PDOException $e) {
    echo '<div class="alert alert-danger"><strong>Database error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>