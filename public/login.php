<?php
require_once __DIR__ . "/../config/db.php";

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$success = "";

// ========== HANDLE REGISTRATION ==========
if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM User WHERE User_mail = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO User (User_mail, Password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $password);
        if ($stmt->execute()) {
            $success = "Registration successful. You can now log in.";
        } else {
            $errors[] = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// ========== HANDLE LOGIN ==========
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM User WHERE User_mail = ? AND Password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $row['User_id'];
        $_SESSION['user_mail'] = $row['User_mail'];
        header("Location: index.php");
        exit();
    } else {
        $errors[] = "Invalid email or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>University Portal - Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="auth-container">
    <img src="../assets/bracu_logo.png" alt="University Logo" class="auth-logo">

    <h2>Welcome to University Portal</h2>

    <!-- Show messages -->
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
                <p><?php echo htmlspecialchars($e); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-box">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="tabs">
        <button id="loginTab" onclick="showForm('login')">Sign In</button>
        <button id="registerTab" onclick="showForm('register')">Register</button>
    </div>

    <!-- Login Form -->
    <form id="loginForm" method="post" class="form-box">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Sign In</button>
    </form>

    <!-- Register Form -->
    <form id="registerForm" method="post" class="form-box" style="display:none;">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="register">Register</button>
    </form>
</div>

<script>
function showForm(type) {
    document.getElementById('loginForm').style.display = (type === 'login') ? 'block' : 'none';
    document.getElementById('registerForm').style.display = (type === 'register') ? 'block' : 'none';
}
</script>

</body>
</html>
