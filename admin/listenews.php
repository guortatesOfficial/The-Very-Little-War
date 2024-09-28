<?php
include("redirectionmotdepasse.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Very Little War - Liste des news</title>
    <style type="text/css">
        h3,
        th,
        td {
            text-align: center;
        }

        table {
            border-collapse: collapse;
            border: 2px solid black;
            margin: auto;
        }

        th,
        td {
            border: 1px solid black;
        }
    </style>
</head>

<body>

    <h3><a href="redigernews.php">Ajouter une news</a></h3>
    <?php
    include("../includes/connexion.php");
    //-----------------------------------------------------
    // Vérification 1 : est-ce qu'on veut poster une news ?
    //-----------------------------------------------------
    if (isset($_POST['titre']) and isset($_POST['contenu'])) {
        $titre = addslashes($_POST['titre']);
        $contenu = addslashes($_POST['contenu']);
        // On vérifie si c'est une modification de news ou non.
        if ($_POST['id_news'] == 0) {
            // Ce n'est pas une modification, on crée une nouvelle entrée dans la table.
            mysqli_query($base, "INSERT INTO news VALUES(default, '" . $titre . "', '" . $contenu . "', '" . (time()) .  "')") or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
            // On protège la variable "id_news" pour éviter une faille SQL.
            $_POST['id_news'] = addslashes($_POST['id_news']);
            // C'est une modification, on met juste à jour le titre et le contenu.
            mysqli_query($base, "UPDATE news SET titre='" . $titre . "', contenu='" . $contenu . "' WHERE id='" . $_POST['id_news'] . "'");
        }
    }

    //--------------------------------------------------------
    // Vérification 2 : est-ce qu'on veut supprimer une news ?
    //--------------------------------------------------------
    if (isset($_GET['supprimer_news'])) // Si l'on demande de supprimer une news.
    {
        // Alors on supprime la news correspondante.
        // On protège la variable « id_news » pour éviter une faille SQL.
        $_GET['supprimer_news'] = addslashes($_GET['supprimer_news']);
        mysqli_query($base, 'DELETE FROM news WHERE id=\'' . $_GET['supprimer_news'] . '\'');
    }
    ?>
    <table>
        <tr>
            <th>Modifier</th>
            <th>Supprimer</th>
            <th>Titre</th>
            <th>Date</th>
        </tr>
        <?php
        $retour = mysqli_query($base, 'SELECT * FROM news ORDER BY id DESC');
        while ($donnees = mysqli_fetch_array($retour)) // On fait une boucle pour lister les news.
        {
        ?>
            <tr>
                <td><?php echo '<a href="redigernews.php?modifier_news=' . $donnees['id'] . '">'; ?>Modifier</a></td>
                <td><?php echo '<a href="listenews.php?supprimer_news=' . $donnees['id'] . '">'; ?>Supprimer</a></td>
                <td><?php echo stripslashes($donnees['titre']); ?></td>
                <td><?php echo date('d/m/Y', $donnees['timestamp']); ?></td>
            </tr>
        <?php
        } // Fin de la boucle qui liste les news.
        ?>
    </table>
</body>

</html>