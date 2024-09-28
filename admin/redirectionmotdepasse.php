<?php
session_start();
if (!isset($_SESSION['motdepasseadmin']) or $_SESSION['motdepasseadmin'] != "Faux mot de passe") {
	header('Location: index.php');
	exit();
}
