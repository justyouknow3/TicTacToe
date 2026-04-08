<<<<<<< HEAD
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: /feeds/login.php");
        exit;
    }
}

function requireRole($roles = [])
{
    requireLogin();
    if (!in_array($_SESSION["role"], $roles, true)) {
        header("Location: /feeds/index.php");
        exit;
    }
}
?>
=======
<<<<<<< HEAD
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: /feeds/login.php");
        exit;
    }
}

function requireRole($roles = [])
{
    requireLogin();
    if (!in_array($_SESSION["role"], $roles, true)) {
        header("Location: /feeds/index.php");
        exit;
    }
}
?>
=======
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: /feeds/login.php");
        exit;
    }
}

function requireRole($roles = [])
{
    requireLogin();
    if (!in_array($_SESSION["role"], $roles, true)) {
        header("Location: /feeds/index.php");
        exit;
    }
}
?>
>>>>>>> e7f6847 (first update)
>>>>>>> 4062a24 (first update)
