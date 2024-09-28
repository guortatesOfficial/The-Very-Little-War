<?php
session_start();
include("includes/connexion.php");
include("includes/fonctions.php");
if (isset($_GET['inscription'])) {
	$ex = mysqli_query($base,'SELECT numerovisiteur FROM statistiques');
	$nb = mysqli_fetch_array($ex);
	mysqli_query($base,'UPDATE statistiques SET numerovisiteur=\''.($nb['numerovisiteur']+1).'\''); // on ajoute 1 pour avoir un numero différent
	$log = 'Visiteur'.($nb['numerovisiteur']-1);
	$ex = mysqli_query($base,'SELECT timestamp FROM membre WHERE login=\''.$log.'\'');
	$time = mysqli_fetch_array($ex);
	if(time()-$time['timestamp'] > 60){ // pour éviter d'avoir trop de joueurs
		inscrire("Visiteur".$nb['numerovisiteur'],"Visiteur".$nb['numerovisiteur'],"Visiteur".$nb['numerovisiteur']."@tvlw.com");
		$_SESSION['login'] = ucfirst(mb_strtolower(mysqli_real_escape_string($base,stripslashes(htmlentities("Visiteur".$nb['numerovisiteur'])))));
		$_SESSION['mdp'] = md5("Visiteur".$nb['numerovisiteur']);
		header('Location: tutoriel.php?deployer=1');
	}
	else {
		header('Location: index.php?att=1');
	}
}
else {
	//Si les champs sont vides
	if ((isset($_POST['login']) && !empty($_POST['login'])) && (isset($_POST['pass']) && !empty($_POST['pass'])) && (isset($_POST['pass_confirm']) && !empty($_POST['pass_confirm'])) && (isset($_POST['email']) && !empty($_POST['email']))) { 
		//Si les deux mots de passe sont différents
		if ($_POST['pass'] != $_POST['pass_confirm']) { 
			$erreur = 'Les deux mots de passe sont différents.'; 
		}		
		else {
			if(preg_match("#^[A-Za-z0-9]*$#", $_POST['login'])) {
				if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#",$_POST['email'])) {
					$_POST['login'] = ucfirst(mb_strtolower($_POST['login']));
					$sql = 'SELECT count(*) FROM membre WHERE login="'.mysqli_real_escape_string($base,$_POST['login']).'"';
					$req = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());	 
					$data = mysqli_fetch_array($req);
					//Si le login est déjà utilisé
					if($data[0] == 0) {
						$_POST['login'] = mysqli_real_escape_string($base,addslashes(antihtml(trim($_POST['login']))));
						mysqli_query($base,'UPDATE autre SET login=\''.$_POST['login'].'\' WHERE login=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE grade SET login=\''.$_POST['login'].'\' WHERE login=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE constructions SET login=\''.$_POST['login'].'\' WHERE login=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE invitations SET invite=\''.$_POST['login'].'\' WHERE invite=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE membre SET login=\''.$_POST['login'].'\',pass_md5=\''.mysqli_real_escape_string($base,stripslashes(antihtml(trim(md5($_POST['pass']))))).'\',email=\''.mysqli_real_escape_string($base,addslashes(antihtml(trim($_POST['email'])))).'\' WHERE login=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE messages SET destinataire=\''.$_POST['login'].'\' WHERE destinataire=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE messages SET expeditaire=\''.$_POST['login'].'\' WHERE expeditaire=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE moderation SET destinataire=\''.$_POST['login'].'\' WHERE destinataire=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE molecules SET proprietaire=\''.$_POST['login'].'\' WHERE proprietaire=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE rapports SET destinataire=\''.$_POST['login'].'\' WHERE destinataire=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE reponses SET auteur=\''.$_POST['login'].'\' WHERE auteur=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE ressources SET login=\''.$_POST['login'].'\' WHERE login=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE sanctions SET joueur=\''.$_POST['login'].'\' WHERE joueur=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE statutforum SET login=\''.$_POST['login'].'\' WHERE login=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE sujets SET auteur=\''.$_POST['login'].'\' WHERE auteur=\''.$_SESSION['login'].'\'');
						mysqli_query($base,'UPDATE autre SET niveaututo=8 WHERE login=\''.$_POST['login'].'\'');
						$_SESSION['login'] = ucfirst(mb_strtolower(mysqli_real_escape_string($base,stripslashes(htmlentities($_POST['login'])))));
						$_SESSION['mdp'] = md5($_POST['pass']);
						
						echo '<script type="text/javascript">
						window.location.href = "index.php?inscrit=1";
						</script>';
						exit(); 
						} 
					else { 
						$erreur = 'Ce login est déjà utilisé.'; 
					} 
				}
				else {
					$erreur = 'L\'email n\'est pas correct.';
				}
			}
			else {
				$erreur = 'Vous ne pouvez pas utiliser de caractères spéciaux dans votre login';
			}
		} 
	} 
	else { 
		$erreur = 'Un ou plusieurs champs sont vides.'; 
	}
    echo '<script>document.location.href="constructions.php?erreur='.$erreur.'";</script>';
}

