<?php
//Ressources
function statut($joueur)
{
    $req = query('SELECT count(*) AS nb FROM membre WHERE derniereConnexion >=\'' . (time() - 2678400) . '\' AND x!=-1000 AND login=\'' . $joueur . '\'');
    $actifs = mysqli_fetch_array($req);

    if ($actifs['nb'] == 1) {
        return 1;
    } else {
        return 0;
    }
}

function compterActifs()
{
    $ex = query('SELECT count(*) AS nb FROM membre WHERE derniereConnexion >=\'' . (time() - 2678400) . '\' AND x!=-1000');
    $nb = mysqli_fetch_array($ex);

    return $nb['nb'];
}

function pointsVictoireJoueur($classement)
{
    $actifs = compterActifs();
    if ($classement == 1) {
        return 100;
    }
    if ($classement == 2) {
        return 80;
    }
    if ($classement == 3) {
        return 70;
    }
    if ($classement <= 10) {
        return 70 - ($classement - 3) * 5;
    }
    if ($classement <= 20) {
        return 35 - ($classement - 10) * 2;
    }
    if ($classement <= 50) {
        return floor(15 - ($classement - 20) * 0.5);
    }
    return 0;
}

function pointsVictoireAlliance($classement)
{
    if ($classement == 1) {
        return 15;
    }
    if ($classement == 2) {
        return 10;
    }
    if ($classement == 3) {
        return 7;
    }
    if ($classement < 10) {
        return 10 - $classement;
    }

    return 0;
}

function pointsAttaque($nbMorts)
{
    return floor($nbMorts / 500);
}

function pointsDefense($nbMorts)
{
    return floor($nbMorts / 500);
}

function pointsPillage($nbRessources)
{
    return (tanh($nbRessources / 200000) * 15);
}

function bonusDuplicateur($niveau)
{
    return $niveau / 100;
}

function drainageProducteur($niveau)
{
    return round(0.4 * (pow(1.20, $niveau) + pow($niveau, 1.4)));
}

function revenuEnergie($niveau, $joueur, $detail = 0)
{ // BUG ICI 
    global $base;
    global $paliersEnergievore;
    global $bonusMedailles;
    global $nomsRes;

    $sql1 = 'SELECT * FROM constructions WHERE login=\'' . htmlentities(mysqli_real_escape_string($base, stripslashes($joueur))) . '\'';
    $req1 = mysqli_query($base, $sql1);
    $constructions = mysqli_fetch_array($req1);

    $niveauxAtomes = explode(';', $constructions['pointsCondenseur']);
    foreach ($nomsRes as $num => $ressource) {
        ${'niveau' . $ressource} = $niveauxAtomes[$num];
    }

    $ex = query('SELECT producteur FROM constructions WHERE login=\'' . $joueur . '\'');
    $producteur = mysqli_fetch_array($ex);

    $ex = mysqli_query($base, 'SELECT idalliance,totalPoints FROM autre WHERE login=\'' . $joueur . '\'');
    $idalliance = mysqli_fetch_array($ex);
    $bonusDuplicateur = 1;
    if ($idalliance['idalliance'] > 0) {
        $ex = mysqli_query($base, 'SELECT duplicateur FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'');
        $duplicateur = mysqli_fetch_array($ex);
        $bonusDuplicateur = 1 + bonusDuplicateur($duplicateur['duplicateur']);
    }

    //Prise en compte des revenus par l'iode des molecules
    $totalIode = 0;
    for ($i = 1; $i <= 4; $i++) {
        $requete = query('SELECT * FROM molecules WHERE proprietaire=\'' . $joueur . '\' AND numeroclasse=\'' . $i . '\'');
        $molecules = mysqli_fetch_array($requete);
        $totalIode += productionEnergieMolecule($molecules['iode'], $niveauiode) * $molecules['nombre'];
        //A FAIRE COMPTER L'IODE TOTALE ET AJOUTER AUX REVENUS
    }

    $exMedaille = mysqli_query($base, 'SELECT energieDepensee FROM autre WHERE login=\'' . $joueur . '\'');
    $donneesMedaille = mysqli_fetch_array($exMedaille);
    $bonus = 0;

    foreach ($paliersEnergievore as $num => $palier) {
        if ($donneesMedaille['energieDepensee'] >= $palier) {
            $bonus = $bonusMedailles[$num];
        }
    }

    $prodBase = (3 * (pow(1.2, $niveau) + pow($niveau, 1.2)));
    $prodIode = $prodBase + $totalIode;
    $prodMedaille = (1 + ($bonus / 100)) * $prodIode;
    $prodDuplicateur = $bonusDuplicateur * $prodMedaille;
    $prodProducteur = $prodDuplicateur - drainageProducteur($producteur['producteur']);
    if ($detail == 0) {
        return round($prodProducteur);
    } elseif ($detail == 1) {
        return round($prodDuplicateur);
    } elseif ($detail == 2) {
        return round($prodMedaille);
    } elseif ($detail == 3) {
        return round($prodIode);
    } else {
        return round($prodBase);
    }
}


function revenuAtome($num, $joueur)
{
    global $base;

    $ex = mysqli_query($base, 'SELECT pointsProducteur FROM constructions WHERE login=\'' . $joueur . '\'');
    $pointsProducteur = mysqli_fetch_array($ex);

    $niveau = explode(';', $pointsProducteur['pointsProducteur'])[$num];

    $ex = mysqli_query($base, 'SELECT idalliance FROM autre WHERE login=\'' . $joueur . '\'');
    $idalliance = mysqli_fetch_array($ex);
    $bonusDuplicateur = 1;
    if ($idalliance['idalliance'] > 0) {
        $ex = mysqli_query($base, 'SELECT duplicateur FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'');
        $duplicateur = mysqli_fetch_array($ex);
        $bonusDuplicateur = 1 + bonusDuplicateur($duplicateur['duplicateur']);
    }

    return round($bonusDuplicateur * 3 * (pow(1.2, $niveau) + pow($niveau, 1.2)));
}

function revenuAtomeJavascript($joueur)
{
    global $base;

    $ex = mysqli_query($base, 'SELECT idalliance FROM autre WHERE login=\'' . $joueur . '\'');
    $idalliance = mysqli_fetch_array($ex);
    $bonusDuplicateur = 1;
    if ($idalliance['idalliance'] > 0) {
        $ex = mysqli_query($base, 'SELECT duplicateur FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'');
        $duplicateur = mysqli_fetch_array($ex);
        $bonusDuplicateur = 1 + bonusDuplicateur($duplicateur['duplicateur']);
    }

    echo '
    <script>
    function revenuAtomeJavascript(niveau){
        return Math.round(' . $bonusDuplicateur . '*3*(Math.pow(1.2,niveau)+Math.pow(niveau,1.2)));
    }
    </script>
    ';
}

function coutClasse($numero)
{
    global $base;
    return (pow($numero + 1, 6));
}

function placeDepot($niveau)
{
    global $base;
    return 100 + round(240 * (pow(1.20, $niveau) + pow($niveau, 1.4)));
}

//Utilitaires
function attaque($oxygene, $niveau, $joueur)
{
    global $paliersAttaque;
    global $bonusMedailles;

    $exMedaille = query('SELECT pointsAttaque FROM autre WHERE login=\'' . $joueur . '\'');
    $donneesMedaille = mysqli_fetch_array($exMedaille);
    $bonus = 0;

    foreach ($paliersAttaque as $num => $palier) {
        if ($donneesMedaille['pointsAttaque'] >= $palier) {
            $bonus = $bonusMedailles[$num];
        }
    }

    return round((1 + (0.1 * $oxygene) * (0.1 * $oxygene) + $oxygene) * (1 + $niveau / 50) * (1 + $bonus / 100));
}

function defense($carbone, $niveau, $joueur)
{
    global $paliersDefense;
    global $bonusMedailles;

    $exMedaille = query('SELECT pointsDefense FROM autre WHERE login=\'' . $joueur . '\'');
    $donneesMedaille = mysqli_fetch_array($exMedaille);
    $bonus = 0;

    foreach ($paliersDefense as $num => $palier) {
        if ($donneesMedaille['pointsDefense'] >= $palier) {
            $bonus = $bonusMedailles[$num];
        }
    }

    return round((1 + (0.1 * $carbone) * (0.1 * $carbone) + $carbone) * (1 + $niveau / 50) * (1 + $bonus / 100));
}

function pointsDeVieMolecule($brome, $niveau)
{
    return round((1 + (0.1 * $brome) * (0.1 * $brome) + $brome) * (1 + $niveau / 50));
}

function potentielDestruction($hydrogene, $niveau)
{
    return round(((0.075 * $hydrogene) * (0.075 * $hydrogene) + $hydrogene) * (1 + $niveau / 50));
}

function pillage($soufre, $niveau, $joueur)
{
    global $paliersPillage;
    global $bonusMedailles;

    $exMedaille = query('SELECT ressourcesPillees FROM autre WHERE login=\'' . $joueur . '\'');
    $donneesMedaille = mysqli_fetch_array($exMedaille);
    $bonus = 0;

    foreach ($paliersPillage as $num => $palier) {
        if ($donneesMedaille['ressourcesPillees'] >= $palier) {
            $bonus = $bonusMedailles[$num];
        }
    }

    return round(((0.1 * $soufre) * (0.1 * $soufre) + $soufre / 3) * (1 + $niveau / 50) * (1 + $bonus / 100));
}

function productionEnergieMolecule($iode, $niveau)
{
    return round(pow(0.1 * $iode, 1.2) * 60 * (1 + $niveau / 50)) / 100;
}

function vitesse($chlore, $niveau)
{
    return floor((1 + 0.5 * $chlore) * (1 + $niveau / 50) * 100) / 100;
}

function bonusLieur($niveau)
{
    return floor(100 * pow(1.07, $niveau)) / 100;
}

function tempsFormation($azote, $niveau, $ntotal, $joueur)
{
    $ex = query('SELECT lieur FROM constructions WHERE login=\'' . $joueur . '\'');
    $constructions = mysqli_fetch_array($ex);
    return ceil($ntotal / (1 + pow(0.09 * $azote, 1.09)) / (1 + $niveau / 20) / bonusLieur($constructions['lieur']) * 100) / 100;
}


function coefDisparition($joueur, $classeOuNbTotal, $type = 0)
{
    global $base;
    global $nomsRes;
    global $paliersPertes;
    global $bonusMedailles;

    if ($type == 0) {
        $sql = 'SELECT * FROM molecules WHERE proprietaire=\'' . $joueur . '\' AND numeroclasse=\'' . $classeOuNbTotal . '\'';
        $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
        $donnees = mysqli_fetch_array($ex);
    }

    $ex1 = mysqli_query($base, 'SELECT stabilisateur FROM constructions WHERE login=\'' . $joueur . '\'');
    $stabilisateur = mysqli_fetch_array($ex1);

    $exMedaille = mysqli_query($base, 'SELECT moleculesPerdues FROM autre WHERE login=\'' . $joueur . '\'');
    $donneesMedaille = mysqli_fetch_array($exMedaille);
    $bonus = 0;

    foreach ($paliersPertes as $num => $palier) {
        if ($donneesMedaille['moleculesPerdues'] >= $palier) {
            $bonus = $bonusMedailles[$num];
        }
    }

    if ($type == 0) {
        $nbAtomes = 0;
        foreach ($nomsRes as $num => $ressource) {
            $nbAtomes = $nbAtomes + $donnees[$ressource];
        }
    } else {
        $nbAtomes = $classeOuNbTotal;
    }
    return pow(pow(0.99, pow(1 + $nbAtomes / 100, 2) / 5000), (1 - ($bonus / 100)) * (1 - ($stabilisateur['stabilisateur'] * 0.005)));
}

function demiVie($joueur, $classeOuNbTotal, $type = 0)
{
    return round((log(0.5, 0.99) / log(coefDisparition($joueur, $classeOuNbTotal, $type), 0.99)));
}


function pointsDeVie($niveau)
{
    global $base;
    return round(20 * (pow(1.2, $niveau) + pow($niveau, 1.2)));
}

function vieChampDeForce($niveau)
{
    return round(50 * (pow(1.2, $niveau) + pow($niveau, 1.2)));
}

function inscrire($pseudo, $mdp, $mail)
{
    global $base;
    $sql1 = 'SELECT inscrits FROM statistiques';
    $req1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
    $data1 = mysqli_fetch_array($req1);
    $tempsPrecedent = time();
    $nbinscrits = $data1['inscrits'] + 1;

    $alea = mt_rand(1, 200);
    if ($alea <= 100) {
        $alea = 0;
    } elseif ($alea > 100 && $alea <= 150) {
        $alea = 1;
    } elseif ($alea > 150 && $alea <= 175) {
        $alea = 2;
    } elseif ($alea > 175 && $alea <= 187) {
        $alea = 3;
    } elseif ($alea > 187 && $alea <= 193) {
        $alea = 4;
    } elseif ($alea > 193 && $alea <= 197) {
        $alea = 5;
    } elseif ($alea > 197 && $alea <= 199) {
        $alea = 6;
    } else {
        $alea = 7;
    }


    $sql = 'INSERT INTO membre VALUES(default, "' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", "' . mysqli_real_escape_string($base, stripslashes(antihtml(trim(md5($mdp))))) . '", "' . time() . '", "' . $_SERVER['REMOTE_ADDR'] . '", "' . time() . '", 0, "' . $alea . '", 0, 0, "' . mysqli_real_escape_string($base, addslashes(antihtml(trim($mail)))) . '",-1000,-1000)';
    $sql1 = 'INSERT INTO autre VALUES("' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", default, default, "Pas de description", "' . time() . '", default, default, default, default, default, default, default, default,default,default,"' . time() . ',' . time() . ',' . time() . ',' . time() . '",default,default,default,default,"",default)';
    $sql2 = 'INSERT INTO ressources VALUES(default,"' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", default, default, default, default, default, default, default, default, default, default, default, default, default, default, default, default, default, default, default, default)';
    $sql3 = 'UPDATE statistiques SET inscrits=\'' . $nbinscrits . '\'';
    $sql4 = 'INSERT INTO molecules VALUES(default, default, default, default, default, default,default, default, default, default, 1, "' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", default),
	(default, default, default, default, default, default,default, default, default, default, 2, "' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", default),
	(default, default, default, default, default, default,default, default, default, default, 3, "' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", default),
	(default , default, default, default, default, default,default, default, default, default, 4, "' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", default)';
    $sql5 = 'INSERT INTO constructions VALUES("' . mysqli_real_escape_string($base, addslashes(antihtml(trim($pseudo)))) . '", default, default, default, default, default, default, default, default, ' . pointsDeVie(1) . ', ' . vieChampDeForce(0) . ', ' . pointsDeVie(1) . ',' . pointsDeVie(1) . ',default,default,default,default)';

    mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysqli_error($base) . 'sd');
    mysqli_query($base, $sql1) or die('Erreur SQL !' . $sql1 . '<br />' . mysqli_error($base));
    mysqli_query($base, $sql2) or die('Erreur SQL !' . $sql2 . '<br />' . mysqli_error($base));
    mysqli_query($base, $sql3) or die('Erreur SQL !' . $sql3 . '<br />' . mysqli_error($base));
    mysqli_query($base, $sql4) or die('Erreur SQL !' . $sql4 . '<br />' . mysqli_error($base));
    mysqli_query($base, $sql5) or die('Erreur SQL !' . $sql5 . '<br />' . mysqli_error($base));
}

function updateRessources($joueur)
{
    global $nomsRes;
    global $base;
    global $bonusMedailles;
    global $paliersPertes;

    $req1 = 'SELECT tempsPrecedent FROM autre WHERE login=\'' . $joueur . '\''; // On prends le dernier chargement de page
    $tempsPrecedent1 = mysqli_query($base, $req1) or die('Erreur SQL !<br />' . $req1 . '<br />' . mysqli_error($base));
    $donnees = mysqli_fetch_array($tempsPrecedent1);
    $nbsecondes = time() - $donnees['tempsPrecedent']; // On calcule la différence de secondes
    $sql = mysqli_query($base, 'SELECT * FROM ressources WHERE login=\'' . $joueur . '\''); // On prends l'energie en ce moment
    $donnees = mysqli_fetch_array($sql);
    $req = 'UPDATE autre SET tempsPrecedent=\'' . (time()) . '\' WHERE login = \'' . $joueur . '\''; // On écrit le nouveau 
    $ex = mysqli_query($base, $req) or die('Erreur SQL !<br />' . $req . '<br />' . mysqli_error($base));


    $requete = mysqli_query($base, 'SELECT * FROM constructions WHERE login=\'' . $joueur . '\'');
    $depot = mysqli_fetch_array($requete);
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////ENERGIE

    $revenuenergie = revenuEnergie($depot['generateur'], $joueur);
    $energie = $donnees['energie'] + $revenuenergie * ($nbsecondes / 3600); // On calcule l'energie que l'on doit avoir
    if ($energie >= placeDepot($depot['depot'])) {
        $energie = placeDepot($depot['depot']); // on limite l'energie pouvant être reçu (depots de ressources)
    }
    if ($energie < 0) {
        $energie = 0;
    }
    $req = 'UPDATE ressources SET energie=\'' . $energie . '\' WHERE login = \'' . $joueur . '\''; // on inscrit ce nouveau energie
    $ex = mysqli_query($base, $req) or die('Erreur SQL !<br />' . $req . '<br />' . mysqli_error($base));

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////RESSOURCES
    foreach ($nomsRes as $num => $ressource) {
        ${'revenu' . $ressource} = revenuAtome($num, $joueur);
        $$ressource = $donnees[$ressource] + ${'revenu' . $ressource} * ($nbsecondes / 3600);
        if ($$ressource >= placeDepot($depot['depot'])) {
            $$ressource = placeDepot($depot['depot']);
        }
        $req = 'UPDATE ressources SET ' . $ressource . '=\'' . $$ressource . '\' WHERE login = \'' . $joueur . '\'';
        $ex = mysqli_query($base, $req) or die('Erreur SQL !<br />' . $req . '<br />' . mysqli_error($base));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////Gestion des molécules disparaissant


    $ex1 = mysqli_query($base, 'SELECT stabilisateur FROM constructions WHERE login=\'' . $joueur . '\'');
    $stabilisateur = mysqli_fetch_array($ex1);

    $nbheuresDebut = ($nbsecondes / 3600); // nombre d'heures depuis la derniere connexion

    $exMedaille = mysqli_query($base, 'SELECT moleculesPerdues FROM autre WHERE login=\'' . $joueur . '\'');
    $donneesMedaille = mysqli_fetch_array($exMedaille);

    $sql = 'SELECT * FROM molecules WHERE proprietaire=\'' . $joueur . '\'';
    $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));

    $compteur = 0;
    while ($molecules = mysqli_fetch_array($ex)) {

        $moleculesRestantes = (pow(coefDisparition($joueur, $compteur + 1), $nbsecondes) * $molecules['nombre']);
        ${'nombre' . ($compteur + 1)} = $molecules['nombre'];


        $sql1 = 'UPDATE molecules SET nombre=\'' . $moleculesRestantes . '\' WHERE id=\'' . $molecules['id'] . '\'';
        $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));

        $ex2 = mysqli_query($base, 'SELECT moleculesPerdues FROM autre WHERE login=\'' . $joueur . '\'');
        $moleculesPerdues = mysqli_fetch_array($ex2);
        mysqli_query($base, 'UPDATE autre SET moleculesPerdues=\'' . ($molecules['nombre'] - $moleculesRestantes + $moleculesPerdues['moleculesPerdues']) . '\' WHERE login=\'' . $joueur . '\'');

        $compteur++;
    }

    if ($nbheuresDebut > 6) {
        $sql = 'SELECT nombre, formule FROM molecules WHERE proprietaire=\'' . $joueur . '\' AND numeroclasse=1';
        $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
        $donnees5 = mysqli_fetch_array($ex);
        $sql1 = 'SELECT nombre, formule FROM molecules WHERE proprietaire=\'' . $joueur . '\' AND numeroclasse=2';
        $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
        $donnees6 = mysqli_fetch_array($ex1);
        $sql2 = 'SELECT nombre, formule FROM molecules WHERE proprietaire=\'' . $joueur . '\' AND numeroclasse=3';
        $ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
        $donnees7 = mysqli_fetch_array($ex2);
        $sql3 = 'SELECT nombre, formule FROM molecules WHERE proprietaire=\'' . $joueur . '\' AND numeroclasse=4';
        $ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysqli_error($base));
        $donnees8 = mysqli_fetch_array($ex3);
        if (($nombre1 - $donnees5['nombre']) != 0 or ($nombre2 - $donnees6['nombre']) != 0 or ($nombre3 - $donnees7['nombre']) != 0 or ($nombre4 - $donnees8['nombre']) != 0) {
            $titreRapport = 'Rapport des pertes durant votre absence';
            $contenuRapport = 'Durant votre absence de ' . $nbheuresDebut . ' heures, vos pertes de molécules ont été : <br/>
			' . couleurFormule($donnees5['formule']) . ' : ' . number_format(($nombre1 - $donnees5['nombre']), 0, ' ', ' ') . ' molécules<br/>
			' . couleurFormule($donnees6['formule']) . ' : ' . number_format(($nombre2 - $donnees6['nombre']), 0, ' ', ' ') . ' molécules<br/>
			' . couleurFormule($donnees7['formule']) . ' : ' . number_format(($nombre3 - $donnees7['nombre']), 0, ' ', ' ') . ' molécules<br/>
			' . couleurFormule($donnees8['formule']) . ' : ' . number_format(($nombre4 - $donnees8['nombre']), 0, ' ', ' ') . ' molécules';
            $sql = 'INSERT INTO rapports VALUES(default, "' . (time()) . '", \'' . $titreRapport . '\', \'' . $contenuRapport . '\', "' . $joueur . '", default,"<img alt=\"skull\" src=\"images/rapports/rapportpertes.png\"/ class=\"imageAide\">")';
            $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
        }
    }
}

function updateActions($joueur)
{
    global $autre;
    global $points;
    global $constructions;
    global $nomsRes;
    global $base;
    global $nbRes;
    global $nbClasses;

    initPlayer($joueur);

    // Constructions
    $ex = query('SELECT * FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND fin<\'' . time() . '\'');
    while ($actions = mysqli_fetch_array($ex)) {
        augmenterBatiment($actions['batiment'], $joueur);

        query('DELETE FROM actionsconstruction WHERE id=\'' . $actions['id'] . '\'');
    }

    // Formation

    $ex = query('SELECT * FROM actionsformation WHERE login=\'' . $joueur . '\' AND debut<\'' . time() . '\''); // toutes les formations qui sont en cours

    //neutrinos
    $neutrinos = mysqli_fetch_array(query('SELECT neutrinos FROM autre WHERE login=\'' . $joueur . '\''));

    while ($actions = mysqli_fetch_array($ex)) {
        $ex1 = query('SELECT * FROM molecules WHERE id=\'' . $actions['idclasse'] . '\'');
        $molecule = mysqli_fetch_array($ex1);

        if ($actions['fin'] >= time()) {
            $derniereFormation = ($actions['nombreDebut'] - $actions['nombreRestant']) * $actions['tempsPourUn'] + $actions['debut'];
            if ($actions['idclasse'] != 'neutrino') {
                query('UPDATE molecules SET nombre=\'' . ($molecule['nombre'] + floor((time() - $derniereFormation) / $actions['tempsPourUn'])) . '\' WHERE id=\'' . $actions['idclasse'] . '\'');
            } else {
                query('UPDATE autre SET neutrinos=\'' . ($neutrinos['neutrinos'] + floor((time() - $derniereFormation) / $actions['tempsPourUn'])) . '\' WHERE login=\'' . $joueur . '\'');
                //$autre['neutrinos'] = ($neutrinos['neutrinos'] + floor((time()-$derniereFormation)/$actions['tempsPourUn']));
            }
            query('UPDATE actionsformation SET nombreRestant=\'' . ($actions['nombreRestant'] - floor((time() - $derniereFormation) / $actions['tempsPourUn'])) . '\' WHERE id=\'' . $actions['id'] . '\'');
        } else {
            query('DELETE FROM actionsformation WHERE id=\'' . $actions['id'] . '\'');
            if ($actions['idclasse'] != 'neutrino') {
                query('UPDATE molecules SET nombre=\'' . ($molecule['nombre'] + $actions['nombreRestant']) . '\' WHERE id=\'' . $actions['idclasse'] . '\'');
            } else {
                query('UPDATE autre SET neutrinos=\'' . ($neutrinos['neutrinos'] + $actions['nombreRestant']) . '\' WHERE login=\'' . $joueur . '\'');
                //$autre['neutrinos'] = ($neutrinos['neutrinos'] + $actions['nombreRestant']);
            }
        }
    }

    // Attaques

    $ex = query('SELECT * FROM actionsattaques WHERE attaquant=\'' . $joueur . '\' OR defenseur=\'' . $joueur . '\' ORDER BY tempsAttaque DESC');

    while ($actions = mysqli_fetch_array($ex)) {
        if ($actions['attaqueFaite'] == 0 && $actions['tempsAttaque'] < time()) { // on fait l'attaque
            if ($actions['troupes'] != 'Espionnage') {
                query('UPDATE actionsattaques SET attaqueFaite=1 WHERE id=\'' . $actions['id'] . '\'');

                if ($actions['attaquant'] == $joueur) {
                    $enFace = $actions['defenseur'];
                    updateRessources($actions['defenseur'], $nomsRes);
                    updateActions($actions['defenseur']);
                } else {
                    $enFace = $actions['attaquant'];
                    updateRessources($actions['attaquant'], $nomsRes);
                    updateActions($actions['attaquant']);
                }

                $nbsecondes = $actions['tempsAttaque'] - $actions['tempsAller'];
                $molecules = explode(";", $actions['troupes']);

                $ex3 = query('SELECT * FROM molecules WHERE proprietaire=\'' . $actions['attaquant'] . '\' ORDER BY numeroclasse ASC');

                $compteur = 1;
                $chaine = '';
                while ($moleculesProp = mysqli_fetch_array($ex3)) { // mise à jour des molécules perdues sur le trajet
                    $moleculesRestantes = (pow(coefDisparition($actions['attaquant'], $compteur), $nbsecondes) * $molecules[$compteur - 1]);

                    $chaine = $chaine . $moleculesRestantes . ';';

                    $ex2 = mysqli_query($base, 'SELECT moleculesPerdues FROM autre WHERE login=\'' . $actions['attaquant'] . '\'');
                    $moleculesPerdues = mysqli_fetch_array($ex2);
                    mysqli_query($base, 'UPDATE autre SET moleculesPerdues=\'' . ($molecules[$compteur - 1] - $moleculesRestantes + $moleculesPerdues['moleculesPerdues']) . '\' WHERE login=\'' . $actions['attaquant'] . '\'');

                    $compteur++;
                }

                $actions['troupes'] = $chaine;

                include("includes/combat.php"); // Les pertes sont calculées, le gagnant est désigné et les troupes sont mises à jour dans la BD, les ressources sont pillées

                $totalTroupesJoueur = $classeAttaquant1['nombre'] + $classeAttaquant2['nombre'] + $classeAttaquant3['nombre'] + $classeAttaquant4['nombre'];
                $totalTroupesDefenseur = $classeDefenseur1['nombre'] + $classeDefenseur2['nombre'] + $classeDefenseur3['nombre'] + $classeDefenseur4['nombre'];

                if ($gagnant == 2) {
                    $titreRapportJoueur = "Vous gagnez contre " . $actions['defenseur'] . " !";
                    $titreRapportDefenseur = "Vous perdez contre " . $actions['attaquant'] . " !";
                } elseif ($gagnant == 1) {
                    $titreRapportJoueur = "Vous perdez contre " . $actions['defenseur'] . " !";
                    $titreRapportDefenseur = "Vous gagnez contre " . $actions['attaquant'] . " !";
                } else {
                    $titreRapportJoueur = "Egalité contre " . $actions['defenseur'] . " !";
                    $titreRapportDefenseur = "Egalité contre " . $actions['attaquant'] . " !";
                }

                $chaine = "Aucune";
                foreach ($nomsRes as $num => $ressource) {
                    if (${$ressource . 'Pille'} > 0) {
                        if ($chaine == "Aucune") {
                            $chaine = nombreAtome($num, ${$ressource . 'Pille'});
                        } else {
                            $chaine = $chaine . nombreAtome($num, ${$ressource . 'Pille'});
                        }
                    }
                }



                // verifier si on a envoyé des molécules de cette classe

                for ($i = 1; $i <= $nbClasses; $i++) {
                    if (${'classeAttaquant' . $i}['nombre'] == 0) {
                        ${'classeAttaquant' . $i}['formuleAfficher'] = "?";
                    } else {
                        ${'classeAttaquant' . $i}['formuleAfficher'] = couleurFormule(${'classeAttaquant' . $i}['formule']);
                    }
                }

                $information = "";
                if ($attaquantsRestants == 0) {
                    $information = "<strong>Aucune molécule n\'est revenue !</strong><br/><br/>";
                }

                $debutRapport = "<p>
                            <div class=\"table-responsive\">
                            " . important('Attaquant') . "<br/>
                            " . chipInfo($attaquePts, 'images/molecule/sword.png') . chipInfo($pillagePts, 'images/molecule/bag.png') . "<br/><br/>
                            <table class=\"table table-bordered\">
                            <caption style=\"color:red;font-weight:bold;\"><img src=\"images/attaquer/gladius.png\" alt=\"epee\" class=\"imageAide\"/><a style=\"color:red\" href=\"joueur.php?id=" . $actions['attaquant'] . "\">" . $actions['attaquant'] . "</caption>
                            <thead>
                            <tr>
                            <th></th>
                            <th>" . $classeAttaquant1['formuleAfficher'] . "</th>
                            <th>" . $classeAttaquant2['formuleAfficher'] . "</th>
                            <th>" . $classeAttaquant3['formuleAfficher'] . "</th>
                            <th>" . $classeAttaquant4['formuleAfficher'] . "</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                            <th>Troupes</th>
                            <td>" . number_format($classeAttaquant1['nombre'], 0, ' ', ' ') . "</td>
                            <td>" . number_format($classeAttaquant2['nombre'], 0, ' ', ' ') . "</td>
                            <td>" . number_format($classeAttaquant3['nombre'], 0, ' ', ' ') . "</td>
                            <td>" . number_format($classeAttaquant4['nombre'], 0, ' ', ' ') . "</td>
                            </tr>

                            <tr>
                            <th>Pertes</th>
                            <td>" . number_format($classe1AttaquantMort, 0, ' ', ' ') . "</td>
                            <td>" . number_format($classe2AttaquantMort, 0, ' ', ' ') . "</td>
                            <td>" . number_format($classe3AttaquantMort, 0, ' ', ' ') . "</td>
                            <td>" . number_format($classe4AttaquantMort, 0, ' ', ' ') . "</td>
                            </tr>
                            </tbody>
                            </table></div><br/><br/>

                            $information
                            <br/><br/>
                            " . important('Défenseur') . "<br/>
                            " . chipInfo($defensePts, 'images/molecule/shield.png') . chipInfo($pillagePts1, 'images/molecule/bag.png') . "<br/><br/>
                            <div class=\"table-responsive\">
                            <table class=\"table table-bordered\">
                            <caption style=\"color:green;font-weight:bold;\"><img src=\"images/attaquer/shield.png\" alt=\"bouclier\" class=\"imageAide\"/><a style=\"color:green\" href=\"joueur.php?id=" . $actions['defenseur'] . "\">" . $actions['defenseur'] . "</a></caption>

                            <thead>
                            <tr>
                            <th></th>";

                $classeDefenseur1['nombre'] = separerZeros($classeDefenseur1['nombre']);
                $classeDefenseur2['nombre'] = separerZeros($classeDefenseur2['nombre']);
                $classeDefenseur3['nombre'] = separerZeros($classeDefenseur3['nombre']);
                $classeDefenseur4['nombre'] = separerZeros($classeDefenseur4['nombre']);

                $classe1DefenseurMort = separerZeros($classe1DefenseurMort);
                $classe2DefenseurMort = separerZeros($classe2DefenseurMort);
                $classe3DefenseurMort = separerZeros($classe3DefenseurMort);
                $classe4DefenseurMort = separerZeros($classe4DefenseurMort);

                $milieuDefenseur = "
                            <th>" . couleurFormule($classeDefenseur1['formule']) . "</th>
                            <th>" . couleurFormule($classeDefenseur2['formule']) . "</th>
                            <th>" . couleurFormule($classeDefenseur3['formule']) . "</th>
                            <th>" . couleurFormule($classeDefenseur4['formule']) . "</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                            <th>Troupes</th>
                            <td>" . $classeDefenseur1['nombre'] . "</td>
                            <td>" . $classeDefenseur2['nombre'] . "</td>
                            <td>" . $classeDefenseur3['nombre'] . "</td>
                            <td>" . $classeDefenseur4['nombre'] . "</td>
                            </tr>

                            <tr>
                            <th>Pertes</th>
                            <td>" . $classe1DefenseurMort . "</td>
                            <td>" . $classe2DefenseurMort . "</td>
                            <td>" . $classe3DefenseurMort . "</td>
                            <td>" . $classe4DefenseurMort . "</td>
                            </tr>";

                if ($attaquantsRestants == 0) { // si aucune molécule n'est revenue alors on a aucune information sur les troupes en face
                    $classeDefenseur1['formule'] = "?";
                    $classeDefenseur2['formule'] = "?";
                    $classeDefenseur3['formule'] = "?";
                    $classeDefenseur4['formule'] = "?";

                    $classeDefenseur1['nombre'] = "?";
                    $classeDefenseur2['nombre'] = "?";
                    $classeDefenseur3['nombre'] = "?";
                    $classeDefenseur4['nombre'] = "?";

                    $classe1DefenseurMort = "?";
                    $classe2DefenseurMort = "?";
                    $classe3DefenseurMort = "?";
                    $classe4DefenseurMort = "?";

                    query('DELETE FROM actionsattaques WHERE id=\'' . $actions['id'] . '\''); // pas de retour si ils sont morts

                }


                $milieuAttaquant = "
                            <th>" . couleurFormule($classeDefenseur1['formule']) . "</th>
                            <th>" . couleurFormule($classeDefenseur2['formule']) . "</th>
                            <th>" . couleurFormule($classeDefenseur3['formule']) . "</th>
                            <th>" . couleurFormule($classeDefenseur4['formule']) . "</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                            <th>Troupes</th>
                            <td>" . $classeDefenseur1['nombre'] . "</td>
                            <td>" . $classeDefenseur2['nombre'] . "</td>
                            <td>" . $classeDefenseur3['nombre'] . "</td>
                            <td>" . $classeDefenseur4['nombre'] . "</td>
                            </tr>

                            <tr>
                            <th>Pertes</th>
                            <td>" . $classe1DefenseurMort . "</td>
                            <td>" . $classe2DefenseurMort . "</td>
                            <td>" . $classe3DefenseurMort . "</td>
                            <td>" . $classe4DefenseurMort . "</td>
                            </tr>";


                $finRapport = "
                            </tbody>
                            </table></div><br/><br/>

                            " . important('Ressources pillées') . "
                            " . $chaine . "
                            <br/><br/>

                            " . important('Bâtiments endommagés') . "

                            <strong>Générateur : </strong>" . number_format($degatsGenEnergie, 0, ' ', ' ') . " (" . $destructionGenEnergie . ")<br/>
                            <strong>Champ de force : </strong>" . number_format($degatschampdeforce, 0, ' ', ' ') . " (" . $destructionchampdeforce . ")<br/>
                            <strong>Producteur : </strong>" . number_format($degatsProducteur, 0, ' ', ' ') . " (" . $destructionProducteur . ")<br/>
                            <strong>Stockage: </strong>" . number_format($degatsDepot, 0, ' ', ' ') . " (" . $destructionDepot . ")<br/>
                            </p>
                            ";

                $contenuRapportAttaquant = $debutRapport . $milieuAttaquant . $finRapport;
                $contenuRapportDefenseur = $debutRapport . $milieuDefenseur . $finRapport;

                // Les rapports sont créés
                $sql = 'INSERT INTO rapports VALUES(default, "' . $actions['tempsAttaque'] . '", \'' . $titreRapportJoueur . '\', \'' . $contenuRapportAttaquant . '\', "' . $actions['attaquant'] . '", default,"<img alt=\"attack\" src=\"images/rapports/sword.png\"/ class=\"imageAide\">")';
                mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));

                $sql1 = 'INSERT INTO rapports VALUES(default, "' . $actions['tempsAttaque'] . '", \'' . $titreRapportDefenseur . '\', \'' . $contenuRapportDefenseur . '\', "' . $actions['defenseur'] . '", default,"<img alt=\"attack\" src=\"images/rapports/sword.png\"/ class=\"imageAide\">")';
                mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
            } else {
                $nDef = mysqli_fetch_array(query('SELECT neutrinos FROM autre WHERE login=\'' . $actions['defenseur'] . '\''));

                if (($nDef['neutrinos'] / 2) < $actions['nombreneutrinos']) {
                    $exEspionnage = query('SELECT * FROM molecules WHERE proprietaire=\'' . $actions['defenseur'] . '\' ORDER BY numeroclasse ASC');
                    $i = 1;
                    while ($donneesEspionnage = mysqli_fetch_array($exEspionnage)) {
                        ${'classe' . $i} = $donneesEspionnage;
                        $i++;
                    }


                    $ex = mysqli_query($base, 'SELECT * FROM ressources WHERE login=\'' . $actions['defenseur'] . '\'');
                    $ressourcesJoueur = mysqli_fetch_array($ex);

                    $ex = mysqli_query($base, 'SELECT * FROM constructions WHERE login=\'' . $actions['defenseur'] . '\'');
                    $constructionsJoueur = mysqli_fetch_array($ex);

                    $titreRapportJoueur = "Vous espionnez " . $actions['defenseur'];
                    $chaine1 = "";
                    foreach ($nomsRes as $num => $ressource) {
                        $chaine1 = $chaine1 . nombreAtome($num, number_format($ressourcesJoueur[$ressource], 0, ' ', ' '));
                    }

                    $contenuRapportJoueur = "
                    <p>" . important('Armée') . "
                    <strong>" . couleurFormule($classe1['formule']) . " : </strong>" . nombreMolecules(number_format($classe1['nombre'], 0, ' ', ' ')) . "<br/>
                    <strong>" . couleurFormule($classe2['formule']) . " : </strong>" . nombreMolecules(number_format($classe2['nombre'], 0, ' ', ' ')) . "<br/>
                    <strong>" . couleurFormule($classe3['formule']) . " : </strong>" . nombreMolecules(number_format($classe3['nombre'], 0, ' ', ' ')) . "<br/>
                    <strong>" . couleurFormule($classe4['formule']) . " : </strong>" . nombreMolecules(number_format($classe4['nombre'], 0, ' ', ' ')) . "<br/>
                    <br/><br/>
                    " . important('Ressources') . "
                    " . nombreEnergie(number_format($ressourcesJoueur['energie'], 0, ' ', ' ')) . "
                    " . $chaine1 . "
                    <br/><br/>
                    " . important('Bâtiments') . "
                    <div class=\"table-responsive\">
                    <table class=\"table table-striped table-bordered\">
                    <thead>
                    <tr>
                    <th>Batiment</th>
                    <th>Niveau</th>
                    <th>Vie</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td>Générateur</td>
                    <td>" . $constructionsJoueur['generateur'] . "</td>
                    <td>
                    " . $constructionsJoueur['vieGenerateur'] . "/" . pointsDeVie($constructionsJoueur['generateur']) . "</div>
                    </div></td>
                    </tr>
                    <tr>
                    <td>Producteur</td>
                    <td>" . $constructionsJoueur['producteur'] . "</td>
                    <td>
                    " . $constructionsJoueur['vieProducteur'] . "/" . pointsDeVie($constructionsJoueur['producteur']) . "</td>
                    </tr>
                    <tr>
                    <td>Stockage</td>
                    <td>" . $constructionsJoueur['depot'] . "</td>
                    <td>
                    " . $constructionsJoueur['vieDepot'] . "/" . pointsDeVie($constructionsJoueur['depot']) . "</td>
                    </tr>
                    <tr>
                    <td>Champ de force</td>
                    <td>" . $constructionsJoueur['champdeforce'] . "</td>
                    <td>
                    " . $constructionsJoueur['vieChampdeforce'] . "/" . vieChampDeForce($constructionsJoueur['champdeforce']) . "</td>
                    </tr>
                    <tr>
                    <td>Ionisateur</td>
                    <td>" . $constructionsJoueur['ionisateur'] . "</td>
                    <td>Pas de vie</td>
                    </tr>
                    <tr>
                    <td>Condenseur</td>
                    <td>" . $constructionsJoueur['condenseur'] . "</td>
                    <td>Pas de vie</td>
                    </tr>
                    <tr>
                    <td>Lieur</td>
                    <td>" . $constructionsJoueur['lieur'] . "</td>
                    <td>Pas de vie</td>
                    </tr>
                    <tr>
                    <td>Stabilisateur</td>
                    <td>" . $constructionsJoueur['stabilisateur'] . "</td>
                    <td>Pas de vie</td>
                    </tr>
                    </tbody>
                    </table>
                    </div>
                    </p>";
                } else {
                    $titreRapportJoueur = "Espionnage raté";
                    $contenuRapportJoueur = "<p>Votre espionnage a raté, vous avez envoyé moins de la moitié des neutrinos de votre adversaire.</p>";
                }


                $sql1 = 'INSERT INTO rapports VALUES(default, "' . $actions['tempsAttaque'] . '", \'' . $titreRapportJoueur . '\', \'' . $contenuRapportJoueur . '\', "' . $actions['attaquant'] . '", default, "<img alt=\"attaque\" src=\"images/rapports/binoculars.png\"/ class=\"imageAide\">")';
                mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));

                query('DELETE FROM actionsattaques WHERE id=\'' . $actions['id'] . '\'');
            }
        }

        if ($actions['tempsRetour'] < time() && $joueur == $actions['attaquant'] && $actions['troupes'] != 'Espionnage') { // dans ce cas là on remet à jour les troupes


            $nbsecondes = $actions['tempsRetour'] - $actions['tempsAttaque'];
            $molecules = explode(";", $actions['troupes']);

            $ex3 = query('SELECT * FROM molecules WHERE proprietaire=\'' . $joueur . '\' ORDER BY numeroclasse ASC');

            $compteur = 1;

            while ($moleculesProp = mysqli_fetch_array($ex3)) {
                $moleculesRestantes = (pow(coefDisparition($joueur, $compteur), $nbsecondes) * $molecules[$compteur - 1]);

                $sql1 = 'UPDATE molecules SET nombre=\'' . ($moleculesProp['nombre'] + $moleculesRestantes) . '\' WHERE id=\'' . $moleculesProp['id'] . '\'';
                $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));

                $ex2 = mysqli_query($base, 'SELECT moleculesPerdues FROM autre WHERE login=\'' . $joueur . '\'');
                $moleculesPerdues = mysqli_fetch_array($ex2);
                mysqli_query($base, 'UPDATE autre SET moleculesPerdues=\'' . ($molecules[$compteur - 1] - $moleculesRestantes + $moleculesPerdues['moleculesPerdues']) . '\' WHERE login=\'' . $joueur . '\'');

                $compteur++;
            }

            query('DELETE FROM actionsattaques WHERE id=\'' . $actions['id'] . '\'');
        }
    }

    $ex = query('SELECT * FROM actionsenvoi WHERE (receveur=\'' . $joueur . '\' OR envoyeur=\'' . $joueur . '\') AND tempsArrivee<\'' . time() . '\'');

    while ($actions = mysqli_fetch_array($ex)) {
        query('DELETE FROM actionsenvoi WHERE id=\'' . $actions['id'] . '\'');

        $envoyees = explode(";", $actions['ressourcesEnvoyees']);
        $recues = explode(";", $actions['ressourcesRecues']);

        $chaine1 = "";
        foreach ($nomsRes as $num => $ressource) {
            if ($envoyees[$num] > 0) {
                $chaine1 = $chaine1 . nombreAtome($num, number_format($envoyees[$num], 0, ' ', ' '));
            }
        }

        $chaine2 = "";
        foreach ($nomsRes as $num => $ressource) {
            if ($recues[$num] > 0) {
                $chaine2 = $chaine2 . nombreAtome($num, number_format($recues[$num], 0, ' ', ' '));
            }
        }

        $energieEnvoyee = "";
        if ($envoyees[sizeof($nomsRes)] > 0) {
            $energieEnvoyee = nombreEnergie(number_format($envoyees[sizeof($nomsRes)], 0, ' ', ' '));
        }

        $energieRecue = "";
        if ($recues[sizeof($nomsRes)] > 0) {
            $energieRecue = nombreEnergie(number_format($recues[sizeof($nomsRes)], 0, ' ', ' '));
        }

        $titreRapport = "Rapport d\'apport de ressources par " . $actions['envoyeur'];
        $contenuRapport = "<a href=\"joueur.php?id=" . $actions['envoyeur'] . "\">" . $actions['envoyeur'] . "</a> vous envoie les ressources suivantes : <br/><br/>
        " . important('Ressources envoyées') . "
        " . $energieEnvoyee . $chaine1 . "<br/><br/>
        " . important('Ressources reçues') . "
        " . $energieRecue . $chaine2;

        $sql = 'INSERT INTO rapports VALUES(default, "' . time() . '", \'' . $titreRapport . '\', \'' . $contenuRapport . '\', "' . $actions['receveur'] . '", default,"<img alt=\"fleche\" src=\"images/rapports/retour.png\" class=\"imageAide\">")';
        mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));

        $ressourcesDestinataire = mysqli_fetch_array(query('SELECT * FROM ressources WHERE login=\'' . $actions['receveur'] . '\''));
        $chaine = "";
        foreach ($nomsRes as $num => $ressource) {
            $plus = "";
            $recues[$num] = max(0, $recues[$num]);
            if ($num < $nbRes) {
                $plus = ",";
            }
            $chaine = $chaine . '' . $ressource . '=' . round($ressourcesDestinataire[$ressource] + $recues[$num]) . '' . $plus;
        }

        $recues[sizeof($nomsRes)] = max(0, $recues[sizeof($nomsRes)]);
        mysqli_query($base, 'UPDATE ressources SET energie=\'' . round($ressourcesDestinataire['energie'] + $recues[sizeof($nomsRes)]) . '\',' . $chaine . ' WHERE login=\'' . $actions['receveur'] . '\'');
    }

    initPlayer($_SESSION['login']);
}

function ajouterPoints($nb, $joueur, $type = 0)
{
    $ex = query('SELECT * FROM autre WHERE login=\'' . $joueur . '\'');
    $points = mysqli_fetch_array($ex);

    if ($type == 0) {
        // points de constructions
        if ($points['points'] + $nb >= 0) {
            query('UPDATE autre SET points=\'' . ($points['points'] + $nb) . '\', totalPoints=\'' . ($points['totalPoints'] + $nb) . '\' WHERE login=\'' . $joueur . '\'');
            return $nb;
        }
    }
    if ($type == 1) {
        // points d'attaque
        query('UPDATE autre SET pointsAttaque=\'' . ($points['pointsAttaque'] + $nb) . '\', totalPoints=\'' . ($points['totalPoints'] - pointsAttaque($points['pointsAttaque']) + pointsAttaque($points['pointsAttaque'] + $nb)) . '\' WHERE login=\'' . $joueur . '\'');
        return -pointsAttaque($points['pointsAttaque']) + pointsAttaque($points['pointsAttaque'] + $nb);
    }
    if ($type == 2) {
        // points de defense
        query('UPDATE autre SET pointsDefense=\'' . ($points['pointsDefense'] + $nb) . '\', totalPoints=\'' . ($points['totalPoints'] - pointsDefense($points['pointsDefense']) + pointsDefense($points['pointsDefense'] + $nb)) . '\' WHERE login=\'' . $joueur . '\'');
        return -pointsDefense($points['pointsDefense']) + pointsDefense($points['pointsDefense'] + $nb);
    }
    if ($type == 3) {
        // points de pillage
        query('UPDATE autre SET ressourcesPillees=\'' . ($points['ressourcesPillees'] + $nb) . '\' WHERE login=\'' . $joueur . '\'');
        return chiffrePetit($nb, 0);
    }
}

function initPlayer($joueur)
{
    global $nomsRes;
    global $base;
    global $paliersConstructeur;
    global $bonusMedailles;
    global $ressources;
    global $revenu;
    foreach ($nomsRes as $num => $ressource) {
        global ${'revenu' . $ressource};
    }
    global $constructions;
    global $autre;
    foreach ($nomsRes as $num => $ressource) {
        global ${'points' . $ressource};
        global ${'niveau' . $ressource};
    }
    global $membre;
    global $revenuEnergie;
    global $placeDepot;
    global $points;
    global $plusHaut;
    global $production;
    global $productionCondenseur;
    global $listeConstructions;


    $sql1 = 'SELECT * FROM ressources WHERE login=\'' . htmlentities(mysqli_real_escape_string($base, stripslashes($joueur))) . '\'';
    $req1 = mysqli_query($base, $sql1);
    $ressources = mysqli_fetch_array($req1);

    foreach ($nomsRes as $num => $ressource) {
        ${'revenu' . $ressource} = revenuAtome($num, $joueur);
        $revenu[$ressource] = revenuAtome($num, $joueur);
    }

    // AUTRES

    $sql1 = 'SELECT * FROM constructions WHERE login=\'' . htmlentities(mysqli_real_escape_string($base, stripslashes($joueur))) . '\'';
    $req1 = mysqli_query($base, $sql1);
    $constructions = mysqli_fetch_array($req1);

    $niveaux = explode(';', $constructions['pointsProducteur']);
    $niveauxAtomes = explode(';', $constructions['pointsCondenseur']);
    foreach ($nomsRes as $num => $ressource) {
        ${'points' . $ressource} = $niveaux[$num];
        ${'niveau' . $ressource} = $niveauxAtomes[$num];
    }

    $sql1 = 'SELECT * FROM autre WHERE login=\'' . htmlentities(mysqli_real_escape_string($base, stripslashes($joueur))) . '\'';
    $req1 = mysqli_query($base, $sql1);
    $autre = mysqli_fetch_array($req1);

    $sql1 = 'SELECT * FROM membre WHERE login=\'' . htmlentities(mysqli_real_escape_string($base, stripslashes($joueur))) . '\'';
    $req1 = mysqli_query($base, $sql1);
    $membre = mysqli_fetch_array($req1);

    $revenuEnergie = revenuEnergie($constructions['generateur'], $joueur);
    $revenu['energie'] = $revenuEnergie;
    $placeDepot = placeDepot($constructions['depot'], $joueur);


    // CONSTRUCTIONS

    $points = ['condenseur' => 3, 'producteur' => sizeof($nomsRes)];

    $plusHaut = batMax($joueur);
    mysqli_query($base, 'UPDATE autre SET batmax=\'' . $plusHaut . '\' WHERE login=\'' . $joueur . '\'');

    $bonus = 0;
    foreach ($paliersConstructeur as $num => $palier) {
        if ($plusHaut >= $palier) {
            $bonus = $bonusMedailles[$num];
        }
    }

    $max = 0;
    foreach ($nomsRes as $num => $ressource) {
        $max = max($max, 3600 * ($placeDepot - $ressources[$ressource]) / $revenu[$ressource]);
    }

    $production = '<strong><span id="nbPointsRestants">' . $constructions['pointsProducteurRestants'] . '</span> points</strong> à placer<br/><form method="post" action="constructions.php" name="formPointsProducteur">';
    revenuAtomeJavascript($joueur); // ecrit la fonction en javscript qui donne la production pour un nombre de points
    foreach ($nomsRes as $num => $ressource) {
        $production = $production . nombreAtome($num, '<span style="color:green">+<span id="nbPointsAffichage' . $ressource . '">' . $revenu[$ressource] . '</span>/h</span> <input type="hidden" value="0" id="nbPoints' . $ressource . '" name="nbPoints' . $ressource . '"/><a href="#"><img class="imageAide" src="images/add.png" alt="add" style="margin-left:10px" id="add' . $ressource . '"/></a>');

        $production = $production . '
        <script>
            document.getElementById("add' . $ressource . '").addEventListener("click",function(){
                var pointsRestants = parseInt(document.getElementById("nbPointsRestants").innerHTML);
                if(pointsRestants > 0){
                    document.getElementById("nbPointsRestants").innerHTML = pointsRestants-1;
                    document.getElementById("nbPoints' . $ressource . '").value++;
                    document.getElementById("nbPointsAffichage' . $ressource . '").innerHTML = revenuAtomeJavascript(parseInt(document.getElementById("nbPoints' . $ressource . '").value)+parseInt(' . ${'points' . $ressource} . '));
                }
            });
        </script>';
    }
    $production = $production . '<br/><br/><center><input type="image" class="w32" style="margin-right:20px" src="images/yes.png" name="actioninvitation" value="Sauvegarder"/></form><a href="constructions.php"><img src="images/croix.png" class="w32" style="margin-left:20px"  alt="Ne pas sauvegarder"/></a></center>';

    $bonusDuplicateur = 1;
    if ($autre['idalliance'] > 0) {
        $ex = mysqli_query($base, 'SELECT duplicateur FROM alliances WHERE id=\'' . $autre['idalliance'] . '\'');
        $duplicateur = mysqli_fetch_array($ex);
        $bonusDuplicateur = 1 + ((0.1 * $duplicateur['duplicateur']) / 100);
    }

    $productionCondenseur = '<strong><span id="nbPointsCondenseurRestants">' . $constructions['pointsCondenseurRestants'] . '</span> points</strong> à placer<br/><form method="post" action="constructions.php" name="formPointsCondenseur">';
    foreach ($nomsRes as $num => $ressource) {
        $productionCondenseur = $productionCondenseur . nombreAtome($num, 'Niveau <span id="nbPointsCondenseurAffichage' . $ressource . '">' . ${'niveau' . $ressource} . '</span><input type="hidden" value="0" id="nbPointsCondenseur' . $ressource . '" name="nbPointsCondenseur' . $ressource . '"/><a href="#"><img class="imageAide" src="images/add.png" alt="add" style="margin-left:10px" id="addCondenseur' . $ressource . '"/></a>');

        $productionCondenseur = $productionCondenseur . '
        <script>
            document.getElementById("addCondenseur' . $ressource . '").addEventListener("click",function(){
                var pointsRestants = parseInt(document.getElementById("nbPointsCondenseurRestants").innerHTML);
                if(pointsRestants > 0){
                    document.getElementById("nbPointsCondenseurRestants").innerHTML = pointsRestants-1;
                    document.getElementById("nbPointsCondenseur' . $ressource . '").value++;
                    document.getElementById("nbPointsCondenseurAffichage' . $ressource . '").innerHTML=parseInt(document.getElementById("nbPointsCondenseur' . $ressource . '").value)+parseInt(' . ${'niveau' . $ressource} . ');
                }
            });
        </script>';
    }
    $productionCondenseur = $productionCondenseur . '<br/><br/><center><input type="image" class="w32" style="margin-right:20px" src="images/yes.png" name="actioninvitation" value="Sauvegarder"/></form><a href="constructions.php"><img src="images/croix.png" class="w32" style="margin-left:20px"  alt="Ne pas sauvegarder"/></a></center>';


    /////////////////////////////////////////

    $exNiveauActuel = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'generateur\' ORDER BY niveau DESC');
    $niveauActuel = mysqli_fetch_array($exNiveauActuel);
    $nb = mysqli_num_rows($exNiveauActuel);
    if ($nb == 0) {
        $niveauActuel['niveau'] = $constructions['generateur'];
    }

    if ($niveauActuel['niveau'] == 1) {
        $tempsGenerateur = 10;
    } else {
        $tempsGenerateur = round(60 * pow($niveauActuel['niveau'], 1.5));
    }


    $exNiveauActuel1 = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'producteur\' ORDER BY niveau DESC');
    $niveauActuel1 = mysqli_fetch_array($exNiveauActuel1);
    $nb1 = mysqli_num_rows($exNiveauActuel1);
    if ($nb1 == 0) {
        $niveauActuel1['niveau'] = $constructions['producteur'];
    }

    if ($niveauActuel1['niveau'] == 1) {
        $tempsProducteur = 10;
    } else {
        $tempsProducteur = round(40 * pow($niveauActuel1['niveau'], 1.5));
    }

    $exNiveauActuel1 = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'depot\' ORDER BY niveau DESC');
    $niveauActuelDepot = mysqli_fetch_array($exNiveauActuel1);
    $nb = mysqli_num_rows($exNiveauActuel1);
    if ($nb == 0) {
        $niveauActuelDepot['niveau'] = $constructions['depot'];
    }
    $exNiveauActuel1 = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'champdeforce\' ORDER BY niveau DESC');
    $niveauActuelChampDeForce = mysqli_fetch_array($exNiveauActuel1);
    $nb = mysqli_num_rows($exNiveauActuel1);
    if ($nb == 0) {
        $niveauActuelChampDeForce['niveau'] = $constructions['champdeforce'];
    }
    $exNiveauActuel1 = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'ionisateur\' ORDER BY niveau DESC');
    $niveauActuelIonisateur = mysqli_fetch_array($exNiveauActuel1);
    $nb = mysqli_num_rows($exNiveauActuel1);
    if ($nb == 0) {
        $niveauActuelIonisateur['niveau'] = $constructions['ionisateur'];
    }
    $exNiveauActuel1 = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'condenseur\' ORDER BY niveau DESC');
    $niveauActuelCondenseur = mysqli_fetch_array($exNiveauActuel1);
    $nb = mysqli_num_rows($exNiveauActuel1);
    if ($nb == 0) {
        $niveauActuelCondenseur['niveau'] = $constructions['condenseur'];
    }
    $exNiveauActuel1 = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'lieur\' ORDER BY niveau DESC');
    $niveauActuelLieur = mysqli_fetch_array($exNiveauActuel1);
    $nb = mysqli_num_rows($exNiveauActuel1);
    if ($nb == 0) {
        $niveauActuelLieur['niveau'] = $constructions['lieur'];
    }
    $exNiveauActuel1 = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $joueur . '\' AND batiment=\'stabilisateur\' ORDER BY niveau DESC');
    $niveauActuelStabilisateur = mysqli_fetch_array($exNiveauActuel1);
    $nb = mysqli_num_rows($exNiveauActuel1);
    if ($nb == 0) {
        $niveauActuelStabilisateur['niveau'] = $constructions['stabilisateur'];
    }

    $listeConstructions = [
        'generateur' => [
            'titre' => 'Générateur',
            'bdd' => 'generateur',
            'image' => 'images/batiments/generateur.png',
            'progressBar' => true,
            'niveau' => $constructions['generateur'],
            'revenu' => nombreEnergie('<span style="color:green" >+' . chiffrePetit(revenuEnergie($constructions['generateur'], $joueur, 4)) . '/h</span>'),
            'revenu1' => nombreEnergie('<span style="color:green" >+' . chiffrePetit(revenuEnergie($niveauActuel['niveau'] + 1, $joueur, 4)) . '/h</span>'),
            'effetSup' => '<br/><br/><strong>Stockage plein : </strong>' . date('d/m/Y', time() + 3600 * ($placeDepot - $ressources['energie']) / $revenu['energie']) . ' à ' . date('H\hi', time() + 3600 * ($placeDepot - $ressources['energie']) / $revenuEnergie),
            'description' => 'Le générateur <strong>produit de l\'énergie</strong>.',
            'coutEnergie' => round((1 - ($bonus / 100)) * 5 * (pow(1.20, $niveauActuel['niveau']) + pow($niveauActuel['niveau'], 1.4))),
            'coutAtomes' => round((1 - ($bonus / 100)) * 2 * (pow(1.20, $niveauActuel['niveau']) + pow($niveauActuel['niveau'], 1.4))),
            'points' => 1 + floor($niveauActuel['niveau'] * 0.1),
            'tempsConstruction' => $tempsGenerateur,
            'vie' => $constructions['vieGenerateur'],
            'vieMax' => pointsDeVie($constructions['generateur'])
        ],

        'producteur' => [
            'titre' => 'Producteur',
            'bdd' => 'producteur',
            'image' => 'images/batiments/producteur.png',
            'progressBar' => true,
            'niveau' => $constructions['producteur'],
            'revenu' => $constructions['pointsProducteurRestants'] . ' points restants',
            'revenu1' => '+' . $points['producteur'] . ' points à placer',
            'effetSup' => '<br/><br/><strong>Stockage plein : </strong>' . date('d/m/Y', time() + $max) . ' à ' . date('H\hi', time() + $max) . '<br/><br/>
                    ' . important("Production") . '
                    ' . $production,
            'description' => 'Le producteur <strong>produit des atomes</strong> à partir d\'énergie. A chaque niveau supplémentaire, vous obtenez un certain nombre de points qu\'il faut placer dans les atomes que vous voulez produire. Plus il y a de points dans un atome, plus sa production sera grande.',
            'coutEnergie' => round((1 - ($bonus / 100)) * 2 * (pow(1.20, $niveauActuel1['niveau']) + pow($niveauActuel1['niveau'], 1.4))),
            'coutAtomes' => round((1 - ($bonus / 100)) * 5 * (pow(1.20, $niveauActuel1['niveau']) + pow($niveauActuel1['niveau'], 1.4))),
            'drainage' => drainageProducteur($niveauActuel1['niveau'] + 1),
            'points' => 1 + floor($niveauActuel1['niveau'] * 0.1),
            'tempsConstruction' => $tempsProducteur,
            'vie' => $constructions['vieProducteur'],
            'vieMax' => pointsDeVie($constructions['producteur']),
        ],

        'depot' =>  [
            'titre' => 'Stockage',
            'bdd' => 'depot',
            'image' => 'images/batiments/depot.png',
            'progressBar' => true,
            'niveau' => $constructions['depot'],
            'revenu' => chiffrePetit($placeDepot) . ' ressources max',
            'revenu1' => chiffrePetit(placeDepot($niveauActuelDepot['niveau'] + 1)) . ' ressources max',
            'effetSup' => '',
            'description' => 'Le stockage est l\'endroit où sont stockés les ressources. Il détermine donc <strong>la quantité maximale de ressources</strong> que l\'on peut avoir.',
            'coutEnergie' => round((1 - ($bonus / 100)) * 10 * (pow(1.20, $niveauActuelDepot['niveau']) + pow($niveauActuelDepot['niveau'], 1.4))),
            'points' => 1 + floor($niveauActuelDepot['niveau'] * 0.1),
            'tempsConstruction' => round(80 * pow($niveauActuelDepot['niveau'], 1.5)),
            'vie' => $constructions['vieDepot'],
            'vieMax' => pointsDeVie($constructions['depot'])
        ],


        'champdeforce' => [
            'titre' => 'Champ de force',
            'bdd' => 'champdeforce',
            'image' => 'images/batiments/champdeforce.png',
            'progressBar' => true,
            'niveau' => $constructions['champdeforce'],
            'revenu' => chip('+' . floor($bonusDuplicateur * $constructions['champdeforce'] * 2) . '%', '<img src="images/batiments/shield.png" alt="shield" style="border-radius:0px;height:20px;width:20px" />', "white", "", true),
            'revenu1' => chip('+' . floor($bonusDuplicateur * ($niveauActuelChampDeForce['niveau'] + 1) * 2) . '%', '<img src="images/batiments/shield.png" alt="shield" style="border-radius:0px;height:20px;width:20px" />', "white", "", true),
            'effetSup' => '',
            'description' => 'Le champ de force vous protège des attaques adverses en donnant un <strong>bonus de défense</strong> lorsque vous êtes attaqué. Il prend aussi <strong>les dégâts des attaques adverses</strong> en premier si son niveau est superieur aux autres bâtiments.',
            'coutCarbone' => round((1 - ($bonus / 100)) * 8 * (pow(1.25, $niveauActuelChampDeForce['niveau']) + pow($niveauActuelChampDeForce['niveau'], 1.7))),
            'points' => 1 + floor($niveauActuelChampDeForce['niveau'] * 0.075),
            'tempsConstruction' => round(20 * pow($niveauActuelChampDeForce['niveau'] + 2, 1.7)),
            'vie' => $constructions['vieChampdeforce'],
            'vieMax' => vieChampDeForce($constructions['champdeforce'])
        ],

        'ionisateur' => [
            'titre' => 'Ionisateur',
            'bdd' => 'ionisateur',
            'image' => 'images/batiments/ionisateur.png',
            'progressBar' => false,
            'niveau' => $constructions['ionisateur'],
            'revenu' => chip('+' . floor($bonusDuplicateur * $constructions['ionisateur'] * 2) . '%', '<img src="images/batiments/sword.png" alt="shield" style="border-radius:0px;height:20px;width:20px" />', "white", "", true),
            'revenu1' => chip('+' . floor($bonusDuplicateur * ($niveauActuelIonisateur['niveau'] + 1) * 2) . '%', '<img src="images/batiments/sword.png" alt="shield" style="border-radius:0px;height:20px;width:20px" />', "white", "", true),
            'effetSup' => '',
            'description' => 'L\'ionisateur améliore votre capacité offensive en donnant un <strong>bonus d\'attaque</strong> à vos molécules.',
            'coutOxygene' => round((1 - ($bonus / 100)) * 8 * (pow(1.25, $niveauActuelIonisateur['niveau']) + pow($niveauActuelIonisateur['niveau'], 1.7))),
            'points' => 1 + floor($niveauActuelIonisateur['niveau'] * 0.075),
            'tempsConstruction' => round(20 * pow($niveauActuelIonisateur['niveau'] + 2, 1.7))
        ],

        'condenseur' => [
            'titre' => 'Condenseur',
            'bdd' => 'condenseur',
            'image' => 'images/batiments/condenseur.png',
            'progressBar' => false,
            'niveau' => $constructions['condenseur'],
            'revenu' => $constructions['pointsCondenseurRestants'] . ' points restants',
            'revenu1' => '+' . $points['condenseur'] . ' points à placer',
            'effetSup' => '<br/><br/>
                    ' . important("Effets") . '
                    ' . $productionCondenseur,
            'description' => 'Le condenseur permet de condenser la matière. Ainsi, les <strong>atomes</strong> formant les molécules s\'en trouvent <strong>renforcés</strong>, plus puissants. Vous pouvez choisir d\'augmenter certains atomes (et donc leur effets respectifs) plus que d\'autres.',
            'coutEnergie' => round((1 - ($bonus / 100)) * 3 * (pow(1.8, $niveauActuelCondenseur['niveau']) + pow($niveauActuelCondenseur['niveau'], 2))),
            'coutAtomes' => round((1 - ($bonus / 100)) * 8 * (pow(1.35, $niveauActuelCondenseur['niveau']) + pow($niveauActuelCondenseur['niveau'], 2))),
            'points' => 2 + floor($niveauActuelCondenseur['niveau'] * 0.1),
            'tempsConstruction' => round(120 * pow($niveauActuelCondenseur['niveau'] + 1, 1.8))
        ],

        'lieur' => [
            'titre' => 'Lieur',
            'bdd' => 'lieur',
            'image' => 'images/batiments/lieur.png',
            'progressBar' => false,
            'niveau' => $constructions['lieur'],
            'revenu' => chip('-' . floor((bonusLieur($constructions['lieur']) - 1) * 100) . '%', '<img src="images/batiments/tempsMolecule.png" alt="tpsMol" style="border-radius:0px;width:22px;height:22px"/>', "white", "", true),
            'revenu1' => chip('-' . floor((bonusLieur($niveauActuelLieur['niveau'] + 1) - 1) * 100) . '%', '<img src="images/batiments/tempsMolecule.png" alt="tpsMol" style="border-radius:0px;width:22px;height:22px"/>', "white", "", true),
            'effetSup' => '',
            'description' => 'Le lieur forme des liaisons entre atomes afin de créer des molécules. Il permet ainsi de <strong>réduire le temps de formation des molécules</strong> de votre armée.',
            'coutAzote' => round((1 - ($bonus / 100)) * 15 * (pow(1.35, $niveauActuelLieur['niveau']) + pow($niveauActuelLieur['niveau'], 1.35))),
            'points' => 2 + floor($niveauActuelLieur['niveau'] * 0.1),
            'tempsConstruction' => round(100 * pow($niveauActuelLieur['niveau'] + 1, 1.7))
        ],


        'stabilisateur' => [
            'titre' => 'Stabilisateur',
            'bdd' => 'stabilisateur',
            'image' => 'images/batiments/stabilisateur.png',
            'progressBar' => false,
            'niveau' => $constructions['stabilisateur'],
            'revenu' => ($constructions['stabilisateur'] * 0.5) . '% de réduction des chances de disparition des molécules',
            'revenu1' => (($niveauActuelStabilisateur['niveau'] + 1) * 0.5) . '% de réduction des chances de disparition des molécules',
            'effetSup' => '',
            'description' => 'Le stabilisateur permet à vos <strong>molécules d\'être plus stables</strong>, c\'est à dire qu\'elles disparaitront au bout d\'un temps plus long',
            'coutAtomes' => round((1 - ($bonus / 100)) * 5 * (pow(1.7, $niveauActuelStabilisateur['niveau']) + pow($niveauActuelStabilisateur['niveau'], 1.7))),
            'points' => 3 + floor($niveauActuelStabilisateur['niveau'] * 0.1),
            'tempsConstruction' => round(120 * pow($niveauActuelStabilisateur['niveau'] + 1, 1.7))
        ]
    ];
}

function augmenterBatiment($nom, $joueur)
{ // BUG listeconstructions
    global $listeConstructions;
    global $points;
    initPlayer($joueur);

    $ex = query('SELECT * FROM constructions WHERE login=\'' . $joueur . '\'');
    $batiments = mysqli_fetch_array($ex);

    $plus = '';
    if ($nom == "champdeforce" || $nom == "generateur" || $nom == "producteur" || $nom == "depot") {
        if ($nom == "champdeforce") {
            $plus = ', vie' . ucfirst($nom) . '=\'' . vieChampDeForce($batiments[$nom] + 1) . '\'';
        } else {
            $plus = ', vie' . ucfirst($nom) . '=\'' . pointsDeVie($batiments[$nom] + 1) . '\'';
        }
    }

    $ex = query('SELECT points FROM autre WHERE login=\'' . $joueur . '\'');
    $pointsDEMERDE = mysqli_fetch_array($ex);

    if ($nom == 'producteur') {
        query('UPDATE constructions SET pointsProducteurRestants=\'' . ($batiments['pointsProducteurRestants'] + $points['producteur']) . '\' WHERE login=\'' . $joueur . '\'');
    }
    if ($nom == 'condenseur') {
        query('UPDATE constructions SET pointsCondenseurRestants=\'' . ($batiments['pointsCondenseurRestants'] + $points['condenseur']) . '\' WHERE login=\'' . $joueur . '\'');
    }

    query('UPDATE constructions SET ' . $nom . '=\'' . ($batiments[$nom] + 1) . '\'' . $plus . ' WHERE login=\'' . $joueur . '\'');
    ajouterPoints($listeConstructions[$nom]['points'], $joueur);

    initPlayer($_SESSION['login']);
}

function diminuerBatiment($nom, $joueur)
{ // pour résoudre les bugs, construire un objet PLAYER qui initialise toutes ses constantes
    global $nomsRes;
    global $points;
    global $constructions;
    global $listeConstructions;
    foreach ($nomsRes as $num => $ressource) {
        global ${'points' . $ressource};
        global ${'niveau' . $ressource};
    }

    initPlayer($joueur);

    $ex = query('SELECT ' . $nom . ' FROM constructions WHERE login=\'' . $joueur . '\'');
    $batiments = mysqli_fetch_array($ex);

    if ($batiments[$nom] > 0) {
        $plus = '';
        if ($nom == "champdeforce" || $nom == "generateur" || $nom == "producteur" || $nom == "depot") {
            if ($nom == "champdeforce") {
                $plus = ', vie' . ucfirst($nom) . '=\'' . vieChampDeForce($batiments[$nom] - 1) . '\'';
            } else {
                $plus = ', vie' . ucfirst($nom) . '=\'' . pointsDeVie($batiments[$nom] - 1) . '\'';
            }
        }

        $ex = query('SELECT points FROM autre WHERE login=\'' . $joueur . '\'');
        $pointsJoueur = mysqli_fetch_array($ex);

        if ($nom == 'producteur') {
            if ($constructions['pointsProducteurRestants'] >= $points['producteur']) {
                query('UPDATE constructions SET pointsProducteurRestants=\'' . ($constructions['pointsProducteurRestants'] - $points['producteur']) . '\' WHERE login=\'' . $joueur . '\'');
            } else {
                $pointsAEnlever = $points['producteur'] - $constructions['pointsProducteurRestants'];
                query('UPDATE constructions SET pointsProducteurRestants=0 WHERE login=\'' . $joueur . '\'');

                $chaine = "";
                foreach ($nomsRes as $num => $ressource) {
                    if ($pointsAEnlever <= ${'points' . $ressource} - 1) {
                        $chaine = $chaine . (${'points' . $ressource} - $pointsAEnlever) . ";";
                        $pointsAEnlever = 0;
                    } else {
                        $chaine = $chaine . "1;";
                        $pointsAEnlever = $pointsAEnlever - (${'points' . $ressource} - 1);
                    }
                }

                query('UPDATE constructions SET pointsProducteur=\'' . $chaine . '\' WHERE login=\'' . $joueur . '\'');
            }
        }
        if ($nom == 'condenseur') {
            if ($constructions['pointsCondenseurRestants'] >= $points['condenseur']) {
                query('UPDATE constructions SET pointsCondenseurRestants=\'' . ($constructions['pointsCondenseurRestants'] - $points['condenseur']) . '\' WHERE login=\'' . $joueur . '\'');
            } else {
                query('UPDATE constructions SET pointsCondenseurRestants=0 WHERE login=\'' . $joueur . '\'');
                $pointsAEnlever = $points['condenseur'] - $constructions['pointsCondenseurRestants'];

                $chaine = "";
                foreach ($nomsRes as $num => $ressource) {
                    if ($pointsAEnlever <= ${'niveau' . $ressource}) {
                        $chaine = $chaine . (${'niveau' . $ressource} - $pointsAEnlever) . ";";
                        $pointsAEnlever = 0;
                    } else {
                        $chaine = $chaine . "0;";
                        $pointsAEnlever = $pointsAEnlever - (${'niveau' . $ressource} - 1);
                    }
                }

                query('UPDATE constructions SET pointsCondenseur=\'' . $chaine . '\' WHERE login=\'' . $joueur . '\'');
            }
        }

        query('UPDATE constructions SET ' . $nom . '=\'' . ($batiments[$nom] - 1) . '\'' . $plus . ' WHERE login=\'' . $joueur . '\'');
        ajouterPoints(-$listeConstructions[$nom]['points'], $joueur);
    }

    initPlayer($_SESSION['login']);
}

function coordonneesAleatoires()
{
    $ex = query('SELECT tailleCarte,nbDerniere FROM statistiques');
    $inscrits = mysqli_fetch_array($ex);

    if ($inscrits['nbDerniere'] > $inscrits['tailleCarte'] - 2) {
        $inscrits['nbDerniere'] = 0;
        $inscrits['tailleCarte'] += 1;
    }

    $carte = [];
    for ($i = 0; $i < $inscrits['tailleCarte']; $i++) {
        $temp = [];
        for ($j = 0; $j < $inscrits['tailleCarte']; $j++) {
            $temp[] = 0;
        }
        $carte[] = $temp;
    }

    $ex = query('SELECT x,y FROM membre');
    while ($joueurs = mysqli_fetch_array($ex)) {
        $carte[$joueurs['x']][$joueurs['y']] = 1;
    }

    $alea = mt_rand(0, 1);
    if ($alea == 0) { // horizontale
        $y = $inscrits['tailleCarte'] - 1;

        $x = mt_rand(0, $inscrits['tailleCarte'] - 1);
        while ($carte[$x][$y] != 0) {
            $x = mt_rand(0, $inscrits['tailleCarte'] - 1);
        }
    } else {
        $x = $inscrits['tailleCarte'] - 1;

        $y = mt_rand(0, $inscrits['tailleCarte'] - 1);
        while ($carte[$x][$y] != 0) {
            $y = mt_rand(0, $inscrits['tailleCarte'] - 1);
        }
    }

    query('UPDATE statistiques SET tailleCarte=\'' . $inscrits['tailleCarte'] . '\', nbDerniere=\'' . ($inscrits['nbDerniere'] + 1) . '\'');

    return ['x' => $x, 'y' => $y];
}


function batMax($pseudo)
{
    global $nomsRes;
    global $nbRes;
    global $base;

    $liste = ['generateur', 'producteur', 'champdeforce', 'ionisateur', 'depot', 'stabilisateur', 'condenseur', 'lieur'];
    // on ne peut pas faire un foreach car il y a un probleme de priorités
    $exTableau = mysqli_query($base, 'SELECT * FROM constructions WHERE login=\'' . $pseudo . '\'');
    $tableau = mysqli_fetch_array($exTableau);
    $plusHaut = $tableau['generateur'];

    foreach ($liste as $num => $batiment) {
        if ($tableau[$batiment] > $plusHaut) {
            $plusHaut = $tableau[$batiment];
        }
    }

    return $plusHaut;
}

function joueur($joueur)
{
    $act = statut($joueur);
    if ($act == 0) {
        return '<a href="joueur.php?id=' . $joueur . '" class="lienVisible"><span style="color:darkgray">' . $joueur . '</span></a>';
    } else {
        return '<a href="joueur.php?id=' . $joueur . '" class="lienVisible">' . $joueur . '</a>';
    }
}

function alliance($alliance)
{
    return '<a href="alliance.php?id=' . $alliance . '" class="lienVisible">' . $alliance . '</a>';
}

function image($num)
{
    global $nomsRes;
    global $nomsAccents;
    return '<img style="vertical-align:middle;width:37px;height:37px;" alt="Energie" src="images/' . $nomsRes[$num] . '.png" alt="' . $nomsRes[$num] . '" title="' . ucfirst($nomsAccents[$num]) . '" />';
}

function imageEnergie($imageAide = false)
{
    if ($imageAide) {
        $class = 'class="imageAide"';
    } else {
        $class = 'style="vertical-align:middle;width:25px;height:25px;"';
    }
    return '<img src="images/energie.png" ' . $class . '  alt="Energie" title="Energie" />';
}

function imagePoints()
{
    return '<img src="images/points.png" style="vertical-align:middle" alt="Points" title="Points" />';
}

function imageLabel($image, $label, $lien = false)
{
    if (!$lien) {
        $lien = "";
        $typeLabel = 'labelClassement';
    } else {
        $lien = '<a href="' . $lien . '" class="lienSousMenu">';
        $typeLabel = 'labelSousMenu';
    }
    return $lien . $image . '<br/><span class="' . $typeLabel . '"  style="color:black">' . $label . '</span></a>';
}

//Forum
function rangForum($joueur)
{
    global $base;
    global $paliersPipelette;

    $ex2 = mysqli_query($base, 'SELECT count(*) AS nbmessages FROM reponses WHERE auteur=\'' . $joueur . '\'');
    $donnees = mysqli_fetch_array($ex2);

    $ex = query('SELECT login FROM membre WHERE login=\'' . $joueur . '\'');
    $nb = mysqli_num_rows($ex);

    if ($nb == 0) {
        $couleur = "gray";
        $nom = "Supprimé";
    } else {

        $ex3 = mysqli_query($base, 'SELECT moderateur, login, codeur FROM membre WHERE login=\'' . $joueur . '\'');
        $donnees2 = mysqli_fetch_array($ex3); // Recupere si le membre est moderateur
        if ($donnees2['login'] == "Guortates") {
            $couleur = "#FFCC99";
            $nom = "Créateur";
        } elseif ($donnees2['moderateur'] == 1) { //Si il est moderateur
            $couleur = "#a42800"; //Couleur speciale
            $nom = "Modérateur";
        } elseif ($donnees2['codeur'] == 1) {
            $couleur = "#740152";
            $nom = "Codeur";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[7]) {
            $couleur = 'red';
            $nom = "Diamant rouge";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[6]) {
            $couleur = '#40e0d0';
            $nom = "Diamant";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[5]) {
            $couleur = 'red';
            $nom = "Rubis";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[4]) {
            $couleur = 'blue';
            $nom = "Saphir";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[3]) {
            $couleur = 'green';
            $nom = "Emeraude";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[2]) {
            $couleur = '#d9a710';
            $nom = "Or";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[1]) {
            $couleur = '#cecece';
            $nom = "Argent";
        } elseif ($donnees['nbmessages'] >= $paliersPipelette[0]) {
            $couleur = '#614e1a';
            $nom = "Bronze";
        } else {
            $couleur = '#200001';
            $nom = "Apprenti";
        }
    }

    return ['couleur' => $couleur, 'nom' => $nom];
}

function creerBBcode($nomTextArea, $interieur = NULL, $reponse = 0)
{
    debutContent();
    echo 'BBcode <strong>activé</strong> ' . aide('bbcode', true);
    finContent();
}

function antihtml($phrase)
{
    return htmlspecialchars($phrase, ENT_SUBSTITUTE, 'ISO8859-1');
}

function antiXSS($phrase, $specialTexte = false)
{
    global $base;
    if ($specialTexte) {
        return mysqli_real_escape_string($base, antihtml($phrase));
    } else {
        return mysqli_real_escape_string($base, addslashes(antihtml(trim($phrase))));
    }
}

function separerZeros($nombre)
{
    return number_format($nombre, 0, ' ', ' ');
}

function couleur($chiffre)
{ // si négatif alors rouge, si positif alors vert
    if ($chiffre < 0) {
        return '<span style="color:red">' . $chiffre . '</span>';
    } elseif ($chiffre > 0) {
        return '<span style="color:green">+' . $chiffre . '</span>';
    } else {
        return $chiffre;
    }
}

function miseAJour()
{
    global $base;
    global $nomsRes;

    $sql1 = 'SELECT * FROM constructions WHERE login=\'' . htmlentities(mysqli_real_escape_string($base, stripslashes($_SESSION['login']))) . '\'';
    $req1 = mysqli_query($base, $sql1);
    $constructions = mysqli_fetch_array($req1);

    $niveaux = explode(';', $constructions['pointsProducteur']);
    foreach ($nomsRes as $num => $ressource) {
        ${'points' . $ressource} = $niveaux[$num];
    }
}

function remiseAZero()
{
    global $base;
    global $nomsRes;
    global $nbRes;

    mysqli_query($base, 'UPDATE autre SET points=0, niveaututo=1, nbattaques=0, neutrinos=default,moleculesPerdues=0, energieDepensee=0, energieDonnee=0, bombe=0, batMax=1, totalPoints=0, pointsAttaque=0, pointsDefense=0, ressourcesPillees = 0, missions=\'\'');
    mysqli_query($base, 'UPDATE constructions SET generateur=default, producteur=default,pointsProducteur=default,pointsProducteurRestants=default, pointsCondenseur=default, pointsCondenseurRestants=default,champdeforce=default, lieur=default,ionisateur=default, depot=1, stabilisateur=default, condenseur=0,vieGenerateur=' . pointsDeVie(1) . ', vieChampdeforce=' . vieChampDeForce(0) . ', vieProducteur=' . pointsDeVie(1) . ', vieDepot=' . pointsDeVie(1) . '');
    mysqli_query($base, 'UPDATE alliances SET energieAlliance=0,duplicateur=0');
    mysqli_query($base, 'UPDATE molecules SET formule="Vide", nombre=0');
    mysqli_query($base, 'UPDATE membre SET timestamp=' . time() . '');

    $chaine = "";
    foreach ($nomsRes as $num => $ressource) {
        $plus = "";
        if ($num < $nbRes) {
            $plus = ",";
        }
        $chaine = $chaine . '' . $ressource . '=default' . $plus;
    }
    $sql = 'UPDATE ressources SET energie=default, terrain=default, revenuenergie=default, niveauclasse=1, ' . $chaine . '';
    echo $sql;
    mysqli_query($base, $sql);
    mysqli_query($base, 'DELETE FROM declarations');
    mysqli_query($base, 'DELETE FROM invitations');
    mysqli_query($base, 'DELETE FROM messages');
    mysqli_query($base, 'DELETE FROM rapports');
    mysqli_query($base, 'DELETE FROM actionsconstruction');
    mysqli_query($base, 'DELETE FROM actionsformation');
    mysqli_query($base, 'DELETE FROM actionsenvoi');
    mysqli_query($base, 'DELETE FROM actionsattaques');

    query('UPDATE statistiques SET nbDerniere=0, tailleCarte=1');
    query('UPDATE membre SET x=-1000, y=-1000'); // on les enleve de la carte, ils sont replacés quand ils se reconnectent
    /* $ex = query('SELECT * FROM membre');
    while($jou = mysqli_fetch_array($ex)){
        $co = coordonneesAleatoires();
        query('UPDATE membre SET x=\''.$co['x'].'\', y=\''.$co['y'].'\' WHERE login=\''.$jou['login'].'\'');
    }*/



    mysqli_query($base, 'DELETE FROM cours');

    $chaine = "";
    for ($i = 0; $i <= $nbRes; $i++) {
        $plus = "";
        if ($i != $nbRes) {
            $plus = ",";
        }
        $chaine = $chaine . "1" . $plus;
    }
    mysqli_query($base, 'INSERT INTO cours VALUES (default,"' . $chaine . '","' . time() . '")');
}

function pref($ressource)
{ // retourne le bon prefixe
    if (preg_match("#^[aeiouyh]#", $ressource)) {
        return "d'";
    } else {
        return "de ";
    }
}

function query($truc)
{
    global $base;
    $ex = mysqli_query($base, $truc) or die('Erreur SQL !<br />' . $truc . '<br />' . mysqli_error());
    return $ex;
}

function chiffrePetit($chiffre, $type = 1)
{
    $nombreDepart = floor($chiffre);
    $nombreFinal = floor($nombreDepart);
    $derriere = "";
    $negatif = "";
    if ($chiffre < 0) {
        $negatif = "-";
        $nombreFinal = -$nombreFinal;
    }

    while ($nombreFinal >= 1000) {
        if ($nombreFinal >= 1000000000000000000000000) {
            $nombreFinal = $nombreFinal / 1000000000000000000000000;
            $derriere = "Y" . $derriere . "";
        } elseif ($nombreFinal >= 1000000000000000000000) {
            $nombreFinal = $nombreFinal / 1000000000000000000000;
            $derriere = "Z" . $derriere . "";
        } elseif ($nombreFinal >= 1000000000000000000) {
            $nombreFinal = $nombreFinal / 1000000000000000000;
            $derriere = "E" . $derriere . "";
        } elseif ($nombreFinal >= 1000000000000000) {
            $nombreFinal = $nombreFinal / 1000000000000000;
            $derriere = "P" . $derriere . "";
        } elseif ($nombreFinal >= 1000000000000) {
            $nombreFinal = $nombreFinal / 1000000000000;
            $derriere = "T" . $derriere . "";
        } elseif ($nombreFinal >= 1000000000) {
            $nombreFinal = $nombreFinal / 1000000000;
            $derriere = "G" . $derriere . "";
        } elseif ($nombreFinal >= 1000000) {
            $nombreFinal = $nombreFinal / 1000000;
            $derriere = "M" . $derriere . "";
        } elseif ($nombreFinal >= 1000) {
            $nombreFinal = $nombreFinal / 1000;
            $derriere = "K" . $derriere . "";
        }
    }
    if ($nombreFinal <= 10) {
        $nombreFinal = floor($nombreFinal * 100) / 100;
    } elseif ($nombreFinal <= 100) {
        $nombreFinal = floor($nombreFinal * 10) / 10;
    } else {
        $nombreFinal = floor($nombreFinal);
    }

    $nombreFinal = $negatif . $nombreFinal;
    if ($type == 1) {
        return '<span title="' . number_format($nombreDepart, 0, ' ', ' ') . '">' . $nombreFinal . '' . $derriere . '</span>';
    } else {
        return $nombreFinal . '' . $derriere . '';
    }
}

function supprimerAlliance($alliance)
{
    global $base;
    mysqli_query($base, 'UPDATE autre SET energieDonnee=0 WHERE idalliance=\'' . $alliance . '\'');
    $sql = 'DELETE FROM alliances WHERE id=\'' . $alliance . '\'';
    mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));

    $sql1 = 'UPDATE autre SET idalliance=0 WHERE idalliance=\'' . $alliance . '\'';
    mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));

    $sql2 = 'DELETE FROM invitations WHERE idalliance=\'' . $alliance . '\'';
    mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));

    mysqli_query($base, 'DELETE FROM declarations WHERE (alliance1=\'' . $alliance . '\' OR alliance2=\'' . $alliance . '\')');

    mysqli_query($base, 'DELETE FROM grades WHERE idalliance=\'' . $alliance . '\'');
}

function supprimerJoueur($joueur)
{
    global $base;
    $modif = 'DELETE FROM autre WHERE login =\'' . $joueur . '\'';
    $modif1 = 'DELETE FROM membre WHERE login =\'' . $joueur . '\'';
    $modif2 = 'DELETE FROM ressources WHERE login =\'' . $joueur . '\'';
    $modif4 = 'DELETE FROM molecules WHERE proprietaire =\'' . $joueur . '\'';
    $modif5 = 'DELETE FROM constructions WHERE login =\'' . $joueur . '\'';
    $modif6 = 'DELETE FROM invitations WHERE invite =\'' . $joueur . '\'';
    $modif7 = 'DELETE FROM messages WHERE destinataire =\'' . $joueur . '\' OR expeditaire =\'' . $joueur . '\'';
    $modif8 = 'DELETE FROM rapports WHERE destinataire =\'' . $joueur . '\'';
    $modif8 = 'DELETE FROM grades WHERE login =\'' . $joueur . '\'';

    $ex = mysqli_query($base, $modif) or die('Erreur SQL !<br/>' . $modif . '<br/>' . mysqli_error($base));
    $ex1 = mysqli_query($base, $modif1) or die('Erreur SQL !<br/>' . $modif1 . '<br/>' . mysqli_error($base));
    $ex2 = mysqli_query($base, $modif2) or die('Erreur SQL !<br/>' . $modif2 . '<br/>' . mysqli_error($base));
    $ex4 = mysqli_query($base, $modif4) or die('Erreur SQL !<br/>' . $modif4 . '<br/>' . mysqli_error($base));
    $ex5 = mysqli_query($base, $modif5) or die('Erreur SQL !<br/>' . $modif5 . '<br/>' . mysqli_error($base));
    $ex6 = mysqli_query($base, $modif6) or die('Erreur SQL !<br/>' . $modif6 . '<br/>' . mysqli_error($base));
    $ex7 = mysqli_query($base, $modif7) or die('Erreur SQL !<br/>' . $modif7 . '<br/>' . mysqli_error($base));
    $ex8 = mysqli_query($base, $modif8) or die('Erreur SQL !<br/>' . $modif8 . '<br/>' . mysqli_error($base));

    $modif3 = 'SELECT  inscrits FROM statistiques';
    $ex3 = mysqli_query($base, $modif3) or die('Erreur SQL !<br />' . $modif3 . '<br />' . mysqli_error($base));
    $donnees = mysqli_fetch_array($ex3);
    $nbinscrits = $donnees['inscrits'] - 1;
    $sql = 'UPDATE statistiques SET inscrits=\'' . $nbinscrits . '\'';
    mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysqli_error($base));
}

function transformInt($nombre)
{
    $nombre = preg_replace('#K#i', '000', $nombre);
    $nombre = preg_replace('#M#i', '000000', $nombre);
    $nombre = preg_replace('#G#i', '000000000', $nombre);
    $nombre = preg_replace('#T#i', '000000000000', $nombre);
    $nombre = preg_replace('#P#i', '000000000000000', $nombre);
    $nombre = preg_replace('#E#i', '000000000000000000', $nombre);
    $nombre = preg_replace('#Z#i', '000000000000000000000', $nombre);
    $nombre = preg_replace('#Y#i', '000000000000000000000000', $nombre);
    return $nombre;
}

// Affichage

function affichageTemps($secondes, $petitTemps = false)
{
    if ($petitTemps && $secondes <= 60) {
        return $secondes . 's';
    }

    if ($secondes >= 24 * 2 * 3600) {
        return (floor($secondes / 3600 / 24 * 100) / 100) . ' jours';
    }

    $heures = intval($secondes / 3600) . ':';
    $minutes = intval(($secondes % 3600) / 60) . ':';
    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    $secondes = intval((($secondes % 3600) % 60));
    if ($secondes < 10) {
        $secondes = '0' . $secondes;
    }
    return $heures . $minutes . $secondes;
}
function coutEnergie($cout)
{
    global $ressources;

    if ($ressources['energie'] >= $cout) {
        return chip(chiffrePetit($cout), imageEnergie(), "white", "green", true);
    } else {
        return chip(chiffrePetit($cout), imageEnergie(), "white", "red", true);
    }
}

function coutAtome($num, $cout)
{
    global $nomsRes;
    global $ressources;

    if ($ressources[$nomsRes[$num]] >= $cout) { // BUG ICI
        return chip(chiffrePetit($cout), image($num), "white", "green");
    } else {
        return chip(chiffrePetit($cout), image($num), "white", "red");
    }
}

function coutTout($cout)
{
    global $nomsRes;
    global $ressources;

    $ok = true;
    foreach ($nomsRes as $num => $ressource) {
        if ($ressources[$ressource] < $cout) {
            $ok = false;
        }
    }

    if ($ok) {
        $couleur = 'green';
    } else {
        $couleur = 'red';
    }

    return '
    <div class="chip bg-' . $couleur . '">
        <div class="chip-media bg-white" style="width:143px;border-radius:20px"><img src="images/tout.png" style="border-radius:0px;margin-right:0px;" alt="toutes" title="Toutes les ressources" /></div>
        <div class="chip-label">' . $cout . '</div>
    </div>';
}

function nombreMolecules($nombre)
{
    return chip($nombre, '<img src="images/molecule.png" alt="molecule" title="Population" style="width:20px;height;20px;border-radius:0px"/>', "white", "", true);
}

function nombrePoints($nombre)
{
    return chip($nombre, '<img src="images/points.png" alt="points" style="width:23px;height:23px;border-radius:0px"/>', "white", "", true);
}

function nombreAtome($num, $nombre)
{
    return chip($nombre, image($num), "white");
}

function nombreNeutrino($nombre)
{
    return chip($nombre, '<img style="vertical-align:middle;width:37px;height:37px;" alt="Neutrino" src="images/neutrino.png" title="Neutrino" />', "white");
}

function nombreEnergie($nombre, $id = false)
{
    return chip($nombre, imageEnergie(), "white", "", true, $id);
}

function nombreTemps($nombre)
{
    return chip($nombre, '<img alt="sablier" style="width:23px;height:23px;border-radius:1px;" src="images/sand-clock.png"/>', "white", "", true);
}

function nombreTout($nombre)
{
    return '
        <div class="chip bg-">
            <div class="chip-media bg-white" style="width:143px;border-radius:20px"><img src="images/tout.png" style="border-radius:0px;margin-right:0px;" alt="toutes" title="Toutes les ressources" /></div>
            <div class="chip-label">' . $nombre . '</div>
        </div>';
}

function ajouter($champ, $bdd, $nombre, $joueur)
{
    $ex = query('SELECT ' . $champ . ' FROM ' . $bdd . ' WHERE login=\'' . $joueur . '\'');
    $d = mysqli_fetch_array($ex);

    query('UPDATE ' . $bdd . ' SET ' . $champ . '=\'' . ($d[$champ] + $nombre) . '\' WHERE login=\'' . $joueur . '\'');
}

function couleurFormule($formule)
{
    global $nomsRes;
    global $lettre;
    global $couleurs;

    foreach ($nomsRes as $num => $ressource) {
        $formule = preg_replace('#(' . $lettre[$num] . ')(<sub>[0-9]*</sub>)#', '<span style="color:' . $couleurs[$num] . ';font-weight:bold;">$1$2</span>', $formule);
    }

    return $formule;
}

function popover($nom, $image)
{
    return '<a href="#" data-popover=".' . $nom . '" class="open-popover" style=""><img src="' . $image . '" alt="question" style="width:20px;height:20px;vertical-align:middle;"></a>';
}

function carteForum($avatar, $login, $date, $titre, $contenu, $grade, $sujet = false)
{
    if ($sujet) {
        $sujet = '<div class="card-footer no-border">
        ' . $sujet . '
        </div>';
    } else {
        $sujet = '';
    }
    echo '
    <div class="card facebook-card">
      <div class="card-header no-border">
        <div class="facebook-avatar">' . $avatar . '</div>
        <div class="facebook-grade">' . $login . '<br/><span style="color:' . $grade['couleur'] . '">' . $grade['nom'] . '</span></div>
        <div class="facebook-name">' . $titre . '</div><br/>
        <div class="facebook-date">' . $date . '</div>
      </div>
      <div class="card-content-inner">' . $contenu . '</div>
      ' . $sujet . '
    </div>';
}

function debutCarte($titre = false, $style = "", $image = false, $overflow = false)
{
    if ($image) {
        $classe = "demo-card-header-pic";
        $style = $style . "background-image:url('" . $image . "');";
    } else {
        $classe = "";
    }
    if ($titre) {
        $titre = '
        <div class="card-header" style="' . $style . '">
            ' . $titre . '
        </div>';
    } else {
        $titre = "";
    }

    if ($overflow) {
        $overflow = 'id="' . $overflow . '" style="overflow-x:scroll;overflow-y:scroll;"';
    } else {
        $overflow = "";
    }

    echo '
    <div class="card ' . $classe . '" >
        <div class="card-content" >
            ' . $titre . '
            <div class="card-content-inner" ' . $overflow . ' >
            <p>';
}

function finCarte($footer = false)
{
    if ($footer) {
        $footer = '<div class="card-footer no-border">
        ' . $footer . '
        </div>';
    } else {
        $footer = "";
    }
    echo '   </p>
    	   </div>
           ' . $footer . '
	   </div>
    </div>
    ';
}

function debutListe($retour = false)
{
    $contenu = '
    <div class="list-block media-list">
        <ul>';
    if ($retour) {
        return $contenu;
    } else {
        echo $contenu;
    }
}

function finListe($retour = false)
{
    $contenu = '
        </ul>
    </div>';
    if ($retour) {
        return $contenu;
    } else {
        echo $contenu;
    }
}

function debutContent($inner = false, $return = false)
{
    if ($inner) {
        $inner = '<div class="content-block-inner">';
    } else {
        $inner = "";
    }

    if ($return) {
        return '
        <div class="content-block">' . $inner;
    } else {
        echo '
        <div class="content-block">' . $inner;
    }
}

function finContent($inner = false, $return = false)
{
    if ($inner) {
        $inner = '</div>';
    } else {
        $inner = "";
    }

    if ($return) {
        return $inner . '
        </div>
        ';
    } else {
        echo $inner . '
        </div>
        ';
    }
}

function debutAccordion()
{
    echo '
    <div class="list-block accordion-list">
        <ul>';
}

function finAccordion()
{
    echo '
        </ul>
    </div>';
}

function item($options)
{
    if (!array_key_exists("noList", $options) || !$options["noList"]) {
        $d = '<li>';
        $e = '</li>';
    } else {
        $d = '';
        $e = '';
    }

    if (array_key_exists("floating", $options) && $options["floating"]) {
        $floating = "floating-label";
    } else {
        $floating = "";
    }

    if (array_key_exists("disabled", $options) && $options["disabled"]) {
        $disabled = " disabled";
    } else {
        $disabled = "";
    }

    if (array_key_exists("media", $options) && $options["media"]) {
        $media =
            '<div class="item-media">
            ' . $options["media"] . '
        </div>';
    } else {
        $media = "";
    }

    if (array_key_exists("input", $options) && $options["input"]) {
        $input = '
        <div class="item-input">
            ' . $options["input"] . '
        </div>';
    } else {
        $input = "";
    }

    if (array_key_exists("style", $options) && $options["style"]) {
        $style = $options["style"];
    } else {
        $style = "";
    }

    if (array_key_exists("after", $options) && $options["after"]) {
        $after = '
        <div class="item-after" style="' . $style . '">
            ' . $options["after"] . '          
        </div>';
    } else {
        $after = "";
    }

    if (array_key_exists("titre", $options) && $options["titre"]) {
        $titre = '
        <div class="item-title ' . $floating . '">
            ' . $options["titre"] . '
        </div>';
    } else {
        $titre = "";
    }

    if (array_key_exists("soustitre", $options) && $options["soustitre"]) {
        $soustitre = '
        <div class="item-subtitle">
            ' . $options["soustitre"] . '
        </div>';

        $titre = '<div class="item-title-row">' . $titre . $after . '</div>';
        $after = '';
    } else {
        $soustitre = "";
    }

    if (array_key_exists("accordion", $options) && $options["accordion"]) {
        $options['link'] = '#';
        $d = '<li class="accordion-item">';
        $e = '</li>';
        $accordion = '
        <div class="accordion-item-content">
            <div class="content-block">
                <p>' . $options["accordion"] . '</p>
            </div>
        </div>';
    } else {
        $accordion = '';
    }

    if (array_key_exists("autocomplete", $options) && $options["autocomplete"] && !array_key_exists("link", $options)) {
        $options['link'] = '#';
        $options['ajax'] = true;
    }

    if (array_key_exists("link", $options) && $options["link"]) {
        if (array_key_exists("ajax", $options) && $options["ajax"]) {
            $ajax = ' ajax';
        } else {
            $ajax = '';
        }

        if (array_key_exists("autocomplete", $options) && $options["autocomplete"]) {
            $autocomplete = ' autocomplete-opener';
            $autocompleteId = 'id="' . $options["autocomplete"] . '"';
        } else {
            $autocomplete = '';
            $autocompleteId = '';
        }

        $link = '<a class="item-link' . $ajax . $autocomplete . '" onclick="javascript:myApp.closePanel()" data-view=".view-main" href="' . $options["link"] . '" ' . $autocompleteId . '>';
        $finLink = '</a>';
    } else {
        $link = "";
        $finLink = '';
    }

    if (array_key_exists("form", $options) && $options["form"]) {
        if (array_key_exists("sup", $options["form"])) {
            $sup = $options["form"]["sup"];
        } else {
            $sup = "";
        }

        $form = '<form method="post" action="' . $options["form"][0] . '" name="' . $options["form"][1] . '" ' . $sup . '>';
        $finForm = '</form>';
    } else {
        $form = "";
        $finForm = '';
    }

    if (array_key_exists("select", $options) && $options["select"]) {
        $js = "";
        if (array_key_exists("javascript", $options["select"]) && $options["select"]["javascript"]) {
            $js = $options["select"]["javascript"];
        }

        if (array_key_exists("hauteur", $options["select"]) && $options["select"]["hauteur"]) {
            $options["select"]["hauteur"] = 'data-picker-height="' . $options["select"]["hauteur"] . 'px"';
        } else {
            $options["select"]["hauteur"] = '';
        }
        $select = '<a href="#" class="item-link smart-select" data-picker-close-text="Fermer" ' . $options["select"]['hauteur'] . '> 
                    <select name="' . $options["select"][0] . '" id="' . $options["select"][0] . '" class="form-control" ' . $js . '>
                        ' . $options["select"][1] . '
                    </select>';
        $finSelect = '</a>';
    } else {
        $select = "";
        $finSelect = '';
    }


    if (array_key_exists("retour", $options) && $options["retour"]) { // si l'on veut pas afficher mais l'inclure dans une variable
        return  '
            ' . $d . '
                ' . $form . '
                ' . $link . '
                ' . $select . '
                <div class="item-content' . $disabled . '">
                    ' . $media . '
                    <div class="item-inner">
                        ' . $titre . '
                        ' . $soustitre . '
                        ' . $input . '
                    </div>
                    ' . $after . '
                </div>
                ' . $finSelect . '
                ' . $finLink . '
                ' . $finForm . '
            ' . $e;
    } else { // si on veut éviter de mettre echo à chaque fois
        echo '
        ' . $d . '
            ' . $form . '
            ' . $link . '
            ' . $select . '
            <div class="item-content' . $disabled . '">
                ' . $media . '
                <div class="item-inner">
                    ' . $titre . '
                    ' . $soustitre . '
                    ' . $input . '
                </div>
                ' . $after . '
            </div>
            ' . $finSelect . '
            ' . $finLink . '
            ' . $accordion . '
            ' . $finForm . '
        ' . $e;
    }
}

function progressBar($vie, $vieMax, $couleur)
{
    return '
        <br/><br/><br/>
        <div class="item-content" style="margin:0;padding:0;">
            <div class="item-inner" style="width: 80px;padding-right:0px;">
              <div data-progress="' . ($vie / $vieMax * 100) . '" class="progressbar color-' . $couleur . '" style="height:6px;border:2px solid black"></div>
              <center><strong style="font-size:13px">' . $vie . '/' . $vieMax . '</strong></center>
        </div>
        </div>';
}

function itemAccordion($titre = false, $media = false, $contenu = false, $id = false)
{
    if ($media) {
        $media =
            '<div class="item-media">
            ' . $media . '
        </div>';
    } else {
        $media = "";
    }

    if ($titre) {
        $titre = '
        <div class="item-title-row">' . $titre . '<br/></div>';
    } else {
        $titre = "";
    }

    if ($id) {
        $id = 'id="' . $id . '"';
    } else {
        $id = "";
    }

    echo '
    <li class="accordion-item" ' . $id . '><a href="#" class="item-content item-link">
        ' . $media . '
        <div class="item-inner">
            ' . $titre . '
        </div></a>
        <div class="accordion-item-content">
            <div class="content-block">
                <p>' . $contenu . '</p>
            </div>
        </div>
    </li>';
}

function accordion($options)
{
    if (array_key_exists("media", $options) && $options["media"]) {
        $media =
            '<div class="item-media">
            ' . $options["media"] . '
        </div>';
    } else {
        $media = "";
    }

    if (array_key_exists("titre", $options) && $options["titre"]) {
        $titre =
            '<div class="item-media">
            ' . $options["titre"] . '
        </div>';
    } else {
        $titre = "";
    }

    if (array_key_exists("titre", $contenu) && $options["contenu"]) {
        $contenu =
            '<div class="item-media">
            ' . $options["contenu"] . '
        </div>';
    } else {
        $contenu = "";
    }

    echo '<div class="accordion-item"><a href="#" class="item-content item-link">
        ' . $media . '
        <div class="item-inner">
            ' . $titre . '
        </div></a>
        <div class="accordion-item-content">
            <div class="content-block">
                <p>' . $contenu . '</p>
            </div>
        </div>
        </div>';
}

function checkbox($liste)
{
    $options = '';

    foreach ($liste as $key => $value) {

        if (array_key_exists("after", $value) && $value["after"]) {
            $after = '<div class="item-after">
                ' . $value['after'] . '
                </div>';
        } else {
            $after = "";
        }

        if (!array_key_exists("noList", $value) || !$value["noList"]) {
            $d = '<li>';
            $e = '</li>';
        } else {
            $d = '';
            $e = '';
        }
        $options = $options . '
            <li>
            <label class="label-checkbox item-content">
                <input type="checkbox" name="' . $value['name'] . '" id="' . $value['name'] . '">
                <div class="item-media">
                    <i class="icon icon-form-checkbox"></i>
                </div>
                <div class="item-inner">
                    <div class="item-title">' . $value['titre'] . '</div>
                </div>
            ' . $after . '
            </label>
            </li>';
    }

    return '
    ' . $d . '
    <div class="list-block">
        <ul>
        ' . $options . '
        </ul>
    </div>
    ' . $e;
}

function chip($label, $image, $couleurImage = "black", $couleur = "", $circle = false, $id = false)
{
    if ($circle) {
        $style = "border:1px solid black";
    } else {
        $style = "";
    }

    if ($id) {
        $id = 'id="' . $id . '"';
    } else {
        $id = "";
    }
    return '<div class="chip bg-' . $couleur . '" style="margin-right:3px;margin-left:3px;">
                <div class="chip-media bg-' . $couleurImage . '" style="' . $style . '">' . $image . '</div>
                <div class="chip-label" ' . $id . '>' . $label . '</div>
            </div>';
}

function chipInfo($label, $image, $id = false)
{
    return chip($label, '<img alt="tag" src="' . $image . '" style="width:25px;height:25px;border-radius:0px;"/>', "white", "", true, $id);
}

function imageClassement($rang)
{
    if ($rang == 1) {
        return '<img src="images/classement/or.png" alt="or" title="1er" style="vertical-align:middle;height:28px;width:28px;"/>';
    } elseif ($rang == 2) {
        return '<img src="images/classement/argent.png" alt="argent" title="2e" style="vertical-align:middle;height:25px;width:25px;"/>';
    } elseif ($rang == 3) {
        return '<img src="images/classement/bronze.png" alt="bronze" title="3e" style="vertical-align:middle;height:21px;width:21px;"/>';
    } else {
        return $rang;
    }
}

function slider($options)
{
    $min = 0;
    if (array_key_exists("min", $options) && $options["min"]) {
        $min = $options['min'];
    }

    $max = 100;
    if (array_key_exists("max", $options) && $options["max"]) {
        $max = $options['max'];
    }

    $value = 50;
    if (array_key_exists("value", $options) && $options["value"]) {
        $value = $options['value'];
    }

    $step = 1;
    if (array_key_exists("step", $options) && $options["step"]) {
        $step = $options['step'];
    }

    $color = '';
    if (array_key_exists("color", $options) && $options["color"]) {
        $color = 'class="color-' . $options['color'] . '"';
    }


    return '
    <div class="range-slider" ' . $color . '>
        <input type="range" min="' . $min . '" max="' . $max . '" value="' . $value . '" step="' . $step . '">
    </div>';
}

function submit($options)
{
    if (array_key_exists("style", $options) && $options["style"]) {
        $style = $options['style'];
    } else {
        $style = "";
    }

    if (array_key_exists("titre", $options) && $options["titre"]) {
        $titre = $options['titre'];
    } else {
        $titre = "";
    }

    if (array_key_exists("form", $options) && $options["form"]) {
        $form = 'javascript:document.' . $options['form'] . '.submit()';
    } else {
        $form = "";
    }

    if (array_key_exists("link", $options) && $options["link"]) {
        $form = $options["link"];
    }

    if (array_key_exists("id", $options) && $options["id"]) {
        $id = 'id="' . $options["id"] . '"';
    } else {
        $id = '';
    }

    if (array_key_exists("classe", $options) && $options["classe"]) {
        $classe = $options['classe'];
    } else {
        $classe = "button-raised button-fill";
    }

    if (array_key_exists("image", $options) && $options["image"]) {
        $image1 = '<img alt="imageCote" src="' . $options['image'] . '" style="float:left;vertical-align:middle;width:25px;height:25px;margin-top:5px;margin-left:-3px"/>';
        if (!array_key_exists("simple", $options) || !$options["simple"]) {
            $image2 = '<img alt="imageCote" src="' . $options['image'] . '" style="float:right;vertical-align:middle;width:25px;height:25px;margin-top:5px;margin-right:-3px"/>';
        } else {
            $image2 = "";
        }
    } else {
        $image1 = "";
        $image2 = "";
    }

    if (array_key_exists("nom", $options) && $options["nom"]) {
        $nom = '<input type="hidden" name="' . $options['nom'] . '"/>';
    } else {
        $nom = '';
    }

    return $nom . '<a class="button ' . $classe . '" style="' . $style . '" href="' . $form . '" ' . $id . '>' . $image1 . $titre . $image2 . '</a>';
}

function important($contenu)
{
    return '<span class="important">' . $contenu . '</span><hr/>';
}

function aide($page, $noir = false)
{ // renvoie l'icone d'aide et lorsque l'on clique dessus, cela affiche l'aide associée, la liste des aides étant inscrite dans basicprivatehtml.php
    if ($noir) {
        return popover('popover-' . $page, 'images/question.png');
    } else {
        return popover('popover-' . $page, 'images/aide.png');
    }
}

function scriptAffichageTemps()
{
    echo '
    <script>
        function affichageTemps(secondes){
            var heures=String(Math.floor(secondes / 3600))+":";
            var minutes=Math.floor((secondes % 3600) / 60);
            if(minutes < 10){
                minutes = "0"+String(minutes)+":";
            }
            else {
                minutes = String(minutes)+":";
            }
            secondes=Math.floor(((secondes % 3600) % 60));
            if(secondes < 10){
                secondes = "0"+String(secondes);
            }
            return heures+minutes+secondes;
        }
    </script>';
}
