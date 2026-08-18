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
    <title>Edit Participant – Cit-E Cycling</title>
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
        <h1 class="visually-hidden">Edit Participant</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
<?php
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validate
        $id             = (int)($_POST['id'] ?? 0);
        $power_output   = trim($_POST['power_output'] ?? '');
        $distance       = trim($_POST['distance_travelled'] ?? '');
        $errors = [];

        if ($id <= 0) $errors[] = 'Invalid participant ID.';
        if (!is_numeric($power_output) || $power_output < 0) $errors[] = 'Power output must be a non-negative number.';
        if (!is_numeric($distance) || $distance < 0) $errors[] = 'Distance must be a non-negative number.';

        if (!empty($errors)) {
            echo '<div class="alert alert-danger"><ul class="mb-0">';
            foreach ($errors as $err) echo '<li>' . htmlspecialchars($err) . '</li>';
            echo '</ul></div>';
            // Re-fetch participant to show form again
            $stmt = $conn->prepare("SELECT * FROM participant WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);
            include 'edit_participant_form.php';
        } else {
            $stmt = $conn->prepare("UPDATE participant SET power_output = :power_output, distance = :distance WHERE id = :id");
            $stmt->bindParam(':power_output', $power_output);
            $stmt->bindParam(':distance',     $distance);
            $stmt->bindParam(':id',           $id, PDO::PARAM_INT);
            $stmt->execute();
            echo '<div class="card border-0 shadow-sm"><div class="card-body p-4 text-center">';
            echo '<div class="display-4 mb-3">✅</div>';
            echo '<h2 class="fw-bold">Scores Updated!</h2>';
            echo '<p class="text-muted">The participant\'s scores have been saved successfully.</p>';
            echo '<a href="/view-participants-edit-delete" class="btn btn-dark me-2">← View All Participants</a>';
            echo '<a href="/edit-participant?id=' . $id . '" class="btn btn-warning">Edit Again</a>';
            echo '</div></div>';
        }
    } else {
        // GET – load form
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo '<div class="alert alert-danger">No participant specified.</div>';
            echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← Back</a>';
        } else {
            $stmt = $conn->prepare("SELECT * FROM participant WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participant) {
                echo '<div class="alert alert-warning">Participant not found.</div>';
                echo '<a href="/view-participants-edit-delete" class="btn btn-dark">← Back</a>';
            } else {
                include 'edit_participant_form.php';
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