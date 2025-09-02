<?php
require_once __DIR__ . "/../config/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

$user_id = $_SESSION['user_id'] ?? null;
$user_mail = $_SESSION['user_mail'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Check if user exists in Student table
$student_check = $conn->prepare("SELECT * FROM Student WHERE User_id = ?");
$student_check->bind_param("i", $user_id);
$student_check->execute();
$student_result = $student_check->get_result();
$student_check->close();

// Check if user exists in Alumni table
$alumni_check = $conn->prepare("SELECT * FROM Alumni WHERE User_id = ?");
$alumni_check->bind_param("i", $user_id);
$alumni_check->execute();
$alumni_result = $alumni_check->get_result();
$alumni_check->close();

// Redirect if already in a table
if ($student_result->num_rows > 0) {
    header("Location: ../student/dashboard.php");
    exit;
} elseif ($alumni_result->num_rows > 0) {
    header("Location: ../alumni/dashboard.php");
    exit;
}

// Show type selection form if not found
$errors = [];
if (isset($_POST['choose_type'])) {
    $type = $_POST['user_type'] ?? '';

    if ($type === "student") {
        $stmt = $conn->prepare("INSERT INTO Student (User_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: ../student/dashboard.php");
            exit;
        } else {
            $errors[] = "Failed to create Student record: " . $conn->error;
        }
    } elseif ($type === "alumni") {
        $stmt = $conn->prepare("INSERT INTO Alumni (User_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: ../alumni/dashboard.php");
            exit;
        } else {
            $errors[] = "Failed to create Alumni record: " . $conn->error;
        }
    } else {
        $errors[] = "Please choose a valid type.";
    }
}
?>

<?php include __DIR__ . "/../includes/header.php"; ?>

<h2>Complete Your Profile</h2>

<?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
<?php endif; ?>

<form method="post" class="form-box">
    <label>Select your type:</label>
    <select name="user_type" required>
        <option value="">-- Choose --</option>
        <option value="student">Student</option>
        <option value="alumni">Alumni</option>
    </select>
    <button type="submit" name="choose_type">Continue</button>
</form>

<?php include __DIR__ . "/../includes/footer.php"; ?>
