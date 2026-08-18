<?php
include 'dbconnect.php';

// ── Server-side validation ────────────────────────────────────────────────────
$errors = [];

$firstname   = trim($_POST['firstname'] ?? '');
$surname     = trim($_POST['surname']   ?? '');
$email       = trim($_POST['email']     ?? '');
$terms       = $_POST['terms']          ?? '';
$club_id_raw = $_POST['club_id']        ?? '';
$club_id     = ($club_id_raw !== '' && ctype_digit($club_id_raw)) ? (int)$club_id_raw : null;

if (empty($firstname)) $errors[] = 'First name is required.';
if (empty($surname))   $errors[] = 'Surname is required.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'A valid email address is required.';
if ($terms !== 'yes')  $errors[] = 'You must accept the terms and conditions.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Interest – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #9a532f 0%, #9a532f 0%); min-height: 100vh; }

        /* ── Already-registered banner ── */
        .already-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .already-card .top-bar {
            background: linear-gradient(90deg, #e67e22, #f39c12);
            height: 6px;
        }
        .email-pill {
            display: inline-block;
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            word-break: break-all;
        }

        /* ── Success card ── */
        .success-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .success-card .top-bar { background: linear-gradient(90deg, #198754, #28a745); height: 6px; }

        /* ── Error card ── */
        .error-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark" style="background-color:#0f3460;">
        <div class="container">
            <a class="navbar-brand fw-bold" href=".">🚴 Cit-E Cycling</a>
            <a href="register_form.html" class="btn btn-outline-light btn-sm">← Back to Form</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

<?php if (!empty($errors)): ?>
                <!-- ── Validation errors ────────────────────────────────── -->
                <div class="card error-card p-4">
                    <div class="text-center mb-3">
                        <div class="display-4">⚠️</div>
                        <h4 class="fw-bold mt-2">Please fix the following</h4>
                    </div>
                    <ul class="mb-4">
                        <?php foreach ($errors as $e): ?>
                            <li class="mb-1"><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="register_form.html" class="btn btn-danger w-100">← Go Back and Try Again</a>
                </div>

<?php else: ?>
<?php
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ── Duplicate email check ─────────────────────────────────────────
        $dupCheck = $conn->prepare("SELECT id FROM interest WHERE email = :email LIMIT 1");
        $dupCheck->bindParam(':email', $email);
        $dupCheck->execute();

        if ($dupCheck->fetch()):
            // ── ALREADY REGISTERED ──────────────────────────────────────
?>
                <div class="card already-card">
                    <div class="top-bar"></div>
                    <div class="card-body p-4 text-center">
                        <div style="font-size:3.5rem; line-height:1; margin-bottom:12px;">📧</div>
                        <h4 class="fw-bold mb-1" style="color:#d35400;">Already Registered!</h4>
                        <p class="text-muted mb-3" style="font-size:0.95rem;">
                            This email address is already signed up for Cit-E Cycling updates.
                        </p>

                        <div class="mb-4">
                            <span class="email-pill"><?= htmlspecialchars($email) ?></span>
                        </div>

                        <div class="alert alert-warning text-start" style="border-radius:10px; font-size:0.88rem;">
                            <strong>Already on our list?</strong><br>
                            You'll automatically receive notifications about upcoming events.
                            If you believe this is a mistake or need to update your details,
                            please contact us directly.
                        </div>

                        <div class="d-grid gap-2">
                            <a href="register_form.html" class="btn btn-warning fw-semibold">
                                ← Try a Different Email
                            </a>
                            <a href="." class="btn btn-outline-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>

<?php
        else:
            // ── INSERT new registration ───────────────────────────────────
            $termsInt = 1;

            $stmt = $conn->prepare(
                "INSERT INTO interest (firstname, surname, email, terms, club_id)
                 VALUES (:firstname, :surname, :email, :terms, :club_id)"
            );
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':surname',   $surname);
            $stmt->bindParam(':email',     $email);
            $stmt->bindParam(':terms',     $termsInt, PDO::PARAM_INT);
            if ($club_id === null) {
                $stmt->bindValue(':club_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':club_id', $club_id, PDO::PARAM_INT);
            }
            $stmt->execute();

            // Fetch club name for confirmation
            $clubName = null;
            if ($club_id !== null) {
                $cStmt = $conn->prepare("SELECT name FROM club WHERE id = :id");
                $cStmt->bindParam(':id', $club_id, PDO::PARAM_INT);
                $cStmt->execute();
                $clubRow = $cStmt->fetch(PDO::FETCH_ASSOC);
                if ($clubRow) $clubName = $clubRow['name'];
            }
?>
                <!-- ── SUCCESS ─────────────────────────────────────────── -->
                <div class="card success-card">
                    <div class="top-bar"></div>
                    <div class="card-body p-4 text-center">
                        <div style="font-size:3.5rem; line-height:1; margin-bottom:12px;">✅</div>
                        <h3 class="fw-bold mb-1">Registration Successful!</h3>
                        <p class="text-muted mb-4" style="font-size:0.95rem;">
                            Thanks, <strong><?= htmlspecialchars($firstname . ' ' . $surname) ?></strong>!
                            We've registered your interest and will be in touch at:
                        </p>

                        <div class="mb-3">
                            <span class="d-inline-block bg-light border rounded-pill px-3 py-2 fw-semibold" style="font-size:0.9rem; color:#333;">
                                📧 <?= htmlspecialchars($email) ?>
                            </span>
                        </div>

                        <?php if ($clubName): ?>
                        <div class="mb-4">
                            <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">
                                🚴 <?= htmlspecialchars($clubName) ?>
                            </span>
                            <div class="text-muted small mt-1">Club preference recorded</div>
                        </div>
                        <?php else: ?>
                        <div class="mb-4">
                            <span class="text-muted small">No club preference selected</span>
                        </div>
                        <?php endif; ?>

                        <a href="." class="btn btn-danger w-100 fw-semibold">Back to Home</a>
                    </div>
                </div>

<?php
        endif;

    } catch (PDOException $e) {
?>
                <div class="card error-card p-4 text-center">
                    <div class="display-4 mb-2">🔧</div>
                    <h5 class="fw-bold">Database Error</h5>
                    <p class="text-muted"><?= htmlspecialchars($e->getMessage()) ?></p>
                    <a href="register_form.html" class="btn btn-danger">← Go Back</a>
                </div>
<?php
    }
?>
<?php endif; ?>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>