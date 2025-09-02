<?php
require_once __DIR__ . "/../config/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../public/login.php");
    exit;
}

$errors = [];
$success = "";

// Check if user is student or alumni
$is_student = $conn->query("SELECT * FROM Student WHERE User_id = $user_id")->num_rows > 0;
$is_alumni  = $conn->query("SELECT * FROM Alumni WHERE User_id = $user_id")->num_rows > 0;

if (!$is_student && !$is_alumni) {
    $errors[] = "You are not registered as Student or Alumni.";
}

// Submit verification request
if (isset($_POST['request_verification'])) {
    $today = date('Y-m-d');

    if ($is_student) {
        $check = $conn->prepare("SELECT * FROM Student_Verification WHERE Std_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "You have already requested verification.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Student_Verification (Std_id, Verification_date, Student_username) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $today, $_SESSION['user_mail']);
            if ($stmt->execute()) {
                $success = "Student verification request sent.";
            } else {
                $errors[] = "Failed to request verification: " . $conn->error;
            }
            $stmt->close();
        }
    }

    if ($is_alumni) {
        $check = $conn->prepare("SELECT * FROM Alumni_Verification WHERE Alm_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "You have already requested verification.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Alumni_Verification (Alm_id, Verification_date, Alumni_username) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $today, $_SESSION['user_mail']);
            if ($stmt->execute()) {
                $success = "Alumni verification request sent.";
            } else {
                $errors[] = "Failed to request verification: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<?php include __DIR__ . "/../includes/header.php"; ?>

<h2>Request Verification</h2>

<?php foreach ($errors as $e) echo "<p style='color:red;'>$e</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    <button type="submit" name="request_verification">Request Verification</button>
</form>

<?php include __DIR__ . "/../includes/footer.php"; ?>
