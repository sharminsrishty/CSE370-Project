<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/admin_auth.php";

$students_result = $conn->query("
    SELECT s.User_id, s.User_name, s.Department, s.Name, 
           u.Approve AS user_approve,
           sv.Approve_state AS verification_approve, 
           sv.Verification_date, sv.Admin_Username, sv.Student_username
    FROM Student s
    LEFT JOIN User u ON s.User_id = u.User_id
    LEFT JOIN Student_Verification sv ON s.User_id = sv.Std_id
");

if (!$students_result) {
    die("Error fetching students: " . $conn->error);
}

$alumni_result = $conn->query("
    SELECT a.User_id, a.User_name, a.Session, a.Designation, a.Job_location, 
           u.Approve AS user_approve,
           av.Approve_state AS verification_approve, 
           av.Verification_date, av.Alumni_username
    FROM Alumni a
    LEFT JOIN User u ON a.User_id = u.User_id
    LEFT JOIN Alumni_Verification av ON a.User_id = av.Alm_id
");


if (!$alumni_result) {
    die("Error fetching alumni: " . $conn->error);
}
?>

<?php include __DIR__ . "/../includes/admin_header.php"; ?>

<div class="tabs-container">
    <div class="tabs">
        <button class="tablink active" onclick="openTab(event, 'studentsTab')">Students</button>
        <button class="tablink" onclick="openTab(event, 'alumniTab')">Alumni</button>
    </div>

    <!-- Students Tab -->
    <div id="studentsTab" class="tabcontent" style="display:block;">
        <h2>Students Info</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Verification Approved?</th>
                <th>Verified By</th>
                <th>Verification Date</th>
            </tr>
            <?php if($students_result->num_rows > 0): ?>
                <?php while($row = $students_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['User_id']) ?></td>
                        <td><?= htmlspecialchars($row['User_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['Department'] ?? '-') ?></td>
                        <td><?= !empty($row['verification_approve']) ? "Yes" : "No" ?></td>
                        <td><?= htmlspecialchars($row['Admin_Username'] ?? $row['Student_username'] ?? '-') ?></td>
                        <td><?= $row['Verification_date'] ?? "-" ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No students found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Alumni Tab -->
    <div id="alumniTab" class="tabcontent">
        <h2>Alumni Info</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Session</th>
                <th>Designation</th>
                <th>Job Location</th>
                <th>Verification Approved?</th>
                <th>Verified By</th>
                <th>Verification Date</th>
            </tr>
            <?php if($alumni_result->num_rows > 0): ?>
                <?php while($row = $alumni_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['User_id']) ?></td>
                        <td><?= htmlspecialchars($row['User_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['Session'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['Designation'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['Job_location'] ?? '-') ?></td>
                        <td><?= !empty($row['verification_approve']) ? "Yes" : "No" ?></td>
                        <td><?= htmlspecialchars($row['Admin_Username'] ?? $row['Alumni_username'] ?? '-') ?></td>
                        <td><?= $row['Verification_date'] ?? "-" ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">No alumni found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    const tabcontent = document.getElementsByClassName("tabcontent");
    for (let i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    const tablinks = document.getElementsByClassName("tablink");
    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("active");
}
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>
