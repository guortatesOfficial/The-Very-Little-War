<?php
include("includes/connexion.php");
include("includes/fonctions.php");
$sqlMaintenance = mysqli_query($base, 'SELECT maintenance FROM statistiques');
$maintenance = mysqli_fetch_array($sqlMaintenance);
/*if ($maintenance['maintenance'] != 0) {
	header('Location: maintenance.php');
	exit();
}*/

if (!isset($_SESSION['start'])) {
	session_start();
}

//Si une session existe elle est détruite 
session_unset();
session_destroy();

//Si le formulaire de connexion a été soumis et que le couple mdp login est bon, on se connecte
if (isset($_POST['loginConnexion']) && isset($_POST['passConnexion'])) {
	if (!empty($_POST['loginConnexion']) && !empty($_POST['passConnexion'])) {
		$untreatedPass = $_POST['passConnexion'];
		$_POST['loginConnexion'] = ucfirst(mb_strtolower(antiXSS($_POST['loginConnexion'])));
		$_POST['passConnexion'] = md5(antiXSS($_POST['passConnexion']));
		$sql = 'SELECT count(*) FROM membre WHERE login="' . $_POST['loginConnexion'] . '" AND pass_md5="' . $_POST['passConnexion'] . '"';
		$req = mysqli_query($base, $sql) or die('Erreur SQL !<br/>' . $sql . '<br />' . mysqli_error($base));
		$data = mysqli_fetch_array($req);
		mysqli_free_result($req);

		$a = mysqli_query($base, "SELECT login FROM membre WHERE login LIKE 'Visiteur%' AND derniereConnexion < " . (time() - 3600 * 3) . "");
		while ($supp = mysqli_fetch_array($a)) {
			supprimerJoueur($supp['login']);
		}
		if ($data[0] == 1) {
			session_start();
			$_SESSION['login'] = $_POST['loginConnexion'];
			$_SESSION['mdp'] = $_POST['passConnexion'];

			$sql = 'UPDATE membre SET ip =\'' . $_SERVER['REMOTE_ADDR'] . '\' WHERE login=\'' . $_POST['loginConnexion'] . '\'';
			mysqli_query($base, $sql);

			$sql1 = 'SELECT niveaututo FROM autre WHERE login=\'' . $_SESSION['login'] . '\'';
			$ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
			$joueur = mysqli_fetch_array($ex1);
			echo '
            <script>
                localStorage.setItem("login", "' . $_SESSION['login'] . '");
                localStorage.setItem("mdp", "' . $untreatedPass . '");
                window.location = "constructions.php";
            </script>';
		} elseif ($data[0] == 0) {
			$erreur = 'Le couple login-mot de passe est erronné';
		} else {
			$erreur = 'Plusieurs membres du site ont le même identifiant. FATAL ERROR';
		}
	} else {
		$erreur = 'Un des deux champs n\'a pas été rempli';
	}
}

// Toutes les entrées vieilles de plus de 5 minutes sont supprimées (nombres de connectes)
$timestamp_5min = time() - (60 * 5); // 60 * 5 = nombre de secondes écoulées en 5 minutes
mysqli_query($base, 'DELETE FROM connectes WHERE timestamp < ' . $timestamp_5min);
