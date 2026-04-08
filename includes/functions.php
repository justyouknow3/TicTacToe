<?php
function cleanInput($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function redirectByRole($role)
{
    if ($role === "Student") {
        header("Location: /feeds/student/dashboard.php");
    } elseif ($role === "Officer") {
        header("Location: /feeds/officer/dashboard.php");
    } elseif ($role === "Admin") {
        header("Location: /feeds/admin/dashboard.php");
    } else {
        header("Location: /feeds/index.php");
    }
    exit;
}
?>
