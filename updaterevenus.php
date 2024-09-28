<?php
$base = mysql_connect ('localhost', 'theveryl_admin', 'mno33d65e') ; 
mysql_select_db ('theveryl_theverylittlewar', $base)or die ('Erreur de connexion a la base de données'.mysql_error()); 
$tab = array("soufre","chlore","brome","iode");
foreach($tab as $num => $ressource) {
	mysqli_query($base,"ALTER TABLE  `constructions` ADD  `generateur".$ressource."` INT( 11 ) NOT NULL DEFAULT  '1' AFTER  `generateurazote`");
	mysqli_query($base,"ALTER TABLE  `constructions` ADD  `vieGenerateur".ucfirst($ressource)."` BIGINT( 100 ) NOT NULL DEFAULT  '30' AFTER  `vieGenerateurAzote`");
	mysqli_query($base,"ALTER TABLE  `molecules` ADD  `".$ressource."` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `azote`");
	mysqli_query($base,"ALTER TABLE  `ressources` ADD  `".$ressource."` INT( 11 ) NOT NULL DEFAULT  '64' AFTER  `oxygene`");
	mysqli_query($base,"ALTER TABLE  `ressources` ADD  `revenu".$ressource."` INT( 11 ) NOT NULL DEFAULT  '9' AFTER  `revenuoxygene`");
	mysqli_query($base,"ALTER TABLE  `tutoriel` ADD  `bonus".$ressource."` INT( 11 ) NOT NULL AFTER  `bonusazote`");
	mysqli_query($base,"ALTER TABLE  `moderation` ADD  `".$ressource."` BIGINT( 50 ) NOT NULL DEFAULT  '0' AFTER  `oxygene`");
}
?>