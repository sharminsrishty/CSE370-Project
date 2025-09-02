<?php
require_once __DIR__ . "/../config/db.php";
include __DIR__ . "/../includes/admin_auth.php";
include __DIR__ . "/../includes/admin_header.php";

$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];

$errors = [];
$success = "";

// Handle approval
if (isset($_POST['approve'])) {
    $type = $_POST['type']; // 'student' or 'alumni'
    $verify_id = (int)$_POST['verify_id'];
    $user_id = (int)$_POST['user_id'];

    $today = date('Y-m-d');

    if ($type === "student") {
        // Update Student_Verification
        $stmt = $conn->prepare("UPDATE Student_Verification SET Approve_state=1, Admin_Username=?, Verification_date=? WHERE Verified_id=?");
        $stmt->bind_param("ssi", $admin_username, $today, $verify_id);
        $stmt->execute();
        $stmt->close();

        // Update Admin_verify table
        $stmt = $conn->prepare("INSERT INTO Admin_verify (Admin_id, Alm_verified_id, Std_verified_id) VALUES (?, NULL, ?)");
        $stmt->bind_param("ii", $admin_id, $verify_id);
        $stmt->execute();
        $stmt->close();

        // Update User table
        $stmt = $conn->prepare("UPDATE User SET Approve=1 WHERE User_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $success = "Student verification approved successfully.";
    }

    if ($type === "alumni") {
        // Update Alumni_Verification
        $stmt = $conn->prepare("UPDATE Alumni_Verification SET Approve_state=1, Admin_Username=?, Verification_date=? WHERE Verified_id=?");
        $stmt->bind_param("ssi", $admin_username, $today, $verify_id);
        $stmt->execute();
        $stmt->close();

        // Update Admin_verify table
        $stmt = $conn->prepare("INSERT INTO Admin_verify (Admin_id, Alm_verified_id, Std_verified_id) VALUES (?, ?, NULL)");
        $stmt->bind_param("ii", $admin_id, $verify_id);
        $stmt->execute();
        $stmt->close();

        // Update User table
        $stmt = $conn->prepare("UPDATE User SET Approve=1 WHERE User_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $success = "Alumni verification approved successfully.";
    }
}

// Fetch pending verification requests
$student_requests = $conn->query("
    SELECT sv.Verified_id, s.User_id, s.User_name, sv.Student_username, sv.Approve_state
    FROM Student_Verification sv
    JOIN Student s ON sv.Std_id = s.User_id
    WHERE sv.Approve_state = 0
");

$alumni_requests = $conn->query("
    SELECT av.Verified_id, a.User_id, a.User_name, av.Alumni_username, av.Approve_state
    FROM Alumni_Verification av
    JOIN Alumni a ON av.Alm_id = a.User_id
    WHERE av.Approve_state = 0
");
?>

<h2>Verification Requests</h2>

<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<?php foreach ($errors as $e) echo "<p style='color:red;'>$e</p>"; ?>

<h3>Student Verification Requests</h3>
<?php if ($student_requests->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php while($row = $student_requests->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['User_id']; ?></td>
            <td><?php echo htmlspecialchars($row['User_name']); ?></td>
            <td><?php echo htmlspecialchars($row['Student_username']); ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="type" value="student">
                    <input type="hidden" name="verify_id" value="<?php echo $row['Verified_id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $row['User_id']; ?>">
                    <button type="submit" name="approve">Approve</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No pending student verification requests.</p>
<?php endif; ?>

<h3>Alumni Verification Requests</h3>
<?php if ($alumni_requests->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php while($row = $alumni_requests->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['User_id']; ?></td>
            <td><?php echo htmlspecialchars($row['User_name']); ?></td>
            <td><?php echo htmlspecialchars($row['Alumni_username']); ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="type" value="alumni">
                    <input type="hidden" name="verify_id" value="<?php echo $row['Verified_id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $row['User_id']; ?>">
                    <button type="submit" name="approve">Approve</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No pending alumni verification requests.</p>
<?php endif; ?>

<?php include __DIR__ . "/../includes/footer.php"; ?>
