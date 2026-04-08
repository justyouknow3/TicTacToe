<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Officer"]);
include __DIR__ . "/../includes/header.php";
?>
<h3>Officer Dashboard</h3>
<div class="list-group">
    <a class="list-group-item list-group-item-action" href="/feeds/officer/feedback_list.php">Manage Feedback</a>
    <a class="list-group-item list-group-item-action" href="/feeds/officer/surveys_manage.php">Create Survey</a>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
