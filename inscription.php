<?php
include("includes/basicpublicphp.php");

//Si le bouton inscription a été cliqué
if (isset($_POST['login'])) {
	//Si les champs sont vides
	if ((isset($_POST['login']) && !empty($_POST['login'])) && (isset($_POST['pass']) && !empty($_POST['pass'])) && (isset($_POST['pass_confirm']) && !empty($_POST['pass_confirm'])) && (isset($_POST['email']) && !empty($_POST['email']))) {
		//Si les deux mots de passe sont différents
		$_POST['login'] = ucfirst(mb_strtolower(antiXSS($_POST['login'])));
		$_POST['pass'] = antiXSS($_POST['pass']);
		$_POST['pass_confirm'] = antiXSS($_POST['pass_confirm']);
		$_POST['email'] = antiXSS($_POST['email']);
		if ($_POST['pass'] != $_POST['pass_confirm']) {
			$erreur = 'Les deux mots de passe sont différents.';
		} else {
			if (preg_match("#^[A-Za-z0-9]*$#", $_POST['login'])) {
				$exMail = mysqli_query($base, 'SELECT count(*) AS nb FROM membre WHERE email=\'' . $_POST['email'] . '\'');
				$nb = mysqli_fetch_array($exMail);
				if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email']) && $nb['nb'] == 0) {
					$sql = 'SELECT count(*) FROM membre WHERE login="' . $_POST['login'] . '"';
					$req = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
					$data = mysqli_fetch_array($req);
					//Si le login est déjà utilisé
					if ($data[0] == 0) {
						inscrire($_POST['login'], $_POST['pass'], $_POST['email']);
						echo '<script type="text/javascript">
						window.location.href = "index.php?inscrit=1";
						</script>';
						exit();
					} else {
						$erreur = 'Ce login est déjà utilisé.';
					}
				} else {
					$erreur = 'L\'email n\'est pas correct ou déjà utilisé.';
				}
			} else {
				$erreur = 'Vous ne pouvez pas utiliser de caractères spéciaux dans votre login';
			}
		}
	} else {
		$erreur = 'Un ou plusieurs champs sont vides.';
	}
}
include("includes/tout.php");
debutCarte("Inscription");
echo '<form action="inscription.php" method="post" name="inscription">';
debutListe();
item(['floating' => true, 'media' => '<img alt="login" src="images/accueil/player.png" class="w32"/>', 'titre' => 'Login', 'input' => '<input type="text" name="login" id="login" maxlength="13" value="Login">', 'after' => submit(['link' => 'javascript:generate()', 'titre' => 'Générer'])]);
item(['floating' => true, 'media' => '<img alt="login" src="images/accueil/email.png" class="w32"/>', 'titre' => 'E-mail', 'input' => '<input type="text" name="email" id="email" maxlength="100" class="form-control">', 'after' => popover('popover-mail', 'images/question.png')]);
item(['floating' => true, 'media' => '<img alt="login" src="images/accueil/door-key.png" class="w32"/>', 'titre' => 'Mot de passe', 'input' => '<input type="password" name="pass" id="pass" class="form-control">']);
item(['floating' => true, 'media' => '<img alt="login" src="images/accueil/door-key.png" class="w32"/>', 'titre' => 'Confirmation', 'input' => '<input type="password" name="pass_confirm" id="pass_confirm" class="form-control">']);
echo '<p style="margin-left:5px">En vous inscrivant vous acceptez nos <a href="regles.php" class="external lien lienVisible">Conditions Générales d\'Utilisation</a></p>';
item(['input' => submit(['form' => 'inscription', 'titre' => 'Inscription'])]);
finListe();
echo '</form>';
finCarte();
?>

<div class="popover popover-mail">
	<div class="popover-angle"></div>
	<div class="popover-inner">
		<div class="content-block">
			<p>Un mail sera envoyé à cette adresse pour <span class="important">confirmer votre inscription</span> et vous prévenir du début d'une nouvelle partie.<br /> Il peut être changé dans "Mon compte".</p>
		</div>
	</div>
</div>
<?php include("includes/copyright.php"); ?>