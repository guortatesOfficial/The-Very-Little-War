<?php
include("includes/basicprivatephp.php");
include("includes/redirectionVacance.php");

if (isset($_POST['emplacementmoleculesupprimer']) and !empty($_POST['emplacementmoleculesupprimer']) and preg_match("#^[0-9]*$#", $_POST['emplacementmoleculesupprimer']) and $_POST['emplacementmoleculesupprimer'] <= 5) { // Si l'on veut supprimer une classe de molécules
    $sql2 = 'SELECT formule,id FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND numeroclasse=\'' . $_POST['emplacementmoleculesupprimer'] . '\'';
    $ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysql_error());
    $molecules = mysqli_fetch_array($ex2);

    if ($molecules['formule'] != "Vide") {
        $sql3 = 'SELECT niveauclasse FROM ressources WHERE login=\'' . $_SESSION['login'] . '\'';
        $ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysql_error());
        $niveauclasse = mysqli_fetch_array($ex3);
        $sql1 = 'UPDATE ressources SET niveauclasse = \'' . ($niveauclasse['niveauclasse'] - 1) . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
        $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());

        $chaine = ""; // on passe toutes les chaines sauf conditions pour les ressources en dynamique 
        foreach ($nomsRes as $num => $ressource) {
            $plus = "";
            if ($num < $nbRes) {
                $plus = ",";
            }
            $chaine = $chaine . '' . $ressource . '=default' . $plus;
        }

        $sql = 'UPDATE molecules SET formule = default, ' . $chaine . ', nombre = default WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND numeroclasse=\'' . $_POST['emplacementmoleculesupprimer'] . '\'';
        $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());

        query('DELETE FROM actionsformation WHERE login=\'' . $_SESSION['login'] . '\' AND idclasse=\'' . $molecules['id'] . '\''); // on enleve les formations en cours, il faut vérifier maitenant s'il y en a d'autres derriere et les mettre à jour
        $nvxDebut = time();
        $exActuActions = query('SELECT * FROM actionsformation WHERE login=\'' . $_SESSION['login'] . '\'');
        while ($actionsformation = mysqli_fetch_array($exActuActions)) {
            if (time() < $actionsformation['debut']) {
                query('UPDATE actionsformation SET debut=\'' . $nvxDebut . '\', fin=\'' . ($nvxDebut + $actionsformation['nombreRestant'] * $actionsformation['tempsPourUn']) . '\' WHERE id=\'' . $actionsformation['id'] . '\'');
                $nvxDebut = $nvxDebut + $actionsformation['nombreRestant'] * $actionsformation['tempsPourUn'];
            } else {
                $nvxDebut = $actionsformation['fin'];
            }
        }

        // on enleve ces types de molécules dans les attaques lancées
        $ex = query('SELECT * FROM actionsattaques WHERE attaquant=\'' . $_SESSION['login'] . '\'');
        while ($actionsattaques = mysqli_fetch_array($ex)) {
            $explosion = explode(";", $actionsattaques['troupes']);
            $chaine = "";
            for ($i = 1; $i < $nbClasses; $i++) {
                if ($i == $_POST['emplacementmoleculesupprimer']) {
                    $chaine = "0;";
                } else {
                    $chaine = $explosion[$i - 1] . ";";
                }
            }

            query('UPDATE actionsattaques SET troupes=\'' . $chaine . '\' WHERE id=\'' . $actionsattaques['id'] . '\'');
        }

        $information = "Vous avez supprimé la classe de molécules.";
    } else {
        $erreur = "Cet emplacement est déja vide.";
    }
}

// NEUTRINOS
if (isset($_POST['nombreneutrinos']) and !empty($_POST['nombreneutrinos'])) {
    $_POST['nombreneutrinos'] = transformInt($_POST['nombreneutrinos']);
    if (preg_match("#^[0-9]*$#", $_POST['nombreneutrinos']) and $_POST['nombreneutrinos'] >= 1) {
        $_POST['nombreneutrinos'] = antiXSS($_POST['nombreneutrinos']);
        if ($_POST['nombreneutrinos'] * $coutNeutrino <= $ressources['energie']) {
            /*$ex = query('SELECT * FROM actionsformation WHERE login=\''.$_SESSION['login'].'\' ORDER BY fin DESC');
                $nb = mysqli_num_rows($ex);
                if($nb > 0){ // s'il y a deja quelque chose en cours, on le met derriere
                    $actionsformation = mysqli_fetch_array($ex);
                    $tempsDebut = $actionsformation['fin'];
                }
                else {
                    $tempsDebut = time();
                }*/

            //query('INSERT INTO actionsformation VALUES("","neutrino","'.$_SESSION['login'].'","'.$tempsDebut.'","'.($tempsDebut+$_POST['nombreneutrinos']*$tempsNeutrino).'","'.$_POST['nombreneutrinos'].'","'.$_POST['nombreneutrinos'].'","neutrinos","'.$tempsNeutrino.'")');

            query('UPDATE autre SET neutrinos=\'' . ($autre['neutrinos'] + $_POST['nombreneutrinos']) . '\' WHERE login=\'' . $_SESSION['login'] . '\'');
            $autre['neutrinos'] += $_POST['nombreneutrinos'];

            $sql3 = 'UPDATE ressources SET energie=\'' . ($ressources['energie'] - $_POST['nombreneutrinos'] * $coutNeutrino) . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
            $req3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysql_error());
            query('UPDATE autre SET energieDepensee=\'' . ($autre['energieDepensee'] + $_POST['nombreneutrinos'] * $coutNeutrino) . '\' WHERE login=\'' . $_SESSION['login'] . '\'');

            $information = 'Vous avez formé ' . $_POST['nombreneutrinos'] . ' neutrinos.';
        } else {
            $erreur = "Vous n'avez pas assez d'énergie.";
        }
    } else {
        $erreur = "Seul des nombres positifs et entiers doivent être entrés.";
    }
}

if (isset($_POST['emplacementmoleculeformer']) and !empty($_POST['emplacementmoleculeformer']) and preg_match("#^[0-9]*$#", $_POST['emplacementmoleculeformer']) and $_POST['emplacementmoleculeformer'] <= 5) {
    $_POST['nombremolecules'] = transformInt($_POST['nombremolecules']);
    if (isset($_POST['nombremolecules']) and !empty($_POST['nombremolecules']) and preg_match("#^[0-9]*$#", $_POST['nombremolecules'])) {
        $_POST['nombremolecules'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['nombremolecules'])));
        $sql = 'SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\'AND numeroclasse=\'' . $_POST['emplacementmoleculeformer'] . '\'';
        $req = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
        $donneesFormer = mysqli_fetch_array($req);
        $sql1 = 'SELECT * FROM ressources WHERE login=\'' . $_SESSION['login'] . '\'';
        $req1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());;
        $ressources = mysqli_fetch_array($req1);

        $bool = 1;
        foreach ($nomsRes as $num => $ressource) {
            if (($donneesFormer[$ressource] * $_POST['nombremolecules']) > $ressources[$ressource]) {
                $bool = 0;
            }
        }
        if ($bool == 1) {
            if ($donneesFormer['formule'] != "Vide") {
                $sqlNbMolecules = 'SELECT nombre FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND nombre!=0';
                $exNbMolecules = mysqli_query($base, $sqlNbMolecules) or die('Erreur SQL !<br />' . $sqlNbMolecules . '<br />' . mysql_error());
                $nb_molecules = 0;
                /*while($nbMolecules = mysqli_fetch_array($exNbMolecules)) {
                    $nb_molecules = $nb_molecules + $nbMolecules['nombre'];
                }
                if(($_POST['nombremolecules'] + $nb_molecules) <= $ressources['terrain']) {*/
                $total = 0;
                foreach ($nomsRes as $num => $ressource) {
                    $total = $total + $donneesFormer[$ressource];
                }

                $ex = query('SELECT * FROM actionsformation WHERE login=\'' . $_SESSION['login'] . '\' ORDER BY fin DESC');
                $nb = mysqli_num_rows($ex);
                if ($nb > 0) { // s'il y a deja quelque chose en cours, on le met derriere
                    $actionsformation = mysqli_fetch_array($ex);
                    $tempsDebut = $actionsformation['fin'];
                } else {
                    $tempsDebut = time();
                }

                query('INSERT INTO actionsformation VALUES(default,"' . $donneesFormer['id'] . '","' . $_SESSION['login'] . '","' . $tempsDebut . '","' . ($tempsDebut + tempsFormation($donneesFormer['azote'], $niveauazote, $total, $_SESSION['login']) * $_POST['nombremolecules']) . '","' . $_POST['nombremolecules'] . '","' . $_POST['nombremolecules'] . '","' . $donneesFormer['formule'] . '","' . tempsFormation($donneesFormer['azote'], $niveauazote, $total, $_SESSION['login']) . '")');

                $chaine = ""; // on passe toutes les chaines sauf conditions pour les ressources en dynamique 
                foreach ($nomsRes as $num => $ressource) {
                    $plus = "";
                    if ($num < $nbRes) {
                        $plus = ",";
                    }
                    $chaine = $chaine . '' . $ressource . '=' . ($ressources[$ressource] - ($_POST['nombremolecules'] * $donneesFormer[$ressource])) . '' . $plus;
                }
                $sql3 = 'UPDATE ressources SET ' . $chaine . ' WHERE login=\'' . $_SESSION['login'] . '\'';
                $req3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysql_error());

                $information = 'Vous avez lancé la formation de ' . $_POST['nombremolecules'] . ' molécules de ' . couleurFormule($donneesFormer['formule']) . '';
                /*}
                else {
                    $erreur = "Vous n'avez pas assez d'espace.";
                }*/
            } else {
                $erreur = "Cet emplacement est vide.";
            }
        } else {
            $erreur = "Vous n'avez pas assez d'atomes.";
        }
    } else {
        $erreur = "Seul des nombres positifs et entiers doivent être entrés.";
    }
}

if (isset($_POST['emplacementmoleculecreer1']) and !empty($_POST['emplacementmoleculecreer1']) and preg_match("#^[0-9]*$#", $_POST['emplacementmoleculecreer1']) and $_POST['emplacementmoleculecreer1'] <= 5) {
    $bool = 1;
    foreach ($nomsRes as $num => $ressource) {
        if (!(isset($_POST[$ressource]) and preg_match("#^[0-9]*$#", $_POST[$ressource]))) {
            $bool = 0;
        }
    }
    if ($bool == 1) { // on vérifie que c'est un chiffre positif
        $bool = 1;
        foreach ($nomsRes as $num => $ressource) { // si on est en dessous de 200 atomes de chaque
            if ($_POST[$ressource] > 200) {
                $bool = 0;
            }
        }
        if ($bool == 1) {
            $bool = 1;
            foreach ($nomsRes as $num => $ressource) { // si on est en dessous de 200 atomes de chaque
                if (!empty($_POST[$ressource])) {
                    $bool = 0;
                }
            }
            if ($bool == 0) {
                $sql4 = 'SELECT formule FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND numeroclasse=\'' . $_POST['emplacementmoleculesupprimer'] . '\'';
                $ex4 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysql_error());
                $emplacement = mysqli_fetch_array($ex4);
                if($emplacement['formule'] == "Vide") {
                    $sql = 'SELECT energie, niveauclasse FROM ressources WHERE login=\'' . $_SESSION['login'] . '\'';
                    $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
                    $cout = mysqli_fetch_array($ex);
                    if ($cout['energie'] >= (coutClasse($cout['niveauclasse']))) {
                        $formule = "";
                        foreach ($nomsRes as $num => $ressource) {
                            if (!empty($_POST[$ressource])) {
                                $$ressource = $_POST[$ressource];
                                if ($_POST[$ressource] > 1) {
                                    $formule = '' . $formule . '' . $lettre[$num] . '<sub>' . $_POST[$ressource] . '</sub>';
                                } else {
                                    $formule = $formule . '' . $lettre[$num];
                                }
                            } else {
                                $$ressource = 0;
                            }
                        }

                        $sql1 = 'UPDATE ressources SET niveauclasse = \'' . ($cout['niveauclasse'] + 1) . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
                        $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());

                        $chaine = "";
                        foreach ($nomsRes as $num => $ressource) {
                            $plus = "";
                            if ($num < $nbRes) {
                                $plus = ",";
                            }
                            $chaine = $chaine . '' . $ressource . '=' . $$ressource . '' . $plus;
                        }
                        $sql2 = 'UPDATE molecules SET ' . $chaine . ', formule=\'' . $formule . '\' WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND numeroclasse=\'' . $_POST['emplacementmoleculecreer1'] . '\'';
                        $req2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysql_error());

                        $sql3 = 'UPDATE ressources SET energie = \'' . ($cout['energie'] - coutClasse($cout['niveauclasse'])) . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
                        $ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysql_error());

                        $information = "Une nouvelle classe de molécule a été créée.";
                    } else {
                        $erreur = "Vous n'avez pas assez d'energie.";
                    }
                } else {
                    $erreur = "Cette classe existe déjà.";
                }
            } else {
                $erreur = "Votre molécule doit au moins être composée d'un atome.";
            }
        } else {
            $erreur = "Les molécules ne doivent pas excéder 200 atomes de chaque.";
        }
    } else {
        $erreur = "Seul des nombres positifs et entiers doivent être entrés.";
    }
}

include("includes/tout.php");

$ex = mysqli_query($base, 'SELECT * FROM actionsformation WHERE login=\'' . $_SESSION['login'] . '\' ORDER BY debut ASC');
$nb = mysqli_num_rows($ex);
if ($nb > 0) {
    debutCarte();
    scriptAffichageTemps();
    echo '<div class="table-responsive"><table>';
    echo '<tr><th>Molécule</th><th>Prochaine</th><th>Total</th></tr>';

    $c = 0;
    while ($actionsformation = mysqli_fetch_array($ex)) {
        $offset = max(0, $actionsformation['debut'] - time());

        $ex1 = query('SELECT * FROM molecules WHERE id=\'' . $actionsformation['idclasse'] . '\'');
        $moleculeEnCours = mysqli_fetch_array($ex1);

        // rajouter formation dynamique (et non pas recharger, rehcarger uniquement si tout fini et surtout ne pas oublier de former les molécules en traitement dans updateActions)
        $tempsVirgule = $actionsformation['tempsPourUn']; // on a l'int pour les % et le double pour afficahge dynamique
        $actionsformation['tempsPourUn'] = ceil($actionsformation['tempsPourUn']);
        if ((($actionsformation['fin'] - time() - $offset) % $actionsformation['tempsPourUn']) == 0) {
            $prochaine = affichageTemps($offset + $actionsformation['tempsPourUn']);
        } else {
            $prochaine = affichageTemps($offset + ($actionsformation['fin'] - time() - $offset) % $actionsformation['tempsPourUn']);
        }

        if ($actionsformation['idclasse'] != 'neutrino') {
            $affichageFormule = couleurFormule($moleculeEnCours['formule']);
        } else {
            $affichageFormule =  $actionsformation['formule'];
        }

        echo '<tr><td><strong id="nombreRestants' . $actionsformation['id'] . '">' . $actionsformation['nombreRestant'] . '</strong> ' . $affichageFormule . '</td><td id="affichageProchain' . $actionsformation['id'] . '">' . $prochaine . '</td><td id="affichage' . $actionsformation['id'] . '">' . affichageTemps($actionsformation['fin'] - time()) . '</td></tr>';

        echo '
        <script>
            var valeur' . $actionsformation['id'] . ' = ' . ($actionsformation['fin'] - time()) . ';
            
            
            if(' . ($actionsformation['fin'] - time() - $offset) % $actionsformation['tempsPourUn'] . ' == 0){
                var valeurProchain' . $actionsformation['id'] . ' = ' . ($offset + $actionsformation['tempsPourUn']) . ';
            }
            else {
                var valeurProchain' . $actionsformation['id'] . ' = ' . ($offset + ($actionsformation['fin'] - time() - $offset) % $actionsformation['tempsPourUn']) . ';
            }

            function tempsDynamique' . $actionsformation['id'] . '(){
                var nombreParSeconde = 1/' . $tempsVirgule . ';
                var vraiRestant =  parseInt(document.getElementById("nombreRestants' . $actionsformation['id'] . '").innerHTML);
                if(valeur' . $actionsformation['id'] . ' != 0){
                    valeur' . $actionsformation['id'] . ' -= 1;
                    document.getElementById("affichage' . $actionsformation['id'] . '").innerHTML = affichageTemps(valeur' . $actionsformation['id'] . ');
                    
                    if(valeurProchain' . $actionsformation['id'] . ' == 0){
                        valeurProchain' . $actionsformation['id'] . ' = ' . ($offset + $actionsformation['tempsPourUn']) . ';
                        vraiRestant = vraiRestant-nombreParSeconde;
                        document.getElementById("nombreRestants' . $actionsformation['id'] . '").innerHTML = parseInt(vraiRestant);
                    }
                    valeurProchain' . $actionsformation['id'] . ' = valeurProchain' . $actionsformation['id'] . '-1;
                    
                    document.getElementById("affichageProchain' . $actionsformation['id'] . '").innerHTML = affichageTemps(valeurProchain' . $actionsformation['id'] . ');
                }
                else {
                    document.location.href="armee.php";
                }
            }
            
            setInterval(tempsDynamique' . $actionsformation['id'] . ', 1000);
            </script>';
        $c++; // sert a savoir si c'est la formation en cours
    }
    echo '</table></div>';
    finCarte();
}

if (isset($_POST['emplacementmoleculecreer'])) {
    debutCarte("Composition de la classe" . aide("composition"));
    echo '<form action="armee.php" method="post" name="creernouvelleclasse1">';
    debutListe();
    foreach ($nomsRes as $num => $ressource) {
        item(['titre' => nombreAtome($num, 'Nombre ' . pref($ressource) . '<strong>' . $nomsAccents[$num] . '</strong>') . aide($ressource, true), 'input' => '<input type="number" name="' . $ressource . '" id="' . $ressource . '" placeholder="' . $utilite[$num] . '" class="form-control" oninput="javascript:actualiserStats()"/>']);
    }  ?>
    <input type="hidden" name="emplacementmoleculecreer1" value="<?php echo $_POST['emplacementmoleculecreer']; ?>" />
<?php
    item(['input' => submit(['form' => 'creernouvelleclasse1', 'titre' => 'Créer'])]);
    finListe();
    echo '</form>';
    finCarte();
}

if (!isset($_GET['sub']) || $_GET['sub'] == 0) {
    debutCarte('Molécule ' . aide('armee'));
    $sql = 'SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' ORDER BY numeroclasse';
    $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
    $nbclasse = mysqli_num_rows($ex);

    $sql1 = 'SELECT * FROM ressources WHERE login=\'' . $_SESSION['login'] . '\'';
    $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());
    $ressources = mysqli_fetch_array($ex1);
    $compteur = 0;
    while ($molecule = mysqli_fetch_array($ex)) {
        echo '<form action="armee.php" method="post"><img src="images/' . $molecule['numeroclasse'] . '.png" alt="' . $molecule['numeroclasse'] . '" style="vertical-align: middle;height:40px;width:40px;"/>';
        echo '<a href="molecule.php?id=' . $molecule['id'] . '" style="margin-left: 20px;font-weight:bold;" class="lienFormule">' . couleurFormule($molecule['formule']) . '</a>  ';
        echo nombreMolecules(ceil($molecule['nombre']));
        if ($molecule['formule'] != "Vide") {
            $nbmoleculesMax = 10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000;
            foreach ($nomsRes as $num => $ressource) {
                if ($molecule[$ressource] > 0) {
                    $nbmoleculesMax = min($nbmoleculesMax, floor($ressources[$ressource] / $molecule[$ressource]));
                }
            }

            echo '
            <input type="hidden" name="emplacementmoleculesupprimer" value="' . $molecule['numeroclasse'] . '"/>
            <input src="images/croix.png" class="w32" alt="supprimer" type="image" value="Supprimer" style="vertical-align: middle;float:right;" name="supprimermolecule" title="Supprimer la classe de molécule">';

            echo '</form></br><br/>';
            debutListe();
            item(['titre' => 'Former', 'form' => ['armee.php', 'formermolecule' . $molecule['numeroclasse']], 'input' => '<input type="text" name="nombremolecules" id="nombremolecules" class="form-control" placeholder="Nombre de molécules"/><input type="hidden" name="emplacementmoleculeformer" value="' . $molecule['numeroclasse'] . '"/>', 'after' => '<a name="generer" id="generer" onclick="javascript:document.getElementsByName(\'nombremolecules\')[' . $compteur . '].value = ' . $nbmoleculesMax . ';" value="Générer" class="button button-raised button-fill" style="margin-right:5px">Max : ' . chiffrePetit($nbmoleculesMax, 0) . '</a>']);
            item(['input' => submit(['form' => 'formermolecule' . $molecule['numeroclasse'], 'titre' => 'Former'])]);
            if ($compteur != 3) {
                echo '<hr class="corps"><br/>';
            }
            $compteur++;
            //echo "<a onclick=\"javascript:document.getElementsByName('nombremolecules')[0].value = '$nbmoleculesMax';\">(Max : {$nbmoleculesMax})</a>";';
            finListe();
        } else {
            $sql1 = 'SELECT niveauclasse FROM ressources WHERE login=\'' . $_SESSION['login'] . '\'';
            $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());
            $cout = mysqli_fetch_array($ex1);
            echo '<input src="images/plus.png" alt="creer" type="image" value="Créer" name="creernouvelleclasse" title="Créer une classe de molécule" style="vertical-align: middle;float:right;" class="w32">
            <input type="hidden" name="emplacementmoleculecreer" value="' . $molecule['numeroclasse'] . '"/> ' . coutEnergie(coutClasse($cout['niveauclasse'])) . '<hr/></form>';
        }
    }

    finCarte();

    debutCarte("Neutrinos" . aide('neutrinos'));
    echo nombreNeutrino($autre['neutrinos']);
    echo coutEnergie($coutNeutrino) . '<br/><br/>';
    debutListe();
    item(['titre' => 'Former', 'form' => ['armee.php', 'formerneutrino'], 'input' => '<input type="text" name="nombreneutrinos" id="nombreneutrinos" class="form-control" placeholder="Nombre de neutrinos"/>']);
    item(['input' => submit(['form' => 'formerneutrino', 'titre' => 'Former'])]);
    finListe();
    finCarte();
} else {
    debutCarte("Armée" . aide("vueEnsemble"));
    debutContent();
    $sql = 'SELECT * FROM molecules WHERE proprietaire=\'' . $_SESSION['login'] . '\' AND formule!="Vide" ORDER BY numeroclasse';
    $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
    $nbclasse = mysqli_num_rows($ex);
?>
    <div class="reponsive-table">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th style="width:100px;"><?php echo imageLabel('<img alt="molecule" src="images/classement/molecule.png" title="Formule" class="imageSousMenu"/>', 'Formule'); ?></th>
                    <th style="width:100px;">Quantité</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($molecule = mysqli_fetch_array($ex)) {
                    $mx = $molecule['oxygene'];
                    foreach ($nomsRes as $num => $ressource) {
                        $mx = max($mx, $molecule[$ressource]);
                    }
                    foreach ($nomsRes as $num => $ressource) {
                        if ($mx == $molecule[$ressource]) {
                            $img = $ressource;
                        }
                    }

                    echo '<tr><td><img alt="' . $img . '" src="images/accueil/' . $img . '.png" class="imageAide2">
        <a href="molecule.php?id=' . $molecule['id'] . '" class="lienFormule">' . couleurFormule($molecule['formule']) . '</a></td>
        <td>' . number_format($molecule['nombre'], 0, ' ', ' ') . '</td>
        </tr>';
                }

                if ($autre['neutrinos'] > 0) {
                    echo '<tr><td><img alt="neutrinos" src="images/neutrino.png" class="imageAide2">
        <span style="margin-left:8px">Neutrinos</span></td>
        <td>' . number_format($autre['neutrinos'], 0, ' ', ' ') . '</td>
        </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
    finContent();
    finCarte();
}
include("includes/copyright.php"); ?>