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
    <title>Delete Participant – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #f0f4f8; }</style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4" style="background-color:#0f3460;">
        <div class="container">
            <a class="navbar-brand fw-bold" href=".">🚴 Cit-E Cycling</a>
            <div class="d-flex gap-2">
                <a href="/view-participants-edit-delete" class="btn btn-outline-light btn-sm">← Back to Participants</a>
                <a href="/logout" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="visually-hidden">Delete Participant</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
<?php
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Confirmed deletion
        $id = (int)($_POST['id'] ?? 0);
        $confirm = $_POST['confirm'] ?? '';

        if ($confirm !== 'yes') {
            echo '<div class="alert alert-warning">Deletion was not confirmed. No changes were made.</div>';
            echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← Back to Participants</a>';
        } elseif ($id <= 0) {
            echo '<div class="alert alert-danger">Invalid participant ID.</div>';
            echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← Back</a>';
        } else {
            // Verify participant exists first
            $check = $conn->prepare("SELECT firstname, surname FROM participant WHERE id = :id");
            $check->bindParam(':id', $id, PDO::PARAM_INT);
            $check->execute();
            $exists = $check->fetch(PDO::FETCH_ASSOC);

            if (!$exists) {
                echo '<div class="alert alert-warning">Participant not found – they may have already been deleted.</div>';
                echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← Back</a>';
            } else {
                $stmt = $conn->prepare("DELETE FROM participant WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                echo '<div class="card border-0 shadow-sm"><div class="card-body p-4 text-center">';
                echo '<div class="display-4 mb-3">🗑️</div>';
                echo '<h2 class="fw-bold">Participant Deleted Successfully!</h2>';
                echo '<p class="text-muted"><strong>' . htmlspecialchars($exists['firstname'] . ' ' . $exists['surname']) . '</strong> has been removed from the system.</p>';
                echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← View All Participants</a>';
                echo '</div></div>';
            }
        }

    } else {
        // GET – show confirmation screen
        $id   = (int)($_GET['id']   ?? 0);
        $name = htmlspecialchars($_GET['name'] ?? '');

        if ($id <= 0) {
            echo '<div class="alert alert-danger">No participant specified.</div>';
            echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← Back</a>';
        } else {
            // Fetch participant to confirm they exist and show details
            $stmt = $conn->prepare("SELECT p.*, c.name AS club_name FROM participant p LEFT JOIN club c ON p.club_id = c.id WHERE p.id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$p) {
                echo '<div class="alert alert-warning">Participant not found.</div>';
                echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← Back</a>';
            } else {
?>
                <div class="card border-0 shadow-sm border-danger" style="border-top: 4px solid #dc3545 !important;">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <div class="display-4">⚠️</div>
                            <h2 class="fw-bold mt-2">Confirm Deletion</h2>
                            <p class="text-muted">Are you sure you want to permanently delete this participant? This action cannot be undone.</p>
                        </div>
                        <div class="bg-light rounded p-3 mb-4">
                            <table class="table table-sm mb-0">
                                <tr><th>Name</th><td><?= htmlspecialchars($p['firstname'] . ' ' . $p['surname']) ?></td></tr>
                                <tr><th>Email</th><td><?= htmlspecialchars($p['email']) ?></td></tr>
                                <tr><th>Club</th><td><?= htmlspecialchars($p['club_name'] ?? 'No Club') ?></td></tr>
                                <tr><th>Power Output</th><td><?= htmlspecialchars($p['power_output']) ?> W</td></tr>
                                <tr class="mb-0"><th>Distance</th><td><?= htmlspecialchars($p['distance']) ?> km</td></tr>
                            </table>
                        </div>
                        <form action="/delete" method="POST">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="confirm" value="yes">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="submit" class="btn btn-danger px-4">🗑️ Yes, Delete Permanently</button>
                                <a href="/view-participants-edit-delete" class="btn btn-outline-secondary px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
<?php
            }
        }
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger"><strong>Database error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>