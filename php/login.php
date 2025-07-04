<?php
session_start();
date_default_timezone_set('America/Los_Angeles');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$username = "thredqwx_admin";
$password = "Mostin2003$";
$database = "thredqwx_threadline";
$conn = new mysqli($host, $username, $password, $database);

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ThreadLine | Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<nav class="navbar">
  <a class="logo" href="../html/index.html">ThreadLine</a>
  <ul class="nav-links">
    <li><a href="../html/index.html">Home</a></li>
    <li><a href="codeForBothJackets.php">Shop</a></li>
    <li><a href="../html/signup.html">Signup</a></li>
  </ul>
</nav>
<div class="signup-container">
HTML;

if ($conn->connect_error) {
    die("<h2>❌ Connection failed: " . $conn->connect_error . "</h2></div></body></html>");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("<h2>❌ Query error: " . $conn->error . "</h2></div></body></html>");
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            $now = date('Y-m-d H:i:s');
            $ip = $_SERVER['HTTP_CLIENT_IP'] ??
                  $_SERVER['HTTP_X_FORWARDED_FOR'] ??
                  $_SERVER['REMOTE_ADDR'];

            $update = $conn->prepare("UPDATE users SET is_logged_in = 1, last_login = ?, last_login_ip = ? WHERE id = ?");
            $update->bind_param("ssi", $now, $ip, $id);
            $update->execute();

            header("Location: codeForBothJackets.php");
            exit();
        } else {
            echo "<h2>❌ Incorrect password.</h2>";
        }
    } else {
        echo "<h2>❌ No account found with that email.</h2>";
    }

    $stmt->close();
} else {
    echo <<<FORM
    <h2>Login</h2>
    <form class="signup-form" action="login.php" method="POST">
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <p style="margin-top: 1rem;">
      Don't have an account? <a href="../html/signup.html" style="color: #075eb6; font-weight: bold;">Sign Up</a>
    </p>
FORM;
}

$conn->close();

echo <<<HTML
</div>
<footer>
  <p>© 2025 ThreadLine. All rights reserved.</p>
</footer>
</body>
</html>
HTML;
?>
