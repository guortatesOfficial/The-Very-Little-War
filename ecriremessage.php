<?php

include("includes/basicprivatephp.php");
include("includes/bbcode.php");

if (isset($_POST['titre']) and isset($_POST['destinataire']) and isset($_POST['contenu'])) {
	if (!empty($_POST['titre']) and !empty($_POST['destinataire']) and !empty($_POST['contenu'])) {
		$_POST['titre'] = mysqli_real_escape_string($base, antihtml($_POST['titre']));
		$_POST['destinataire'] = ucfirst(mysqli_real_escape_string($base, $_POST['destinataire']));
		$_POST['contenu'] = mysqli_real_escape_string($base, $_POST['contenu']);
		if ($_POST['destinataire'] == "[alliance]") {
			$ex = mysqli_query($base, 'SELECT idalliance FROM autre WHERE login=\'' . $_SESSION['login'] . '\'');
			$idalliance = mysqli_fetch_array($ex);
			$ex = mysqli_query($base, 'SELECT * FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\' AND login !=\'' . $_SESSION['login'] . '\'');
			while ($destinataire = mysqli_fetch_array($ex)) {
				$sql1 = 'INSERT INTO messages VALUES(default, "' . time() . '", "' . $_POST['titre'] . '", "' . $_POST['contenu'] . '", "' . $_SESSION['login'] . '", "' . $destinataire['login'] . '", default)';
				mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());
			}
			$information = "Le message a bien été envoyé à toute l'alliance.";
		} elseif ($_POST['destinataire'] == "[all]" && $_SESSION['login'] == "Guortates") {
			$ex = query('SELECT * FROM autre') or die('Erreur SQL !<br /><br />' . mysql_error());
			while ($destinataire = mysqli_fetch_array($ex)) {
				$sql1 = 'INSERT INTO messages VALUES(default, "' . time() . '", "' . $_POST['titre'] . '", "' . $_POST['contenu'] . '", "' . $_SESSION['login'] . '", "' . $destinataire['login'] . '", default)';
				query($sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());
			}
			$information = "Le message a bien été à tous les joueurs.";
		} else {
			$sql = 'SELECT login FROM autre WHERE login=\'' . $_POST['destinataire'] . '\'';
			$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysql_error());
			$joueurExiste = mysqli_num_rows($ex);
			if ($joueurExiste > 0) {
				$sql1 = 'INSERT INTO messages VALUES(default, "' . time() . '", "' . $_POST['titre'] . '", "' . $_POST['contenu'] . '", "' . $_SESSION['login'] . '", "' . $_POST['destinataire'] . '", default)';
				mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysql_error());
				$information =  "Le message a bien été envoyé.";
				echo '
                <script>
                document.location.href="messages.php?information=' . $information . '"
                </script>';
			} else {
				$erreur = 'Le joueur ' . mysqli_real_escape_string($base, stripslashes(antihtml($_POST['destinataire']))) . ' n\'existe pas.';
			}
		}
	} else {
		$erreur = "Tous les champs ne sont pas remplis.";
	}
}

include("includes/tout.php");

if (isset($_GET['id'])) {
	$_GET['id'] = antiXSS($_GET['id']);
	$ex = mysqli_query($base, 'SELECT expeditaire, contenu, destinataire FROM messages WHERE id=\'' . $_GET['id'] . '\'');
	$message = mysqli_fetch_array($ex);
} elseif (isset($_POST['id'])) {
	$_POST['id'] = antiXSS($_POST['id']);
	$ex = mysqli_query($base, 'SELECT expeditaire, contenu, destinataire FROM messages WHERE id=\'' . $_POST['id'] . '\'');
	$message = mysqli_fetch_array($ex);
} else {
	$message['contenu'] = "";
	$message['expeditaire'] = "";
	$message['destinataire'] = $_SESSION['login'];
}

if ($message['destinataire'] != $_SESSION['login']) {
	$erreur = "Vous ne pouvez pas répondre à un message qui ne vous est pas destiné.";
	$message['expeditaire'] = "";
	$message['contenu'] = "";
}


debutCarte("Ecrire un message");
echo '<form action="ecriremessage.php" method="post" name="formEcrire">';
debutListe();
$valueTitre = "";
if (isset($_GET['reponse'])) {
	$valueTitre = '[Réponse]';
}
if (isset($_POST['titre'])) {
	$valueTitre = $_POST['titre'];
}
item(['floating' => true, 'titre' => 'Titre', 'input' => '<input type="text" class="form-control" name="titre" id="titre" value="' . $valueTitre . '" />']);

$valueDestinataire = antiXSS($message['expeditaire']);
if (isset($_GET['destinataire'])) {
	$valueDestinataire = antiXSS($_GET['destinataire']);
}
if (isset($_POST['destinataire'])) {
	$valueDestinataire = antiXSS($_POST['destinataire']);
}
item(['floating' => true, 'titre' => 'Destinataire', 'input' => '<input type="text" class="form-control" name="destinataire" id="destinataire" value="' . $valueDestinataire . '" />']);

if (isset($_GET['id'])) {
	creerBBcode("contenu", $message['contenu'], 1);
	$options = $message['contenu'];
} elseif (isset($_POST['contenu'])) {
	creerBBcode("contenu", stripslashes(preg_replace('#(\\\r\\\n|\\\r|\\\n)#', "\n", ($_POST['contenu']))));
	$options = stripslashes(preg_replace('#(\\\r\\\n|\\\r|\\\n)#', "\n", ($_POST['contenu'])));
} else {
	creerBBcode("contenu");
	$options = "";
}

item(['floating' => true, 'titre' => "Contenu", 'input' => '<textarea name="contenu" id="contenu" rows="10" cols="50">' . $options . '</textarea>']);


item(['input' => submit(['form' => 'formEcrire', 'titre' => 'Envoyer'])]);
echo '<form/>';
finListe();
finCarte();
include("includes/copyright.php");
