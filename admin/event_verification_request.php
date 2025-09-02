<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/admin_auth.php"; // ensure only logged-in admins can access

$admin_id = $_SESSION['admin_id'] ?? null;
$admin_username = $_SESSION['admin_username'] ?? '';

$errors = [];
$success = "";

// Approve event request
if (isset($_POST['approve_event'])) {
    $event_ver_id = intval($_POST['event_ver_id']);
    $today = date('Y-m-d');

    // Update Event_verification table
    $stmt = $conn->prepare("UPDATE Event_verification SET Admin_username=?, Verification_date=? WHERE Event_verification_id=?");
    $stmt->bind_param("ssi", $admin_username, $today, $event_ver_id);
    if ($stmt->execute()) {
        // Insert into Event_verification_request
        $stmt2 = $conn->prepare("INSERT INTO Event_verification_request (Admin_id, Event_verification_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $admin_id, $event_ver_id);
        $stmt2->execute();
        $stmt2->close();

        $success = "Event verification approved successfully.";
    } else {
        $errors[] = "Failed to approve event verification: " . $conn->error;
    }
    $stmt->close();
}

// Fetch pending event verification requests
$events_query = "
    SELECT ev.Event_verification_id, e.Event_name, e.Event_description, e.Event_creator, e.Start_time, e.End_time
    FROM Event_verification ev
    JOIN Event e ON ev.Event_id = e.Id
    WHERE ev.Admin_username IS NULL
";
$events_result = $conn->query($events_query);
?>

<?php include __DIR__ . "/../includes/admin_header.php"; ?>

<h2>Pending Event Verification Requests</h2>

<?php foreach ($errors as $e) echo "<p style='color:red;'>$e</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<?php if ($events_result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Event Name</th>
            <th>Creator</th>
            <th>Description</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $events_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Event_name']); ?></td>
                <td><?php echo htmlspecialchars($row['Event_creator']); ?></td>
                <td><?php echo htmlspecialchars($row['Event_description']); ?></td>
                <td><?php echo htmlspecialchars($row['Start_time']); ?></td>
                <td><?php echo htmlspecialchars($row['End_time']); ?></td>
                <td>
                    <form method="post" style="margin:0;">
                        <input type="hidden" name="event_ver_id" value="<?php echo $row['Event_verification_id']; ?>">
                        <button type="submit" name="approve_event">Approve</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No pending event verification requests.</p>
<?php endif; ?>


<?php include __DIR__ . "/../includes/footer.php"; ?>
