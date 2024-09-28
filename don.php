<?php 
include("includes/basicprivatephp.php");

if(isset($_POST['energieEnvoyee'])) {
	if(empty($_POST['energieEnvoyee'])) {
		$_POST['energieEnvoyee'] = 0;
	}
	$_POST['energieEnvoyee'] = transformInt($_POST['energieEnvoyee']);
	if(preg_match("#^[0-9]*$#", $_POST['energieEnvoyee'])) {
		$ex = mysqli_query($base,'SELECT idalliance FROM autre WHERE login=\''.$_SESSION['login'].'\'');
		$idalliance = mysqli_fetch_array($ex);
		
		if($idalliance['idalliance'] > 0) {
			$ex = mysqli_query($base,'SELECT count(*) AS verificationAlliance FROM alliances WHERE id=\''.$idalliance['idalliance'].'\'');
			$verification = mysqli_fetch_array($ex);
			
			if($verification['verificationAlliance'] > 0) {
					$ex = mysqli_query($base,'SELECT energie FROM ressources WHERE login=\''.$_SESSION['login'].'\'');
					$ressources = mysqli_fetch_array($ex);
					
					if($ressources['energie'] >= $_POST['energieEnvoyee']) {
					
					$ex = mysqli_query($base,'SELECT energieDonnee FROM autre WHERE login=\''.$_SESSION['login'].'\'');
					$energieDonnee = mysqli_fetch_array($ex);
					
					
					$ex = mysqli_query($base,'SELECT energieAlliance, energieTotaleRecue FROM alliances WHERE id=\''.$idalliance['idalliance'].'\'');
					$ressourcesAlliance = mysqli_fetch_array($ex);
					
					mysqli_query($base,'UPDATE ressources SET energie=\''.($ressources['energie'] - $_POST['energieEnvoyee']).'\' WHERE login=\''.$_SESSION['login'].'\'');
					mysqli_query($base,'UPDATE autre SET energieDonnee=\''.($energieDonnee['energieDonnee'] + $_POST['energieEnvoyee']).'\' WHERE login=\''.$_SESSION['login'].'\'');
					mysqli_query($base,'UPDATE alliances SET energieAlliance=\''.($ressourcesAlliance['energieAlliance'] + $_POST['energieEnvoyee']).'\', energieTotaleRecue=\''.($ressourcesAlliance['energieTotaleRecue'] + $_POST['energieEnvoyee']).'\' WHERE id=\''.$idalliance['idalliance'].'\'');
					$information = "Le don a bien été reçu !";
                    echo '<script>document.location.href=\'alliance.php?information='.$information.'\';</script>';
				}
				else {
					$erreur = "Vous n'avez pas assez d'energie.";
				}
			}
			else {
				$erreur = "Cette alliance n'existe pas.";
			}
		}
		else {
			$erreur = "Vous n'avez pas d'alliance.";
		}
	}
	else {
		$erreur = "Seul des nombres entiers et positifs doivent être entrés.";
	}
}

include("includes/tout.php");

debutCarte("Faire un don");
    debutListe();
        echo '<form name="faireUnDon" method="post" action="don.php" />';
        item(['floating' => false, 'titre' => nombreEnergie('Energie'), 'input' => '<input type="text" name="energieEnvoyee" id="energieEnvoyee" class="form-control" placeholder="Energie à donner"/>' ]);
        item(['input' => submit(['form' => 'faireUnDon', 'titre' => 'Donner', 'image' => 'images/boutons/cadeau.png'])]);
        echo '</form>';
    finListe();
finCarte();
include("includes/copyright.php"); ?>