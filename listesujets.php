<?php
session_start();
$_SESSION['start'] = "start"; // Sert a savoir si il faut de nouveau ouvrir une nouvelle session ou non
if (isset($_SESSION['login'])) {
	include("includes/basicprivatephp.php");
} else {
	include("includes/basicpublicphp.php");
}
include("includes/bbcode.php");
if (isset($_POST['titre']) and isset($_POST['contenu']) and isset($_GET['id'])) {
	if ($_GET['id'] >= 1 and $_GET['id'] <= 8 and preg_match("#^[0-9]*$#", $_GET['id'])) {
		if (isset($_SESSION['login'])) {
			if (!empty($_POST['titre']) and !empty($_POST['contenu'])) {
				$_POST['titre'] = mysqli_real_escape_string($base, ($_POST['titre']));
				$_POST['contenu'] = mysqli_real_escape_string($base, $_POST['contenu']);
				$sql = 'INSERT INTO sujets VALUES(default, "' . $_GET['id'] . '", "' . $_POST['titre'] . '", "' . $_POST['contenu'] . '", "' . $_SESSION['login'] . '", default, "' . (time()) . '")';
				mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysql_error());

				$ex = query('SELECT id FROM sujets WHERE contenu=\'' . $_POST['contenu'] . '\'');
				$sujet = mysqli_fetch_array($ex);

				query('INSERT INTO statutforum VALUES("' . $_SESSION['login'] . '","' . $sujet['id'] . '", "' . $_GET['id'] . '")');
				$information = "Votre sujet a été créé.";
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

if (isset($_SESSION['login'])) {
	include("includes/basicprivatehtml.php");
} else {
	include("includes/basicpublichtml.php");
}

$sql = 'SELECT titre, id FROM forums WHERE id=\'' . $_GET['id'] . '\'';
$ex = mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysql_error());
$idforum = mysqli_fetch_array($ex);
?>

<div class="table-responsive">
	<?php
	if (isset($_GET['id'])) {
		debutCarte($idforum['titre']);
		$sql = 'SELECT * FROM sujets WHERE idforum=\'' . $_GET['id'] . '\'';
		$ex = mysqli_query($base, $sql) or die('Erreur SQL !' . $sql . '<br />' . mysql_error());
		$nb_resultats = mysqli_num_rows($ex);
		$nombreDeSujetsParPage = 10;
		$nombreDePages  = ceil($nb_resultats / $nombreDeSujetsParPage);
		if (isset($_GET['page']) and $_GET['page'] <= $nombreDePages and $_GET['page'] > 0 and preg_match("#\d#", $_GET['page'])) // Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
		{
			$page = $_GET['page']; // Récuperation du numéro de la page
		} else // La variable n'existe pas, c'est la première fois qu'on charge la page
		{
			$page = 1; // On se met sur la page 1 (par défaut)
		}

		// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
		$premierSujetAafficher = ($page - 1) * $nombreDeSujetsParPage;
		$sql1 = 'SELECT * FROM sujets WHERE idforum=\'' . $_GET['id'] . '\' ORDER BY statut, timestamp DESC LIMIT ' . $premierSujetAafficher . ', ' . $nombreDeSujetsParPage . '';
		$ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !' . $sql1 . '<br />' . mysql_error());
		if ($nb_resultats > 0) {
			echo '
        <div class="table-responsive">
		<table class="table table-striped table-bordered">
		<thead>
		<tr>
        <th>Statut</th>
		<th>Sujet</th>
		<th>Auteur</th>
		<th>Date</th>
		</tr>
		</thead>
		<tbody>';
			while ($sujet = mysqli_fetch_array($ex1)) {
				echo '<tr>';
				if ($sujet['statut'] == 0) {
					if (isset($_SESSION['login'])) {
						$ex = mysqli_query($base, 'SELECT count(*) AS luOuPas FROM statutforum WHERE idsujet=\'' . $sujet['id'] . '\' AND login=\'' . $_SESSION['login'] . '\'');
						$statutForum = mysqli_fetch_array($ex);

						if ($statutForum['luOuPas'] == 0) {
							echo '<td><img src="images/forum/nouveauMessage.png" alt="nouveauMessage" class="w32"/></td>';
						} else {
							echo '<td><img src="images/forum/pasDeNouveauMessage.png" alt="pasDeNouveauMessage" class="w32"/></td>';
						}
					} else {
						echo '<td><img src="images/forum/pasDeNouveauMessage.png" alt="pasDeNouveauMessage" class="w32"/></td>';
					}
				} else {
					echo '<td><img src="images/forum/sujetVerouille.png" alt="sujetVerouille" class="w32"/></td>';
				}
				echo '<td><a href="sujet.php?id=' . $sujet['id'] . '">' . $sujet['titre'] . '</a>';
				if (isset($_SESSION['login']) and $_SESSION['login'] == $sujet['auteur']) {
					echo '<br/><a href="editer.php?id=' . $sujet['id'] . '&type=1"><em>Editer</em></a>';
				}
				echo '</td>';
				echo '
			<td>' . joueur($sujet['auteur']) . '</td>
			<td><em>' . date('d/m/Y à H\hi', $sujet['timestamp']) . '</em></td>';
				echo '</tr>';
			}
			echo '</tbody></table></div><br/>';
			echo '<p>Page : ';
			$adresse = "listesujets.php?";
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
			echo $premier . ' ' . $pointsD . ' ' . $precedent . ' <strong>' . $page . '</strong> ' . $suivant . ' ' . $pointsF . ' ' . $dernier;
	?></p>
			<p class="legende">
				<?php
				if (isset($_SESSION['login'])) {
					echo important('Légende'); ?>
					<img src="images/forum/nouveauMessage.png" alt="nouveauMessage" style="vertical-align:middle" class="w32" /> : Un ou plusieurs nouveaux messages<br /><br />
					<img src="images/forum/pasDeNouveauMessage.png" alt="pasDeNouveauMessage" style="vertical-align:middle" class="w32" /> : Pas de nouveaux messages<br /><br />
					<img src="images/forum/sujetVerouille.png" alt="sujetVerouille" style="vertical-align:middle" class="w32" /> : Sujet verrouillé<br />
				<?php
				} else {
					echo important('Légende'); ?>
					<img src="images/forum/pasDeNouveauMessage.png" alt="pasDeNouveauMessage" style="vertical-align:middle" /> : Sujet ouvert<br />
					<img src="images/forum/sujetVerouille.png" alt="sujetVerouille" style="vertical-align:middle" /> : Sujet verouillé<br />
				<?php } ?>
			</p><?php
			} else {
				echo '<p>Cette partie du forum ne contient aucun sujets ou ce forum n\'existe pas.</p>';
			}
			finCarte();

			if (isset($_SESSION['login'])) {
				debutCarte("Créer un sujet");

				?><form action="listesujets.php?id=<?php if (isset($_GET['id'])) {
												echo $_GET['id'];
											} ?>" method="post" name="formCreerSujet"><?php
																																		debutListe();
																																		item(['titre' => 'Titre', 'input' => '<input type="text" name="titre" id="titre" class="form-control"/>', 'floating' => true]);
																																		creerBBcode("contenu");
																																		item(['floating' => true, 'titre' => "Contenu", 'input' => '<textarea name="contenu" id="contenu" rows="10" cols="50"></textarea>']);
																																		item(['input' => submit(['titre' => 'Créer', 'form' => 'formCreerSujet'])]);
																																		finListe();
																																		?></form><?php
					finCarte();
				}
			} else {
				echo "<p>Bravo, t'es un vrai hackeur maintenant que tu sais modifier la barre URL !</p>";
			}
			include("includes/copyright.php"); ?>