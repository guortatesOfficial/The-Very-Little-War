<?php 
include("includes/basicprivatephp.php");

include("includes/tout.php");

if(isset($_GET['id']) AND !empty($_GET['id'])) {
	$_GET['id'] = antiXSS($_GET['id']);
	$sql = 'SELECT * FROM molecules WHERE id=\''.$_GET['id'].'\' AND proprietaire=\''.$_SESSION['login'].'\'';
	$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$molecule = mysqli_fetch_array($ex);
	$nb_resultats = mysqli_num_rows($ex);
    
    debutCarte('Statistiques de la classe');
    
	if($nb_resultats > 0){
    
    $totalAtomes = 0;
	$mx = $molecule['oxygene'];
	foreach($nomsRes as $num => $ressource) {
		$mx = max($mx,$molecule[$ressource]);
        $totalAtomes += $molecule[$ressource];
	}
	foreach($nomsRes as $num => $ressource) {
		if($mx == $molecule[$ressource]) {
			$img = $ressource;
		}
	}
        
        $demivie = affichageTemps(demiVie($_SESSION['login'],$molecule['numeroclasse'])); 
        
        debutListe();
        item(['titre' => 'Formule', 'soustitre' => couleurFormule($molecule['formule']), 'media' => '<img alt="moelcule" src="images/molecule/formule.png" class="imageMedia"/>']);
        item(['titre' => 'Quantité', 'soustitre' => (separerZeros($molecule['nombre'])), 'media' => '<img alt="moelcule" src="images/molecule/molecule.png" class="imageMedia"/>']);
	    item(['titre' => 'Attaque', 'soustitre' => attaque($molecule['oxygene'],$niveauoxygene,$_SESSION['login']), 'media' => '<img alt="moelcule" src="images/molecule/sword.png" class="imageMedia"/>']);
        item(['titre' => 'Défense', 'soustitre' => defense($molecule['carbone'],$niveaucarbone,$_SESSION['login']), 'media' => '<img alt="moelcule" src="images/molecule/shield.png" class="imageMedia"/>']);
        item(['titre' => 'Points de vie', 'soustitre' => pointsDeVieMolecule($molecule['brome'],$niveaubrome), 'media' => '<img alt="moelcule" src="images/molecule/sante.png" class="imageMedia"/>']);
        item(['titre' => 'Vitesse', 'soustitre' => vitesse($molecule['chlore'],$niveauchlore).' cases/heure', 'media' => '<img alt="moelcule" src="images/molecule/vitesse.png" class="imageMedia"/>']);
        item(['titre' => 'Dégâts aux bâtiments', 'soustitre' => potentielDestruction($molecule['hydrogene'],$niveauhydrogene), 'media' => '<img alt="moelcule" src="images/molecule/fire.png" class="imageMedia"/>']);
        item(['titre' => 'Temps de formation', 'soustitre' => affichageTemps(tempsFormation($molecule['azote'],$niveauazote,$totalAtomes,$_SESSION['login']),true), 'media' => '<img alt="moelcule" src="images/molecule/temps.png" class="imageMedia"/>']);
        // affichage en petitTemps si <60 secondes dans affichageTemps(..,true)
        item(['titre' => 'Capacité de pillage', 'soustitre' => pillage($molecule['soufre'],$niveausoufre,$_SESSION['login']), 'media' => '<img alt="moelcule" src="images/molecule/bag.png" class="imageMedia"/>']);
        item(['titre' => 'Production d\'énergie', 'soustitre' => nombreEnergie('<span style="color:green">+'.productionEnergieMolecule($molecule['iode'],$niveauiode).'/h</span>'), 'media' => '<img alt="moelcule" src="images/energie.png" class="imageMedia"/>']);
        item(['titre' => 'Demi-vie', 'soustitre' => $demivie, 'media' => '<img alt="moelcule" src="images/molecule/demivie.png" class="imageMedia"/>']);
        finListe();
	}
	else {
		echo "Cette molecule n'existe pas ou ne vous appartient pas.";
	}
    finCarte();
}
else {
    debutCarte("Bonjour");
    debutContent();
	echo "Trés amusant de changer les variables dans la barre URL non ?";
    finContent();
    finCarte('Au revoir');
}

include("includes/copyright.php"); ?>
