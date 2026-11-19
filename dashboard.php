<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Delete Record
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php");
    exit();
}

// Sorting logic
$sort_by = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'ASC';

$allowed_sorts = ['id', 'fullname', 'username', 'created_at'];
$allowed_orders = ['ASC', 'DESC'];

if (!in_array($sort_by, $allowed_sorts)) $sort_by = 'id';
if (!in_array($order, $allowed_orders)) $order = 'ASC';

$stmt = $pdo->query("SELECT * FROM users ORDER BY $sort_by $order");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-container: rgba(255, 255, 255, 0.88);
            --text-color: #212529;
            --table-bg: rgba(255, 255, 255, 0.7);
            --navbar-bg: rgba(33, 37, 41, 0.9);
            --card-bg: rgba(255, 255, 255, 0.92);
            --card-border: rgba(0, 0, 0, 0.08);
        }
        [data-theme="dark"] {
            --bg-container: rgba(18, 18, 18, 0.85);
            --text-color: #f8f9fa;
            --table-bg: rgba(30, 30, 30, 0.8);
            --navbar-bg: rgba(15, 15, 15, 0.95);
            --card-bg: rgba(28, 28, 28, 0.95);
            --card-border: rgba(255, 255, 255, 0.12);
        }
        body {
            background: url('url_bg2.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            transition: background 0.3s ease;
        }
        .glass-navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .glass-container {
            background: var(--bg-container);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .realtime-clock {
            font-weight: 600; font-size: 0.85rem; color: #0dcaf0;
            background: rgba(0, 0, 0, 0.35); padding: 6px 14px; border-radius: 20px;
        }
        .theme-toggle-btn, .about-btn {
            background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);
            color: #fff; border-radius: 50%; width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            text-decoration: none; font-size: 1.1rem; transition: all 0.2s;
        }
        .theme-toggle-btn:hover, .about-btn:hover { background: rgba(255,255,255,0.4); color: #fff; }
        
        .modal-content {
            background: var(--bg-container);
            backdrop-filter: blur(20px);
            color: var(--text-color);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
        }

        /* Desktop vs Mobile Switcher */
        .desktop-table { display: table; width: 100%; }
        .mobile-cards-view { display: none; }

        .table-glass th, .table-glass td {
            background: var(--table-bg) !important;
            color: var(--text-color) !important;
            backdrop-filter: blur(6px);
        }

        /* ----------------------------------------------------------------- */
        /* MOBILE PORTRAIT COMFORT OPTIMIZATION                               */
        /* ----------------------------------------------------------------- */
        @media (max-width: 768px) {
            .desktop-table { display: none !important; }
            .mobile-cards-view { display: flex; flex-direction: column; gap: 16px; }
            
            .glass-container {
                padding: 1.25rem !important;
                border-radius: 16px;
            }
            .user-card-item {
                background: var(--card-bg);
                backdrop-filter: blur(12px);
                border: 1px solid var(--card-border);
                border-radius: 16px;
                padding: 18px;
                box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            }
            .user-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid var(--card-border);
                padding-bottom: 10px;
                margin-bottom: 12px;
            }
            .user-card-body {
                display: flex;
                flex-direction: column;
                gap: 10px;
                font-size: 1rem;
            }
            .card-field-group {
                display: flex;
                flex-direction: column;
            }
            .card-label {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                opacity: 0.65;
                font-weight: 700;
                margin-bottom: 2px;
            }
            .card-value {
                font-size: 1.05rem;
                font-weight: 500;
                word-break: break-word;
            }
            .user-card-actions {
                display: flex;
                gap: 10px;
                margin-top: 14px;
                padding-top: 12px;
                border-top: 1px solid var(--card-border);
            }
            .user-card-actions .btn {
                flex: 1;
                padding: 10px;
                font-weight: 600;
                border-radius: 10px;
            }
            /* Navbar adjustments for mobile portrait */
            .navbar-brand { font-size: 1.1rem; }
            .welcome-text { display: none; } /* Hide long welcome on small screens to save space */
        }
    </style>
</head>
<body data-theme="dark">
<nav class="navbar navbar-expand-lg navbar-dark glass-navbar px-3 px-md-4 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Dashboard</a>
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <div id="realtimeClock" class="realtime-clock d-none d-lg-block"></div>
            <span class="text-light welcome-text">Welcome, <b><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']) ?></b></span>
            
            <button class="about-btn" data-bs-toggle="modal" data-bs-target="#aboutModal" title="About System">ℹ️</button>
            <button class="theme-toggle-btn" id="themeToggle" title="Toggle Light/Dark Mode">☀️</button>
            <a href="logout.php" class="btn btn-outline-light btn-sm px-3 rounded-pill">Logout</a>
        </div>
    </div>
</nav>

<!-- About Modal -->
<div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3 shadow-lg">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="aboutModalLabel">About This System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">
            <b>System Purpose:</b> This Web-Based User Registration and Management System is designed to streamline user data management, secure authentication, and profile tracking through a reliable CRUD architecture. It provides an efficient platform for handling user credentials, maintaining system database records, and offering an interactive dashboard interface.
        </p>
        <hr class="opacity-25">
        <p class="mb-0 text-end fw-semibold text-primary">
            Web Developer: Jaymart Azucena 3BNT
        </p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="container mt-3 mt-md-5 mb-5">
    <div class="glass-container p-3 p-md-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h2 class="fw-bold m-0 fs-3">Registered Users</h2>
            
            <!-- Sorting Controls Form -->
            <form method="GET" action="dashboard.php" class="d-flex align-items-center gap-2">
                <label class="fw-semibold text-nowrap fs-6">Sort:</label>
                <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="id" <?= $sort_by == 'id' ? 'selected' : '' ?>>ID</option>
                    <option value="fullname" <?= $sort_by == 'fullname' ? 'selected' : '' ?>>Full Name</option>
                    <option value="username" <?= $sort_by == 'username' ? 'selected' : '' ?>>Username</option>
                    <option value="created_at" <?= $sort_by == 'created_at' ? 'selected' : '' ?>>Date</option>
                </select>
                <select name="order" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
                    <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending</option>
                </select>
            </form>
        </div>
        <hr>

        <!-- 1. LAPTOP / LANDSCAPE VIEW (Classic Glass Table) -->
        <div class="table-responsive desktop-table">
            <table class="table table-bordered table-hover table-glass align-middle shadow-sm rounded overflow-hidden">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Course / Year / Sec</th>
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    foreach($users as $row): 
                    ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['course_year_sec']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm fw-semibold shadow-sm">Edit</a>
                            <a href="dashboard.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm fw-semibold shadow-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE PORTRAIT VIEW (Comfortable Touch Cards Layout) -->
        <div class="mobile-cards-view">
            <?php 
            $m_count = 1;
            foreach($users as $row): 
            ?>
            <div class="user-card-item">
                <div class="user-card-header">
                    <span class="badge bg-primary px-3 py-2 fs-6">Entry #<?= $m_count++ ?></span>
                    <span class="small text-muted"><?= $row['created_at'] ?></span>
                </div>
                <div class="user-card-body">
                    <div class="card-field-group">
                        <span class="card-label">Full Name</span>
                        <span class="card-value fw-bold text-primary"><?= htmlspecialchars($row['fullname']) ?></span>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="card-field-group">
                                <span class="card-label">Username</span>
                                <span class="card-value"><?= htmlspecialchars($row['username']) ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-field-group">
                                <span class="card-label">Course / Sec</span>
                                <span class="card-value"><?= htmlspecialchars($row['course_year_sec']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-field-group">
                        <span class="card-label">Email Address</span>
                        <span class="card-value text-break"><?= htmlspecialchars($row['email']) ?></span>
                    </div>
                </div>
                <div class="user-card-actions">
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm shadow-sm">Edit Profile</a>
                    <a href="dashboard.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

    function updateRealtimeClock() {
        const now = new Date();
        const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const clockEl = document.getElementById('realtimeClock');
        if(clockEl) clockEl.innerText = now.toLocaleString('en-US', options);
    }
    setInterval(updateRealtimeClock, 1000);
    updateRealtimeClock();
</script>
</body>
</html>