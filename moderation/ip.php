<?php
include("mdp.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<title>The Very Little War - Ip multiple</title>
<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="../style.css" >
</head>
<body>
<div class="panel panel-default margin-10 text-center pattern-bg">
<div class="panel-heading">
<h4>Multicomptes</h4></div>
<div class="panel-body">
<?php
echo '<h4>Pseudos avec l\'ip '.$_GET['ip'].'\'</h4><p>';
include("../includes/connexion.php");

$retour = mysqli_query($base,'SELECT * FROM membre WHERE ip=\''.$_GET['ip'].'\'');
while ($donnees = mysqli_fetch_array($retour)) {
	echo '<a href="../joueur.php?id='.$donnees['login'].'">'.$donnees['login'].'</a><br/>';
}
echo '</p>';
?>
</div>
</div>
</body>
</html>
