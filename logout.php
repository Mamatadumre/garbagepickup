<?php
session_start();

session_unset();
session_destroy();

header("Location: worker_login.php");
exit();
?>