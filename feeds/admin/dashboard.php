<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Admin"]);
include __DIR__ . "/../includes/header.php";
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Admin Dashboard</h3>
        <p class="text-muted mb-0">Manage accounts, reviews, reports, and surveys.</p>
    </div>
</div>

<div class="portal-grid">
    <a class="portal-tile tile-maroon" href="/feeds/admin/accounts.php">
        <div class="tile-icon"><i class="bi bi-person-check-fill"></i></div>
        <div class="tile-text">
            <div class="tile-title">Accounts</div>
            <div class="tile-subtitle">Approval & Lifecycle</div>
        </div>
        <div class="tile-arrow"><i class="bi bi-chevron-right"></i></div>
    </a>

    <a class="portal-tile tile-gold" href="/feeds/admin/reports.php">
        <div class="tile-icon"><i class="bi bi-tablet-fill"></i></div>
        <div class="tile-text">
            <div class="tile-title">Reports</div>
            <div class="tile-subtitle">Feedback by Status/Category</div>
        </div>
        <div class="tile-arrow"><i class="bi bi-chevron-right"></i></div>
    </a>

    <a class="portal-tile tile-maroon-2" href="/feeds/admin/surveys.php">
        <div class="tile-icon"><i class="bi bi-ui-checks-grid"></i></div>
        <div class="tile-text">
            <div class="tile-title">Surveys</div>
            <div class="tile-subtitle">View Answers</div>
        </div>
        <div class="tile-arrow"><i class="bi bi-chevron-right"></i></div>
    </a>

    <a class="portal-tile tile-gold-2" href="/feeds/admin/surveys_manage.php">
        <div class="tile-icon"><i class="bi bi-plus-circle-fill"></i></div>
        <div class="tile-text">
            <div class="tile-title">Create Survey</div>
            <div class="tile-subtitle">Add Questions & Options</div>
        </div>
        <div class="tile-arrow"><i class="bi bi-chevron-right"></i></div>
    </a>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
