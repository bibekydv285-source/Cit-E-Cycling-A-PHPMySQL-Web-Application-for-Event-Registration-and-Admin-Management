<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #f0f4f8; }</style>
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
        <h2 class="fw-bold mb-4">Search Participants &amp; Clubs</h2>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">🔍 Search Participant</h5>
                        <p class="text-muted small mb-3">Search by first name or surname.</p>
                        <form action="search_result.php" method="POST" novalidate id="participantForm">
                            <div class="mb-3">
                                <label for="firstname" class="form-label fw-semibold">First Name or Surname</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="e.g. Bibek" required>
                                <div class="invalid-feedback">Please enter a name to search.</div>
                            </div>
                            <input type="hidden" name="participant" value="1">
                            <button type="submit" class="btn btn-dark">Search Participants</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">🏆 Search Club</h5>
                        <p class="text-muted small mb-3">Search by club name to see all members and stats.</p>
                        <form action="search_result.php" method="POST" novalidate id="clubForm">
                            <div class="mb-3">
                                <label for="club" class="form-label fw-semibold">Club Name</label>
                                <input type="text" class="form-control" id="club" name="club" placeholder="e.g. Roker Rollers" required>
                                <div class="invalid-feedback">Please enter a club name to search.</div>
                            </div>
                            <button type="submit" class="btn btn-dark">Search Clubs</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        ['participantForm','clubForm'].forEach(function(id) {
            document.getElementById(id).addEventListener('submit', function(e) {
                if (!this.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
                this.classList.add('was-validated');
            });
        });
    </script>
</body>
</html>
