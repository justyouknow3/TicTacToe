<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Officer"]);
require_once __DIR__ . "/../config/db.php";

$officer_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $feedback_id = (int)($_POST["feedback_id"] ?? 0);
    $status = $_POST["status"] ?? "Pending";
    $note = trim($_POST["note"] ?? "");

    if (in_array($status, ["Pending", "Under Review", "Resolved"], true) && $feedback_id > 0) {
        $updateFeedback = $conn->prepare("UPDATE feedback SET status = ? WHERE id = ?");
        $updateFeedback->bind_param("si", $status, $feedback_id);
        $updateFeedback->execute();

        $log = $conn->prepare("INSERT INTO feedback_updates (feedback_id, officer_id, status, note) VALUES (?, ?, ?, ?)");
        $log->bind_param("iiss", $feedback_id, $officer_id, $status, $note);
        $log->execute();
    }
}

$list = $conn->query("SELECT f.id, f.category, f.message, f.status, f.created_at, f.is_anonymous, u.name
                      FROM feedback f
                      LEFT JOIN users u ON u.id = f.user_id
                      ORDER BY f.created_at DESC");

include __DIR__ . "/../includes/header.php";
?>
<h3>Feedback List</h3>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead><tr><th>ID</th><th>Student</th><th>Category</th><th>Message</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php while ($row = $list->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["is_anonymous"] ? "Anonymous" : htmlspecialchars($row["name"]); ?></td>
                <td><?php echo htmlspecialchars($row["category"]); ?></td>
                <td><?php echo htmlspecialchars($row["message"]); ?></td>
                <td><?php echo htmlspecialchars($row["status"]); ?></td>
                <td>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="feedback_id" value="<?php echo $row["id"]; ?>">
                        <select name="status" class="form-select form-select-sm">
                            <option>Pending</option>
                            <option>Under Review</option>
                            <option>Resolved</option>
                        </select>
                        <input name="note" class="form-control form-control-sm" placeholder="Resolution note">
                        <button class="btn btn-sm btn-primary">Save</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
