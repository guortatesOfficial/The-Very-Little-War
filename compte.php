<?php
include("includes/basicprivatephp.php");
include("includes/bbcode.php");

if (isset($_POST['verification']) and isset($_POST['oui'])) {
    supprimerJoueur($_SESSION['login']);
    echo "<script>window.location.replace(\"deconnexion.php\")</script>";
}

if (isset($_POST['dateFin'])) { // Conversion de la date au format anglais
    list($jour, $mois, $annee) = explode('/', $_POST['dateFin']);
    $dateT = new DateTime();
    $dateT->setDate($annee, $mois, $jour);
    if ($dateT->getTimestamp() >= time() + (3600 * 24 * 3)) {
        query('DELETE FROM actionsformation WHERE login=\'' . $_SESSION['login'] . '\'');
        $date = $annee . '-' . $mois . '-' . $jour;
        $sql3 = 'INSERT INTO vacances VALUES (default,' . $membre['id'] . ',CURRENT_DATE,\'' . $date . '\')';
        $ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br/>' . $sql3 . '<br />' . mysql_error());
        $sql6 = 'UPDATE membre SET vacance=1 WHERE id=' . $membre['id'] . '';
        $ex6 = mysqli_query($base, $sql6) or die('Erreur SQL !<br/>' . $sql6 . '<br/>' . mysql_error());
        // Rafraichissement de la page
        echo "<script>window.location.replace(\"compte.php\")</script>";
    } else {
        $erreur = "Vous ne pouvez pas vous mettre en vacances moins de trois jours.";
    }
}



if (isset($_POST['changermdp']) and isset($_POST['changermdp1'])) {
    if (!empty($_POST['changermdp']) and !empty($_POST['changermdp1'])) {
        $_POST['changermdp'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['changermdp'])));
        $_POST['changermdp1'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['changermdp1'])));
        if ($_POST['changermdp'] == $_POST['changermdp1']) {
            $sql = 'UPDATE membre SET pass_md5=\'' . md5($_POST['changermdp']) . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
            mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());

            $information = "Votre mot de passe a été changé.";
        } else {
            $erreur = "Les deux mots de passe ne sont pas les mêmes.";
        }
    } else {
        $erreur = "Tous les champs ne sont pas remplis.";
    }
}

if (isset($_POST['changermail'])) {
    if (!empty($_POST['changermail'])) {
        if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['changermail'])) {
            $_POST['changermail'] = antiXSS($_POST['changermail']);
            $sql = 'UPDATE membre SET email=\'' . $_POST['changermail'] . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
            mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
            $information = "Votre adresse e-mail a été changée.";
        } else {
            $erreur = "Votre email n'est pas correct.";
        }
    } else {
        $erreur = "Tous les champs ne sont pas remplis.";
    }
}

if (isset($_POST['changerdescription'])) {
    $_POST['changerdescription'] = antiXSS($_POST['changerdescription'], true);
    $sql = 'UPDATE autre SET description=\'' . $_POST['changerdescription'] . '\' WHERE login=\'' . $_SESSION['login'] . '\'';
    mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
    $autre['description'] = $_POST['changerdescription'];

    $information = "Votre description a été changée.";
}

if (isset($_FILES['photo']['name']) and !empty($_FILES['photo']['name'])) {
    $dossier = 'images/profil/';
    $fichier = basename($_FILES['photo']['name']);
    $taille_maxi = 100000;
    $taille = filesize($_FILES['photo']['tmp_name']);
    $extensions = array('.png', '.gif', '.jpg', '.jpeg');
    $extension = strrchr($_FILES['photo']['name'], '.');
    $img_size = getimagesize($_FILES['photo']['tmp_name']);
    //Début des vérifications de sécurité...
    if (!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
    {
        $erreur = 'Seuls les fichiers de type png, gif, jpg, jpeg sont autorisés.';
    } elseif ($img_size[0] > 150 or $img_size[1] > 150) {
        $erreur = "Erreur : image trop grande ! (les dimensions requises sont au maximum 150*150)";
    } elseif ($taille > $taille_maxi) {
        $erreur = 'L\'image est trop grosse !';
    } else //S'il n'y a pas d'erreur, on upload
    {
        //On formate le nom du fichier ici...
        $fichier = strtr(
            $fichier,
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy'
        );
        $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
        move_uploaded_file($_FILES['photo']['tmp_name'], $dossier . $fichier);
        mysqli_query($base, 'UPDATE autre SET image=\'' . $fichier . '\' WHERE login=\'' . $_SESSION['login'] . '\'');
        $information = "Votre image a bien été enregistrée.";
    }
}

include("includes/tout.php");

if (!isset($_POST['supprimercompte'])) {
    debutCarte("Gestion du compte");
    echo important("Changer le mot de passe");
    debutListe();
    echo '<form action="compte.php" method="post" name="formChangerMdp">';
    item(['media' => '<img alt="login" src="images/accueil/door-key.png" class="w32"/>', 'floating' => true, 'titre' => 'Nouveau mot de passe', 'input' => '<input type="password" name="changermdp" id="changermdp" class="form-control"/>']);
    item(['media' => '<img alt="login" src="images/accueil/door-key.png" class="w32"/>', 'floating' => true, 'titre' => 'Confirmation', 'input' => '<input type="password" name="changermdp1" id="changermdp1" class="form-control"/>']);
    item(['input' => submit(['titre' => 'Changer', 'form' => 'formChangerMdp'])]);
    echo '</form><br/>';
    finListe();

    echo important("Changer le mail");

    $sql = 'SELECT email FROM membre WHERE login=\'' . $_SESSION['login'] . '\'';
    $ex = mysqli_query($base, $sql);
    $mail = mysqli_fetch_array($ex);

    debutListe();
    echo '<form action="compte.php" method="post" name="formChangerMail">';
    item(['media' => '<img alt="login" src="images/accueil/email.png" class="w32"/>', 'floating' => true, 'titre' => 'Mail', 'input' => '<input type="text" name="changermail" id="changermail" class="form-control" value="' . $mail['email'] . '"/>']);
    item(['input' => submit(['titre' => 'Changer', 'form' => 'formChangerMail'])]);
    echo '</form><br/>';
    finListe();

    $sql2 = 'SELECT id FROM membre WHERE login=\'' . $_SESSION['login'] . '\'';
    $ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysql_error());
    $joueur = mysqli_fetch_array($ex2);
    $sql4 = 'SELECT vacance FROM membre WHERE id=\'' . $joueur['id'] . '\'';
    $ex4 = mysqli_query($base, $sql4) or die('Erreur SQL !<br />' . $sql4 . '<br />' . mysql_error());
    $estEnVac = mysqli_fetch_array($ex4);

    // Si le joueur est déjà en vacances
    if ($estEnVac[0]) {
        $sql5 = 'SELECT dateDebut, dateFin FROM vacances WHERE idJoueur=\'' . $joueur['id'] . '\'';
        $ex5 = mysqli_query($base, $sql5) or die('Erreur SQL !<br />' . $sql5 . '<br />' . mysql_error());
        $vacance = mysqli_fetch_array($ex5);
        // Convertion des dates 
        list($annee, $mois, $jour) = explode('-', $vacance['dateDebut']);
        $vacance['dateDebut'] = $jour . '/' . $mois . '/' . $annee;
        list($annee, $mois, $jour) = explode('-', $vacance['dateFin']);
        $vacance['dateFin'] = $jour . '/' . $mois . '/' . $annee;
        $debut =  $vacance['dateDebut'];
        $fin =  "<input type=\"text\" name=\"dateFin\" id=\"dateFin\" class=\"form-control\" value=\"" . $vacance['dateFin'] . "\"/>";
        $activation = "";
        $disabled = "disabled";
    }
    // Si il n'est pas en vacances
    else {
        $debut = date("d/m/Y  H:i:s");
        $fin = '<input type="text" name="dateFin" placeholder="Sélectionnez" readonly id="calVacs">';
        $activation = submit(['titre' => 'Activer', 'form' => 'formVacances']);
        $disabled = false;
    }

    echo important('Partir en vacances');
    debutListe();
    echo '<form action="compte.php" method="post" name="formVacances"><br/><br/><div class="content-block">La mise en vacance supprimera tout ordre de production de molécule en cours.</div><br/>';
    item(['floating' => false, 'titre' => 'Date de début', 'input' => '<input type="text" name="dateDebut" id="dateDebut" class="form-control" value="' . $debut . '"/>', 'disabled' => true]);
    item(['floating' => false, 'titre' => 'Date de fin', 'input' => $fin, 'disabled' => $disabled]);
    item(['input' => $activation]);
    echo '</form>';
    finListe();

    echo important("Supprimer le compte");
    debutListe();
    $ex = mysqli_query($base, 'SELECT timestamp FROM membre WHERE login=\'' . $_SESSION['login'] . '\'');
    $donnees = mysqli_fetch_array($ex);
    if ((time() - $donnees['timestamp']) > 604800) {
        item(['form' => ["compte.php", "formSupprimer"], 'input' => '<input type="hidden" name="supprimercompte"/>' . submit(['titre' => 'Supprimer le compte', 'style' => 'background-color:red', 'form' => 'formSupprimer'])]);
    } else {
        debutContent();
        echo '<br/>Le compte ne peut être supprimé qu\'au bout d\'une semaine.';
        finContent();
    }
    finListe();
    finCarte();

    debutCarte("Gestion du profil");

    echo important("Modifier la description");
    debutListe();

    $sql = 'SELECT description FROM autre WHERE login=\'' . $_SESSION['login'] . '\'';
    $ex = mysqli_query($base, $sql);
    $description = mysqli_fetch_array($ex);
    echo '<br/>';
    creerBBcode("changerdescription", $description['description']);

    item(['form' => ["compte.php", "formChangerDescription"], 'floating' => false, 'titre' => "Description", 'input' => '<textarea name="changerdescription" id="changerdescription" rows="10" cols="50">' . $description['description'] . '</textarea>']);
    item(['input' => submit(['titre' => 'Modifier', 'form' => 'formChangerDescription'])]);

    finListe();
    echo '<br/>';

    echo important("Photo de profil (150x150)") . '<br/>';
    debutListe();
    item(['form' => ["compte.php", "formChangerPhoto", 'sup' => 'enctype="multipart/form-data"'], 'floating' => false, 'input' => '<input type="file" name="photo" id="photo" class="filestyle" data-buttonName="btn-primary" data-buttonBefore="true" data-icon="false"/><input type="hidden" name="MAX_FILE_SIZE" value="100000"/>']);

    item(['input' => submit(['titre' => 'Modifier', 'form' => 'formChangerPhoto'])]);
    finListe();
    finCarte();
} else {
    debutCarte("Suppression du compte");
    important("Supprimer votre compte ?");
    debutListe();
    item(['input' => '
                 <center>
                    <input type="image" style="vertical-align:middle;margin-right:80px" src="images/yes.png" name="oui" value="Oui"/><input src="images/croix.png" style="vertical-align:middle" type ="image" name="non" value="Non"/>
	               <input type="hidden" name="verification"/>
                </center>', 'form' => ["compte.php", "supprimerLeCompte"]]);
    finListe();
    finCarte();
}
?>
<?php include("includes/copyright.php"); ?>
