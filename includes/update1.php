<?php
//////////////////////////////////////////////////////////// Gestion des ressources
$sql = 'SELECT tempsPrecedent FROM autre WHERE login=\''.$_POST['joueurAEspionner1'].'\'';
$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
$adversaire = mysqli_fetch_array($ex);
$nbsecondesAdverse = time() - $adversaire['tempsPrecedent'];// On calcule la différence de secondes
$requete = mysqli_query($base,'SELECT depot FROM constructions WHERE login=\''.$_POST['joueurAEspionner1'].'\'');
$depotAdverse = mysqli_fetch_array($requete);

$sql1 = 'UPDATE autre SET tempsPrecedent=\''.time().'\' WHERE login=\''.$_POST['joueurAEspionner1'].'\'';
$ex1 = mysqli_query($base,$sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////ENERGIE

$sql = mysqli_query($base,'SELECT energie, revenuenergie FROM ressources WHERE login=\''.$_POST['joueurAEspionner1'].'\'');// On prends l'energie en ce moment
$donnees = mysqli_fetch_array($sql);

$energie = $donnees['energie'] + round($donnees['revenuenergie']*$nbsecondesAdverse/3600);// On calcule l'energie que l'on doit avoir
if($energie>=(4*pow(4, $depotAdverse['depot']+2)))
{
$energie= (4*pow(4, $depotAdverse['depot']+2)); // on limite l'energie pouvant être reçu (depots de ressources)
}
$req='UPDATE ressources SET energie=\'' . $energie . '\' WHERE login = \'' . $_POST['joueurAEspionner1'] . '\'';// on inscrit ce nouveau energie
$ex = mysqli_query($base,$req) or die ('Erreur SQL !<br />'.$req.'<br />'.mysql_error());

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////RESSOURCES

foreach($nomsRes as $num => $ressource) {
	$sql = mysqli_query($base,'SELECT '.$ressource.', revenu'.$ressource.' FROM ressources WHERE login=\''.$_POST['joueurAEspionner1'].'\'');
	$donnees = mysqli_fetch_array($sql);

	$$ressource = $donnees[$ressource] + round($donnees['revenu'.$ressource]*$nbsecondesAdverse/3600);
	if($$ressource>=(4*pow(4, $depotAdverse['depot']+2)))
	{
	$$ressource = (4*pow(4, $depotAdverse['depot']+2)); 
	}
	$req='UPDATE ressources SET '.$ressource.'=\'' . $$ressource . '\' WHERE login = \'' . $_POST['joueurAEspionner1'] . '\'';
	$ex = mysqli_query($base,$req) or die ('Erreur SQL !<br />'.$req.'<br />'.mysql_error());
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////Gestion des molécules disparaissant

$sql = 'SELECT * FROM molecules WHERE proprietaire=\''.$_POST['joueurAEspionner1'].'\' AND nombre > 0';
$ex = mysqli_query($base,$sql) or die ('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

while($molecules = mysqli_fetch_array($ex)) {
	$nbAtomes = 0;
	foreach($nomsRes as $num => $ressource) {
		$nbAtomes = $nbAtomes+$molecules[$ressource];
	}
	$nbheures = round($nbsecondesAdverse/3600);
	$moleculesAEnlever = 0;
	$moleculesRestantes = $molecules['nombre'];
	while($nbheures > 0) {
		$moleculesAEnlever = ($nbAtomes / 1000) * $moleculesRestantes;
		$moleculesRestantes = $moleculesRestantes - $moleculesAEnlever; 
		$nbheures = $nbheures - 1;
	}
	
	$sql1 = 'UPDATE molecules SET nombre=\''.$moleculesRestantes.'\' WHERE id=\''.$molecules['id'].'\'';
	$ex1 = mysqli_query($base,$sql1) or die ('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
}

?>

