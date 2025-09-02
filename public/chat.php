<?php
require_once __DIR__ . "/../config/db.php";
include __DIR__ . "/../includes/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$user_mail = $_SESSION['user_mail'] ?? null;

if (!$user_id) {
    echo "<p>Please <a href='login.php'>login</a> to use chat.</p>";
    include __DIR__ . "/../includes/footer.php";
    exit;
}

// Determine user type (student or alumni) and verification
$type_result = $conn->query("SELECT * FROM Student WHERE User_id = $user_id");
if ($type_result && $type_result->num_rows > 0) {
    $user_type = "student";
    $row = $type_result->fetch_assoc();
    $verified = $row['Verified'] ?? 0; // assuming column 'Verified' in Student table
} else {
    $user_type = "alumni";
    $type_result = $conn->query("SELECT * FROM Alumni WHERE User_id = $user_id");
    $row = $type_result->fetch_assoc();
    $verified = $row['Verified'] ?? 0; // assuming column 'Verified' in Alumni table
}

// If not verified
if (!$verified) {
    $html = <<<HTML
<div class="warning-box">
    <p><strong>Not verified yet.</strong> You cannot use chat.</p>
    <a href="../student/verification.php" class="verify-now-btn">Verify Now</a>
</div>
HTML;
    echo $html;
    include __DIR__ . "/../includes/footer.php";
    exit;
}

// ========== SEND MESSAGE ==========
if (isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if ($message && $receiver_id) {
        // Get receiver email
        $receiver_result = $conn->query("SELECT User_mail FROM User WHERE User_id = $receiver_id");
        $receiver_row = $receiver_result->fetch_assoc();
        $receiver_mail = $receiver_row['User_mail'];

        // Insert into chat_history
        $stmt = $conn->prepare("INSERT INTO Chat_history (Message, Sender_username, Receiver_username) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $message, $user_mail, $receiver_mail);
        $stmt->execute();
        $chat_id = $stmt->insert_id;
        $stmt->close();

        // Insert into Access table
        $stmt2 = $conn->prepare("INSERT INTO Access (Chat_id, User_id) VALUES (?, ?), (?, ?)");
        $stmt2->bind_param("iiii", $chat_id, $user_id, $chat_id, $receiver_id);
        $stmt2->execute();
        $stmt2->close();
    }
}

// ========== GET RECIPIENTS ==========
if ($user_type == "student") {
    $recipients = $conn->query("SELECT User.User_id, Student.User_name FROM User INNER JOIN Student ON User.User_id = Student.User_id WHERE User.User_id != $user_id");
} else {
    $recipients = $conn->query("SELECT User.User_id, Alumni.User_name FROM User INNER JOIN Alumni ON User.User_id = Alumni.User_id WHERE User.User_id != $user_id");
}

// ========== GET CHAT HISTORY ==========
$chats = [];
$chat_result = $conn->query("SELECT * FROM Chat_history WHERE Sender_username = '$user_mail' OR Receiver_username = '$user_mail' ORDER BY Time_sent ASC");
while ($row = $chat_result->fetch_assoc()) {
    $chats[] = $row;
}
?>

<div class="chat-container">
    <div class="chat-header">
        <h2>Chat Portal</h2>
    </div>

    <div class="chat-history">
        <?php if (!empty($chats)): ?>
            <?php foreach ($chats as $c): ?>
                <div class="chat-message <?php echo ($c['Sender_username'] == $user_mail) ? 'sent' : 'received'; ?>">
                    <div class="bubble">
                        <p><?php echo htmlspecialchars($c['Message']); ?></p>
                        <small><?php echo ($c['Sender_username'] == $user_mail) ? "You" : htmlspecialchars($c['Sender_username']); ?> • 
                        <?php echo date("M d, Y H:i", strtotime($c['Time_sent'])); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">No messages yet.</p>
        <?php endif; ?>
    </div>

    <form method="post" class="chat-form" id="chatForm">
        <select name="receiver_id" required>
            <option value="">Select User</option>
            <?php while ($r = $recipients->fetch_assoc()): ?>
            <option value="<?php echo $r['User_id']; ?>"><?php echo htmlspecialchars($r['User_name']); ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="message" rows="2" placeholder="Type a message..." required></textarea>
        <button type="submit" name="send_message">Send</button>
    </form>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
