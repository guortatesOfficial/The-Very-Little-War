<?php 
include("includes/basicprivatephp.php");

if(isset($_GET['supprimer']) AND preg_match("#\d#",$_GET['supprimer'])) {
	if($_GET['supprimer'] == 1) {
		$sql = 'DELETE FROM rapports WHERE destinataire=\''.$_SESSION['login'].'\'';
		mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	}
	else {
		$sql = 'DELETE FROM rapports WHERE id=\''.$_GET['supprimer'].'\' AND destinataire=\''.$_SESSION['login'].'\'';
		mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	}
}

include("includes/tout.php");

if(isset($_GET['rapport'])) {
	$_GET['rapport'] = mysqli_real_escape_string($base,stripslashes(antihtml($_GET['rapport'])));
	$sql = 'SELECT * FROM rapports WHERE id=\''.$_GET['rapport'].'\' AND destinataire=\''.$_SESSION['login'].'\'';
	$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$rapports = mysqli_fetch_array($ex);
	$nb_rapports = mysqli_num_rows($ex);
	if($nb_rapports > 0) {
		$sql1 = 'UPDATE rapports SET statut=1 WHERE id=\''.$_GET['rapport'].'\'';
		$ex1 = mysqli_query($base,$sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
		
        debutCarte($rapports['titre']);
		debutContent();
		echo $rapports['contenu'];
        finContent();
        finCarte(imageLabel('<img  src="images/croix.png" alt="supprimer" class="imageSousMenu">','Supprimer','rapports.php?supprimer='.$rapports['id']));
	}
	else {
		header('Location: rapports.php'); 
		exit();
	}
}
else { 
	$nombreDeRapportsParPage = 15; 
	$retour = mysqli_query($base,'SELECT COUNT(*) AS nb_rapports FROM rapports WHERE destinataire=\''.$_SESSION['login'].'\'');
	$donnees = mysqli_fetch_array($retour);
	$totalDesRapports = $donnees['nb_rapports'];
	$nombreDePages  = ceil($totalDesRapports / $nombreDeRapportsParPage); // Calcul du nombre de pages créées
	// Puis on fait une boucle pour écrire les liens vers chacune des pages
	
	if (isset($_GET['page']) AND $_GET['page'] <= $nombreDePages AND $_GET['page'] > 0 AND preg_match("#\d#",$_GET['page']))// Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
	{
        $page = $_GET['page']; // Récuperation du numéro de la page
	}
	else // La variable n'existe pas, c'est la première fois qu'on charge la page
	{
        $page = 1; // On se met sur la page 1 (par défaut)
	}
 
	// On calcule le numéro du premier rapport qu'on prend pour le LIMIT de MySQL
	$premierRapportAafficher = ($page - 1) * $nombreDeRapportsParPage;  
	
	$sql = 'SELECT * FROM rapports WHERE destinataire=\''.$_SESSION['login'].'\' ORDER BY timestamp DESC LIMIT ' . $premierRapportAafficher . ', ' . $nombreDeRapportsParPage .''; 
	$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$nb_rapports = mysqli_num_rows($ex);
    
    debutCarte("Rapports");
	if($nb_rapports > 0) {
		echo '
        <div class="table-responsive">
		<table class="table table-striped table-bordered">
        <tbody>';
		while($rapports = mysqli_fetch_array($ex)) {
            echo '<td>'.$rapports['image'].'</td>';
			if($rapports['statut'] != 0){
				echo '<td><a href="rapports.php?rapport='.$rapports['id'].'">'.$rapports['titre'].'</a></td>';
			}
			else {
				echo '<td><strong><a href="rapports.php?rapport='.$rapports['id'].'">'.$rapports['titre'].'</a></strong></td>';
			}
			echo '<td><em>'.date('d/m/Y à H\hi', $rapports['timestamp']).'</em></td>';
			echo '<td><a href="rapports.php?supprimer='.$rapports['id'].'"><img  src="images/croix.png" alt="supprimer" class="w32"></a></td></tr>';
		}
		echo '</tbody></table></div>';
        $supprimer = '<a href="rapports.php?supprimer=1">Supprimer tous les rapports</a>';
		$adresse = "rapports.php?";
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
		echo "<br/>Vous n'avez aucun rapports.<br/>";
        finContent();
	}
	finCarte($supprimer.$pages);	
} 

include("includes/copyright.php"); ?>