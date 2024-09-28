<?php
session_start();
include("../includes/connexion.php");
include("../includes/constantesBase.php");
if (isset($_POST['motdepasseadmin'])) {
	$_SESSION['motdepasseadmin'] = $_POST['motdepasseadmin'];
}
if (!isset($_SESSION['motdepasseadmin']) or $_SESSION['motdepasseadmin'] != "Faux mot de passe") {
?>
	<form action="index.php" method="post">
		<label for="motdepasseadmin">Mot de passe : </label>
		<input type="password" name="motdepasseadmin" id="motdepasseadmin" />
		<input type="submit" name="valider" value="Valider" />
	</form> <?php
		} else {

			if (isset($_POST['deplacer']) and isset($_POST['deplacerSubmit']) and isset($_POST['idSujet'])) {
				mysqli_query($base, 'UPDATE sujets SET idforum=\'' . $_POST['deplacer'] . '\' WHERE id=\'' . $_POST['idSujet'] . '\'');
				$erreur = "Le sujet a été déplacé.";
			}
			if (isset($_POST['joueurBombe'])) {
				$sql = 'SELECT count(login) AS nb FROM membre WHERE login=\'' . $_POST['joueurBombe'] . '\'';
				$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br/>' . $sql . '<br/>' . mysqli_error($base));
				$d = mysqli_fetch_array($ex);

				if ($d['nb'] > 0) {
					$ex = mysqli_query($base, 'SELECT bombe FROM autre WHERE login=\'' . $_POST['joueurBombe'] . '\'');
					$joueur = mysqli_fetch_array($ex);
					mysqli_query($base, 'UPDATE autre SET bombe=\'' . ($joueur['bombe'] + 1) . '\' WHERE login=\'' . $_POST['joueurBombe'] . '\'');
					$erreur = "Vous avez rajouté un point de bombe à " . $_POST['joueurBombe'] . ".";
				} else {
					$erreur = "Ce joueur n'existe pas.";
				}
			}

			if (isset($_GET['supprimersujet'])) {
				$_GET['supprimersujet'] = addslashes($_GET['supprimersujet']);
				mysqli_query($base, 'DELETE FROM sujets WHERE id=\'' . $_GET['supprimersujet'] . '\'');
				mysqli_query($base, 'DELETE FROM statutforum WHERE idsujet=\'' . $_GET['supprimersujet'] . '\'');
			}
			if (isset($_GET['verouillersujet'])) {
				$_GET['verouillersujet'] = addslashes($_GET['verouillersujet']);
				mysqli_query($base, 'UPDATE sujets SET statut = 1 WHERE id=\'' . $_GET['verouillersujet'] . '\'');
				mysqli_query($base, 'DELETE FROM statutforum WHERE idsujet=\'' . $_GET['verouillersujet'] . '\'');
			}
			if (isset($_GET['deverouillersujet'])) {
				$_GET['deverouillersujet'] = addslashes($_GET['deverouillersujet']);
				mysqli_query($base, 'UPDATE sujets SET statut = 0 WHERE id=\'' . $_GET['deverouillersujet'] . '\'');
			}

			$bool = 1;
			foreach ($nomsRes as $num => $ressource) {
				if (!(isset($_POST[$ressource . 'Envoyee']))) {
					$bool = 0;
				}
			}
			if (isset($_POST['energieEnvoyee']) and $bool == 1 and isset($_POST['destinataire'])) {
				if (!empty($_POST['destinataire'])) {
					if (empty($_POST['energieEnvoyee'])) {
						$_POST['energieEnvoyee'] = 0;
					}
					foreach ($nomsRes as $num => $ressource) {
						if (empty($_POST[$ressource . 'Envoyee'])) {
							$_POST[$ressource . 'Envoyee'] = 0;
						}
					}

					$bool = 1;
					foreach ($nomsRes as $num => $ressource) {
						if (!(preg_match("#^[0-9]*$#", $_POST[$ressource . 'Envoyee']))) {
							$bool = 0;
						}
					}
					if (preg_match("#^[0-9]*$#", $_POST['energieEnvoyee']) and $bool == 1) {

						$ex = mysqli_query($base, 'SELECT count(*) AS joueurOuPas FROM membre WHERE login=\'' . $_POST['destinataire'] . '\'');
						$verification = mysqli_fetch_array($ex);
						if ($verification['joueurOuPas'] == 1) {
							$ex = mysqli_query($base, 'SELECT * FROM ressources WHERE login=\'' . $_POST['destinataire'] . '\'');
							$ressourcesDestinataire = mysqli_fetch_array($ex);

							$chaine = "";
							foreach ($nomsRes as $num => $ressource) {
								$plus = "";
								if ($num < $nbRes) {
									$plus = ",";
								}
								$chaine = $chaine . '' . $ressource . '=' . round($ressourcesDestinataire[$ressource] + $_POST[$ressource . 'Envoyee']) . '' . $plus;
							}
							mysqli_query($base, 'UPDATE ressources SET energie=\'' . round($ressourcesDestinataire['energie'] + $_POST['energieEnvoyee']) . '\', ' . $chaine . ' WHERE login=\'' . $_POST['destinataire'] . '\'');

							$chaine = "";
							foreach ($nomsRes as $num => $ressource) {
								$plus = "";
								if ($num < $nbRes) {
									$plus = ",";
								}
								$chaine = $chaine . '"' . $_POST[$ressource . 'Envoyee'] . '"' . $plus;
							}
							mysqli_query($base, 'INSERT INTO moderation VALUES(default,"' . $_POST['destinataire'] . '", "' . $_POST['energieEnvoyee'] . '", ' . $chaine . ', "' . mysqli_real_escape_string($base, stripslashes(htmlentities(trim($_POST['justification'])))) . '")');

							$chaine = "";
							foreach ($nomsRes as $num => $ressource) {
								$plus = "";
								if ($num < $nbRes) {
									$plus = ",";
								}
								$chaine = $chaine . '' . number_format($_POST[$ressource . 'Envoyee'], 0, ' ', ' ') . '<img src="../images/' . $ressource . '.png" alt="' . $ressource . '"/>' . $plus;
							}
							$erreur = "Vous avez donné " . number_format($_POST['energieEnvoyee'], 0, ' ', ' ') . "<img src=\"../images/energie.png\" alt=\"energie\"/>, " . $chaine . " à " . $_POST['destinataire'] . ".";
						} else {
							$erreur = "Le destinataire n'existe pas.";
						}
					} else {
						$erreur = "Seul des nombres entiers et positifs doivent être entrés.";
					}
				} else {
					$erreur = "Vous n'avez pas entré de destinataire.";
				}
			}
			?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

	<head>
		<title>The Very Little War - Modération</title>
		<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<style type="text/css">
			label {
				display: block;
				width: 270px;
				float: left;
			}
		</style>
		<link rel="stylesheet" type="text/css" href="../style/css/templatemo-style.css">
	</head>

	<body>
		<div class="panel panel-default margin-10 text-center pattern-bg">
			<div class="panel-heading">
				<h4>Modération</h4>
			</div>
			<div class="panel-body">
				<p>
					<!-- <a href="index.php?sub=1">Donner des ressources</a><br /> -->
					<a href="index.php?sub=2">Multicompte</a><br />
					<a href="index.php">Accueil modération (bombe + sujets)</a><br />
				</p>
				<?php
				if (isset($erreur)) {
					echo $erreur;
				}
				if (isset($_GET['sub'])) {
					//if ($_GET['sub'] == 1) {
				?>
					<!--<p>
							<form action="index.php?sub=1" method="post">
								<label for="energieEnvoyee">Energie : </label><input type="number" name="energieEnvoyee" id="energieEnvoyee" /><br />
								<?php
								/*foreach ($nomsRes as $num => $ressource) {
									echo '<label for="' . $ressource . 'Envoyee">' . ucfirst($nomsAccents[$num]) . ' : </label><input type="number" name="' . $ressource . 'Envoyee" id="' . $ressource . 'Envoyee"/><br/>';
								}*/
								?>
								<br />
								<label for="destinataire">Destinataire : </label><input type="text" name="destinataire" id="destinataire" /><br /><br />
								<label for="justification">Justification : </label><textarea name="justification" id="justification" /></textarea><br /><br />
								<input type="submit" name="envoyer" value="Envoyer" />
							</form>
						</p>-->
					<?php
					//}
					if ($_GET['sub'] == 2) { ?>
						<h4>Liste des multi-comptes</h4>
						<p>Veuillez donner un avertissement aux joueurs concernés avant de supprimer les comptes.<br /><br />
						<table>
							<tr>
								<th>Ip multiple</th>
							</tr>
							<?php $retour = mysqli_query($base, 'SELECT ip FROM membre GROUP BY ip HAVING (count(*)>1)');
							while ($donnees = mysqli_fetch_array($retour)) {
							?>
								<tr>
									<td><?php echo '<a href="ip.php?ip=' . $donnees['ip'] . '">' . $donnees['ip'] . '</a>'; ?></td>
								</tr>
							<?php
							}
							?>
						</table>
						</p>
					<?php
					}
				} else {
					?>
					<p>

						<span class="important">Ajouter +1 à la bombe</span> :
					<form action="index.php" method="post"><input type="text" name="joueurBombe" /><input type="submit" name="bombe" value="ajouter" /></form>
					</p>
					<p>
					<table>
						<tr>
							<th>Vérouiller</th>
							<th>Dévérouiller</th>
							<th>Supprimer</th>
							<th>Titre</th>
							<th>Auteur</th>
							<th>Statut</th>
							<th>Déplacer</th>
							<th>Date</th>
						</tr>
						<?php
						$retour = mysqli_query($base, 'SELECT * FROM sujets ORDER BY auteur DESC');
						while ($donnees = mysqli_fetch_array($retour)) {
						?>
							<tr>
								<td><?php echo '<a href="index.php?verouillersujet=' . $donnees['id'] . '">'; ?>Vérouiller</a></td>
								<td><?php echo '<a href="index.php?deverouillersujet=' . $donnees['id'] . '">'; ?>Dévérouiller</a></td>
								<td><?php echo '<a href="index.php?supprimersujet=' . $donnees['id'] . '">'; ?>Supprimer</a></td>
								<td><?php echo stripslashes($donnees['titre']); ?></td>
								<td><?php echo stripslashes($donnees['auteur']); ?></td>
								<td><?php if ($donnees['statut'] == 0) {
										echo "Ouvert";
									} else {
										echo "Vérouillé";
									} ?></td>
								<td>
									<form action="index.php" method="post">
										<select name="deplacer">
											<?php
											$ex = mysqli_query($base, 'SELECT id,titre FROM forums');
											while ($forum = mysqli_fetch_array($ex)) {
												$selected = "";
												if ($forum['id'] == $donnees['idforum']) {
													$selected = "selected";
												}
												echo '<option value="' . $forum['id'] . '" ' . $selected . '>' . $forum['titre'] . '</option>';
											}
											?>
										</select>
										<input type="hidden" name="idSujet" value="<?php echo $donnees['id']; ?>" />
										<input type="submit" value="Déplacer" name="deplacerSubmit" />
									</form>
								</td>
								<td><?php echo date('d/m/Y', $donnees['timestamp']); ?></td>
							</tr>
						<?php
						}
						?>
					</table>
					</p>
			<?php
				}
			}
			?>
			</div>
		</div>
	</body>

	</html>