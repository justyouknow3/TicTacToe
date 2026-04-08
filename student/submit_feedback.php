<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Student"]);
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/functions.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = cleanInput($_POST["category"] ?? "");
    $feedback_message = cleanInput($_POST["message"] ?? "");
    $is_anonymous = isset($_POST["is_anonymous"]) ? 1 : 0;
    $status = "Pending";
    $user_id = $_SESSION["user_id"];

    if (!in_array($category, ["Concern", "Suggestion", "Comment"], true) || $feedback_message === "") {
        $error = "Please complete all fields correctly.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, category, message, is_anonymous, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issis", $user_id, $category, $feedback_message, $is_anonymous, $status);
        if ($stmt->execute()) {
            $message = "Feedback submitted successfully.";
        } else {
            $error = "Failed to submit feedback.";
        }
    }
}

include __DIR__ . "/../includes/header.php";
?>
<h3>Submit Feedback</h3>
<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
<form method="post" class="card card-body">
    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select" required>
            <option value="">Select Category</option>
            <option value="Concern">Concern</option>
            <option value="Suggestion">Suggestion</option>
            <option value="Comment">Comment</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea name="message" rows="4" class="form-control" required></textarea>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" name="is_anonymous" id="anon">
        <label for="anon" class="form-check-label">Submit as Anonymous</label>
    </div>
    <button class="btn btn-primary">Submit</button>
</form>
<?php include __DIR__ . "/../includes/footer.php"; ?>
