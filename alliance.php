<?php
session_start();
$_SESSION['start'] = "start";
if (isset($_SESSION['login'])) {
    include("includes/basicprivatephp.php");
} else {
    include("includes/basicpublicphp.php");
}
include("includes/bbcode.php");

// alliance du joueur
$ex = query('SELECT * FROM alliances WHERE id=\'' . $autre['idalliance'] . '\'');
$allianceJoueur = mysqli_fetch_array($ex);

//si pas d'alliance alors -1
if (mysqli_num_rows($ex) == 0) {
    $allianceJoueur['tag'] = -1;
}

// si pas d'id alors on cherche notre alliance
if (!isset($_GET['id'])) {
    $_GET['id'] = $allianceJoueur['tag'];
} else {
    $_GET['id'] = antiXSS($_GET['id']);
}

if (isset($_POST['nomalliance']) and isset($_POST['tagalliance']) && $allianceJoueur['tag'] == -1) {
    if (!empty($_POST['nomalliance']) and !empty($_POST['tagalliance'])) {
        $sql = 'SELECT idalliance FROM autre WHERE login=\'' . $_SESSION['login'] . '\'';
        $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
        $idalliance = mysqli_fetch_array($ex);
        if ($idalliance['idalliance'] <= 0) {
            $_POST['nomalliance'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['nomalliance'])));
            $_POST['tagalliance'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['tagalliance'])));

            if (preg_match("#^[a-zA-Z0-9_]{3,16}$#", $_POST['tagalliance'])) {

                $sql2 = 'SELECT nom FROM alliances WHERE tag=\'' . $_POST['tagalliance'] . '\' OR nom=\'' . $_POST['nomalliance'] . '\'';
                $ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
                $nballiance = mysqli_num_rows($ex2);

                if ($nballiance == 0) {
                    $sql3 = 'INSERT INTO alliances VALUES (default, "' . $_POST['nomalliance'] . '", "' . $_POST['tagalliance'] . '", "", default, "' . $_SESSION['login'] . '", default, default, default, default, default, default, default,default)';
                    mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysqli_error($base));

                    $sql4 = 'SELECT id FROM alliances WHERE tag=\'' . $_POST['tagalliance'] . '\'';
                    $ex4 = mysqli_query($base, $sql4) or die('Erreur SQL !<br />' . $sql4 . '<br />' . mysqli_error($base));
                    $nouvellealliance = mysqli_fetch_array($ex4);

                    $sql5 = 'UPDATE autre SET idalliance=\'' . $nouvellealliance['id'] . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
                    mysqli_query($base, $sql5) or die('Erreur SQL !<br />' . $sql5 . '<br />' . mysqli_error($base));

                    $information = "Votre équipe a été créée.";
                    echo '<script>window.location="alliance.php";</script>';
                } else {
                    $erreur = "Une équipe avec ce nom ou ce tag existe déja.";
                }
            } else {
                $erreur = "Le TAG de l'alliance ne peut être composé que de lettres et de nombres.";
            }
        } else {
            $erreur =  "Vous avez déja une équipe";
        }
    } else {
        $erreur = "Tous les champs ne sont pas remplis.";
    }
}
// si notre alliance
if ($_GET['id'] == $allianceJoueur['tag'] && $_GET['id'] != -1) {
    if (isset($_POST['quitter'])) {
        $sql = 'UPDATE autre SET idalliance=0 WHERE login=\'' . $_SESSION['login'] . '\'';
        mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
    }

    $sql = 'SELECT idalliance FROM autre WHERE login=\'' . $_SESSION['login'] . '\'';
    $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
    $idalliance = mysqli_fetch_array($ex);
    $ex = mysqli_query($base, 'SELECT duplicateur FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'');
    $duplicateur = mysqli_fetch_array($ex);
    $cout = round(10 * pow(2.5, ($duplicateur['duplicateur'] + 1)));

    if (isset($_POST['augmenterDuplicateur'])) {
        $ex = mysqli_query($base, 'SELECT energieAlliance FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'');
        $energieAlliance = mysqli_fetch_array($ex);

        if ($energieAlliance['energieAlliance'] >= $cout) {
            mysqli_query($base, 'UPDATE alliances SET duplicateur=\'' . ($duplicateur['duplicateur'] + 1) . '\', energieAlliance=\'' . ($energieAlliance['energieAlliance'] - $cout) . '\' WHERE id=\'' . $idalliance['idalliance'] . '\'');
            $information = "Vous avez augmenté votre duplicateur au niveau " . ($duplicateur['duplicateur'] + 1) . ".";
        } else {
            $erreur = "Vous n'avez pas assez d'énergie.";
        }
    }
}

if ($_GET['id'] == -1) { // si pas d'alliance alors invitations
    if (isset($_POST['actioninvitation']) and isset($_POST['idinvitation'])) {
        $_POST['idinvitation'] = antiXSS($_POST['idinvitation']);
        $sql = 'SELECT idalliance FROM invitations WHERE id=\'' . $_POST['idinvitation'] . '\'';
        $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
        $idalliance = mysqli_fetch_array($ex);

        $ex = query('SELECT login FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\'');
        $nombreJoueurs = mysqli_num_rows($ex);
        if ($nombreJoueurs < $joueursEquipe) {
            if ($_POST['actioninvitation'] == "Accepter") {
                $sql1 = 'UPDATE autre SET idalliance=\'' . $idalliance['idalliance'] . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
                mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
                $information = "Vous avez accepté l'invitation.";
                echo '<script>window.location="alliance.php";</script>';
            }
            $sql2 = 'DELETE FROM invitations WHERE id=\'' . $_POST['idinvitation'] . '\'';
            mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
        } else {
            $erreur = "Le nombre maximal de joueurs dans l'équipe est atteint.";
        }
    }
}
include("includes/tout.php");

// Verification que le chef de l'alliance existe, sinon on supprimmer l'alliance et les invitations et les numeros dans autre
$sql = 'SELECT id as idalliance FROM alliances WHERE tag=\'' . $_GET['id'] . '\'';
$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
$idalliance = mysqli_fetch_array($ex);
if ($_GET['id'] != -1) {
    if (mysqli_num_rows($ex) > 0) {
        $sql1 = 'SELECT chef FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'';
        $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
        $chef = mysqli_fetch_array($ex1);

        $sql2 = 'SELECT idalliance FROM autre WHERE login=\'' . $chef['chef'] . '\'';
        $ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
        $idalliancechef = mysqli_fetch_array($ex2);
        $chefExiste = mysqli_num_rows($ex2);

        if ($chefExiste == 0 or $idalliancechef['idalliance'] != $idalliance['idalliance']) {
            supprimerAlliance($idalliance['idalliance']);
?>
            <script LANGUAGE="JavaScript">
                window.location = "alliance.php";
            </script>
        <?php
            exit();
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sql1 = 'SELECT * FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'';
        $ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
        $allianceJoueurPage = mysqli_fetch_array($ex1);

        $sql2 = 'SELECT totalPoints FROM autre WHERE idalliance="' . $idalliance['idalliance'] . '" ORDER BY points DESC';
        $ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
        $nbjoueurs = mysqli_num_rows($ex2);
        $pointstotaux = 0;
        while ($joueur = mysqli_fetch_array($ex2)) {
            $pointstotaux = $joueur['totalPoints'] + $pointstotaux;
        }

        debutCarte(stripslashes($allianceJoueurPage['nom']));

        $rangQuery = query('SELECT tag FROM alliances ORDER BY pointstotaux DESC');
        $rang = 1;

        while ($rangEx = mysqli_fetch_array($rangQuery)) {
            if ($rangEx['tag'] == $allianceJoueurPage['tag']) {
                break;
            }
            $rang++;
        }

        echo important('Informations');
        echo chipInfo('<span class="important">Rang : </span>' . imageClassement($rang), 'images/alliance/up.png') . '<br/>';
        echo chipInfo('<span class="important">TAG : </span>' . stripslashes($allianceJoueurPage['tag']), 'images/alliance/post-it.png') . '<br/>';
        echo chipInfo('<span class="important">Membres : </span>' . $nbjoueurs, 'images/alliance/sommejoueurs.png') . '<br/>';
        echo chipInfo('<span class="important">Points : </span>' . $pointstotaux, 'images/alliance/points.png') . '<br/>';
        echo chipInfo('<span class="important">Moyenne : </span>' . floor($pointstotaux / $nbjoueurs), 'images/alliance/sommepoints.png') . '<br/>';
        echo chipInfo('<span class="important">Chef : </span>' . joueur($allianceJoueurPage['chef']), 'images/alliance/crown.png') . '<br/>';
        echo chipInfo('<span class="important">Points de victoire : </span>' . $allianceJoueurPage['pointsVictoire'], 'images/classement/victoires.png') . '<br/>';
        echo nombreEnergie('<span class="important">Energie : </span>' . number_format(floor($allianceJoueurPage['energieAlliance']), 0, ' ', ' ')) . '<br/>';

        $ex = mysqli_query($base, 'SELECT * FROM grades WHERE idalliance=\'' . $allianceJoueurPage['id'] . '\'');
        $nb = mysqli_num_rows($ex);

        if ($nb > 0) {
            echo '<br/>' . important("Grades");
            while ($grades = mysqli_fetch_array($ex)) {
                echo '<span class="subimportant">' . $grades['nom'] . ' : </span><a href="joueur.php?id=' . $grades['login'] . '">' . $grades['login'] . '</a><br/>';
            }
        }
        ?>

        <?php
        $ex = mysqli_query($base, 'SELECT * FROM declarations WHERE type=0 AND (alliance1=\'' . $allianceJoueurPage['id'] . '\' OR alliance2=\'' . $allianceJoueurPage['id'] . '\') AND fin=0');
        $nb = mysqli_num_rows($ex);
        if ($nb > 0) {
            echo '<br/><br/>' . important("Guerres");
            while ($guerre = mysqli_fetch_array($ex)) {
                if ($guerre['alliance1'] == $allianceJoueurPage['id']) {
                    $ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $guerre['alliance2'] . '\'');
                    $allianceJoueurAdverse = mysqli_fetch_array($ex1);
                    echo '<br/>- <a href="guerre.php?id=' . $guerre['id'] . '"> contre ' . $allianceJoueurAdverse['tag'] . '</a>';
                } else {
                    $ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $guerre['alliance1'] . '\'');
                    $allianceJoueurAdverse = mysqli_fetch_array($ex1);
                    echo '<br/>- <a href="guerre.php?id=' . $guerre['id'] . '"> contre ' . $allianceJoueurAdverse['tag'] . '</a>';
                }
            }
        }

        $ex = mysqli_query($base, 'SELECT * FROM declarations WHERE type=1 AND (alliance1=\'' . $allianceJoueurPage['id'] . '\' OR alliance2=\'' . $allianceJoueurPage['id'] . '\') AND valide!=0');
        $nb = mysqli_num_rows($ex);
        if ($nb > 0) {
            echo '<br/><br/>' . important("Pactes");
            while ($pacte = mysqli_fetch_array($ex)) {
                if ($pacte['alliance1'] == $allianceJoueurPage['id']) {
                    $ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $pacte['alliance2'] . '\'');
                    $allianceJoueurAllie = mysqli_fetch_array($ex1);
                    echo '<br/>- avec <a href="alliance.php?id=' . $allianceJoueurAllie['tag'] . '">' . $allianceJoueurAllie['tag'] . '</a>';
                } else {
                    $ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $pacte['alliance1'] . '\'');
                    $allianceJoueurAllie = mysqli_fetch_array($ex1);
                    echo '<br/>- avec <a href="alliance.php?id=' . $allianceJoueurAllie['tag'] . '">' . $allianceJoueurAllie['tag'] . '</a>';
                }
            }
        }

        // On regarde si le joueur a un grade si il est dans l'alliance
        if ($_GET['id'] == $allianceJoueur['tag']) {
            $ex = mysqli_query($base, 'SELECT login FROM grades WHERE login=\'' . $_SESSION['login'] . '\' AND idalliance=\'' . $allianceJoueur['id'] . '\'');
            $grade = mysqli_num_rows($ex);
            $admin = '';
            if (mysqli_real_escape_string($base, stripslashes(antihtml($allianceJoueur['chef']))) == $_SESSION['login'] or $grade > 0) {
                $admin = '<a href="allianceadmin.php" class="lienSousMenu"><img alt="admin" src="images/alliance/admin.png" title="Administration" class="imageSousMenu"/><br/><span class="labelSousMenu"  style="color:black">Administration</span></a>';
            }

            echo '<form action="alliance.php" method="post">';
            finCarte($admin . '
            <a href="ecriremessage.php?destinataire=[alliance]" class="lienSousMenu"><img alt="message" src="images/alliance/message_ferme.png" title="Ecrire un message à l\'équipe" class="imageSousMenu"/><br/><span class="labelSousMenu"  style="color:black">Message</span></a>
            <a href="don.php" class="lienSousMenu"><img alt="dpn" src="images/alliance/give.png" title="Faire un don" class="imageSousMenu"/><br/><span class="labelSousMenu"  style="color:black">Donner</span></a>
            <a class=lienSousMenu><input class="imageSousMenu" src="images/alliance/doorway.png" alt="quitteralliance" type="image" value="Quitter l\'équipe" name="quitteralliance" title="Quitter l\'équipe"><br/><span class="labelSousMenu"  style="color:black">Quitter</span></a>
            <input type="hidden" name="quitter"/>');
            echo '</form>';
        } else {
            finCarte();
        }

        debutCarte("Description");
        ?>
        <p>
            <div class="table-reponsive">
                <?php echo BBcode($allianceJoueurPage['description']) ?>
            </div>
        </p>
        <?php
        finCarte();

        if ($_GET['id'] == $allianceJoueur['tag']) {
            debutCarte('Duplicateur');
            debutListe();
            item([
                'titre' => 'Duplicateur',
                'media' => '<img src="images/alliance/duplicateur.png" alt="duplicateur" style="width:50px;height:50px;"/>',
                'soustitre' => '<strong>Niveau ' . $allianceJoueur['duplicateur'] . '</strong>',
                'accordion' => debutContent(true, true) . 'Le duplicateur est un bâtiment propre aux équipes et qui doit être construit collectivement. L\'énergie nécessaire est celle du pot commun de l\'alliance.' . finContent(true, true) .
                    '<br/><br/>' . debutContent(false, true) .
                    '+' . (100 * bonusDuplicateur($allianceJoueur['duplicateur'])) . '% de production de toute les ressources<br/>' .
                    '+ ' . (100 * bonusDuplicateur($allianceJoueur['duplicateur'])) . '% de de défense et d\'attaque<br/>au <strong>niveau ' . $allianceJoueur['duplicateur'] . '</strong><br/><br/>
                  +' . (100 * bonusDuplicateur($allianceJoueur['duplicateur'] + 1)) . '% de production de toute les ressources<br/>' .
                    '+ ' . (100 * bonusDuplicateur($allianceJoueur['duplicateur'] + 1)) . '% de de défense et d\'attaque<br/>au <strong>niveau ' . ($allianceJoueur['duplicateur'] + 1) . '</strong>
                  <br/><br/>' . finContent(false, true) . '
                  <form action="alliance.php" method="post" name="augmenterDuplicateur">' .
                    important('Augmenter') . '
                  ' . nombreEnergie($cout) . '<br/><br/>
                  ' . submit(['titre' => 'niveau ' . ($allianceJoueur['duplicateur'] + 1), 'image' => 'images/boutons/arrow.png', 'form' => 'augmenterDuplicateur']) . '
                <input type="hidden" value="bla" name="augmenterDuplicateur"/></form>'
            ]);
            finListe();
            finCarte();
        }

        debutCarte('Membres'); ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th><img src="images/classement/up.png" alt="up" class="imageSousMenu" /><br /><span class="labelClassement">Rang</span></th>
                        <th><img src="images/classement/joueur.png" alt="joueur" title="Joueur" class="imageSousMenu" /><br /><span class="labelClassement">Joueur</span></th>
                        <th><a href="alliance.php?&clas=6"><img src="images/alliance/give.png" alt="dons" title="Dons" class="imageSousMenu" /><br /><span class="labelClassement">Dons</span></a></th>
                        <th><a href="alliance.php"><img src="images/classement/points.png" alt="points" title="Points" class="imageSousMenu" /><br /><span class="labelClassement">Points</span></a></th>
                        <th><a href="alliance.php?clas=5"><img src="images/classement/museum.png" alt="pointCs" title="Points de construction" class="imageSousMenu" /><br /><span class="labelClassement">Constructions</span></a></th>
                        <th><a href="alliance.php?&clas=2"><img src="images/classement/sword.png" alt="att" title="Attaque" class="imageSousMenu" /><br /><span class="labelClassement">Attaque</span></a></th>
                        <th><a href="alliance.php?&clas=3"><img src="images/classement/shield.png" alt="def" title="Défense" class="imageSousMenu" /><br /><span class="labelClassement">Défense</span></a></th>
                        <th><a href="alliance.php?&clas=4"><img src="images/classement/bag.png" alt="bag" title="Pillage" class="imageSousMenu" /><br /><span class="labelClassement">Pillage</span></a></th>
                        <th><a href="alliance.php?&clas=1"><img src="images/classement/victoires.png" alt="victoires" title="Points de victoire" class="imageSousMenu" /><br /><span class="labelClassement">Victoire</span></a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if (!isset($_GET['clas'])) {
                        $_GET['clas'] = 0;
                    }
                    switch ($_GET['clas']) {
                        case 0:
                            $order = 'totalPoints';
                            break;
                        case 1:
                            $order = 'victoires';
                            break;
                        case 2:
                            $order = 'pointsAttaque';
                            break;
                        case 3:
                            $order = 'pointsDefense';
                            break;
                        case 4:
                            $order = 'ressourcesPillees';
                            break;
                        case 5:
                            $order = 'points';
                            break;
                        case 6:
                            $order = 'energieDonnee';
                            break;
                        default:
                            $order = 'totalPoints';
                            break;
                    }

                    $sql3 = 'SELECT * FROM autre WHERE idalliance="' . $idalliance['idalliance'] . '" ORDER BY ' . $order . ' DESC';
                    $ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysqli_error($base));
                    $c = 1;
                    while ($joueur1 = mysqli_fetch_array($ex3)) {
                    ?>
                        <tr>
                            <td><?php echo imageClassement($c); ?></td>
                            <td><?php echo joueur($joueur1['login']); ?></td>
                            <td><?php if ($allianceJoueurPage['energieTotaleRecue'] > 0) {
                                    echo round($joueur1['energieDonnee'] / $allianceJoueurPage['energieTotaleRecue'] * 100);
                                } else {
                                    echo "0";
                                } ?>%</td>
                            <td><?php echo $joueur1['totalPoints']; ?></td>
                            <td><?php echo $joueur1['points']; ?></td>
                            <td><?php echo pointsAttaque($joueur1['pointsAttaque']); ?></td>
                            <td><?php echo pointsDefense($joueur1['pointsDefense']); ?></td>
                            <td><?php echo $joueur1['ressourcesPillees']; ?></td>
                            <td><?php echo $joueur1['victoires']; ?></td>
                        </tr>
                    <?php
                        $c++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php
        finCarte();
    } else {
        debutCarte('Inconnue');
        debutContent();
        echo 'Cette alliance n\'existe pas.';
        finContent();
        finCarte();
    }
} else {
    debutCarte('Créer une équipe');
    ?>
    Vous n'appartenez à aucune alliance. Envoyez votre candidature au chef de l'alliance que vous voulez intégrer ou créez en une ci dessous.<br /><br />
    <form action="alliance.php" method="post" name="creerallianceForm">
        <input type="hidden" name="creeralliance" />
        <?php
        debutListe();
        item(['floating' => true, 'titre' => 'Nom de l\'équipe', 'input' => '<input type="text" name="nomalliance" id="nomalliance"/>']);
        item(['floating' => true, 'titre' => 'TAG de l\'équipe', 'input' => '<input type="text" name="tagalliance" id="tagalliance" maxlength=10/>']);
        item(['input' => submit(['form' => 'creerallianceForm', 'titre' => 'Créer'])]);
        finListe(); ?>

    </form>
<?php
    finCarte();

    debutCarte('Invitations');
    $sql = 'SELECT * FROM invitations WHERE invite=\'' . $_SESSION['login'] . '\'';
    $ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
    $nbinvitations = mysqli_num_rows($ex);
    if ($nbinvitations > 0) {
        while ($invitation = mysqli_fetch_array($ex)) {
            echo '
            <form action="alliance.php" method="post">Invitation de l\'équipe ' . $invitation['tag'] . ' : <input type="submit" class="w32" style="background-image: url(\'images/yes.png\');background-size:contain;vertical-align:middle;margin-left:15px;margin-right:15px;background-color: Transparent;color: Transparent;background-repeat:no-repeat;border: none;cursor:pointer;overflow: hidden;outline:none;" name="actioninvitation" value="Accepter"/><input class="w32" style="background-image: url(\'images/croix.png\');background-size:contain;vertical-align:middle;background-color: Transparent;color: Transparent;background-repeat:no-repeat;border: none;cursor:pointer;overflow: hidden;outline:none;" type ="submit" name="actioninvitation" value="Refuser"/><input type="hidden" name="idinvitation" value="' . $invitation['id'] . '"/></form>';
        }
    } else {
        echo "Vous n'avez aucune invitation d'équipe.";
    }

    finCarte();
}

include("includes/copyright.php"); ?>