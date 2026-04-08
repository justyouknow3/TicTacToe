<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Student"]);
include __DIR__ . "/../includes/header.php";
?>
<h3>Student Dashboard</h3>
<div class="list-group">
    <a class="list-group-item list-group-item-action" href="/feeds/student/submit_feedback.php">Submit Feedback</a>
    <a class="list-group-item list-group-item-action" href="/feeds/student/surveys.php">Answer Surveys</a>
    <a class="list-group-item list-group-item-action" href="/feeds/student/create_survey.php">Create Survey (1 per day)</a>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
