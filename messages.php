<?php 
include("includes/basicprivatephp.php");
include("includes/bbcode.php");

if(isset($_GET['supprimer']) AND preg_match("#\d#",$_GET['supprimer'])) {
	if($_GET['supprimer'] == 1) {
		$sql = 'DELETE FROM messages WHERE destinataire=\''.$_SESSION['login'].'\'';
		mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	}
	else {
		$sql = 'DELETE FROM messages WHERE id=\''.$_GET['supprimer'].'\' AND destinataire=\''.$_SESSION['login'].'\'';
		mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	}
}

include("includes/tout.php");

if(isset($_GET['message'])) {
	$_GET['message'] = mysqli_real_escape_string($base,stripslashes(antihtml($_GET['message'])));
	$sql = 'SELECT * FROM messages WHERE ( destinataire=\''.$_SESSION['login'].'\' OR expeditaire=\''.$_SESSION['login'].'\' ) AND id=\''.$_GET['message'].'\'';
	$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$messages = mysqli_fetch_array($ex);
	$nb_messages = mysqli_num_rows($ex);
	if($nb_messages > 0) {
		if($_SESSION['login'] == $messages['destinataire']) {
			$sql1 = 'UPDATE messages SET statut=1 WHERE id=\''.$_GET['message'].'\'';
			$ex1 = mysqli_query($base,$sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
		}
		debutCarte($messages['titre']);
		debutContent();
		echo BBcode($messages['contenu']);
        finContent();
        finCarte(imageLabel('<img src="images/message_ferme.png" alt="up" class="imageSousMenu"/>','Répondre','ecriremessage.php?reponse=true&destinataire='.$messages['expeditaire']).imageLabel('<img  src="images/croix.png" alt="supprimer" class="imageSousMenu">','Supprimer','messages.php?supprimer='.$messages['id']));
	}
	else {
		header('Location: messages.php'); 
		exit();
	}
}
else { 
	$nombreDeMessagesParPage = 15; 
	$retour = mysqli_query($base,'SELECT COUNT(*) AS nb_messages FROM messages WHERE destinataire=\''.$_SESSION['login'].'\'');
	$donnees = mysqli_fetch_array($retour);
	$totalDesMessages = $donnees['nb_messages'];
	$nombreDePages  = ceil($totalDesMessages / $nombreDeMessagesParPage); // Calcul du nombre de pages créées
	// Puis on fait une boucle pour écrire les liens vers chacune des pages
	
	if (isset($_GET['page']) AND $_GET['page'] <= $nombreDePages AND $_GET['page'] > 0 AND preg_match("#\d#",$_GET['page']))// Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
	{
        $page = $_GET['page']; // Récuperation du numéro de la page
	}
	else // La variable n'existe pas, c'est la première fois qu'on charge la page
	{
        $page = 1; // On se met sur la page 1 (par défaut)
	}
 
	// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
	$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;  
	
	$sql = 'SELECT * FROM messages WHERE destinataire=\''.$_SESSION['login'].'\' ORDER BY timestamp DESC LIMIT ' . $premierMessageAafficher . ', ' . $nombreDeMessagesParPage .'';
	$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$nb_messages = mysqli_num_rows($ex);
	debutCarte("Messages");
	if($nb_messages > 0) {
		echo '<div class="table-responsive"><table class="table table-striped table-bordered">
		<thead>
		<tr>
		<th>Statut</th>
		<th>Titre</th>
		<th>Auteur</th>
		<th>Date</th>
		<th>Action</th>
		</tr></thead><tbody>';
		while($messages = mysqli_fetch_array($ex)) {
			if($messages['statut'] != 0){
				echo '<tr><td><a href="messages.php?message='.$messages['id'].'"><img src="images/message_ouvert.png" alt="ouvert" title="Lu" class="w32"/></td></a>';
			}
			else {
				echo '<tr><td><a href="messages.php?message='.$messages['id'].'"><img src="images/message_ferme.png" alt="ferme" title="Non lu" class="w32"/></td></a>';
			}
			echo '<td><a href="messages.php?message='.$messages['id'].'">'.$messages['titre'].'</a></td>';
			echo '<td><a href=joueur.php?id='.mysqli_real_escape_string($base,$messages['expeditaire']).'>'.$messages['expeditaire'].'</a></td>';
			echo '<td><em>'.date('d/m/Y à H\hi', $messages['timestamp']).'</em></td>';
			echo '<td><a href="messages.php?supprimer='.$messages['id'].'"><img  src="images/croix.png" alt="supprimer" class="w32"></a></td></tr>';
		}
		echo '</tbody></table></div>';
        $supprimer = '<a href="messages.php?supprimer=1">Supprimer tous les messages</a>';
		$adresse = "messages.php?";
        $premier = '';
        if($page > 2){
            $premier = '<a href="'.$adresse.'page=1">1</a>';
        }
        $pointsD = '';
        if($page > 3){
            $pointsD = '...';
        }
        $precedent = '';
        if($page > 1){
            $precedent = '<a href="'.$adresse.'page='.($page-1).'">'.($page-1).'</a>';
        }
        $suivant = '';
        if($page+1 <= $nombreDePages){
            $suivant = '<a href="'.$adresse.'page='.($page+1).'">'.($page+1).'</a>';
        }
        $pointsF = '';
        if($page+3 <= $nombreDePages){
            $pointsF = '...';
        }
        $dernier = '';
        if($page+2 <= $nombreDePages){
            $dernier = '<a href="'.$adresse.'page='.$nombreDePages.'">'.$nombreDePages.'</a>';
        }
        $pages = 'Pages : '.$premier.' '.$pointsD.' '.$precedent.' <strong>'.$page.'</strong> '.$suivant.' '.$pointsF.' '.$dernier;
		
    }
	else {
        $pages ="";
        $supprimer = "";
        debutContent();
		echo "Vous n'avez aucun messages ou cette page n'existe pas.<br/>";
        finContent();
	}
    finCarte('<a href="ecriremessage.php">Ecrire</a><a href="messagesenvoyes.php">Envoyés</a>'.$pages);
}

include("includes/copyright.php"); ?>