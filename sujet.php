<?php
session_start();
$_SESSION['start'] = "start"; // Sert a savoir si il faut de nouveau ouvrir une nouvelle session ou non
if (isset($_SESSION['login'])) {
	include("includes/basicprivatephp.php");
} else {
	include("includes/basicpublicphp.php");
}
include("includes/bbcode.php");

if (isset($_POST['contenu']) and isset($_GET['id'])) {
	$_GET['id'] = antiXSS($_GET['id']);
	if (preg_match("#^[0-9]*$#", $_GET['id'])) {
		if (isset($_SESSION['login'])) {
			if (!empty($_POST['contenu'])) {
				$_POST['contenu'] = mysqli_real_escape_string($base, ($_POST['contenu']));
				// Modifié par Yojim
				$sql = 'INSERT INTO reponses VALUES(default, "' . $_GET['id'] . '", "1", "' . $_POST['contenu'] . '", "' . $_SESSION['login'] . '", "' . (time()) . '")';
				//
				mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysql_error());
				mysqli_query($base, 'DELETE FROM statutforum WHERE idsujet=\'' . $_GET['id'] . '\'') or die('Erreur SQL !<br />' . mysql_error());
				$ex = mysqli_query($base, 'SELECT nbMessages FROM autre WHERE login=\'' . $_SESSION['login'] . '\'');
				$nbMessages = mysqli_fetch_array($ex);
				mysqli_query($base, 'UPDATE autre SET nbMessages=\'' . ($nbMessages['nbMessages'] + 1) . '\' WHERE login=\'' . $_SESSION['login'] . '\'');
				$information = "Votre réponse a été créée.";
			} else {
				$erreur = "Tous les champs ne sont pas remplis.";
			}
		} else {
			$erreur = "T'as essayé de m'avoir ? Eh bah non !";
		}
	} else {
		$erreur = "Mais c'est que tu es trés marrant toi ?";
	}
}

include("includes/tout.php");

if (isset($_GET['id'])) {
	$_GET['id'] = antiXSS($_GET['id']);
	$sql = 'SELECT * FROM reponses WHERE idsujet=\'' . $_GET['id'] . '\'';
	$ex = mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysql_error());
	$nb_resultats = mysqli_num_rows($ex);
	$nombreDeSujetsParPage = 10;
	$nombreDePages  = ceil($nb_resultats / $nombreDeSujetsParPage);
	if (isset($_GET['page']) and $_GET['page'] <= $nombreDePages and $_GET['page'] > 0 and preg_match("#\d#", $_GET['page'])) // Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
	{
		$page = $_GET['page']; // Récuperation du numéro de la page
	} else // La variable n'existe pas, c'est la première fois qu'on charge la page
	{
		if ($nombreDePages > 0) {
			$page = $nombreDePages;
		} else {
			$page = 1;
		}
	}

	// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
	$premierSujetAafficher = ($page - 1) * $nombreDeSujetsParPage;

	// Modifié par Yojim
	if (isset($_SESSION['login'])) {
		$sql5 = 'SELECT moderateur FROM membre WHERE login=\'' . $_SESSION['login'] . '\'';
		$ex5 = mysqli_query($base, $sql5) or die('Erreur SQL !' . $sql5 . '<br />' . mysql_error());
		$joueur = mysqli_fetch_array($ex5);
		// Si le joueur est modérateur, il a accès aux messages masqués
		if ($joueur['moderateur']) {
			$sql1 = 'SELECT * FROM reponses WHERE idsujet=\'' . $_GET['id'] . '\' ORDER BY timestamp ASC LIMIT ' . $premierSujetAafficher . ', ' . $nombreDeSujetsParPage . '';
		}
		// Si le joueur n'est pas modérateur, il n'a pas accès au messages masqués
		else {
			$sql1 = 'SELECT * FROM reponses WHERE idsujet=\'' . $_GET['id'] . '\' AND visibilite=1 ORDER BY timestamp ASC LIMIT ' . $premierSujetAafficher . ', ' . $nombreDeSujetsParPage . '';
		}
	} else {
		$sql1 = 'SELECT * FROM reponses WHERE idsujet=\'' . $_GET['id'] . '\' AND visibilite=1 ORDER BY timestamp ASC LIMIT ' . $premierSujetAafficher . ', ' . $nombreDeSujetsParPage . '';
	}
	//





	$ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !' . $sql1 . '<br />' . mysql_error());
	$sql3 = 'SELECT * FROM sujets WHERE id=\'' . $_GET['id'] . '\'';
	$ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !' . $sql3 . '<br />' . mysql_error());
	$sujet = mysqli_fetch_array($ex3);

	$javascript = false;
	if ($sujet['idforum'] == 8) {
		$javascript = true;
	}

	if (isset($_SESSION['login'])) {
		$ex = mysqli_query($base, 'SELECT count(*) AS existeDeja FROM statutforum WHERE login=\'' . $_SESSION['login'] . '\' AND idsujet=\'' . $_GET['id'] . '\'');
		$existeDeja = mysqli_fetch_array($ex);

		if ($existeDeja['existeDeja'] == 0 and $sujet['statut'] != 1) {
			mysqli_query($base, 'INSERT INTO statutforum VALUES("' . $_SESSION['login'] . '", "' . $_GET['id'] . '", "' . $sujet['idforum'] . '")') or die('Erreur SQL !<br />' . mysql_error());
		}
	}
	$ex = mysqli_query($base, 'SELECT titre FROM forums WHERE id=\'' . $sujet['idforum'] . '\'');
	$forum = mysqli_fetch_array($ex);

	// Ajout de Yojim
	// On vérifie si l'utilisateur n'est pas banni du forum
	if (isset($_SESSION['login'])) {
		$sql4 = 'SELECT * FROM sanctions WHERE joueur=\'' . $_SESSION['login'] . '\'';
		$ex4 = mysqli_query($base, $sql4) or die('Erreur SQL !' . $sql4 . '<br />' . mysql_error());
	} else {
		$ex4 = mysqli_query($base, 'SELECT * FROM sanctions WHERE joueur="lakzknsdjnsqjdnjibqsdhubqsdjqushd"'); //pour qu'il n'y ait aucun resultat si pas co
	}

	// Si il est banni
	if (mysqli_num_rows($ex4)) {
		$sanction = mysqli_fetch_array($ex4);
		list($annee, $mois, $jour) = explode('-', $sanction['dateFin']);
		$sanction['dateFin'] = $jour . '/' . $mois . '/' . $annee;
		echo "Vous ne pouvez plus accéder au forum car vous avez été banni par <a href=\"ecriremessage.php?destinataire=" . $sanction['moderateur'] . "\" class=\"lienVisible\">" . $sanction['moderateur'] . "</a> jusqu'au <strong>" . $sanction['dateFin'] . "</strong>.<br/>";
		echo "Motif de la sanction : " . BBcode($sanction['motif']);
	} else {

		$ex = mysqli_query($base, 'SELECT image, count(image) as nb FROM autre WHERE login=\'' . $sujet['auteur'] . '\'');
		$image = mysqli_fetch_array($ex);
		$couleur = rangForum($sujet['auteur']);
		if ($image['nb'] == 0) { // s'il le joueur n'existe plus, on prends l'image par défaut
			$image['image'] = "defaut.png";
		}

		$adresse = "sujet.php?";
		$premier = '';
		if ($page > 2) {
			$premier = '<a href="' . $adresse . 'page=1&id=' . $_GET['id'] . '">1</a>';
		}
		$pointsD = '';
		if ($page > 3) {
			$pointsD = '...';
		}
		$precedent = '';
		if ($page > 1) {
			$precedent = '<a href="' . $adresse . 'page=' . ($page - 1) . '&id=' . $_GET['id'] . '">' . ($page - 1) . '</a>';
		}
		$suivant = '';
		if ($page + 1 <= $nombreDePages) {
			$suivant = '<a href="' . $adresse . 'page=' . ($page + 1) . '&id=' . $_GET['id'] . '">' . ($page + 1) . '</a>';
		}
		$pointsF = '';
		if ($page + 3 <= $nombreDePages) {
			$pointsF = '...';
		}
		$dernier = '';
		if ($page + 2 <= $nombreDePages) {
			$dernier = '<a href="' . $adresse . 'page=' . $nombreDePages . '&id=' . $_GET['id'] . '">' . $nombreDePages . '</a>';
		}
		$pages = $premier . ' ' . $pointsD . ' ' . $precedent . ' <strong>' . $page . '</strong> ' . $suivant . ' ' . $pointsF . ' ' . $dernier;

		debutCarte();
		debutContent();
		$ex = mysqli_query($base, 'SELECT titre FROM forums WHERE id=\'' . $sujet['idforum'] . '\'');
		$forum = mysqli_fetch_array($ex);
		echo '<a href="forum.php">Forum</a> > <a href="listesujets.php?id=' . $sujet['idforum'] . '">' . $forum['titre'] . '</a> > ' . $sujet['titre'];
		finContent();
		finCarte();

		$editer = "";
		if (isset($_SESSION['login']) and $_SESSION['login'] == $sujet['auteur']) {
			$editer = '<a href="editer.php?id=' . $sujet['id'] . '&type=1">Editer</a>';
		}
		carteForum('<img alt="profil" src="images/profil/' . $image['image'] . '" style="max-width:70px;max-height:70px;border-radius:10px;"/>', '<a href="joueur.php?id=' . $sujet['auteur'] . '">' . $sujet['auteur'] . '</a>', date('d/m/Y à H\hi', $sujet['timestamp']), $sujet['titre'], BBcode($sujet['contenu']), $couleur, 'Page : ' . $pages . $editer);


		if ($nb_resultats > 0) {
			while ($reponse = mysqli_fetch_array($ex1)) {

				$couleur = rangForum($reponse['auteur']);

				// Ajout de Yojim
				// Si le message est masqué, on change sa couleur
				if (!$reponse['visibilite']) {
					$couleur = '#7A7B7A; opacity:0.35';
				}
				// Sinon on laisse la couleur normale
				//
				$ex = mysqli_query($base, 'SELECT image, count(image) as nb FROM autre WHERE login=\'' . $reponse['auteur'] . '\'');
				$image = mysqli_fetch_array($ex);
				if ($image['nb'] == 0) { // s'il le joueur n'existe plus, on prends l'image par défaut
					$image['image'] = "defaut.png";
				}

				// On regarde si l'utilisateur connecté est un modérateur
				$editer = false;
				if (isset($_SESSION['login'])) {
					$ex4 = mysqli_query($base, 'SELECT moderateur FROM membre WHERE login=\'' . $_SESSION['login'] . '\'');
					$donnees4 = mysqli_fetch_array($ex4);
				}
				if (isset($_SESSION['login']) and $_SESSION['login'] == $reponse['auteur'] and $donnees4['moderateur'] == 0) {
					$editer = '<a href="editer.php?id=' . $reponse['id'] . '&type=2">Editer</a> <a href="editer.php?id=' . $reponse['id'] . '&type=3">Supprimer</a>';
				}
				// Si l'utilisateur est un modérateur
				elseif (isset($_SESSION['login']) and $donnees4['moderateur'] == 1) {
					// Si le message est masqué, on propose de l'afficher
					if (!$reponse['visibilite']) {
						$editer = '<a href="editer.php?id=' . $reponse['id'] . '&type=2">Editer</a> <a href="editer.php?id=' . $reponse['id'] . '&type=3">Supprimer</a> <a href="editer.php?id=' . $reponse['id'] . '&type=4">Afficher</a>';
					}
					// Sinon, on propose de le masquer
					else {
						$editer = '<a href="editer.php?id=' . $reponse['id'] . '&type=2">Editer</a> <a href="editer.php?id=' . $reponse['id'] . '&type=3">Supprimer</a> <a href="editer.php?id=' . $reponse['id'] . '&type=5">Masquer</a>';
					}
				}

				carteForum('<img alt="profil" src="images/profil/' . $image['image'] . '" style="max-width:70px;max-height:70px;border-radius:10px;"/>', '<a href="joueur.php?id=' . $reponse['auteur'] . '">' . $reponse['auteur'] . '</a>', date('d/m/Y à H\hi', $reponse['timestamp']), $sujet['titre'], BBcode($reponse['contenu'], $javascript), $couleur, $editer);
			}
		} else {
			debutCarte();
			debutContent();
			echo '<p>Ce sujet ne contient aucune réponse.</p>';
			finContent();
			finCarte();
		}

		debutCarte();
		debutContent();
		echo 'Page : ' . $pages;
		finContent();
		finCarte();

		if (isset($_SESSION['login'])) {
			debutCarte("Créer une réponse");
			if ($sujet['statut'] == 0) {
				debutListe();
				creerBBcode("contenu");
				item(['form' => ['sujet.php?id=' . $_GET['id'], "reponseForm"], 'floating' => false, 'titre' => "Réponse", 'input' => '<textarea name="contenu" id="contenu" rows="10" cols="50"></textarea>']);
				item(['input' => submit(['titre' => 'Répondre', 'form' => 'reponseForm'])]);
				finListe();
			} else {
				echo "Ce sujet est vérouillé.";
			}
			finCarte();
		}
	}
} else {
	echo "<p>Bravo, t'es un vrai hackeur maintenant que tu sais modifier la barre URL !</p>";
}


include("includes/copyright.php"); ?>
<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>