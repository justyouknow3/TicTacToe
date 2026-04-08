<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Admin"]);
require_once __DIR__ . "/../config/db.php";

$from = $_GET["from"] ?? "";
$to = $_GET["to"] ?? "";
$where = "";
$params = [];
$types = "";

if ($from !== "" && $to !== "") {
    $where = "WHERE DATE(created_at) BETWEEN ? AND ?";
    $params = [$from, $to];
    $types = "ss";
}

$sqlStatus = "SELECT status, COUNT(*) AS total FROM feedback $where GROUP BY status";
$stmtStatus = $conn->prepare($sqlStatus);
if ($where !== "") {
    $stmtStatus->bind_param($types, ...$params);
}
$stmtStatus->execute();
$statusReport = $stmtStatus->get_result();

$sqlCategory = "SELECT category, COUNT(*) AS total FROM feedback $where GROUP BY category";
$stmtCategory = $conn->prepare($sqlCategory);
if ($where !== "") {
    $stmtCategory->bind_param($types, ...$params);
}
$stmtCategory->execute();
$categoryReport = $stmtCategory->get_result();

include __DIR__ . "/../includes/header.php";
?>
<h3>Feedback Reports</h3>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-3"><input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="form-control"></div>
    <div class="col-md-3"><input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="form-control"></div>
    <div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
</form>

<h5>Feedback by Status</h5>
<table class="table table-bordered">
    <thead><tr><th>Status</th><th>Total</th></tr></thead>
    <tbody>
    <?php while ($r = $statusReport->fetch_assoc()): ?>
        <tr><td><?php echo htmlspecialchars($r["status"]); ?></td><td><?php echo $r["total"]; ?></td></tr>
    <?php endwhile; ?>
    </tbody>
</table>

<h5>Feedback by Category</h5>
<table class="table table-bordered">
    <thead><tr><th>Category</th><th>Total</th></tr></thead>
    <tbody>
    <?php while ($r = $categoryReport->fetch_assoc()): ?>
        <tr><td><?php echo htmlspecialchars($r["category"]); ?></td><td><?php echo $r["total"]; ?></td></tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php include __DIR__ . "/../includes/footer.php"; ?>
