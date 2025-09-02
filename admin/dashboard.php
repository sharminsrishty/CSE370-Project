<?php
require_once __DIR__ . "/../config/db.php";
include __DIR__ . "/../includes/admin_auth.php"; // Check if admin is logged in
include __DIR__ . "/../includes/admin_header.php"; // Admin navigation header

$admin_id = $_SESSION['admin_id'];

// Fetch admin info
$stmt = $conn->prepare("SELECT Username, Admin_mail FROM Admin WHERE Admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();
?>

<div class="admin-info-container">
    <h2>Admin Dashboard</h2>
    <div class="admin-info-box">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['Username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['Admin_mail']); ?></p>
        <p><strong>Admin ID:</strong> <?php echo $admin_id; ?></p>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
