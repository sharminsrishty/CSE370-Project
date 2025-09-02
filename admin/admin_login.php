<?php
require_once __DIR__ . "/../config/db.php";
session_start();

$errors = [];
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM Admin WHERE Admin_mail=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($password === $row['Password']) {  // No hashing as per your previous request
            $_SESSION['admin_id'] = $row['Admin_id'];
            $_SESSION['admin_username'] = $row['Username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Invalid password.";
        }
    } else {
        $errors[] = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-auth-container">
    <h2>Admin Login</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="admin-form-box">
        <label>Email:</label>
        <input type="email" name="email" required placeholder="Enter your admin email">

        <label>Password:</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>
