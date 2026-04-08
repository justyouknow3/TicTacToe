<?php
session_start();
if (isset($_SESSION["user_id"])) {
    require_once __DIR__ . "/includes/functions.php";
    redirectByRole($_SESSION["role"]);
}
include __DIR__ . "/includes/header.php";
?>
<div class="p-4 p-md-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold">BSIT Feedback and Resolution System</h1>
        <p class="col-md-9 fs-5">Submit concerns, suggestions, and comments. Officers and admins can review feedback and manage surveys.</p>
        <a href="/feeds/login.php" class="btn btn-primary btn-lg">Get Started</a>
    </div>
</div>
<?php include __DIR__ . "/includes/footer.php"; ?>
