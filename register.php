<?php
require_once 'db_connect.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $course_year_sec = trim($_POST['course_year_sec']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, course_year_sec, password) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$fullname, $username, $email, $course_year_sec, $password])) {
            header("Location: login.php?registered=success");
            exit();
        }
    } catch (PDOException $e) {
        $message = "Error: Username or Email already exists!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-glass: rgba(18, 18, 18, 0.75);
            --text-color: #f8f9fa;
            --input-bg: rgba(30, 30, 30, 0.85);
            --input-text: #f8f9fa;
            --input-focus-bg: rgba(45, 45, 45, 0.95);
            --border-color: rgba(255, 255, 255, 0.15);
        }
        [data-theme="light"] {
            --bg-glass: rgba(255, 255, 255, 0.85);
            --text-color: #212529;
            --input-bg: rgba(240, 240, 240, 0.9);
            --input-text: #212529;
            --input-focus-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(0, 0, 0, 0.15);
        }
        body {
            background: url('url_bg1.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        .glass-card {
            background: var(--bg-glass);
            backdrop-filter: blur(15px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            color: var(--text-color);
        }
        .neu-input {
            background: var(--input-bg) !important;
            color: var(--input-text) !important;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.08);
            padding: 10px;
            transition: all 0.3s ease;
        }
        .neu-input-group {
            background: var(--input-bg) !important;
            color: var(--input-text) !important;
            border: 1px solid var(--border-color);
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.08);
            padding: 10px;
        }
        .neu-input:focus, .neu-input-group:focus {
            background: var(--input-focus-bg) !important;
            color: var(--input-text) !important;
            border-color: #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.3), inset 2px 2px 5px rgba(0,0,0,0.05);
            outline: none;
        }
        .neu-btn {
            background: linear-gradient(145deg, #0d6efd, #0b5ed7);
            border: none; border-radius: 10px; padding: 12px; font-weight: 600;
        }
        .toggle-password-btn {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--input-text);
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            padding: 0 15px;
            cursor: pointer;
        }
        .top-left-clock {
            position: absolute; top: 20px; left: 20px;
            background: var(--bg-glass); backdrop-filter: blur(10px);
            padding: 8px 15px; border-radius: 15px; border: 1px solid var(--border-color);
            color: var(--text-color); font-weight: 600; font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 100;
        }
        .top-right-toggle { position: absolute; top: 20px; right: 20px; z-index: 100; }
        .theme-toggle-btn {
            background: var(--bg-glass); border: 1px solid var(--border-color);
            color: var(--text-color); border-radius: 50%; width: 45px; height: 45px;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body data-theme="dark">

<div class="top-left-clock" id="realtimeClock"></div>
<div class="top-right-toggle">
    <button class="theme-toggle-btn" id="themeToggle">☀️</button>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-card p-4">
                <div class="text-center mb-3">
                    <img src="url_logo.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
                </div>
                <h3 class="text-center mb-3 fw-bold">User Registration</h3>
                <?php if($message): ?>
                    <div class="alert alert-danger py-2"><?= $message ?></div>
                <?php endif; ?>
                <form action="register.php" method="POST">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="fullname" class="form-control neu-input" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control neu-input" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control neu-input" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Course Year Section Specialization <small class="text-muted">(ex. BSIT 4BWM)</small></label>
                        <input type="text" name="course_year_sec" class="form-control neu-input" placeholder="BSIT 4BWM" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="regPassword" class="form-control neu-input-group border-end-0" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePassword('regPassword', this)">👁️</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 neu-btn">Register</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="login.php" class="text-warning text-decoration-none fw-semibold">Login here</a></p>
            </div>
        </div>
    </div>
</div>
<script>
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    themeToggle.addEventListener('click', () => {
        if(body.getAttribute('data-theme') === 'dark') {
            body.setAttribute('data-theme', 'light');
            themeToggle.innerText = '🌙';
        } else {
            body.setAttribute('data-theme', 'dark');
            themeToggle.innerText = '☀️';
        }
    });

    function togglePassword(fieldId, btn) {
        const passwordField = document.getElementById(fieldId);
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            btn.innerText = '🙈';
        } else {
            passwordField.type = 'password';
            btn.innerText = '👁️';
        }
    }

    function updateRealtimeClock() {
        const now = new Date();
        const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        document.getElementById('realtimeClock').innerText = now.toLocaleString('en-US', options);
    }
    setInterval(updateRealtimeClock, 1000);
    updateRealtimeClock();
</script>
</body>
</html>