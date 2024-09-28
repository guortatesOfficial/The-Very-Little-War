<?php
session_start();
$_SESSION['loginTest'] = "test";
$_SESSION['mdpTest'] = "test";
header('Location: index.php'); 
exit();
?>