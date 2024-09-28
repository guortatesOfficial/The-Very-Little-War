<?php
include("../includes/connexion.php");
include("redirectionmotdepasse.php");
include("../includes/fonctions.php");
if (isset($_POST['supprimercompte']))
{
$sql = 'SELECT login FROM membre WHERE login=\''.$_POST['supprimer'].'\'';
$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br/>'.$sql.'<br/>'.mysql_error());
$joueurExiste = mysqli_num_rows($ex);

if($joueurExiste > 0 ) {
		supprimerJoueur($_POST['supprimer']);
	}
	else {
		echo "Ce joueur n'existe pas.";
	} 
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<title>Neocrea - Supprimmer un compte</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<h3>Supprimer un compte</h3><br/>
<form method = "post" action = "supprimercompte.php">
<p> 
Login : <input type="text" name="supprimer"><br />
<input type="submit" name="supprimercompte" value="Supprimer le compte">
</form>
<?php 
if (isset($_POST['supprimercompte']))
{
	if ($_POST['supprimercompte'] == "Supprimer le compte")
	{
		if($joueurExiste > 0 ) {
			echo "Vous avez supprimer le compte " . $_POST['supprimer'] . ".";
		}
	}
}
?>
</p>

</body>
</html>