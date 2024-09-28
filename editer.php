<?php 
include("includes/basicprivatephp.php");
include("includes/bbcode.php");

// On regarde si l'utilisateur est un modérateur
$req = mysqli_query($base,'SELECT moderateur FROM membre WHERE login=\''.$_SESSION['login'].'\'');
$moderateur = mysqli_fetch_array($req);

// Ajout de Yojim
// On recherche le sujet que l'on souhaite éditer
$sql3 = 'SELECT idsujet FROM reponses WHERE id=\''.$_GET['id'].'\'';
$ex3 = mysqli_query($base,$sql3) or die('Erreur SQL !'.$sql3.'<br />'.mysql_error());
$sujet = mysqli_fetch_array($ex3);
//

// Suppression 
if($_GET['type'] == 3 AND isset($_GET['id']) AND !empty($_GET['id']) AND preg_match("#^[0-9]*$#", $_GET['id'])) {
	$ex = mysqli_query($base,'SELECT auteur FROM reponses WHERE id=\''.$_GET['id'].'\'');
	$auteur = mysqli_fetch_array($ex);
	
	$ex = mysqli_query($base,'SELECT count(*) AS modo FROM membre WHERE login=\''.$_SESSION['login'].'\' AND moderateur=1');
	$modo = mysqli_fetch_array($ex);
	if($auteur['auteur'] == $_SESSION['login'] OR $modo['modo'] >= 1) {
		mysqli_query($base,'DELETE FROM reponses WHERE id=\''.$_GET['id'].'\'');
		$ex = mysqli_query($base,'SELECT nbMessages FROM autre WHERE login=\''.$_SESSION['login'].'\'');
		$nbMessages = mysqli_fetch_array($ex);
		mysqli_query($base,'UPDATE autre SET nbMessages=\''.($nbMessages['nbMessages']-1).'\' WHERE login=\''.$_SESSION['login'].'\'');
		// Modifié par Yojim
		echo "<script>window.location.replace(\"sujet.php?id=".$sujet['idsujet']."\")</script>"; // Redirection
		//
	}
	else {
		$erreur = "Vous ne pouvez pas supprimer une réponse dont vous n'êtes pas l'auteur.";
	}
}

// Ajout par Yojim
// Si on souhaite masquer un message
if($_GET['type'] == 5 AND isset($_GET['id']) AND !empty($_GET['id']) AND preg_match("#^[0-9]*$#", $_GET['id'])) {
	$sql = 'UPDATE reponses SET visibilite=0 WHERE id=\''.$_GET['id'].'\'';
	mysqli_query($base,$sql) or die('Erreur SQL !'.$sql.'<br />'.mysql_error());
	echo "<script>window.location.replace(\"sujet.php?id=".$sujet['idsujet']."\")</script>"; // Redirection
}
// Si on souhaite afficher un message
if($_GET['type'] == 4 AND isset($_GET['id']) AND !empty($_GET['id']) AND preg_match("#^[0-9]*$#", $_GET['id'])) {
	$sql1 = 'UPDATE reponses SET visibilite=1 WHERE id=\''.$_GET['id'].'\'';
	mysqli_query($base,$sql1) or die('Erreur SQL !'.$sql1.'<br />'.mysql_error());
	echo "<script>window.location.replace(\"sujet.php?id=".$sujet['idsujet']."\")</script>"; // Redirection
}
//

if(isset($_POST['contenu']) AND !empty($_POST['contenu']) AND isset($_GET['id']) AND preg_match("#^[0-9]*$#", $_GET['id']) AND isset($_GET['type'])) {
	$_POST['contenu'] = mysqli_real_escape_string($base,$_POST['contenu']);
	if(isset($_POST['titre']) AND !empty($_POST['titre'])) { // alors c'est un sujet
		$_POST['titre'] = mysqli_real_escape_string($base,$_POST['titre']);
		$ex = mysqli_query($base,'SELECT auteur FROM sujets WHERE id=\''.$_GET['id'].'\'');
		$auteur = mysqli_fetch_array($ex);
		if($_GET['type'] == 1) {
			if($auteur['auteur'] == $_SESSION['login']) {
				mysqli_query($base,'UPDATE sujets SET contenu=\''.$_POST['contenu'].'\', titre=\''.$_POST['titre'].'\' WHERE id=\''.$_GET['id'].'\'');
				$information = "Le sujet a bien été modifié";
				mysqli_query($base,'DELETE FROM statutforum WHERE idsujet=\''.$_GET['id'].'\'') or die('Erreur SQL !<br />'.mysql_error());
                ?>
                <script>
                    window.location.replace("sujet.php?id=<?php echo $_GET['id']; ?>");
                </script> <?php
			}
			else {
				$erreur = "Vous ne pouvez modifier un sujet donc vous n'êtes pas l'auteur";
			}
		}
	}
	if($_GET['type'] == 2) {
		// Rajout de Yojim
		if($moderateur[0] == '0'){
			$ex = mysqli_query($base,'SELECT auteur FROM reponses WHERE id=\''.$_GET['id'].'\'');
			$auteur = mysqli_fetch_array($ex);
			if($auteur['auteur'] == $_SESSION['login']) {
				$req1 = 'UPDATE reponses SET contenu=\''.$_POST['contenu'].'\' WHERE auteur=\''.$_SESSION['login'].'\' AND id=\''.$_GET['id'].'\'';
				$information = "La réponse a bien été modifiée";
				$ex = mysqli_query($base,'SELECT * FROM reponses WHERE id=\''.$_GET['id'].'\'');
				$reponse = mysqli_fetch_array($ex);
				mysqli_query($base,'DELETE FROM statutforum WHERE idsujet=\''.$reponse['idsujet'].'\'') or die('Erreur SQL !<br />'.mysql_error());
                
                $ex = mysqli_query($base, 'SELECT idsujet FROM reponses WHERE id=\''.$_GET['id'].'\'');
                $sujet = mysqli_fetch_array($ex);7
                ?>
                <script>
                    window.location.replace("sujet.php?id=<?php echo $sujet['idsujet']; ?>");
                </script> <?php
			}
			else {
				$erreur = "Vous ne pouvez pas modifier une réponse donc vous n'êtes pas l'auteur";
			}
		}
		else{
			$req1 = 'UPDATE reponses SET contenu=\''.$_POST['contenu'].'\' WHERE id=\''.$_GET['id'].'\'';
			$information = "La réponse a bien été modifiée";
			$ex = mysqli_query($base,'SELECT * FROM reponses WHERE id=\''.$_GET['id'].'\'');
			$reponse = mysqli_fetch_array($ex);
			mysqli_query($base,'DELETE FROM statutforum WHERE idsujet=\''.$reponse['idsujet'].'\'') or die('Erreur SQL !<br />'.mysql_error());
		}
		mysqli_query($base,$req1) or die('Erreur SQL !<br />'.mysql_error());
	}
}

include("includes/tout.php");

debutCarte("Editer");

if(isset($_GET['id']) AND isset($_GET['type']) AND preg_match("#^[0-9]*$#", $_GET['id'])) {
	// Modification du sujet
	if($_GET['type'] == 1) {
		$ex = mysqli_query($base,'SELECT * FROM sujets WHERE id=\''.$_GET['id'].'\'');
	}
	// Modification d'un des messages
	else {
		$ex = mysqli_query($base,'SELECT * FROM reponses WHERE id=\''.$_GET['id'].'\'');
	}
	$reponse = mysqli_fetch_array($ex);
	$nbReponses = mysqli_num_rows($ex);
	if($_GET['id'] != 1 && $_GET['type'] == 2) { // si c'est un message il n'y a pas de titre
		$reponse['titre'] = "";
	}
	
	if($nbReponses == 1) {
		debutListe();
        echo '<form method="post" action="" name="formEditer">';
		if($_GET['type'] == 1) { 
            item(['titre' => 'Titre', "floating" => true, 'input'=> '<input type="text" name="titre" id="titre" value="'.$reponse['titre'].'"/>']);
		}
        
        creerBBcode("contenu", $reponse['contenu']);
        item(['floating' => false, 'titre' => "Réponse", 'input' => '<textarea name="contenu" id="contenu" rows="10" cols="50">'.$reponse['contenu'].'</textarea>']);
        item(['input' => submit(['titre' => 'Editer', 'form'=>'formEditer'])]);
        echo '</form>';
		finListe();
	}
	else {
		if($_GET['id'] != 3) {
			echo 'Ce sujet ou cette réponse n\'existe pas !';
		}
	}
}
else {
	echo 'Stop jouer avec la barre URL espèce de troll !';
}

finCarte();
include("includes/copyright.php");
