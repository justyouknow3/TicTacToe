<?php
session_start();
session_unset();
session_destroy();
header("Location: /feeds/login.php");
exit;
?>
