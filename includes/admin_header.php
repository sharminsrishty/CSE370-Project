<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>University Portal - Admin</h1>
        <nav>
            <a href="../admin/dashboard.php">Dashboard</a>
            <a href="../admin/event_verification_request.php">Event Verification</a>
            <a href="../admin/student_alumni_info.php">Student & Alumni Info</a>
            <a href="../admin/verification_request.php">Verification Requests</a>
            <a href="../admin/admin_logout.php">Logout</a>
        </nav>
    </header>
    <main>
