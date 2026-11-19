<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $course_year_sec = trim($_POST['course_year_sec']);

    $updateStmt = $pdo->prepare("UPDATE users SET fullname = ?, username = ?, email = ?, course_year_sec = ? WHERE id = ?");
    if ($updateStmt->execute([$fullname, $username, $email, $course_year_sec, $id])) {
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-container: rgba(18, 18, 18, 0.85);
            --text-color: #f8f9fa;
            --input-bg: rgba(30, 30, 30, 0.85);
            --input-text: #f8f9fa;
            --input-focus-bg: rgba(45, 45, 45, 0.95);
            --border-color: rgba(255, 255, 255, 0.15);
        }
        [data-theme="light"] {
            --bg-container: rgba(255, 255, 255, 0.9);
            --text-color: #212529;
            --input-bg: rgba(240, 240, 240, 0.9);
            --input-text: #212529;
            --input-focus-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(0, 0, 0, 0.15);
        }
        body {
            background: url('url_bg2.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        .glass-card {
            background: var(--bg-container); 
            backdrop-filter: blur(15px);
            border: 1px solid var(--border-color); 
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2); 
            color: var(--text-color);
        }
        .neu-input {
            background: var(--input-bg) !important;
            color: var(--input-text) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 10px;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.08);
            padding: 10px;
            transition: all 0.3s ease;
        }
        .neu-input:focus {
            background: var(--input-focus-bg) !important;
            color: var(--input-text) !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.3), inset 2px 2px 5px rgba(0,0,0,0.05) !important;
            outline: none;
        }
        .theme-toggle-btn {
            position: absolute; top: 20px; right: 20px;
            background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3);
            color: #fff; border-radius: 50%; width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
    </style>
</head>
<body data-theme="dark">
<button class="theme-toggle-btn" id="themeToggle" title="Toggle Light/Dark Mode">☀️</button>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-card p-4">
                <h3 class="text-center mb-4 fw-bold">Edit User Profile</h3>
                <form action="edit.php?id=<?= $user['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="fullname" class="form-control neu-input" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control neu-input" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control neu-input" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Course Year Section Specialization</label>
                        <input type="text" name="course_year_sec" class="form-control neu-input" value="<?= htmlspecialchars($user['course_year_sec']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-semibold shadow-sm py-2">Update Record</button>
                    <a href="dashboard.php" class="btn btn-secondary w-100 mt-2 py-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    // Remember theme state if toggled
    themeToggle.addEventListener('click', () => {
        if(body.getAttribute('data-theme') === 'dark') {
            body.setAttribute('data-theme', 'light');
            themeToggle.innerText = '🌙';
        } else {
            body.setAttribute('data-theme', 'dark');
            themeToggle.innerText = '☀️';
        }
    });
</script>
</body>
</html>