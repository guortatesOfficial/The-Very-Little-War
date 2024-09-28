<?php
include("includes/connexion.php");

$ex = mysqli_query($base,'SELECT login FROM membre');

while($d = mysqli_fetch_array($ex)){
mysqli_query($base, 'INSERT INTO messages VALUES(default,'.time().',"Bienvenue","Bienvenue à tous les nouveaux joueurs et merci à SVJ ! Vous avez vu que le premier joueur est loin devant dans le classement mais ne vous découragez pas, une nouvelle partie recommence tous les mois. Prenez donc le temps de cette fin de partie du mois d\'octobre pour vous entrainer ! Bon jeu et bonne chance sur The Very Little War","Guortates","'.$d['login'].'",default)');
}
