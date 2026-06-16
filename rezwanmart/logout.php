<?php
session_start();
session_destroy();
header('Location: /rezwanmart/login.php');
exit;
?>
