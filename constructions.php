<?php
include("includes/basicprivatephp.php");
include("includes/redirectionVacance.php");

// traitement des points à placer pour le producteur et le generateur
if (isset($_POST['nbPointshydrogene'])) { // un au hasard juste pour le formulaire
    $somme = 0;
    $bool = true;

    foreach ($nomsRes as $num => $ressource) {
        $_POST['nbPoints' . $ressource] = intval(antiXSS($_POST['nbPoints' . $ressource]));
        if ($_POST['nbPoints' . $ressource] < 0) {
            $bool = false;
            echo $_POST['nbPoints' . $ressource];
        } else {
            $somme = $somme + $_POST['nbPoints' . $ressource];
        }
    }


    if ($bool && $somme <= $constructions['pointsProducteurRestants']) {
        $chaine = "";
        foreach ($nomsRes as $num => $ressource) {
            $plus = "";
            if ($num - 1 < sizeof($nomsRes)) {
                $plus = ";";
            }

            $chaine = $chaine . ($_POST['nbPoints' . $ressource] + ${'points' . $ressource}) . $plus;
        }

        query('UPDATE constructions SET pointsProducteurRestants=\'' . ($constructions['pointsProducteurRestants'] - $somme) . '\', pointsProducteur=\'' . $chaine . '\' WHERE login=\'' . $_SESSION['login'] . '\'');

        $information = "Les points du producteur ont été sauvegardés.";
        echo '<script>document.location.href="constructions.php?information=' . $information . '"</script>';
    } else {
        $erreur = "Le nombre de points n'est pas valide.";
    }
}

if (isset($_POST['nbPointsCondenseurhydrogene'])) { // un au hasard juste pour le formulaire
    $somme = 0;
    $bool = true;

    foreach ($nomsRes as $num => $ressource) {
        $_POST['nbPointsCondenseur' . $ressource] = intval(antiXSS($_POST['nbPointsCondenseur' . $ressource]));
        if ($_POST['nbPointsCondenseur' . $ressource] < 0) {
            $bool = false;
            echo $_POST['nbPointsCondenseur' . $ressource];
        } else {
            $somme = $somme + $_POST['nbPointsCondenseur' . $ressource];
        }
    }


    if ($bool && $somme <= $constructions['pointsCondenseurRestants']) {
        $chaine = "";
        foreach ($nomsRes as $num => $ressource) {
            $plus = "";
            if ($num - 1 < sizeof($nomsRes)) {
                $plus = ";";
            }

            $chaine = $chaine . ($_POST['nbPointsCondenseur' . $ressource] + ${'niveau' . $ressource}) . $plus;
        }

        query('UPDATE constructions SET pointsCondenseurRestants=\'' . ($constructions['pointsCondenseurRestants'] - $somme) . '\', pointsCondenseur=\'' . $chaine . '\' WHERE login=\'' . $_SESSION['login'] . '\'');

        $information = "Les points du condenseur ont été sauvegardés.";
        echo '<script>document.location.href="constructions.php?information=' . $information . '"</script>';
    } else {
        $erreur = "Le nombre de points n'est pas valide.";
    }
}

// FONCTIONS CONSTRUCTIONS

function mepConstructions($liste)
{
    global $nomsRes;
    global $placeDepot;
    global $ressources;
    global $revenuEnergie;
    global $revenu;

    // on doit calculer le niveau actuel (et dans le futur avec les constructions le précédant)
    $exNiveauActuel = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $_SESSION['login'] . '\' AND batiment=\'' . $liste['bdd'] . '\' ORDER BY niveau DESC');
    $niveauActuel = mysqli_fetch_array($exNiveauActuel);
    $nb = mysqli_num_rows($exNiveauActuel);
    if ($nb == 0) {
        $niveauActuel['niveau'] = $liste['niveau'];
    }

    if (array_key_exists("progressBar", $liste) && $liste['progressBar']) {
        $media = '<img alt="za" src="' . $liste['image'] . '" style="width:80px;height:80px;margin-top:-54px;"/><div style="margin-left:-80px;margin-top:-10px;">' . progressBar($liste['vie'], $liste['vieMax'], "green") . '</div>';
    } else {
        $media = '<img alt="za" src="' . $liste['image'] . '" style="width:80px;height:80px;"/>';
    }

    $cout = "";
    if (array_key_exists("coutEnergie", $liste) && $liste['coutEnergie']) {
        $cout = $cout . coutEnergie($liste['coutEnergie']);
    } else {
        $liste['coutEnergie'] = 0;
    }

    foreach ($nomsRes as $num => $ressource) {
        if (array_key_exists('cout' . ucfirst($ressource), $liste) && $liste['cout' . ucfirst($ressource)]) {
            $cout = $cout . coutAtome($num, $liste['cout' . ucfirst($ressource)]);
        } else {
            $liste['cout' . ucfirst($ressource)] = 0;
        }
    }

    if (array_key_exists("coutAtomes", $liste) && $liste['coutAtomes']) {
        $cout = $cout . coutTout($liste['coutAtomes']);
        foreach ($nomsRes as $num => $ressource) {
            $liste['cout' . ucfirst($ressource)] = $liste['coutAtomes'];
        }
    }

    $bool = 1;

    foreach ($nomsRes as $num => $ressource) {
        if ($liste['cout' . ucfirst($ressource)] > $ressources[$ressource]) {
            $bool = 0;
        }
    }

    $ex = query('SELECT count(*) as nb FROM actionsconstruction WHERE login=\'' . $_SESSION['login'] . '\'');
    $nb = mysqli_fetch_array($ex);
    if ($nb['nb'] < 2) {
        if ($liste['coutEnergie'] >= $ressources['energie'] or $bool == 0) {
            $bool1 = 1;

            foreach ($nomsRes as $num => $ressource) {
                if ($liste['cout' . ucfirst($ressource)] > $placeDepot) {
                    $bool1 = 0;
                }
            }

            if ($placeDepot >= $liste['coutEnergie'] and $bool1 == 1) {
                $max = 3600 * ($liste['coutEnergie'] - $ressources['energie']) / $revenu['energie'];
                foreach ($nomsRes as $num => $ressource) {
                    $max = max(3600 * ($liste['cout' . ucfirst($ressource)] - $ressources[$ressource]) / $revenu[$ressource], $max);
                }
                $augmenter =  'Assez de ressources le ' . date('d/m/Y', time() + $max) . ' à ' . date('H\hi', time() + $max);
            } else {
                $augmenter =  'L\'espace de stockage est trop petit pour autant de ressources.';
            }
        } else {
            $augmenter =  '<input type="hidden" value="Augmenter au niveau ' . ($niveauActuel['niveau'] + 1) . '" name="' . $liste['bdd'] . '" id="' . $liste['bdd'] . '"/>' . submit(['titre' => 'Niveau ' . ($niveauActuel['niveau'] + 1), 'form' => 'form' . $liste['bdd'], 'nom' => $liste['bdd'], 'image' => 'images/boutons/arrow.png']);
        }
    } else {
        $augmenter = 'Vous avez déjà <strong>deux constructions</strong> en cours.';
    }

    $drainage = '';
    if (array_key_exists("drainage", $liste) && $liste['drainage']) {
        $drainage = nombreEnergie('<span style="color:red">-' . $liste['drainage'] . '/h</span>');
    }

    item([
        'titre' => $liste['titre'],
        'media' => $media,
        'soustitre' => '<strong>Niveau ' . $liste['niveau'] . '</strong><br/>' . $liste['revenu'],
        'accordion' => debutContent(true, true) . $liste['description'] . finContent(true, true) .
            '<br/><br/>' . debutContent(false, true) . $liste['revenu'] . ' au <strong>niveau ' . $liste['niveau'] . '</strong><br/>
          ' . $liste['revenu1'] . ' au <strong> niveau ' . ($niveauActuel['niveau'] + 1) . '</strong>
          ' . $liste['effetSup'] . '<br/><br/>' . finContent(false, true) . '
          <form action="constructions.php" method="post" name="form' . $liste['bdd'] . '">' .
            important('Augmenter') . '
          ' . $cout . $drainage . nombreTemps(affichageTemps($liste['tempsConstruction'])) . nombrePoints('+' . $liste['points']) . '<br/><br/>
          ' . $augmenter . '</form><hr>'
    ]);
}

function traitementConstructions($liste)
{
    global $nomsRes;
    global $ressources;
    global $nbRes;
    global $base;
    global $constructions;
    global $ressources;
    global $autre;

    if (isset($_POST[$liste['bdd']])) {
        $ex = query('SELECT count(*) as nb FROM actionsconstruction WHERE login=\'' . $_SESSION['login'] . '\'');
        $nb = mysqli_fetch_array($ex);

        if ($nb['nb'] < 2) {
            if (!array_key_exists("coutEnergie", $liste)) {
                $liste['coutEnergie'] = 0;
            }

            foreach ($nomsRes as $num => $ressource) {
                if (!array_key_exists('cout' . ucfirst($ressource), $liste)) {
                    $liste['cout' . ucfirst($ressource)] = 0;
                }
            }

            foreach ($nomsRes as $num => $ressource) {
                if (array_key_exists("coutAtomes", $liste) && $liste['coutAtomes']) {
                    $liste['cout' . ucfirst($ressource)] = $liste['coutAtomes'];
                }
            }


            $bool = 1;

            foreach ($nomsRes as $num => $ressource) {
                if ($ressources[$ressource] < $liste['cout' . ucfirst($ressource)]) {
                    $bool = 0;
                }
            }

            if ($ressources['energie'] >= $liste['coutEnergie'] and $bool == 1) {
                $chaine = "";

                foreach ($nomsRes as $num => $ressource) {
                    $plus = "";
                    if ($num < $nbRes) {
                        $plus = ",";
                    }
                    $chaine = $chaine . $ressource . '=' . ($ressources[$ressource] - $liste['cout' . ucfirst($ressource)]) . $plus;
                }

                $sql2 = 'UPDATE ressources SET energie=\'' . ($ressources['energie'] - $liste['coutEnergie']) . '\',' . $chaine . ' WHERE login=\'' . $_SESSION['login'] . '\'';
                mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));

                $ex = query('SELECT * FROM actionsconstruction WHERE login=\'' . $_SESSION['login'] . '\' ORDER BY fin DESC');
                $nb = mysqli_num_rows($ex);
                if ($nb > 0) { // s'il y a deja quelque chose en cours, on le met derriere
                    $actionsconstruction = mysqli_fetch_array($ex);
                    $tempsDebut = $actionsconstruction['fin'];
                } else {
                    $tempsDebut = time();
                }

                // on doit calculer le niveau actuel (et dans le futur avec les constructions le précédant)
                $exNiveauActuel = query('SELECT niveau FROM actionsconstruction WHERE login=\'' . $_SESSION['login'] . '\' AND batiment=\'' . $liste['bdd'] . '\' ORDER BY niveau DESC');
                $niveauActuel = mysqli_fetch_array($exNiveauActuel);
                $nb = mysqli_num_rows($exNiveauActuel);

                if ($nb == 0) {
                    $niveauActuel['niveau'] = $constructions[$liste['bdd']];
                }

                mysqli_query($base, 'INSERT INTO actionsconstruction VALUES(default,"' . $_SESSION['login'] . '",' . $tempsDebut . ',' . ($tempsDebut + $liste['tempsConstruction']) . ',"' . $liste['bdd'] . '",' . ($niveauActuel['niveau'] + 1) . ',"' . $liste['titre'] . '",' . $liste['points'] . ')') or die('Erreur SQL !<br /><br />' . mysqli_error($base));

                mysqli_query($base, 'UPDATE autre SET energieDepensee=\'' . ($autre['energieDepensee'] + $liste['coutEnergie']) . '\' WHERE login=\'' . $_SESSION['login'] . '\'');

                $information = "La construction a bien été lancée.";
            } else {
                $erreur = "Vous n'avez pas assez de ressources.";
            }
        } else {
            $erreur = "Vous avez déjà deux constructions en cours.";
        }
    }
}

foreach ($listeConstructions as $num => $b) {
    traitementConstructions($b);
}

include("includes/tout.php");

$ex = mysqli_query($base, 'SELECT * FROM actionsconstruction WHERE login=\'' . $_SESSION['login'] . '\'');
$nb = mysqli_num_rows($ex);
if ($nb > 0) {
    debutCarte();
    scriptAffichageTemps();
    echo '<div class="table-responsive"><table><tr><th>Constructions</th><th>Temps restant</th><th>Fin</th></tr>';
    while ($actionsconstruction = mysqli_fetch_array($ex)) {
        echo '<tr><td>' . $actionsconstruction['affichage'] . ' <strong>niveau ' . $actionsconstruction['niveau'] . '</strong></td><td><span id="affichage' . $actionsconstruction['id'] . '">' . affichageTemps($actionsconstruction['fin'] - time()) . '</span></td><td>' . date('H\hi', $actionsconstruction['fin']) . '</td></tr>';
        echo '<script>
            var valeur' . $actionsconstruction['id'] . ' = ' . ($actionsconstruction['fin'] - time()) . ';
            function tempsDynamique' . $actionsconstruction['id'] . '(){
                if(valeur' . $actionsconstruction['id'] . ' > 0){
                    valeur' . $actionsconstruction['id'] . ' -= 1;
                    document.getElementById("affichage' . $actionsconstruction['id'] . '").innerHTML = affichageTemps(valeur' . $actionsconstruction['id'] . ');
                }
                else {
                    document.location.href="constructions.php";
                }
            }
            setInterval(tempsDynamique' . $actionsconstruction['id'] . ', 1000);
            </script>';
    }
    echo '</table></div>';
    finCarte();
}

include("includes/constantes.php"); // on actualise les constantes

debutCarte("Constructions");
debutListe();
foreach ($listeConstructions as $num => $b) {
    mepConstructions($b);
}
finListe();
finCarte();

include("includes/copyright.php");
