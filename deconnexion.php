<?php
session_start(); 
include("includes/connexion.php");
include("includes/fonctions.php");
if(isset($_POST['verification']) AND isset($_POST['oui'])) {
	supprimerJoueur($_SESSION['login']);
}
  
session_unset();  
session_destroy();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<?php include("includes/meta.php"); ?>
<title>The Very Little War - Deconnexion</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
</head>
<body>

<script> 
    localStorage.removeItem("login");
    localStorage.removeItem("mdp");
    window.location = "index.php";
</script>';
</body>