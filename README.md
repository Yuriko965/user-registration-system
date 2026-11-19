# Web-Based User Registration and Management System

A full-stack web-based CRUD (Create, Read, Update, Delete) user management and authentication system built with **PHP**, **MySQL (PDO)**, **HTML5**, **CSS3**, and **Bootstrap 5**. Designed with a modern Glassmorphism and Neumorphism interface, adaptive layouts for mobile portrait and desktop landscape screens, light/dark theme switching, and real-time clock widgets.

## Developer
* **Web Developer:** Jaymart Azucena 3BNT
* **Course Context:** Web Systems Technologies

---

## Key Features

* **Authentication & Session Management:** Secure user registration with hashed passwords (`password_hash`) and login verification (`password_verify`) backed by PHP sessions.
* **Full CRUD Operations:** Create new user accounts, view registered records, update user details (`edit.php`), and delete records (`dashboard.php`).
* **Course / Year / Section Specialization Support:** Captures and displays academic section information (e.g., `BSIT 4BWM`).
* **Dynamic Sorting & Enumeration:** Sort user records by ID, Full Name, Username, or Registration Date in **Ascending** or **Descending** order with clean sequential row numbering.
* **Adaptive Responsive Layout:**
  * **Desktop / Laptop (Landscape):** Glassmorphism data table with hover highlights.
  * **Mobile (Portrait):** Touch-friendly stacked card layout for comfortable one-handed reading and action buttons.
* **Light & Dark Mode Toggle:** Theme switching across login, registration, dashboard, and edit views with explicit focus contrast.
* **Real-Time Date & Time Clock:** Live ticking date and time display positioned in the top-left corner of authentication screens and on the dashboard navbar.
* **About System Modal:** Accessible from the dashboard navbar to display the system's purpose and developer credits.
* **Password Visibility Toggle:** Show/hide password toggle (`👁️` / `🙈`) on login and registration forms.

---

## Project Structure

```text
├── db_connect.php      # PDO database connection configuration
├── index.php           # Entry redirect to login.php
├── login.php           # User login screen with real-time clock & theme toggle
├── register.php        # User registration form including Course/Year/Sec
├── dashboard.php       # Protected main view with sorting, CRUD actions & About modal
├── edit.php            # Edit user profile form
├── logout.php          # Destroys active session and redirects to login
├── url_logo.png        # Custom system logo asset
├── url_bg1.png         # Background image for authentication pages
└── url_bg2.png         # Background image for dashboard & edit pages
```

---

## Database Setup

### Table Schema (`users`)

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    course_year_sec VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

If updating an existing `users` table without `course_year_sec`, run:

```sql
ALTER TABLE users ADD COLUMN course_year_sec VARCHAR(100) NOT NULL AFTER email;
```

---

## Local & Cloud Deployment

### Local Development (XAMPP)
1. Place the project files inside your `htdocs` directory (e.g., `htdocs/user-system`).
2. Start **Apache** and **MySQL** in XAMPP.
3. Import or create the `users` table in `phpMyAdmin` and update `db_connect.php` with your local database credentials.
4. Open `http://localhost/user-system` in your browser.

### Live Hosting (InfinityFree)
1. Upload all `.php` files and image assets (`url_logo.png`, `url_bg1.png`, `url_bg2.png`) to the `htdocs` folder via File Manager or FTP.
2. Configure your remote MySQL database (`sql312.infinityfree.com`, database `if0_43473820_user_db`) and ensure `db_connect.php` matches your hosting credentials.