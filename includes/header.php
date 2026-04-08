<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Feedback and Resolution Systems</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/feeds/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/feeds/index.php">BSIT FRS</a>
        <div class="d-flex gap-2">
            <?php if (isset($_SESSION["user_id"])): ?>
                <span class="navbar-text text-white">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
                <a class="btn btn-light btn-sm" href="/feeds/logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-light btn-sm" href="/feeds/login.php">Login</a>
                <a class="btn btn-outline-light btn-sm" href="/feeds/register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container py-4 app-shell">
    <?php
    $currentPage = basename($_SERVER["PHP_SELF"] ?? "");
    $hideBackOn = ["index.php", "login.php", "register.php"];

    // Safe fallback per role so Back never accidentally logs out users.
    $defaultBackUrl = "/feeds/index.php";
    if (isset($_SESSION["role"])) {
        if ($_SESSION["role"] === "Admin") {
            $defaultBackUrl = "/feeds/admin/dashboard.php";
        } elseif ($_SESSION["role"] === "Officer") {
            $defaultBackUrl = "/feeds/officer/dashboard.php";
        } elseif ($_SESSION["role"] === "Student") {
            $defaultBackUrl = "/feeds/student/dashboard.php";
        }
    }

    $backUrl = $_SESSION["previous_page"] ?? $defaultBackUrl;
    $referer = $_SERVER["HTTP_REFERER"] ?? "";

    // Store a "previous page" for Back navigation.
    // Important: if you submit a form and the server reloads the same page,
    // the referrer becomes the same page again, so we DO NOT overwrite previous_page.
    $blockedPaths = ["/feeds/logout.php", "/feeds/login.php", "/feeds/register.php"];
    $host = $_SERVER["HTTP_HOST"] ?? "";
    $currentPath = $_SERVER["REQUEST_URI"] ?? "";
    $currentPathOnly = parse_url($currentPath, PHP_URL_PATH) ?? "";
    $currentQuery = $_SERVER["QUERY_STRING"] ?? "";
    $currentKey = $currentPathOnly . ($currentQuery !== "" ? ("?" . $currentQuery) : "");

    if ($referer !== "") {
        $refHost = parse_url($referer, PHP_URL_HOST);
        $refPathOnly = parse_url($referer, PHP_URL_PATH) ?? "";
        $refQuery = parse_url($referer, PHP_URL_QUERY) ?? "";
        $refKey = $refPathOnly . ($refQuery !== "" ? ("?" . $refQuery) : "");

        $isLocal = ($refHost === null) || ($refHost === $host) || (strpos((string)$refHost, (string)$host) !== false);
        if ($isLocal && !in_array($refPathOnly, $blockedPaths, true) && $refKey !== $currentKey) {
            $_SESSION["previous_page"] = $referer;
        }
    }

    // If previous_page was blocked or empty, fallback.
    $prevPathOnly = "";
    if (!empty($_SESSION["previous_page"])) {
        $prevPathOnly = parse_url($_SESSION["previous_page"], PHP_URL_PATH) ?? "";
    }
    if ($prevPathOnly === "" || in_array($prevPathOnly, $blockedPaths, true)) {
        $backUrl = $defaultBackUrl;
    } else {
        $backUrl = $_SESSION["previous_page"];
    }
    ?>
    <?php if (!in_array($currentPage, $hideBackOn, true)): ?>
        <div class="mb-3">
            <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-outline-secondary btn-sm">
                ← Back
            </a>
        </div>
    <?php endif; ?>
