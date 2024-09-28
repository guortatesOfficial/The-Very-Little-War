<?php
include("redirectionmotdepasse.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>The Very Little War - Ip multiple</title>
<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
<?php
echo '<h4>Pseudos avec l\'ip '.$_GET['ip'].'\'<p>';
include("../includes/connexion.php");

$retour = mysqli_query($base,'SELECT * FROM membre WHERE ip=\''.$_GET['ip'].'\'');
while ($donnees = mysqli_fetch_array($retour)) {
	echo '<a href="../joueur.php?id='.$donnees['login'].'">'.$donnees['login'].'</a><br/>';
}
echo '</p>';
?>
</body>
</html>
