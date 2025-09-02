<?php
require_once __DIR__ . "/../config/db.php";
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../public/login.php");
    exit;
}

$errors = [];
$success = "";

// Fetch alumni info along with user info and latest verification
$stmt = $conn->prepare("
    SELECT a.User_id, a.User_name, a.Session, a.Designation, a.Job_location, u.User_mail, u.Approve,
           av.Admin_Username AS approved_by
    FROM Alumni a
    JOIN User u ON a.User_id = u.User_id
    LEFT JOIN Alumni_Verification av 
           ON a.User_id = av.Alm_id AND av.Approve_state = 1
    WHERE a.User_id = ?
    ORDER BY av.Verification_date DESC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$alumni = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $session = trim($_POST['session'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $job_location = trim($_POST['job_location'] ?? '');

    $stmt = $conn->prepare("UPDATE Alumni SET User_name=?, Session=?, Designation=?, Job_location=? WHERE User_id=?");
    $stmt->bind_param("ssssi", $name, $session, $designation, $job_location, $user_id);
    $stmt->execute();
    $stmt->close();

    $success = "Profile updated successfully!";
    header("Location: dashboard.php");
    exit;
}

include __DIR__ . "/../includes/header.php";
?>

<h2>Alumni Dashboard</h2>

<?php if ($errors): ?>
    <div class="error-box"><?php foreach ($errors as $e) echo "<p>$e</p>"; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success-box"><p><?php echo htmlspecialchars($success); ?></p></div>
<?php endif; ?>

<?php if (!$alumni['Approve']): ?>
    <div class="warning-box">
        ⚠ You are not verified yet. 
        <a href="../alumni/verification.php" class="verify-now-btn">Verify Now</a>
    </div>
<?php else: ?>
    <div class="success-box">
        ✅ Verified by Admin: <?php echo htmlspecialchars($alumni['approved_by'] ?? 'N/A'); ?>
    </div>
<?php endif; ?>

<form method="post" class="form-box">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($alumni['User_name']); ?>" required>

    <label>Session:</label>
    <input type="text" name="session" value="<?php echo htmlspecialchars($alumni['Session']); ?>">

    <label>Designation:</label>
    <input type="text" name="designation" value="<?php echo htmlspecialchars($alumni['Designation']); ?>">

    <label>Job Location:</label>
    <input type="text" name="job_location" value="<?php echo htmlspecialchars($alumni['Job_location']); ?>">

    <label>Email:</label>
    <input type="email" value="<?php echo htmlspecialchars($alumni['User_mail']); ?>" disabled>

    <button type="submit" name="update_profile">Update Profile</button>
</form>

<?php include __DIR__ . "/../includes/footer.php"; ?>
