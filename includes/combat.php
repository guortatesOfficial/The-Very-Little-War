<?php
// Récupération des variables d'attaque, de défense, de coup critiques et de capacité de pillage pour chaque classe
// Pour l'attaquant

$sqlClasse1 = 'SELECT * FROM molecules WHERE proprietaire=\'' . $actions['defenseur'] . '\' ORDER BY numeroclasse ASC';
$exClasse1 = mysqli_query($base, $sqlClasse1) or die('Erreur SQL !<br />' . $sqlClasse1 . '<br />' . mysqli_error($base));


$c = 1;
while ($classeDefenseur = mysqli_fetch_array($exClasse1)) {
	${'classeDefenseur' . $c} = $classeDefenseur;
	${'classeDefenseur' . $c}['nombre'] = ceil(${'classeDefenseur' . $c}['nombre']);

	$c++;
}

$sqlClasse1 = 'SELECT * FROM molecules WHERE proprietaire=\'' . $actions['attaquant'] . '\' ORDER BY numeroclasse ASC';
$exClasse1 = mysqli_query($base, $sqlClasse1) or die('Erreur SQL !<br />' . $sqlClasse1 . '<br />' . mysqli_error($base));


$c = 1;
$chaineExplosee = explode(";", $actions['troupes']);
while ($classeAttaquant = mysqli_fetch_array($exClasse1)) {
	${'classeAttaquant' . $c} = $classeAttaquant;
	${'classeAttaquant' . $c}['nombre'] = ceil($chaineExplosee[$c - 1]); // on prends le nombre d'unites en attaque

	$c++;
}

// recupération des niveaux des atomes

$exNiveaux = query('SELECT pointsCondenseur FROM constructions WHERE login=\'' . $actions['attaquant'] . '\'');
$niveauxAttaquant = mysqli_fetch_array($exNiveaux);
$niveauxAttaquant = explode(";", $niveauxAttaquant['pointsCondenseur']);
foreach ($nomsRes as $num => $ressource) {
	$niveauxAtt[$ressource] = $niveauxAttaquant[$num];
}

$exNiveaux = query('SELECT pointsCondenseur FROM constructions WHERE login=\'' . $actions['defenseur'] . '\'');
$niveauxDefenseur = mysqli_fetch_array($exNiveaux);
$niveauxDefenseur = explode(";", $niveauxDefenseur['pointsCondenseur']);
foreach ($nomsRes as $num => $ressource) {
	$niveauxDef[$ressource] = $niveauxDefenseur[$num];
}


$sqlionisateur = 'SELECT ionisateur FROM constructions WHERE login=\'' . $actions['attaquant'] . '\'';
$exionisateur = mysqli_query($base, $sqlionisateur) or die('Erreur SQL !<br />' . $sqlionisateur . '<br />' . mysqli_error($base));
$ionisateur = mysqli_fetch_array($exionisateur);

$sqlchampdeforce = 'SELECT champdeforce FROM constructions WHERE login=\'' . $actions['defenseur'] . '\'';
$exchampdeforce = mysqli_query($base, $sqlchampdeforce) or die('Erreur SQL !<br />' . $sqlchampdeforce . '<br />' . mysqli_error($base));
$champdeforce = mysqli_fetch_array($exchampdeforce);

$exDuplicateurAttaque = mysqli_query($base, 'SELECT idalliance FROM autre WHERE login=\'' . $actions['attaquant'] . '\'');
$idalliance = mysqli_fetch_array($exDuplicateurAttaque);
$bonusDuplicateurAttaque = 1;
if ($idalliance['idalliance'] > 0) {
	$exDuplicateurAttaque = mysqli_query($base, 'SELECT duplicateur FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'');
	$duplicateurAttaque = mysqli_fetch_array($exDuplicateurAttaque);
	$bonusDuplicateurAttaque = 1 + ((0.1 * $duplicateurAttaque['duplicateur']) / 100);
}

$exDuplicateurDefense = mysqli_query($base, 'SELECT idalliance FROM autre WHERE login=\'' . $actions['attaquant'] . '\'');
$idallianceDef = mysqli_fetch_array($exDuplicateurDefense);
$bonusDuplicateurDefense = 1;
if ($idallianceDef['idalliance'] > 0) {
	$exDuplicateurDefense = mysqli_query($base, 'SELECT duplicateur FROM alliances WHERE id=\'' . $idallianceDef['idalliance'] . '\'');
	$duplicateurDefense = mysqli_fetch_array($exDuplicateurDefense);
	$bonusDuplicateurDefense = 1 + ((0.1 * $duplicateurDefense['duplicateur']) / 100);
}


// Calcul des dégâts totaux de l'attaquant (avec coups critiques)
// mettre les niveaux et pour le brome et soufre et tout ca avec les bonnes fonctions
$degatsAttaquant = 0;
$degatsDefenseur = 0;
for ($c = 1; $c <= 4; $c++) {
	$degatsAttaquant += attaque(${'classeAttaquant' . $c}['oxygene'], $niveauxAtt['oxygene'], $actions['attaquant']) * (1 + (($ionisateur['ionisateur'] * 2) / 100)) * $bonusDuplicateurAttaque * ${'classeAttaquant' . $c}['nombre'];
	$degatsDefenseur += defense(${'classeDefenseur' . $c}['carbone'], $niveauxDef['carbone'], $actions['defenseur']) * (1 + (($champdeforce['champdeforce'] * 2) / 100)) * $bonusDuplicateurDefense * ${'classeDefenseur' . $c}['nombre'];
}



// Calcul des pertes 

// Attaquants

$degatsUtilises = 0;
$attaquantsRestants = 0;
$defenseursRestants = 0;

for ($i = 1; $i <= $nbClasses; $i++) {
	${'classe' . $i . 'AttaquantMort'} = 0;
	if (${'classeAttaquant' . $i}['nombre'] > 0 and $degatsUtilises < $degatsDefenseur) {
		if (${'classeAttaquant' . $i}['brome'] > 0) {
			${'classe' . $i . 'AttaquantMort'} = floor(($degatsDefenseur - $degatsUtilises) / (pointsDeVieMolecule(${'classeAttaquant' . $i}['brome'], $niveauxAtt['brome']) * $bonusDuplicateurAttaque));
			if (${'classe' . $i . 'AttaquantMort'} >= ${'classeAttaquant' . $i}['nombre']) {
				${'classe' . $i . 'AttaquantMort'} = ${'classeAttaquant' . $i}['nombre'];
			}
		} else {
			if ($degatsDefenseur > 0) {
				${'classe' . $i . 'AttaquantMort'} = ${'classeAttaquant' . $i}['nombre'];
			}
		}

		if (${'classe' . $i . 'AttaquantMort'} < ${'classeAttaquant' . $i}['nombre']) {
			$degatsUtilises = $degatsAttaquant;
		} else {
			$degatsUtilises = $degatsUtilises + ${'classe' . $i . 'AttaquantMort'} * pointsDeVieMolecule(${'classeAttaquant' . $i}['brome'], $niveauxAtt['brome']) * $bonusDuplicateurAttaque;
		}
	}
	$attaquantsRestants += ${'classeAttaquant' . $i}['nombre'] - ${'classe' . $i . 'AttaquantMort'};
}

$degatsUtilises = 0;
for ($i = 1; $i <= 4; $i++) {
	${'classe' . $i . 'DefenseurMort'} = 0;
	if (${'classeDefenseur' . $i}['nombre'] > 0 and $degatsUtilises < $degatsAttaquant) {
		if (${'classeDefenseur' . $i}['brome'] > 0) {
			${'classe' . $i . 'DefenseurMort'} = floor(($degatsAttaquant - $degatsUtilises) / (pointsDeVieMolecule(${'classeDefenseur' . $i}['brome'], $niveauxDef['brome']) * $bonusDuplicateurDefense));
			if (${'classe' . $i . 'DefenseurMort'} >= ${'classeDefenseur' . $i}['nombre']) {
				${'classe' . $i . 'DefenseurMort'} = ${'classeDefenseur' . $i}['nombre'];
			}
		} else {
			if ($degatsAttaquant > 0) {
				${'classe' . $i . 'DefenseurMort'} = ${'classeDefenseur' . $i}['nombre'];
			}
		}

		if (${'classe' . $i . 'DefenseurMort'} < ${'classeDefenseur' . $i}['nombre']) {
			$degatsUtilises = $degatsAttaquant;
		} else {
			$degatsUtilises = $degatsUtilises + ${'classe' . $i . 'DefenseurMort'} * pointsDeVieMolecule(${'classeDefenseur' . $i}['brome'], $niveauxDef['brome']) * $bonusDuplicateurDefense;
		}
	}
	$defenseursRestants += ${'classeDefenseur' . $i}['nombre'] - ${'classe' . $i . 'DefenseurMort'};
}

if ($attaquantsRestants == 0) {
	if ($defenseursRestants == 0) {
		$gagnant = 0;
	} else {
		$gagnant = 1;
	}
} else {
	if ($defenseursRestants == 0) {
		$gagnant = 2;
	} else {
		$gagnant = 0;
	}
}

// On met à jour les troupes des deux joueurs

//$actions['troupes'] //
$chaine = '';
for ($i = 1; $i <= $nbClasses; $i++) {
	$chaine = $chaine . (${'classeAttaquant' . $i}['nombre'] - ${'classe' . $i . 'AttaquantMort'}) . ';';
}

$actions['troupes'] = $chaine;
query('UPDATE actionsattaques SET troupes=\'' . $chaine . '\' WHERE id=\'' . $actions['id'] . '\'');

// defenseur
$sqlUpdateDefenseur1 = 'UPDATE molecules SET nombre=\'' . ($classeDefenseur1['nombre'] - $classe1DefenseurMort) . '\' WHERE id=\'' . $classeDefenseur1['id'] . '\'';
$sqlUpdateDefenseur2 = 'UPDATE molecules SET nombre=\'' . ($classeDefenseur2['nombre'] - $classe2DefenseurMort) . '\' WHERE id=\'' . $classeDefenseur2['id'] . '\'';
$sqlUpdateDefenseur3 = 'UPDATE molecules SET nombre=\'' . ($classeDefenseur3['nombre'] - $classe3DefenseurMort) . '\' WHERE id=\'' . $classeDefenseur3['id'] . '\'';
$sqlUpdateDefenseur4 = 'UPDATE molecules SET nombre=\'' . ($classeDefenseur4['nombre'] - $classe4DefenseurMort) . '\' WHERE id=\'' . $classeDefenseur4['id'] . '\'';

mysqli_query($base, $sqlUpdateDefenseur1) or die('Erreur SQL !<br />' . $sqlUpdateDefenseur1 . '<br />' . mysqli_error($base));
mysqli_query($base, $sqlUpdateDefenseur2) or die('Erreur SQL !<br />' . $sqlUpdateDefenseur2 . '<br />' . mysqli_error($base));
mysqli_query($base, $sqlUpdateDefenseur3) or die('Erreur SQL !<br />' . $sqlUpdateDefenseur3 . '<br />' . mysqli_error($base));
mysqli_query($base, $sqlUpdateDefenseur4) or die('Erreur SQL !<br />' . $sqlUpdateDefenseur4 . '<br />' . mysqli_error($base));

// Gestion du pillage
$sql = 'SELECT * FROM ressources WHERE login=\'' . $actions['defenseur'] . '\'';
$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
$ressourcesDefenseur = mysqli_fetch_array($ex);

$sql1 = 'SELECT * FROM ressources WHERE login=\'' . $actions['attaquant'] . '\'';
$ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
$ressourcesJoueur = mysqli_fetch_array($ex1);
if ($gagnant == 2 || $gagnant == 0) { // Si le joueur gagnant est l'attaquant
	$ressourcesTotalesDefenseur = 0;
	foreach ($nomsRes as $num => $ressource) {
		$ressourcesTotalesDefenseur = $ressourcesTotalesDefenseur +	$ressourcesDefenseur[$ressource];
	} // On calcule les ressources totales du défenseur

	if ($ressourcesTotalesDefenseur != 0) { // Si elles sont différentes de zéro (pas de division par zéro)
		$ressourcesAPiller = ($classeAttaquant1['nombre'] - $classe1AttaquantMort) * pillage($classeAttaquant1['soufre'], $niveauxAtt['soufre'], $actions['attaquant']) + // Calcul des ressources totales que peut piller l'attaquant
			($classeAttaquant2['nombre'] - $classe2AttaquantMort) * pillage($classeAttaquant2['soufre'], $niveauxAtt['soufre'], $actions['attaquant']) +
			($classeAttaquant3['nombre'] - $classe3AttaquantMort) * pillage($classeAttaquant3['soufre'], $niveauxAtt['soufre'], $actions['attaquant']) +
			($classeAttaquant4['nombre'] - $classe4AttaquantMort) * pillage($classeAttaquant4['soufre'], $niveauxAtt['soufre'], $actions['attaquant']);

		// Calcul du pourcentage de chaque ressource chez le défenseur
		foreach ($nomsRes as $num => $ressource) {
			${'rapport' . $ressource} = $ressourcesDefenseur[$ressource] / $ressourcesTotalesDefenseur;
			if ($ressourcesTotalesDefenseur > $ressourcesAPiller) {
				${$ressource . 'Pille'} = floor($ressourcesAPiller * ${'rapport' . $ressource});
			} else {
				${$ressource . 'Pille'} = floor($ressourcesDefenseur[$ressource]);
			}
		}
	} else {
		foreach ($nomsRes as $num => $ressource) {
			${$ressource . 'Pille'} = 0;
		}
	}
} else {
	foreach ($nomsRes as $num => $ressource) {
		${$ressource . 'Pille'} = 0;
	}
}

//Gestion de la destruction des bâtiments ennemis
$hydrogeneTotal = ($classeAttaquant1['nombre'] - $classe1AttaquantMort) * potentielDestruction($classeAttaquant1['hydrogene'], $niveauxAtt['hydrogene']) + // Calcul des degats que va faire l'attaquant
	($classeAttaquant2['nombre'] - $classe2AttaquantMort) * potentielDestruction($classeAttaquant2['hydrogene'], $niveauxAtt['hydrogene']) +
	($classeAttaquant3['nombre'] - $classe3AttaquantMort) * potentielDestruction($classeAttaquant3['hydrogene'], $niveauxAtt['hydrogene']) +
	($classeAttaquant4['nombre'] - $classe4AttaquantMort) * potentielDestruction($classeAttaquant4['hydrogene'], $niveauxAtt['hydrogene']);
$degatsGenEnergie = 0;
$degatschampdeforce = 0;
$degatsDepot = 0;
$degatsProducteur = 0;
$pointsDefenseur = 0;
$points = 0;
$destructionGenEnergie = "Non endommagé";
$destructionProducteur = "Non endommagé";
$destructionchampdeforce = "Non endommagé";
$destructionDepot = "Non endommagé";

$ex = mysqli_query($base, 'SELECT * FROM constructions WHERE login=\'' . $actions['defenseur'] . '\'');
$constructions = mysqli_fetch_array($ex);

if ($hydrogeneTotal > 0) { // si il y a de l'hydrogène
	// gestion des degats infligés

	if ($constructions['champdeforce'] > $constructions['generateur'] && $constructions['champdeforce'] > $constructions['producteur'] && $constructions['champdeforce'] > $constructions['depot']) {
		for ($i = 1; $i <= $nbClasses; $i++) {
			if (${'classeAttaquant' . $i}['hydrogene'] > 0) {
				$degatsAMettre = potentielDestruction(${'classeAttaquant' . $i}['hydrogene'], $niveauxAtt['hydrogene']) * ${'classeAttaquant' . $i}['nombre'];
				$degatschampdeforce += $degatsAMettre;
			}
		}
	} else {
		for ($i = 1; $i <= $nbClasses; $i++) {
			if (${'classeAttaquant' . $i}['hydrogene'] > 0) {
				$bat = rand(1, 4);
				$degatsAMettre = potentielDestruction(${'classeAttaquant' . $i}['hydrogene'], $niveauxAtt['hydrogene']) * ${'classeAttaquant' . $i}['nombre'];
				switch ($bat) {
					case 1:
						$degatsGenEnergie += $degatsAMettre;
						break;
					case 2:
						$degatschampdeforce += $degatsAMettre;
						break;
					case 3:
						$degatsProducteur += $degatsAMettre;
						break;
					default:
						$degatsDepot += $degatsAMettre;
						break;
				}
			}
		}
	}

	//gestion des destructions de batiments
	if ($degatsGenEnergie > 0) {
		$destructionGenEnergie = round($constructions['vieGenerateur'] / pointsDeVie($constructions['generateur']) * 100) . "% <img alt=\"fleche\" src=\"images/attaquer/arrow.png\"/ class=\"w16\" style=\"vertical-align:middle\"> " . max(round(($constructions['vieGenerateur'] - $degatsGenEnergie) / pointsDeVie($constructions['generateur']) * 100), 0) . "%";
		if ($degatsGenEnergie >= $constructions['vieGenerateur']) {
			if ($constructions['generateur'] > 1) {
				diminuerBatiment("generateur", $actions['defenseur']);
			} else {
				$degatsGenEnergie = 0;
				$destructionGenEnergie = "Niveau minimum";
			}
		} else {
			mysqli_query($base, 'UPDATE constructions SET vieGenerateur=\'' . ($constructions['vieGenerateur'] - $degatsGenEnergie) . '\' WHERE login=\'' . $actions['defenseur'] . '\'');
		}
	}
	if ($degatschampdeforce > 0) {
		$destructionchampdeforce = round($constructions['vieChampdeforce'] / vieChampDeForce($constructions['champdeforce']) * 100) . "% <img alt=\"fleche\" src=\"images/attaquer/arrow.png\"/ class=\"w16\" style=\"vertical-align:middle\"> " . max(round(($constructions['vieChampdeforce'] - $degatschampdeforce) / vieChampDeForce($constructions['champdeforce']) * 100), 0) . "%";
		if ($degatschampdeforce >= $constructions['vieChampdeforce']) {
			if ($constructions['champdeforce'] > 0) {
				diminuerBatiment("champdeforce", $actions['defenseur']);
			} else {
				$degatschampdeforce = 0;
				$destructionchampdeforce = "Niveau minimum";
			}
		} else {
			mysqli_query($base, 'UPDATE constructions SET vieChampdeforce=\'' . ($constructions['vieChampdeforce'] - $degatschampdeforce) . '\' WHERE login=\'' . $actions['defenseur'] . '\'');
		}
	}
	if ($degatsProducteur > 0) {
		$destructionProducteur = round($constructions['vieProducteur'] / pointsDeVie($constructions['producteur']) * 100) . "% <img alt=\"fleche\" src=\"images/attaquer/arrow.png\"/ class=\"w16\" style=\"vertical-align:middle\"> " . max(round(($constructions['vieProducteur'] - $degatsProducteur) / pointsDeVie($constructions['producteur']) * 100), 0) . "%";
		if ($degatsProducteur >= $constructions['vieProducteur']) {
			if ($constructions['producteur'] > 0) {
				diminuerBatiment("producteur", $actions['defenseur']);
			} else {
				$degatsProducteur = 0;
				$destructionProducteur = "Niveau minimum";
			}
		} else {
			mysqli_query($base, 'UPDATE constructions SET vieProducteur=\'' . ($constructions['vieProducteur'] - $degatsProducteur) . '\' WHERE login=\'' . $actions['defenseur'] . '\'');
		}
	}
	if ($degatsDepot > 0) {
		$destructionDepot = round($constructions['vieDepot'] / pointsDeVie($constructions['depot']) * 100) . "% <img alt=\"fleche\" src=\"images/attaquer/arrow.png\"/ class=\"w16\" style=\"vertical-align:middle\"> " . max(round(($constructions['vieDepot'] - $degatsDepot) / pointsDeVie($constructions['depot']) * 100), 0) . "%";
		if ($degatsDepot >= $constructions['vieDepot']) {
			if ($constructions['depot'] > 1) {
				diminuerBatiment("depot", $actions['defenseur']);
			} else {
				$degatsDepot = 0;
				$destructionDepot = "Niveau minimum";
			}
		} else {
			mysqli_query($base, 'UPDATE constructions SET vieDepot=\'' . ($constructions['vieDepot'] - $degatsDepot) . '\' WHERE login=\'' . $actions['defenseur'] . '\'');
		}
	}
}

// calcul des stats de combat

$pertesAttaquant = $classe1AttaquantMort + $classe2AttaquantMort + $classe3AttaquantMort + $classe4AttaquantMort;
$pertesDefenseur = $classe1DefenseurMort + $classe2DefenseurMort + $classe3DefenseurMort + $classe4DefenseurMort;

$pointsAttaquant = 0;
$pointsDefenseur = 0;

$ex = query('SELECT points,pointsAttaque,pointsDefense,totalPoints FROM autre WHERE login=\'' . $actions['attaquant'] . '\'');
$pointsBDAttaquant = mysqli_fetch_array($ex);
$ex = query('SELECT points,pointsAttaque,pointsDefense,totalPoints FROM autre WHERE login=\'' . $actions['defenseur'] . '\'');
$pointsBDDefenseur = mysqli_fetch_array($ex);

if ($gagnant == 1) { // DEFENSEUR
	if ($pointsBDAttaquant['totalPoints'] >= $pointsBDDefenseur['totalPoints']) {
		$pointsAttaquant += -1;
		$pointsDefenseur += 1;
	}
	if ($pointsBDAttaquant['pointsAttaque'] >= $pointsBDDefenseur['pointsDefense']) {
		$pointsAttaquant += -1;
		$pointsDefenseur += 1;
	}
} else if ($gagnant == 2 && $pertesDefenseur > 0) { // ATTAQUANT
	if ($pointsBDAttaquant['totalPoints'] <= $pointsBDDefenseur['totalPoints']) {
		$pointsAttaquant += 1;
		$pointsDefenseur += -1;
	}
	if ($pointsBDAttaquant['pointsAttaque'] <= $pointsBDDefenseur['pointsDefense']) {
		$pointsAttaquant += 1;
		$pointsDefenseur += -1;
	}
}

$totalPille = 0;
foreach ($nomsRes as $num => $ressource) {
	$totalPille += ${$ressource . 'Pille'};
}

// update des stats de combat

$ex = mysqli_query($base, 'SELECT moleculesPerdues,ressourcesPillees FROM autre WHERE login=\'' . $actions['attaquant'] . '\'');
$perduesAttaquant = mysqli_fetch_array($ex);

$ex1 = mysqli_query($base, 'SELECT moleculesPerdues FROM autre WHERE login=\'' . $actions['defenseur'] . '\'');
$perduesDefenseur = mysqli_fetch_array($ex1);

$attaquePts = ajouterPoints($pointsAttaquant, $actions['attaquant'], 1);
$pillagePts = ajouterPoints($totalPille, $actions['attaquant'], 3);
$defensePts = ajouterPoints($pointsDefenseur, $actions['defenseur'], 2);
$pillagePts1 = ajouterPoints(-$totalPille, $actions['defenseur'], 3);

mysqli_query($base, 'UPDATE autre SET moleculesPerdues=\'' . ($pertesAttaquant + $perduesAttaquant['moleculesPerdues']) . '\' WHERE login=\'' . $actions['attaquant'] . '\'');
mysqli_query($base, 'UPDATE autre SET moleculesPerdues=\'' . ($pertesDefenseur + $perduesDefenseur['moleculesPerdues']) . '\' WHERE login=\'' . $actions['defenseur'] . '\'');




// On met à jour les ressources
$chaine = "";
foreach ($nomsRes as $num => $ressource) {
	$plus = "";
	if ($num < $nbRes) {
		$plus = ",";
	}
	$chaine = $chaine . '' . $ressource . '=' . ($ressourcesJoueur[$ressource] + ${$ressource . 'Pille'}) . '' . $plus;
}

$sql = 'UPDATE ressources SET ' . $chaine . ' WHERE login=\'' . $actions['attaquant'] . '\'';
mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));

$chaine = "";
foreach ($nomsRes as $num => $ressource) {
	$plus = "";
	if ($num < $nbRes) {
		$plus = ",";
	}
	$chaine = $chaine . '' . $ressource . '=' . ($ressourcesDefenseur[$ressource] - ${$ressource . 'Pille'}) . '' . $plus;
}

$sql1 = 'UPDATE ressources SET ' . $chaine . ' WHERE login=\'' . $actions['defenseur'] . '\'';
mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));

$sql2 = 'SELECT nbattaques FROM autre WHERE login=\'' . $actions['attaquant'] . '\'';
$ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
$nbattaques = mysqli_fetch_array($ex2);

$sql3 = 'UPDATE autre SET nbattaques=\'' . ($nbattaques['nbattaques'] + 1) . '\' WHERE login=\'' . $actions['attaquant'] . '\'';
mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysqli_error($base));

// Si les alliances sont en guerre on inscrit les pertes

$exGuerre = mysqli_query($base, 'SELECT idalliance FROM autre WHERE login=\'' . $actions['attaquant'] . '\'');
$joueur = mysqli_fetch_array($exGuerre);

$exGuerre = mysqli_query($base, 'SELECT idalliance FROM autre WHERE login=\'' . $actions['defenseur'] . '\'');
$idallianceAutre = mysqli_fetch_array($exGuerre);

$exGuerre = mysqli_query($base, 'SELECT * FROM declarations WHERE type=0 AND fin=0 AND ((alliance1=\'' . $joueur['idalliance'] . '\' AND alliance2=\'' . $idallianceAutre['idalliance'] . '\') OR  (alliance2=\'' . $joueur['idalliance'] . '\' AND alliance1=\'' . $idallianceAutre['idalliance'] . '\'))');
$guerre = mysqli_fetch_array($exGuerre);
$nbGuerres = mysqli_num_rows($exGuerre);
if ($nbGuerres >=  1) {
	if ($guerre['alliance1'] == $joueur['idalliance']) {
		mysqli_query($base, 'UPDATE declarations SET pertes1=\'' . ($guerre['pertes1'] + $pertesAttaquant) . '\', pertes2=\'' . ($guerre['pertes2'] + $pertesDefenseur) . '\'WHERE id=\'' . $guerre['id'] . '\'');
	} else {
		mysqli_query($base, 'UPDATE declarations SET pertes1=\'' . ($guerre['pertes1'] + $pertesDefenseur) . '\', pertes2=\'' . ($guerre['pertes2'] + $pertesAttaquant) . '\'WHERE id=\'' . $guerre['id'] . '\'');
	}
}
