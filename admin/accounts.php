<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Admin"]);
require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = (int)($_POST["user_id"] ?? 0);
    $status = $_POST["status"] ?? "Pending";
    if ($user_id > 0 && in_array($status, ["Pending", "Approved", "Rejected", "Deactivated"], true)) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $user_id);
        $stmt->execute();
    }
}

$users = $conn->query("SELECT id, student_id, name, email, role, status FROM users ORDER BY id DESC");
include __DIR__ . "/../includes/header.php";
?>
<h3>Account Management</h3>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead><tr><th>ID</th><th>Student ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo $u["id"]; ?></td>
                <td><?php echo htmlspecialchars($u["student_id"]); ?></td>
                <td><?php echo htmlspecialchars($u["name"]); ?></td>
                <td><?php echo htmlspecialchars($u["email"]); ?></td>
                <td><?php echo htmlspecialchars($u["role"]); ?></td>
                <td><?php echo htmlspecialchars($u["status"]); ?></td>
                <td>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="user_id" value="<?php echo $u["id"]; ?>">
                        <select name="status" class="form-select form-select-sm">
                            <option>Pending</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                            <option>Deactivated</option>
                        </select>
                        <button class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
