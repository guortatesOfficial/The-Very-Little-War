<?php

include("includes/basicprivatephp.php");
include("includes/redirectionVacance.php");

$exMedaille = query('SELECT nbattaques FROM autre WHERE login=\'' . $_SESSION['login'] . '\'');
$donneesMedaille = mysqli_fetch_array($exMedaille);
$bonus = 0;

foreach ($paliersTerreur as $num => $palier) {
    if ($donneesMedaille['nbattaques'] >= $palier) {
        $bonus = $bonusMedailles[$num];
    }
}

$coutPourUnAtome = 0.15 * (1 + $bonus / 100);

$ex = mysqli_query($base, 'SELECT nbattaques FROM autre WHERE login=\'' . $_SESSION['login'] . '\'');
$donnees = mysqli_fetch_array($ex);
$reduction = 0;

foreach ($paliersTerreur as $num => $palier) {
    if ($donnees['nbattaques'] >= $palier) {
        $reduction = $bonusMedailles[$num];
    }
}

if (isset($_POST['joueurAEspionner']) && isset($_POST['nombreneutrinos'])) {
    if (!empty($_POST['joueurAEspionner']) && !empty($_POST['nombreneutrinos'])) { // Vérification que la variable n'est pas vide
        if ($_POST['joueurAEspionner'] != $_SESSION['login']) {
            $_POST['nombreneutrinos'] = antiXSS($_POST['nombreneutrinos']);
            if (preg_match("#^[0-9]*$#", $_POST['nombreneutrinos']) and $_POST['nombreneutrinos'] >= 1 and $_POST['nombreneutrinos'] <= $autre['neutrinos']) {
                $ex = query('SELECT * FROM membre WHERE login=\'' . $_POST['joueurAEspionner'] . '\'');
                $membreJoueur = mysqli_fetch_array($ex);
                updateRessources($_POST['joueurAEspionner'], $nomsRes);
                updateActions($_POST['joueurAEspionner']);


                $distance = pow(pow($membre['x'] - $membreJoueur['x'], 2) + pow($membre['y'] - $membreJoueur['y'], 2), 0.5);
                $tempsTrajet = round($distance / $vitesseEspionnage * 3600);

                query('INSERT INTO actionsattaques VALUES(default,"' . $_SESSION['login'] . '","' . $_POST['joueurAEspionner'] . '","' . time() . '","' . (time() + $tempsTrajet) . '","' . (time() + 2 * $tempsTrajet) . '","Espionnage","0","' . $_POST['nombreneutrinos'] . '")');
                query('UPDATE autre SET neutrinos=\'' . ($autre['neutrinos'] - $_POST['nombreneutrinos']) . '\' WHERE login=\'' . $_SESSION['login'] . '\'');
                $autre['neutrinos'] -= $_POST['nombreneutrinos'];

                $information = 'Vous avez lancé l\'espionnage de ' . $_POST['joueurAEspionner'] . ' !';
            } else {
                $erreur = "Le nombre de neutrinos n'est pas valable.";
            }
        } else {
            $erreur = "Vous ne pouvez pas vous espionner.";
        }
    } else {
        $erreur = "T'y as cru ?";
    }
}
// Attaque
if (isset($_POST['joueurAAttaquer'])) {
    if (!empty($_POST['joueurAAttaquer'])) { // Vérification que la variable n'est pas vide

        $_POST['joueurAAttaquer'] = antiXSS($_POST['joueurAAttaquer']);
        if ($_POST['joueurAAttaquer'] != $_SESSION['login']) {

            $sqlVac = 'SELECT vacance,timestamp FROM membre WHERE login=\'' . $_POST['joueurAAttaquer'] . '\'';
            $exVac = mysqli_query($base, $sqlVac);
            $enVac = mysqli_fetch_array($exVac);

            if ($enVac['vacance']) {
                $erreur = "Vous ne pouvez pas attaquer un joueur en vacances";
            } elseif (time() - $enVac['timestamp'] < 3600 * 24 * 2) {
                $erreur = "Le joueur est encore sous protection des débutants.";
            } elseif (time() - $membre['timestamp'] < 3600 * 24 * 2) {
                $erreur = "Votre protection de débutant est encore active (encore <strong>" . affichageTemps(3600 * 24 * 2 - time() + $membre['timestamp']) . " h</strong>) et vous ne pouvez donc pas attaquer.";
            } else {

                $sqlPointsDefenseur = 'SELECT * FROM autre WHERE login=\'' . $_POST['joueurAAttaquer'] . '\'';
                $exPointsDefenseur = mysqli_query($base, $sqlPointsDefenseur) or die('Erreur SQL !<br />' . $sqlPointsDefenseur . '<br />' . mysqli_error($base));
                $joueurDefenseur = mysqli_fetch_array($exPointsDefenseur);
                $nb = mysqli_num_rows($exPointsDefenseur);

                $ex = query('SELECT x,y FROM membre WHERE login=\'' . $_POST['joueurAAttaquer'] . '\'');
                $positions = mysqli_fetch_array($ex);

                if ($nb > 0) {
                    $ex = query('SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\'');
                    $bool = 1;

                    $troupesPositives = true; // si sup a 0
                    for ($i = 1; $i <= $nbClasses; $i++) {
                        if (!isset($_POST['nbclasse' . $i])) {
                            $_POST['nbclasse' . $i] = 0;
                        }

                        if ($_POST['nbclasse' . $i] < 0) {
                            $troupesPositives = false;
                        }
                    }

                    if ($troupesPositives) {

                        $c = 1;
                        $tempsTrajet = 0;
                        $troupes = "";
                        $cout = 0;

                        while ($moleculesAttaque = mysqli_fetch_array($ex)) {
                            if (ceil($moleculesAttaque['nombre']) < $_POST['nbclasse' . $c]) {
                                $bool = 0;
                            }

                            if ($moleculesAttaque['nombre'] < $_POST['nbclasse' . $c]) { // si on envoie le tout alors, il faut prendre aussi la virgule
                                $_POST['nbclasse' . $c] =  $moleculesAttaque['nombre'];
                            }

                            if ($_POST['nbclasse' . $c] == "") {
                                $_POST['nbclasse' . $c] = 0;
                            }

                            if ($moleculesAttaque['formule'] != "Vide" && $_POST['nbclasse' . $c] > 0) {
                                $distance = pow(pow($membre['x'] - $positions['x'], 2) + pow($membre['y'] - $positions['y'], 2), 0.5);
                                $tempsTrajet = max($tempsTrajet, round($distance / vitesse($moleculesAttaque['chlore'], $niveauchlore) * 3600));
                            }
                            $troupes = $troupes . $_POST['nbclasse' . $c] . ';';

                            $nbAtomes = 0;
                            foreach ($nomsRes as $num => $res) {
                                $nbAtomes += $moleculesAttaque[$res];
                            }
                            $cout += $_POST['nbclasse' . $c] * $coutPourUnAtome * $nbAtomes;

                            $c++;
                        }

                        if ($cout <= $ressources['energie']) {
                            if ($bool) {
                                $ex = query('SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' ORDER BY numeroclasse ASC');
                                $c = 1;
                                while ($moleculesAttaque = mysqli_fetch_array($ex)) {
                                    query('UPDATE molecules SET nombre=\'' . ($moleculesAttaque['nombre'] - $_POST['nbclasse' . $c]) . '\' WHERE id=\'' . $moleculesAttaque['id'] . '\''); // on enleve les troupes de celles sur place  
                                    $c++;
                                }
                                query('INSERT INTO actionsattaques VALUES(default,"' . $_SESSION['login'] . '","' . $_POST['joueurAAttaquer'] . '","' . time() . '","' . (time() + $tempsTrajet) . '","' . (time() + 2 * $tempsTrajet) . '","' . $troupes . '",0,default)');
                                ajouter('energie', 'ressources', -$cout, $_SESSION['login']);
                                ajouter('energieDepensee', 'autre', $cout, $_SESSION['login']);
                                $information = "L'attaque a été lancée.";
                            } else {
                                $erreur = "Vous n\'avez pas assez de molécules.";
                            }
                        } else {
                            $erreur = "Vous n\'avez pas assez d\'énergie.";
                        }
                    } else {
                        $erreur = "Votre nombre de troupes doit être positif.";
                    }
                } else {
                    $erreur = "Ce joueur n\'existe pas.";
                }
            }
        } else {
            $erreur = "Vous ne pouvez pas vous attaquer.";
        }
    } else {
        $erreur = "T'y as cru ?";
    }
}

include("includes/tout.php");

if (time() - $membre['timestamp'] < 3600 * 24 * 2) {
    debutCarte();
    echo '<div class="table-responsive"><table>';
    echo '<tr><td><img src="images/attaquer/baby.png" class="imageChip" alt="bebe"/><td><td>Fin de la protection des débutants le ' . strftime('%d/%m/%y à %Hh%M', $membre['timestamp'] + 3600 * 24 * 2);
    echo '</table></div>';
    finCarte();
}

$ex = mysqli_query($base, 'SELECT count(*) AS nb FROM actionsattaques WHERE attaquant=\'' . $_SESSION['login'] . '\' OR (defenseur=\'' . $_SESSION['login'] . '\' AND troupes!=\'Espionnage\') ORDER BY tempsAttaque ASC');
$nb = mysqli_fetch_array($ex); // pour ne pas voir l'espionnage

$ex = mysqli_query($base, 'SELECT * FROM actionsattaques WHERE attaquant=\'' . $_SESSION['login'] . '\' OR defenseur=\'' . $_SESSION['login'] . '\' ORDER BY tempsAttaque ASC');
if ($nb['nb'] > 0) {
    debutCarte();
    scriptAffichageTemps();
    echo '<div class="table-responsive"><table>';
    echo '<tr><th>Type</th><th>Joueur</th><th>Temps</th></tr>';

    while ($actionsattaques = mysqli_fetch_array($ex)) {

        if ($_SESSION['login'] == $actionsattaques['attaquant']) { // faire si retour ou non
            if (time() < $actionsattaques['tempsAttaque']) {
                if ($actionsattaques['troupes'] != 'Espionnage') {
                    echo '<tr><td><a href="attaque.php?id=' . $actionsattaques['id'] . '"><img src="images/rapports/sword.png" class="imageChip" alt="epee"/></a></td><td><a href="joueur.php?id=' . $actionsattaques['defenseur'] . '">' . $actionsattaques['defenseur'] . '</a></td><td id="affichage' . $actionsattaques['id'] . '">' . affichageTemps($actionsattaques['tempsAttaque'] - time()) . '</td></tr>';
                } else {
                    echo '<tr><td><img src="images/rapports/binoculars.png" class="imageChip" alt="espion"/></td><td><a href="joueur.php?id=' . $actionsattaques['defenseur'] . '">' . $actionsattaques['defenseur'] . '</a></td><td id="affichage' . $actionsattaques['id'] . '">' . affichageTemps($actionsattaques['tempsAttaque'] - time()) . '</td></tr>';
                }

                echo '
                <script>
                    var valeur' . $actionsattaques['id'] . ' = ' . ($actionsattaques['tempsAttaque'] - time()) . ';

                    function tempsDynamique' . $actionsattaques['id'] . '(){
                        if(valeur' . $actionsattaques['id'] . ' > 0){
                            valeur' . $actionsattaques['id'] . ' -= 1;
                            document.getElementById("affichage' . $actionsattaques['id'] . '").innerHTML = affichageTemps(valeur' . $actionsattaques['id'] . ');
                        }
                        else {
                            document.location.href="attaquer.php";
                        }
                    }

                    setInterval(tempsDynamique' . $actionsattaques['id'] . ', 1000);
                    </script>';
            } else {
                echo '<tr><td><a href="attaque.php?id=' . $actionsattaques['id'] . '"><img src="images/attaquer/retour.png" class="imageChip" alt="epee"/></a></td><td>Retour</td><td id="affichage' . $actionsattaques['id'] . '">' . affichageTemps($actionsattaques['tempsRetour'] - time()) . '</td></tr>';
                echo '<script>
                    var valeur' . $actionsattaques['id'] . ' = ' . ($actionsattaques['tempsRetour'] - time()) . ';

                    function tempsDynamique' . $actionsattaques['id'] . '(){
                        if(valeur' . $actionsattaques['id'] . ' > 0){
                            valeur' . $actionsattaques['id'] . ' -= 1;
                            document.getElementById("affichage' . $actionsattaques['id'] . '").innerHTML = affichageTemps(valeur' . $actionsattaques['id'] . ');
                        }
                        else {
                            document.location.href="attaquer.php";
                        }
                    }

                    setInterval(tempsDynamique' . $actionsattaques['id'] . ', 1000);
                    </script>';
            }
        } else {
            if ($actionsattaques['troupes'] != 'Espionnage' && $actionsattaques['attaqueFaite'] == 0) {
                echo '<tr><td><img src="images/batiments/shield.png" class="imageChip" alt="bouclier"/></td><td><a href="joueur.php?id=' . $actionsattaques['attaquant'] . '">' . $actionsattaques['attaquant'] . '</a></td><td>?</td>';
            }
        }
    }
    echo '</table></div>';
    finCarte();
}


if (!isset($_GET['type'])) {
    $_GET['type'] = 0;
}

if ($_GET['type'] == 0) {
    debutCarte("Carte" . aide("carte"), "", false, 'conteneurCarte');
    $tailleTile = 80;
    $centre = ['x' => $membre['x'], 'y' => $membre['y']];

    $ex = query('SELECT tailleCarte FROM statistiques');
    $tailleCarte = mysqli_fetch_array($ex);

    $carte = [];
    for ($i = 0; $i < $tailleCarte['tailleCarte']; $i++) {
        $temp = [];
        for ($j = 0; $j < $tailleCarte['tailleCarte']; $j++) {
            $temp[] = 0;
        }
        $carte[] = $temp;
    }

    if (isset($_GET['x'])) {
        $x = antiXSS($_GET['x']);
    } else {
        $x = $centre['x'];
    }

    if (isset($_GET['y'])) {
        $y = antiXSS($_GET['y']);
    } else {
        $y = $centre['y'];
    }

    $ex = query('SELECT * FROM membre');
    while ($tableau = mysqli_fetch_array($ex)) {
        $ex1 = query('SELECT points,idalliance FROM autre WHERE login=\'' . $tableau['login'] . '\'');
        $points = mysqli_fetch_array($ex1);

        $exGuerre = mysqli_query($base, 'SELECT count(*) AS estEnGuerre FROM declarations WHERE type=0 AND ((alliance1=\'' . $points['idalliance'] . '\' AND alliance2=\'' . $autre['idalliance'] . '\') OR (alliance2=\'' . $points['idalliance'] . '\' AND alliance1=\'' . $autre['idalliance'] . '\')) AND fin=0');
        $guerre = mysqli_fetch_array($exGuerre);

        $exPacte = mysqli_query($base, 'SELECT count(*) AS estEnPacte FROM declarations WHERE type=1 AND ((alliance1=\'' . $points['idalliance'] . '\' AND alliance2=\'' . $autre['idalliance'] . '\') OR (alliance2=\'' . $points['idalliance'] . '\' AND alliance1=\'' . $autre['idalliance'] . '\')) AND valide!=0');
        $pacte = mysqli_fetch_array($exPacte);

        if ($tableau['login'] == $_SESSION['login']) {
            $type = 'soi';
        } elseif ($guerre['estEnGuerre'] > 0) {
            $type = 'guerre';
        } elseif ($pacte['estEnPacte'] > 0) {
            $type = 'pacte';
        } elseif ($points['idalliance'] == $autre['idalliance'] && $autre['idalliance'] != 0) {
            $type = 'alliance';
        } else {
            $type = 'rien';
        }
        $carte[$tableau['x']][$tableau['y']] = [$tableau['id'], $tableau['login'], $points['points'], $type];
    }

?>
    <div style="width:600px;height:300px;" id="carte">
        <?php
        for ($i = 0; $i < $tailleCarte['tailleCarte']; $i++) {
            for ($j = 0; $j < $tailleCarte['tailleCarte']; $j++) {
                if ($carte[$i][$j] != 0) {
                    if ($carte[$i][$j][2] <= floor($nbPointsVictoire / 16)) {
                        $image = "petit.png";
                    } elseif ($carte[$i][$j][2] <= floor($nbPointsVictoire / 8)) {
                        $image = "moyen.png";
                    } elseif ($carte[$i][$j][2] <= floor($nbPointsVictoire / 4)) {
                        $image = "grand.png";
                    } elseif ($carte[$i][$j][2] <= floor($nbPointsVictoire / 2)) {
                        $image = "tgrand.png";
                    } else {
                        $image = "geant.png";
                    }

                    if ($carte[$i][$j][3] == 'soi') {
                        $border = 'orange 2px';
                    } elseif ($carte[$i][$j][3] == 'guerre') {
                        $border = 'red 2px';
                    } elseif ($carte[$i][$j][3] == 'alliance') {
                        $border = 'blue 2px';
                    } elseif ($carte[$i][$j][3] == 'pacte') {
                        $border = 'green 2px';
                    } else {
                        $border = 'lightgray 1px';
                    }

                    echo '<a href="joueur.php?id=' . $carte[$i][$j][1] . '"><img src="images/carte/' . $image . '" style="position:absolute;display:block;top:' . ($i * $tailleTile) . 'px;left:' . ($j * $tailleTile) . 'px;outline:' . $border . ' solid;width:' . $tailleTile . 'px;height:' . $tailleTile . 'px;" /></a><span style="text-align:center;position:absolute;display:block;top:' . ($i * $tailleTile) . 'px;left:' . ($j * $tailleTile) . 'px;width:' . $tailleTile . 'px;opacity:0.7;background-color:black;color:white;">' . $carte[$i][$j][1] . '</span>';
                } else {
                    echo '<img src="images/carte/rien.png" style="position:absolute;display:block;top:' . ($i * $tailleTile) . 'px;left:' . ($j * $tailleTile) . 'px;outline:lightgray 1px solid" />';
                }
            }
        }
        ?>
    </div>
    <script>
        document.getElementById('conteneurCarte').scrollTop = Math.max(0, parseInt(<?php echo $tailleTile * ($x + 0.5); ?> - document.getElementById('conteneurCarte').offsetHeight / 2));
        document.getElementById('conteneurCarte').scrollLeft = Math.max(0, parseInt(<?php echo $tailleTile * ($y + 0.5); ?> - document.getElementById('conteneurCarte').offsetWidth / 2));
    </script>
<?php
    finCarte();
}

if (isset($_GET['id'])) {
    $_GET['id'] = antiXSS($_GET['id']);

    $ex = query('SELECT * FROM membre WHERE login=\'' . $_GET['id'] . '\'');
    $nb = mysqli_num_rows($ex);
    $joueur = mysqli_fetch_array($ex);

    if ($nb > 0) {
        if ($_GET['type'] == 1) {
            debutCarte("Attaquer");
            echo '<form method="post" action="attaquer.php" name="formAttaquer">';
            $sql = 'SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' ORDER BY numeroclasse';
            $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));

            $distance = pow(pow($membre['x'] - $joueur['x'], 2) + pow($membre['y'] - $joueur['y'], 2), 0.5);
            scriptAffichageTemps();

            $res = array();
            echo important("Cible");

            echo chip(joueur($joueur['login']), '<img alt="coupe" src="images/classement/joueur.png" class="imageChip" style="width:25px;border-radius:0px;"/>', "white", false, true);
            echo chip('<a href="attaquer.php?x=' . $joueur['x'] . '&y=' . $joueur['y'] . '">' . $joueur['x'] . ';' . $joueur['y'] . ' - ' . (round(10 * $distance) / 10) . ' cases</a>', '<img alt="coupe" src="images/attaquer/map.png" class="imageChip" style="width:25px;border-radius:0px;"/>', "white", false, true);

            echo '<br/><br/>' . important("Coûts");
            echo chipInfo('0:00:00', 'images/molecule/temps.png', 'tempsAttaque');
            echo nombreEnergie(0, 'coutEnergie');
            echo '<br/><br/>';
            echo important("Troupes attaquantes");
            debutListe();

            $ex1 = query('SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND formule!="Vide"');

            $nombreClasses = mysqli_num_rows($ex1);
            if ($nombreClasses == 0) {
                echo 'Vous n\'avez aucune molécule et vous ne pouvez donc pas attaquer.';
            } else {
                while ($molecules = mysqli_fetch_array($ex)) {
                    if ($molecules['formule'] != "Vide") {
                        item(['titre' => '<a href="molecule.php?id=' . $molecules['id'] . '" class="lienFormule">' . couleurFormule($molecules['formule']) . '</a>', 'floating' => false, 'input' => '<input type="number" name="nbclasse' . $molecules['numeroclasse'] . '" id="nbclasse' . $molecules['numeroclasse'] . '" placeholder="Nombre" />', 'after' => nombreMolecules('<a href="javascript:document.getElementById(\'nbclasse' . $molecules['numeroclasse'] . '\').value = ' . ceil($molecules['nombre']) . ';actualiseTemps();actualiseCout();" class="lienVisible">' . ceil($molecules['nombre']) . '</a>')]);
                    }
                }
                echo '<input type="hidden" name="joueurAAttaquer" value="' . $joueur['login'] . '"/><br/>';
                echo submit(['titre' => 'Attaquer', 'image' => 'images/attaquer/attaquer.png', 'form' => 'formAttaquer']);
                finListe();
            }
            echo '</form>';

            $sql = 'SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND formule!=\'Vide\' ORDER BY numeroclasse';
            $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
            $nbClasses = mysqli_num_rows($ex);
            // affichage du temps pour attaquer
            echo '
            <script>
            var tempsEnCours = 0;
            var tempsAttaque = [];
            
            function actualiseTemps(){
                tempsEnCours = 0;
                ';

            for ($i = 1; $i <= $nbClasses; $i++) {
                echo '
                    if(document.getElementById("nbclasse' . $i . '").value > 0){
                        tempsEnCours = Math.max(tempsEnCours,tempsAttaque[' . ($i - 1) . ']);
                    }
                    ';
            }
            echo '
                document.getElementById("tempsAttaque").innerHTML = affichageTemps(tempsEnCours);
                return tempsEnCours;
            }
            
            function actualiseCout(){
                var cout = 0;
                ';
            for ($i = 1; $i <= $nbClasses; $i++) {
                $ex1 = query('SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND numeroclasse=\'' . $i . '\'');
                $molecules1 = mysqli_fetch_array($ex1);
                $totAtomes = 0;
                foreach ($nomsRes as $num => $res) {
                    $totAtomes += $molecules1[$res];
                }
                echo '
                cout += document.getElementById("nbclasse' . $i . '").value*' . ($totAtomes * $coutPourUnAtome) . ';';
            }
            echo '
                document.getElementById("coutEnergie").innerHTML = nFormatter(cout);
            }
            
            ';


            $c = 1;
            while ($molecules = mysqli_fetch_array($ex)) {
                echo 'tempsAttaque[' . ($c - 1) . '] = ' . round($distance / vitesse($molecules['chlore'], $niveauchlore) * 3600) . ';';
                echo 'document.getElementById("nbclasse' . $molecules['numeroclasse'] . '").addEventListener("input",function(){
                        var nbUnites = document.getElementById("nbclasse' . $molecules['numeroclasse'] . '").value;
                        if(nbUnites > 0){
                            document.getElementById("tempsAttaque").innerHTML = affichageTemps(Math.max(tempsAttaque[' . ($c - 1) . '],tempsEnCours));
                            tempsEnCours = Math.max(tempsAttaque[' . ($c - 1) . '],tempsEnCours);
                        }
                        else {
                            tempsEnCours = actualiseTemps();
                        }
                        
                        actualiseCout();
                    });';
                $c++;
            }
            echo '
            </script>
            ';

            finCarte();
        } elseif ($_GET['type'] == 2) {
            debutCarte("Espionner");
            echo '<form method="post" action="attaquer.php" name="formEspionner">';
            echo important("Cible");

            echo chip($joueur['login'], '<img alt="coupe" src="images/classement/joueur.png" class="imageChip" style="width:25px;border-radius:0px;"/>', "white", false, true);
            echo chip('<a href="attaquer.php?x=' . $joueur['x'] . '&y=' . $joueur['y'] . '">' . $joueur['x'] . ';' . $joueur['y'] . ' - ' . (round(10 * (pow(pow($membre['x'] - $joueur['x'], 2) + pow($membre['y'] - $joueur['y'], 2), 0.5))) / 10) . ' cases</a>', '<img alt="coupe" src="images/attaquer/map.png" class="imageChip" style="width:25px;border-radius:0px;"/>', "white", false, true);
            echo '<br/><br/>';

            echo important("Neutrinos");
            debutListe();
            item(['input' => '<input type="number" min="0" max="' . $autre['neutrinos'] . '" name="nombreneutrinos" id="nombreneutrinos" class="form-control" placeholder="Nombre de neutrinos"/>', 'after' => nombreNeutrino($autre['neutrinos'])]);
            finListe();
            echo '<br/><br/>';

            echo important("Coût");
            echo nombreTemps(affichageTemps(3600 * pow(pow($membre['x'] - $joueur['x'], 2) + pow($membre['y'] - $joueur['y'], 2), 0.5) / $vitesseEspionnage));

            echo '<input type="hidden" name="joueurAEspionner" value="' . $joueur['login'] . '"/><br/><br/>';
            echo submit(['titre' => 'Espionner', 'image' => 'images/attaquer/espionner.png', 'form' => 'formEspionner']);

            finCarte();
        }
    } else {
        debutCarte("Dommage");
        debutContent();
        echo 'Ce joueur n\'existe pas.';
        finContent();
        finCarte();
    }
}

// Affichage avant attaque et/ou espionnage

include("includes/copyright.php"); ?>