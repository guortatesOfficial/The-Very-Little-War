<?php
if (!isset($_SESSION['start'])) {
    session_start();
}
include("includes/connexion.php");
include("includes/fonctions.php");

if (isset($_SESSION['login']) && isset($_SESSION['mdp'])) {
    $_SESSION['login'] = ucfirst(mb_strtolower(mysqli_real_escape_string($base, stripslashes(htmlentities($_SESSION['login'])))));
    $sql = 'SELECT count(*) FROM membre WHERE login="' . $_SESSION['login'] . '" AND pass_md5="' . $_SESSION['mdp'] . '"';
    $req = query($sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
    $data = mysqli_fetch_array($req);
    if ($data[0] != 1) {
        session_destroy();
        header('Location: index.php');
        exit();
    }
} else {
    session_destroy();
    header('Location: index.php');
    exit();
}


// si c'est la premiere connexion depuis la derniere partie, on le replace
$ex = query('SELECT x,y FROM membre WHERE login=\'' . $_SESSION['login'] . '\'');
$posAct = mysqli_fetch_array($ex);
if ($posAct['x'] == -1000) {
    $position = coordonneesAleatoires();
    query('UPDATE membre SET x=\'' . $position['x'] . '\', y=\'' . $position['y'] . '\' WHERE login=\'' . $_SESSION['login'] . '\'');
}
include("includes/constantes.php");

/////////////////////////////////////////////////////


$sqlMaintenance = mysqli_query($base, 'SELECT maintenance FROM statistiques');
$maintenance = mysqli_fetch_array($sqlMaintenance);
/*if ($maintenance['maintenance'] != 0) {
    header('Location: maintenance.php');
    exit();
}*/


if (isset($_GET['information'])) {
    $information = antiXSS($_GET['information']);
}

if (isset($_GET['erreur'])) {
    $erreur = antiXSS($_GET['erreur']);
}
//////////////////////////////////////////////////////////// Gestion des connectés

//Vérification si l'adresse IP est dans la table
$retour = mysqli_query($base, 'SELECT COUNT(*) AS nbre_entrees FROM connectes WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'');
$donnees = mysqli_fetch_array($retour);

if ($donnees['nbre_entrees'] == 0) //L'IP ne se trouve pas dans la table, on va l'ajouter.
{
    mysqli_query($base, 'INSERT INTO connectes VALUES(\'' . $_SERVER['REMOTE_ADDR'] . '\', ' . time() . ')');
} else //L'IP se trouve déjà dans la table, on met juste à jour le timestamp.
{
    mysqli_query($base, 'UPDATE connectes SET timestamp=' . time() . ' WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'');
}

// Toutes les entrées vieilles de plus de 5 minutes sont supprimées
$timestamp_5min = time() - (60 * 5); // 60 * 5 = nombre de secondes écoulées en 5 minutes
mysqli_query($base, 'DELETE FROM connectes WHERE timestamp < ' . $timestamp_5min);

// Ajout de Yojim
// On vérifie si le joueur connecté est en vacance 
$sqlJoueurVac = 'SELECT vacance FROM membre WHERE login=\'' . $_SESSION['login'] . '\'';
$exJoueurVac = mysqli_query($base, $sqlJoueurVac);
$joueurEnVac = mysqli_fetch_array($exJoueurVac);


updateRessources($_SESSION['login']); // mise a jour
// Si le joueur n'est pas en vacance on fait la mise a jour des ressources ...
if (!$joueurEnVac[0]) {
    mysqli_query($base, 'UPDATE membre SET derniereConnexion=\'' . time() . '\' WHERE login=\'' . $_SESSION['login'] . '\''); // derniere connexion

    $req1 = 'SELECT tempsPrecedent FROM autre WHERE login=\'' . $_SESSION['login'] . '\''; // On prends le dernier chargement de page
    $tempsPrecedent1 = mysqli_query($base, $req1) or die('Erreur SQL !<br />' . $req1 . '<br />' . mysql_error());
    $donnees = mysqli_fetch_array($tempsPrecedent1);
    $nbsecondes = time() - $donnees['tempsPrecedent'];

    updateActions($_SESSION['login']);
    include("includes/constantes.php");
}
// Ajout par Yojim
// Si le joueur est encore en mode vacances
else {
    $requete = mysqli_query($base, 'SELECT depot FROM constructions WHERE login=\'' . $_SESSION['login'] . '\'');
    $depot = mysqli_fetch_array($requete);
    // On récupère la date de fin du mode vacances
    $sql4 = 'SELECT dateFin FROM vacances WHERE idJoueur IN (
	SELECT id FROM membre WHERE login=\'' . $_SESSION['login'] . '\')';
    $ex4 = mysqli_query($base, $sql4) or die('Erreur SQL :<br/>' . $sql4 . '<br/>' . mysql_error());
    $vac = mysqli_fetch_array($ex4);
    // On calcul la différence entre la date de fin et la date actuelle
    $sql5 = 'SELECT DATEDIFF(CURDATE(),\'' . $vac['dateFin'] . '\')';
    $ex5 = mysqli_query($base, $sql5) or die('Erreur SQL :<br/>' . $sql5 . '<br/>' . mysql_error());
    $diff = mysqli_fetch_array($ex5);
    mysqli_query($base, 'UPDATE membre SET derniereConnexion=\'' . time() . '\' WHERE login=\'' . $_SESSION['login'] . '\'');
    // Si la date de fin du mode vacances est passee, on enleve le mode vacances
    if ($diff[0] >= 0) {
        // Mise à jour du champ vacances
        $sql6 = 'UPDATE membre SET vacance=0 WHERE login=\'' . $_SESSION['login'] . '\'';
        $ex6 = mysqli_query($base, $sql6) or die('Erreur SQL :<br/>' . $sql6 . '<br/>' . mysql_error());
        // Supression du tuple de vacances
        $sql7 = 'DELETE FROM vacances WHERE idJoueur IN (
		SELECT id FROM membre WHERE login=\'' . $_SESSION['login'] . '\')';
        $ex7 = mysqli_query($base, $sql7) or die('Erreur SQL :<br/>' . $sql7 . '<br/>' . mysql_error());
        mysqli_query($base, 'UPDATE autre SET tempsPrecedent=\'' . time() . '\' WHERE login = \'' . $_SESSION['login'] . '\'');
    }
}

//////////////////////////////////////////////////////////// Gestion des ressources
//Vérification si nouveau mois le lendemain
$ex = mysqli_query($base, 'SELECT debut FROM statistiques');
$debut = mysqli_fetch_array($ex);

if (date('n', time()) != date('n', $debut["debut"])) {
    $exDejaFait = mysqli_query($base, 'SELECT maintenance FROM statistiques');
    $maintenance = mysqli_fetch_array($exDejaFait);

    $erreur = "Une nouvelle partie recommencera dans 24 heures.";
    mysqli_query($base, 'UPDATE statistiques SET maintenance = 1');

    //archivage de la partie (20 meilleurs)
    $chaine = '';
    $classement = mysqli_query($base, 'SELECT * FROM autre ORDER BY totalPoints DESC LIMIT 0, 20') or die('Erreur SQL !<br />' . mysql_error());
    $compteur = 0;
    while ($data = mysqli_fetch_array($classement)) {
        $sql4 = 'SELECT nombre FROM molecules WHERE proprietaire=\'' . $data['login'] . '\' AND nombre!=0';
        $req4 = mysqli_query($base, $sql4) or die('Erreur SQL !<br />' . $sql4 . '<br />' . mysql_error());
        if ($data['idalliance'] > 0) {
            $sql = 'SELECT tag, id FROM alliances WHERE id=\'' . $data['idalliance'] . '\'';
            $req = mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysql_error());
            $alliance = mysqli_fetch_array($req);
        } else {
            $alliance['tag'] = '';
        }
        $nb_molecules = 0;
        while ($donnees4 = mysqli_fetch_array($req4)) {
            $nb_molecules = $nb_molecules + $donnees4['nombre'];
        }
        $chaine = $chaine . '[' . $data['login'] . ',' . $data['totalPoints'] . ',' . $alliance['tag'] . ',' . $data['points'] . ',' . pointsAttaque($data['pointsAttaque']) . ',' . pointsDefense($data['pointsDefense']) . ',' . $data['ressourcesPillees'] . ',' . $data['victoires'] . '';

        if ($compteur == 0) {
            $vainqueurManche = $data['login'];
        }

        $compteur++;
    }

    //archivage des alliances
    $classement = mysqli_query($base, 'SELECT * FROM alliances ORDER BY pointstotaux DESC LIMIT 0, 20');
    $chaine1 = '';
    while ($data = mysqli_fetch_array($classement)) {
        $sql1 = 'SELECT login FROM autre WHERE idalliance="' . $data['id'] . '"';
        $req1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());
        $nbjoueurs = mysqli_num_rows($req1);
        if ($nbjoueurs != 0) {
            $chaine1 = $chaine1 . '[' . $data['tag'] . ',' . $nbjoueurs . ',' . $data['pointstotaux'] . ',' . $data['pointstotaux'] / $nbjoueurs . ',' . $data['totalConstructions'] . ',' . pointsAttaque($data['totalAttaque']) . ',' . pointsDefense($data['totalDefense']) . ',' . $data['totalPillage'] . ',' . $data['pointsVictoire'] . '';
        }
    }

    //archivage guerres
    $classement = mysqli_query($base, 'SELECT * FROM declarations WHERE pertesTotales!=0 AND type=0 AND fin!= 0 ORDER BY pertesTotales DESC LIMIT 0, 20');
    $chaine2 = '';
    while ($data = mysqli_fetch_array($classement)) {
        $ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $data['alliance1'] . '\'');
        $alliance1 = mysqli_fetch_array($ex1);

        $ex2 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $data['alliance2'] . '\'');
        $alliance2 = mysqli_fetch_array($ex2);
        $sql1 = 'SELECT login FROM autre WHERE idalliance="' . $data['id'] . '"';
        $req1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());
        $nbjoueurs = mysqli_num_rows($req1);
        if ($nbjoueurs != 0) {
            $chaine2 = $chaine2 . '[' . $alliance1['tag'] . ' contre ' . $alliance2['tag'] . ',' . $data['pertesTotales'] . ',' . (($data['fin'] - $data['timestamp']) / 86400) . ',' . $data['id'] . '';
        }
    }

    // ajout des points pour les alliances et joueurs
    $classement = query('SELECT * FROM autre ORDER BY totalPoints DESC');
    $c = 1;
    while ($pointsVictoire = mysqli_fetch_array($classement)) {
        ajouter('victoires', 'autre', pointsVictoireJoueur($c), $pointsVictoire['login']);
        $c++;
    }

    $classement = query('SELECT * FROM alliances ORDER BY pointstotaux DESC');
    $c = 1;
    while ($pointsVictoire = mysqli_fetch_array($classement)) {
        query('UPDATE alliances SET pointsVictoire=\'' . ($pointsVictoire['pointsVictoire'] + pointsVictoireAlliance($c)) . '\' WHERE id=\'' . $pointsVictoire['id'] . '\'');
        $victoiresJoueurs = query('SELECT * FROM autre WHERE idalliance=\'' . $pointsVictoire['id'] . '\'');
        while ($pointsVictoireJoueurs = mysqli_fetch_array($victoiresJoueurs)) {
            ajouter('victoires', 'autre', pointsVictoireAlliance($c), $pointsVictoireJoueurs['login']);
        }
        $c++;
    }

    //remise à zéro et news

    $ex = mysqli_query($base, 'SELECT debut FROM statistiques');
    $debut = mysqli_fetch_array($ex);
    mysqli_query($base, 'INSERT INTO parties VALUES(default,"' . (time()) . '","' . $chaine . '","' . $chaine1 . '","' . $chaine2 . '")');
    remiseAZero();

    mysqli_query($base, 'UPDATE statistiques SET debut=\'' . (time()) . '\'');

    $titre = "Vainqueur de la dernière manche";
    $contenu = 'Le vainqueur de la dernière manche est <a href="joueur.php?id=' . $vainqueurManche . '">' . $vainqueurManche . '</a><br/><br/>Reprise <strong>le ' . date('d/m/Y à H\hi', time()) . '</strong>';

    //mise à jour du nombre de victoires et des news
    mysqli_query($base, "INSERT INTO news VALUES(default, '" . $titre . "', '" . $contenu . "', '" . (time()) .  "')");

    //envoi des mails
    $ex = mysqli_query($base, "SELECT email,login FROM membre");
    while ($donnees = mysqli_fetch_array($ex)) {
        $mail = $donnees['email']; // Déclaration de l'adresse de destination.
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
        {
            $passage_ligne = "\r\n";
        } else {
            $passage_ligne = "\n";
        }
        //=====Déclaration des messages au format texte et au format HTML.
        $message_txt = "Bonjour " . $donnees['login'] . " ! " . $_SESSION['login'] . " vient de remporter la partie en cours le " . date('d/m/Y à H\hi', time()) . ". Les points de tous les joueurs vont être remis à zéro et
            vous pourrez commencer à rejouer la nouvelle partie à partir du " . date('d/m/Y Ã H\hi', time()) . " ! Ne manquez pas cette occasion de prendre la tête du classement. Je vous souhaite donc bonne chance pour la suite
            et à bientôt sur The Very Little War !
            Si vous ne souhaitez plus recevoir ce genre de mail il suffit de changer votre adresse e-mail sur www.theverylittlewar.com dans la partie \"Mon compte\".";
        $message_html = "<html><head></head><body>Bonjour " . $donnees['login'] . " ! <b>" . $_SESSION['login'] . "</b> vient de remporter la partie en cours le " . date('d/m/Y à H\hi', time()) . ". Les points de tous les joueurs vont être remis à zéro et
            vous pourrez commencer à rejouer la nouvelle partie à partir du <b>" . date('d/m/Y Ã H\hi', time()) . "</b> ! Ne manquez pas cette occasion de prendre la tête du classement. Je vous souhaite donc bonne chance pour la suite
            et à bientôt sur <a href=\"www.theverylittlewar.com\">The Very Little War</a> !<br/><br/><br/><br/>
            <i>Si vous ne souhaitez plus recevoir ce genre de mail il suffit de changer votre adresse e-mail sur <a href=\"www.theverylittlewar.com\">www.theverylittlewar.com</a> dans la partie \"Mon compte\".</i></body></html>";
        //==========

        //=====Création de la boundary
        $boundary = "-----=" . md5(rand());
        //==========

        //=====Définition du sujet.
        $sujet = "Début d'une nouvelle partie";
        //=========

        //=====Création du header de l'e-mail.
        $header = "From: \"The Very Little War\"<noreply@theverylittewar.com>" . $passage_ligne;
        $header .= "Reply-to: \"The Very Little War\" <theverylittewar@gmail.com>" . $passage_ligne;
        $header .= "MIME-Version: 1.0" . $passage_ligne;
        $header .= "Content-Type: multipart/alternative;" . $passage_ligne . " boundary=\"$boundary\"" . $passage_ligne;
        //==========

        //=====Création du message.
        $message = $passage_ligne . "--" . $boundary . $passage_ligne;
        //=====Ajout du message au format texte.
        $message .= "Content-Type: text/plain; charset=\"UTF-8\"" . $passage_ligne;
        $message .= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
        $message .= $passage_ligne . $message_txt . $passage_ligne;
        //==========
        $message .= $passage_ligne . "--" . $boundary . $passage_ligne;
        //=====Ajout du message au format HTML
        $message .= "Content-Type: text/html; charset=\"UTF-8\"" . $passage_ligne;
        $message .= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
        $message .= $passage_ligne . $message_html . $passage_ligne;
        //==========
        $message .= $passage_ligne . "--" . $boundary . "--" . $passage_ligne;
        $message .= $passage_ligne . "--" . $boundary . "--" . $passage_ligne;
        //==========

        //=====Envoi de l'e-mail.
        mail($mail, $sujet, $message, $header);
        //==========
    }
}
