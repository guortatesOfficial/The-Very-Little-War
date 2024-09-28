<?php
session_start();
include("includes/bbcode.php"); // Ajout de Yojim
$_SESSION['start'] = "start"; // Sert a savoir si il faut de nouveau ouvrir une nouvelle session ou non
if (isset($_SESSION['login']))
{
	include("includes/basicprivatephp.php");
}
else
{
	include("includes/basicpublicphp.php"); 
}

include("includes/tout.php");
debutCarte("Forum"); ?>
<div class="table-responsive">
<?php
	// Ajout de Yojim
	// On vérifie si l'utilisateur n'est pas banni du forum
	if(isset($_SESSION['login'])) {
		$sql4 = 'SELECT * FROM sanctions WHERE joueur=\''.$_SESSION['login'].'\'';
		$ex4 = mysqli_query($base,$sql4) or die('Erreur SQL !'.$sql4.'<br />'.mysql_error());
	}
	$ex4 = mysqli_query($base,'SELECT * FROM sanctions WHERE joueur="aabbqsdqsdqsdqsqqsd"'); //je savais pas comment faire :D
	// Si il est banni
	if (mysqli_num_rows($ex4)) {
		$sanction = mysqli_fetch_array($ex4);
		// On calcul la différence entre la date de fin et la date actuelle
		$sql5 = 'SELECT DATEDIFF(CURDATE(),\''.$sanction['dateFin'].'\')';
		$ex5 = mysqli_query($base,$sql5) or die ('Erreur SQL :<br/>'.$sql5.'<br/>'.mysql_error());
		$diff = mysqli_fetch_array($ex5);
		// Si la date de fin de la sanction est passée, on supprime la sanction
		if ($diff[0] >= 0){
			$sql7 = 'DELETE FROM sanctions WHERE joueur=\''.$_SESSION['login'].'\'';
			$ex7 = mysqli_query($base,$sql7) or die ('Erreur SQL :<br/>'.$sql7.'<br/>'.mysql_error());
			// Rafraichissement de la page
			echo "<script>window.location.replace(\"forum.php\")</script>";
		}
		else {
			list($annee,$mois,$jour) = explode('-',$sanction['dateFin']);
			$sanction['dateFin'] = $jour.'/'.$mois.'/'.$annee;
			echo "Vous ne pouvez plus accéder au forum car vous avez été banni par <a href=\"ecriremessage.php?destinataire=".$sanction['moderateur']."\" >".$sanction['moderateur']."</a> jusqu'au <strong>".$sanction['dateFin']."</strong>.<br/>";
			echo "Motif de la sanction : ".BBcode($sanction['motif']);
		}
	}
	else {
	

?>
<table class="table table-striped table-bordered" >
<thead>
<tr>
<?php
if(isset($_SESSION['login'])) {
	echo '<th>Statut</th>';
}
?>
<th >Forum</th>
<th>Sujets</th>
<th>Messages</th>
</tr>
</thead>
<tbody>
<?php
$sql = 'SELECT * FROM forums';
$ex = mysqli_query($base,$sql) or die('Erreur SQL !'.$sql.'<br />'.mysql_error());

while($forum = mysqli_fetch_array($ex)) {
    $ex2 = mysqli_query($base,'SELECT count(*) AS nbSujets FROM sujets WHERE idforum=\''.$forum['id'].'\' AND statut = 0');
	$nbSujets = mysqli_fetch_array($ex2);
	echo '<tr>';
    if(isset($_SESSION['login'])) {
		$ex1 = mysqli_query($base,'SELECT count(*) AS nbLus FROM statutforum WHERE login=\''.$_SESSION['login'].'\' AND idforum=\''.$forum['id'].'\'');
		$statutForum = mysqli_fetch_array($ex1);

		if($statutForum['nbLus'] >= $nbSujets['nbSujets']) {
			echo '<td><img src="images/forum/pasDeNouveauMessage.png" alt="pasDeNouveauMessage" class="w32"/> </td>';
		}
		else {
			echo '<td><img src="images/forum/nouveauMessage.png" alt="nouveauMessage" class="w32"/></td>';
		}
	}
    echo '<td><a href="listesujets.php?id='.$forum['id'].'">'.$forum['titre'].'</a></td>';
	
	echo '<td>'.$nbSujets[0].'</td>';
	$req = mysqli_query($base,'SELECT count(*) FROM sujets s, reponses r WHERE idforum='.$forum['id'].' AND s.id=r.idsujet');
	$nbMessages = mysqli_fetch_array($req);
	echo '<td>'.($nbMessages[0]+$nbSujets[0]).'</td>';
	
	echo '</tr>';
}
?>
</tbody>
</table>
</div>
<?php
if(isset($_SESSION['login'])) { ?>
<br/>
<p class="legende">
<?php echo important("Légende"); ?>
<img src="images/forum/nouveauMessage.png" alt="nouveauMessage" style="vertical-align:middle" class="w32"/> : Un ou plusieurs nouveaux messages<br/><br/>
<img src="images/forum/pasDeNouveauMessage.png" alt="pasDeNouveauMessage" style="vertical-align:middle" class="w32"/> : Pas de nouveaux messages
</p>
<?php 
	}
 }// Ajout par Yojim
finCarte();
include("includes/copyright.php"); ?>
