<?php
session_start();
include 'dbconnect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #0f3460; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; }</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4 col-sm-10">
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usernameInput = trim($_POST['username'] ?? '');
    $passwordInput = trim($_POST['password'] ?? '');

    if (empty($usernameInput) || empty($passwordInput)) {
        echo '<div class="alert alert-warning text-center">Please enter both username and password.</div>';
        echo '<div class="text-center"><a href="admin_login.html" class="btn btn-light">← Try Again</a></div>';
    } else {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM user WHERE username = :username AND password = :password LIMIT 1");
            $stmt->bindParam(':username', $usernameInput);
            $stmt->bindParam(':password', $passwordInput);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username']  = $user['username'];
                header('Location: admin_menu.php');
                exit();
            } else {
                echo '<div class="card border-0 shadow-lg p-4 text-center">';
                echo '<div class="display-4 mb-2">❌</div>';
                echo '<h5 class="fw-bold">Wrong username or password</h5>';
                echo '<p class="text-muted">The username or password you entered is incorrect.</p>';
                echo '<a href="admin_login.html" class="btn btn-danger">← Try Again</a>';
                echo '</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger"><strong>Database error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
} else {
    echo '<div class="alert alert-warning text-center">Direct access not allowed. <a href="admin_login.html" class="alert-link">Go to Login</a></div>';
}
?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
