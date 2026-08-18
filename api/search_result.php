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
    <title>Search Results – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #f0f4f8; }</style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4" style="background-color:#0f3460;">
        <div class="container">
            <a class="navbar-brand fw-bold" href=".">🚴 Cit-E Cycling</a>
            <div class="d-flex gap-2">
                <a href="search_form.php" class="btn btn-outline-light btn-sm">← New Search</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
<?php
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ── PARTICIPANT SEARCH ──────────────────────────────────────────────
    if (isset($_POST['participant']) && $_POST['participant'] == '1') {
        $searchTerm = trim($_POST['firstname'] ?? '');

        if (empty($searchTerm)) {
            echo '<div class="alert alert-warning">Please enter a name to search.</div>';
            echo '<a href="search_form.php" class="btn btn-dark">← Back to Search</a>';
        } else {
            $like = '%' . $searchTerm . '%';
            $stmt = $conn->prepare("
                SELECT p.*, c.name AS club_name, c.location AS club_location
                FROM participant p
                LEFT JOIN club c ON p.club_id = c.id
                WHERE p.firstname LIKE :term OR p.surname LIKE :term2
                ORDER BY p.surname ASC
            ");
            $stmt->bindParam(':term',  $like);
            $stmt->bindParam(':term2', $like);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <h2 class="fw-bold mb-1">Participant Search Results</h2>
        <p class="text-muted mb-4">Showing results for: <strong>"<?= htmlspecialchars($searchTerm) ?>"</strong></p>

<?php if (empty($results)): ?>
        <div class="alert alert-info">No participants found matching "<?= htmlspecialchars($searchTerm) ?>".</div>
        <a href="search_form.php" class="btn btn-dark">← Try Another Search</a>
<?php else: ?>
        <div class="row g-3">
            <?php foreach ($results as $p): ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($p['firstname'] . ' ' . $p['surname']) ?></h5>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($p['email']) ?></p>
                        <table class="table table-sm mb-0">
                            <tr><th>Club</th><td><?= htmlspecialchars($p['club_name'] ?? 'No Club') ?></td></tr>
                            <tr><th>Location</th><td><?= htmlspecialchars($p['club_location'] ?? '—') ?></td></tr>
                            <tr><th>Power Output</th><td><?= htmlspecialchars($p['power_output']) ?> W</td></tr>
                            <tr class="mb-0"><th>Distance</th><td><?= htmlspecialchars($p['distance']) ?> km</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-muted small mt-3"><?= count($results) ?> result(s) found.</p>
        <a href="search_form.php" class="btn btn-dark mt-2">← New Search</a>
<?php endif; ?>

<?php
        }

    // ── CLUB SEARCH ─────────────────────────────────────────────────────
    } elseif (isset($_POST['club'])) {
        $searchTerm = trim($_POST['club'] ?? '');

        if (empty($searchTerm)) {
            echo '<div class="alert alert-warning">Please enter a club name to search.</div>';
            echo '<a href="search_form.php" class="btn btn-dark">← Back to Search</a>';
        } else {
            $like = '%' . $searchTerm . '%';

            // Find matching clubs
            $clubStmt = $conn->prepare("SELECT * FROM club WHERE name LIKE :term ORDER BY name ASC");
            $clubStmt->bindParam(':term', $like);
            $clubStmt->execute();
            $clubs = $clubStmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <h2 class="fw-bold mb-1">Club Search Results</h2>
        <p class="text-muted mb-4">Showing results for: <strong>"<?= htmlspecialchars($searchTerm) ?>"</strong></p>

<?php if (empty($clubs)): ?>
        <div class="alert alert-info">No clubs found matching "<?= htmlspecialchars($searchTerm) ?>".</div>
        <a href="search_form.php" class="btn btn-dark">← Try Another Search</a>
<?php else:
            foreach ($clubs as $club):
                // Get all participants + aggregates for this club
                $pStmt = $conn->prepare("
                    SELECT *, 
                           SUM(distance) OVER() AS total_distance,
                           SUM(power_output) OVER() AS total_power,
                           AVG(distance) OVER() AS avg_distance,
                           AVG(power_output) OVER() AS avg_power
                    FROM participant
                    WHERE club_id = :club_id
                    ORDER BY surname ASC
                ");
                $pStmt->bindParam(':club_id', $club['id'], PDO::PARAM_INT);
                $pStmt->execute();
                $members = $pStmt->fetchAll(PDO::FETCH_ASSOC);

                // Compute aggregates manually for clarity
                $aggStmt = $conn->prepare("
                    SELECT COUNT(*) AS member_count,
                           SUM(distance) AS total_distance,
                           SUM(power_output) AS total_power,
                           AVG(distance) AS avg_distance,
                           AVG(power_output) AS avg_power
                    FROM participant
                    WHERE club_id = :club_id
                ");
                $aggStmt->bindParam(':club_id', $club['id'], PDO::PARAM_INT);
                $aggStmt->execute();
                $agg = $aggStmt->fetch(PDO::FETCH_ASSOC);
?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header py-3" style="background-color:#0f3460; color:white;">
                <h5 class="fw-bold mb-0">🏆 <?= htmlspecialchars($club['name']) ?> <span class="fw-normal fs-6 opacity-75">– <?= htmlspecialchars($club['location']) ?></span></h5>
            </div>
            <div class="card-body p-0">
                <!-- Club stats summary -->
                <div class="row g-0 border-bottom text-center">
                    <div class="col-6 col-md-3 p-3 border-end">
                        <div class="fw-bold fs-5"><?= $agg['member_count'] ?></div>
                        <div class="text-muted small">Members</div>
                    </div>
                    <div class="col-6 col-md-3 p-3 border-end">
                        <div class="fw-bold fs-5"><?= number_format($agg['total_distance'] ?? 0, 2) ?></div>
                        <div class="text-muted small">Total Distance (km)</div>
                    </div>
                    <div class="col-6 col-md-3 p-3 border-end">
                        <div class="fw-bold fs-5"><?= number_format($agg['total_power'] ?? 0, 2) ?></div>
                        <div class="text-muted small">Total Power (W)</div>
                    </div>
                    <div class="col-6 col-md-3 p-3">
                        <div class="fw-bold fs-5"><?= number_format($agg['avg_distance'] ?? 0, 2) ?></div>
                        <div class="text-muted small">Avg Distance (km)</div>
                    </div>
                </div>
                <div class="row g-0 border-bottom text-center">
                    <div class="col-12 p-2">
                        <span class="text-muted small">Avg Power Output: <strong><?= number_format($agg['avg_power'] ?? 0, 2) ?> W</strong></span>
                    </div>
                </div>

                <!-- Members table -->
                <?php if (empty($members)): ?>
                <p class="p-3 text-muted mb-0">No participants found for this club.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Power (W)</th>
                                <th>Distance (km)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $m): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m['firstname'] . ' ' . $m['surname']) ?></strong></td>
                                <td class="text-muted small"><?= htmlspecialchars($m['email']) ?></td>
                                <td><?= htmlspecialchars($m['power_output']) ?></td>
                                <td><?= htmlspecialchars($m['distance']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
<?php
            endforeach;
            echo '<a href="search_form.php" class="btn btn-dark">← New Search</a>';
        endif;
        }

    } else {
        echo '<div class="alert alert-warning">Invalid request. <a href="search_form.php" class="alert-link">Go to Search</a></div>';
    }

} catch (PDOException $e) {
    echo '<div class="alert alert-danger"><strong>Database error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
