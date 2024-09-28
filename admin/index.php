<?php
include("../includes/connexion.php");

session_start();
include("../includes/constantesBase.php");
include("../includes/fonctions.php");

if (isset($_POST['motdepasseadmin'])) {
	$_SESSION['motdepasseadmin'] = $_POST['motdepasseadmin'];
}
if (isset($_SESSION['motdepasseadmin']) and $_SESSION['motdepasseadmin'] == "Faux mot de passe") {
	if (isset($_GET['supprimercompte'])) {
		$modif = 'SELECT login FROM membre WHERE ip=\'' . $_GET['supprimercompte'] . '\'';
		$ex = mysqli_query($base, $modif) or die('Erreur SQL !<br/>' . $modif . '<br/>' . mysql_error());
		while ($login = mysqli_fetch_array($ex)) {
			supprimerJoueur($login['login']);
		}
	}

	if (isset($_POST['maintenance'])) {
		mysqli_query($base, 'UPDATE statistiques SET maintenance = 1');
	}

	if (isset($_POST['plusmaintenance'])) {
		mysqli_query($base, 'UPDATE statistiques SET maintenance = 0');
	}

	if (isset($_POST['miseazero'])) {
		//Virage des joueurs inactifs
		remiseAZero();
	}
?>

	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>The Very Little War - Menu d'administration</title>
		<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
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
		<h4>Menu d'aministration</h4>
		<p>
		<ul>
			<li><a href="listenews.php">Liste des news</a></li>
			<li><a href="supprimercompte.php">Supprimer un compte</a></li>
			<li><a href="listesujets.php">Verouiller ou supprimer un sujet</a></li>
			<li><a href="supprimerreponse.php">Supprimer une reponse</a></li>
		</ul>
		</p>

		<h4>Liste des multi-comptes</h4>
		<p>
		<table>
			<tr>
				<th>Ip multiple</th>
				<th>Supprimer les comptes</th>
			</tr>
			<?php $retour = mysqli_query($base, 'SELECT ip FROM membre GROUP BY ip HAVING (count(*)>1)');
			while ($donnees = mysqli_fetch_array($retour)) {
				$ex1 = query('SELECT login FROM membre WHERE ip=\'' . $donnees['ip'] . '\'');
				$a = 0;
				while ($d1 = mysqli_fetch_array($ex1)) {
					if (statut($d1['login'])) {
						$a = 1;
					}
				}
				if ($a) {
			?>
					<tr>
						<td><?php echo '<a href="ip.php?ip=' . $donnees['ip'] . '">' . $donnees['ip'] . '</a>'; ?></td>
						<td><?php echo '<a href="index.php?supprimercompte=' . $donnees['ip'] . '">'; ?>Supprimer</a></td>
					</tr>
			<?php
				}
			}
			?>
		</table>
		</p>
		<h4>Mise en maintenance (plus personne ne pourra aller sur le site sauf ici)</h4>
		<p>
		<form action="index.php" method="post">
			<?php
			$sqlMaintenance = mysqli_query($base, 'SELECT maintenance FROM statistiques');
			$maintenance = mysqli_fetch_array($sqlMaintenance);
			if ($maintenance['maintenance'] != 0) {
			?>
				<input type="submit" name="plusmaintenance" value="Enlever la mise en maintenance" />
			<?php
			} else {
			?>
				<input type="submit" name="maintenance" value="Mise en maintenance" />
			<?php
			}
			?>
		</form>
		</p>
		<h4>Ressources donnees</h4>
		<p>
		<table>
			<tr>
				<th>Energie</th>
				<?php foreach ($nomsRes as $num => $ressource) {
					echo '<th>' . ucfirst($nomsAccents[$num]) . '</th>';
				} ?>
				<th>Justification</th>
				<th>Destinataire</th>
			</tr>
			<?php $retour = mysqli_query($base, 'SELECT * FROM moderation');
			while ($donnees = mysqli_fetch_array($retour)) {
			?>
				<tr>
					<td><?php echo $donnees['energie']; ?></td>
					<?php foreach ($nomsRes as $num => $ressource) {
						echo '<td>' . $donnees[$ressource] . '</td>';
					} ?>
					<td><?php echo $donnees['justification']; ?></td>
					<td><?php echo $donnees['destinataire']; ?></td>
				</tr>
			<?php
			}
			?>
		</table>
		</p>
		<h4>Remise a zero et virage des joueurs inactifs</h4>
		<p>
		<form action="index.php" method="post">
			<input type="submit" name="miseazero" value="Remise à zero" />
		</form>
		</p>
	</body>

	</html>
<?php
} else { ?>
	<form action="index.php" method="post">
		<label for="motdepasseadmin">Mot de passe : </label>
		<input type="text" name="motdepasseadmin" id="motdepasseadmin" />
		<input type="submit" name="valider" value="Valider" />
	</form>
<?php
}
?>