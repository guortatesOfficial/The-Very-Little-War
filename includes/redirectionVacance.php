<?php
// Ajout de Yojim
	// On vérifie si le joueur connecté est en vacances
	$sqlJoueurVac = 'SELECT vacance FROM membre WHERE login=\''.$_SESSION['login'].'\'';
	$exJoueurVac = mysqli_query($base,$sqlJoueurVac);
	$joueurEnVac = mysqli_fetch_array($exJoueurVac);
	if ($joueurEnVac[0]) { ?>
		<script type="text/javascript">
		window.location = "vacance.php"
		</script>
		<?php
	}
?>