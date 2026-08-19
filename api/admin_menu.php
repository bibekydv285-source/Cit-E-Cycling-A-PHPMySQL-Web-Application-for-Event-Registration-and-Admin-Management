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
    <title>Admin Menu – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ── Page background gradient ── */
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f0fe 0%, #f3e8ff 40%, #e8f5e9 70%, #fff3e0 100%);
        }

        /* ── Card base ── */
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s, border-top 0.25s ease;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
        }

        /* Per-card hover border colors */
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-radius: 12px;
        }

        .accent-blue:hover   { border-top: 4px solid #1e88e5 !important; }
        .accent-green:hover  { border-top: 4px solid #43a047 !important; }
        .accent-purple:hover { border-top: 4px solid #8e24aa !important; }
        .accent-red:hover    { border-top: 4px solid #e53935 !important; }

        /* ── Emoji icon circle ── */
        .icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
        }

        /* Icon background tints */
        .icon-blue   { background-color: #e3f2fd; }
        .icon-green  { background-color: #e8f5e9; }
        .icon-purple { background-color: #f3e5f5; }
        .icon-red    { background-color: #ffebee; }

        /* ── Navbar glass effect ── */
        .navbar-glass {
            background: linear-gradient(90deg, #0f3460 0%, #1a237e 100%);
            box-shadow: 0 2px 16px rgba(15,52,96,0.18);
        }

        /* ── Page title area ── */
        .page-title { color: #1a1a2e; }
    </style>
</head>
<body>

    <!-- Top navbar -->
    <nav class="navbar navbar-dark mb-4 navbar-glass">
        <div class="container">
            <a class="navbar-brand fw-bold" href=".">🚴 Cit-E Cycling</a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small">Welcome, <strong class="text-white"><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></span>
                <a href="/logout" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="fw-bold mb-1 page-title">Admin Dashboard</h1>
        <p class="text-muted mb-4">Manage participants, scores, and search the database.</p>

        <div class="row g-4">

            <!-- Search — Blue -->
            <div class="col-md-4 col-sm-6">
                <a href="/search-form" class="text-decoration-none">
                    <div class="card card-hover accent-blue border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle icon-blue">🔍</div>
                            <h2 class="fw-bold" style="color:#1e88e5;">Search</h2>
                            <p class="text-muted small">Search for participants by name or look up club performance.</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- View / Edit / Delete — Green -->
            <div class="col-md-4 col-sm-6">
                <a href="/view-participants-edit-delete" class="text-decoration-none">
                    <div class="card card-hover accent-green border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle icon-green">📋</div>
                            <h2 class="fw-bold" style="color:#43a047;">View / Edit / Delete</h2>
                            <p class="text-muted small">View all participants and update scores or remove entries.</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Registrations — Purple -->
            <div class="col-md-4 col-sm-6">
                <a href="/view-registrations" class="text-decoration-none">
                    <div class="card card-hover accent-purple border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle icon-purple">📬</div>
                            <h2 class="fw-bold" style="color:#8e24aa;">Registrations of Interest</h2>
                            <p class="text-muted small">View and manage all interest registration submissions.</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Logout — Red -->
            <div class="col-md-4 col-sm-6">
                <a href="/logout" class="text-decoration-none">
                    <div class="card card-hover accent-red border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle icon-red">🚪</div>
                            <h2 class="fw-bold" style="color:#e53935;">Logout</h2>
                            <p class="text-muted small">End your admin session securely.</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>