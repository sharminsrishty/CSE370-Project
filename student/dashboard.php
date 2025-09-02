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

// Fetch student info along with user info and latest verification
$stmt = $conn->prepare("
    SELECT s.User_id, s.User_name, s.Department, s.Name, u.User_mail, u.Approve,
           sv.Admin_Username AS approved_by
    FROM Student s
    JOIN User u ON s.User_id = u.User_id
    LEFT JOIN Student_Verification sv 
           ON s.User_id = sv.Std_id AND sv.Approve_state = 1
    WHERE s.User_id = ?
    ORDER BY sv.Verification_date DESC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $department = trim($_POST['department'] ?? '');

    $stmt = $conn->prepare("UPDATE Student SET User_name=?, Department=? WHERE User_id=?");
    $stmt->bind_param("ssi", $name, $department, $user_id);
    $stmt->execute();
    $stmt->close();

    $success = "Profile updated successfully!";
    header("Location: dashboard.php");
    exit;
}

include __DIR__ . "/../includes/header.php";
?>

<h2>Student Dashboard</h2>

<?php if ($errors): ?>
    <div class="error-box"><?php foreach ($errors as $e) echo "<p>$e</p>"; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success-box"><p><?php echo htmlspecialchars($success); ?></p></div>
<?php endif; ?>

<?php if (!$student['Approve']): ?>
    <div class="warning-box">
        ⚠ You are not verified yet. 
        <a href="../student/verification.php" class="verify-now-btn">Verify Now</a>
    </div>
<?php else: ?>
    <div class="success-box">
        ✅ Verified by Admin: <?php echo htmlspecialchars($student['approved_by'] ?? 'N/A'); ?>
    </div>
<?php endif; ?>

<form method="post" class="form-box">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($student['User_name']); ?>" required>

    <label>Department:</label>
    <input type="text" name="department" value="<?php echo htmlspecialchars($student['Department']); ?>">

    <label>Email:</label>
    <input type="email" value="<?php echo htmlspecialchars($student['User_mail']); ?>" disabled>

    <button type="submit" name="update_profile">Update Profile</button>
</form>

<?php include __DIR__ . "/../includes/footer.php"; ?>
