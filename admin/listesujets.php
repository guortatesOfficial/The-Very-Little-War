<?php
include("redirectionmotdepasse.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<title>The Very Little War - Liste des sujets</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
h3, th, td
{
text-align:center;
}
table
{
border-collapse:collapse;
border:2px solid black;
margin:auto;
}
th, td
{
border:1px solid black;
}
</style>
</head>
<body>

<?php
include("../includes/connexion.php");

 

if (isset($_GET['supprimersujet']))
{
    $_GET['supprimersujet'] = addslashes($_GET['supprimersujet']);
    mysqli_query($base,'DELETE FROM sujets WHERE id=\'' . $_GET['supprimersujet'] . '\'');
	mysqli_query($base,'DELETE FROM statutforum WHERE idsujet=\''. $_GET['supprimersujet'] . '\'');
}
if (isset($_GET['verouillersujet']))
{
    $_GET['verouillersujet'] = addslashes($_GET['verouillersujet']);
    mysqli_query($base,'UPDATE sujets SET statut = 1 WHERE id=\'' . $_GET['verouillersujet'] . '\'');
	mysqli_query($base,'DELETE FROM statutforum WHERE idsujet=\''. $_GET['verouillersujet'] . '\'');
}
if (isset($_GET['deverouillersujet']))
{
    $_GET['deverouillersujet'] = addslashes($_GET['deverouillersujet']);
    mysqli_query($base,'UPDATE sujets SET statut = 0 WHERE id=\'' . $_GET['deverouillersujet'] . '\'');
}
?>
<table>
<tr>
<th>Vérouiller</th>
<th>Dévérouiller</th>
<th>Supprimer</th>
<th>Titre</th>
<th>Auteur</th>
<th>Statut</th>
<th>Date</th>
</tr>
<?php
$retour = mysqli_query($base,'SELECT * FROM sujets ORDER BY auteur DESC');
while ($donnees = mysqli_fetch_array($retour))
{
?>
<tr>
<td><?php echo '<a href="listesujets.php?verouillersujet=' . $donnees['id'] . '">'; ?>Vérouiller</a></td>
<td><?php echo '<a href="listesujets.php?deverouillersujet=' . $donnees['id'] . '">'; ?>Dévérouiller</a></td>
<td><?php echo '<a href="listesujets.php?supprimersujet=' . $donnees['id'] . '">'; ?>Supprimer</a></td>
<td><?php echo stripslashes($donnees['titre']); ?></td>
<td><?php echo stripslashes($donnees['auteur']); ?></td>
<td><?php if($donnees['statut'] == 0){ echo "Ouvert"; } else { echo "Vérouillé"; } ?></td>
<td><?php echo date('d/m/Y', $donnees['timestamp']); ?></td>
</tr>
<?php
}
?>
</table>
</body>
</html>