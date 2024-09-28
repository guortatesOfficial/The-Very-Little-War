<?php 
session_start();
$_SESSION['start'] = "start"; // Sert a savoir si il faut de nouveau ouvrir une nouvelle session ou non
if(isset($_SESSION['login']))
{
	include("includes/basicprivatephp.php");
}
else
{
	include("includes/basicpublicphp.php"); 
}

include("includes/tout.php");
debutCarte("Crédits");
debutContent();
echo important("Images");
?>
Ce site regorge de nombreuses images sous license copyright provenant du site web <a href="https://www.flaticon.com/" class="lienVisible">FlatIcon</a>. Voici la liste des auteurs des différentes images : Freepik, VectorMarket, RoundIcons, Madebyoliver, Nikita Golubev, Pixel Buddha, Yannick, Creaticca Creative Agency, Dave Gandy, Dimitry, Gregor Cresnar, Alessio Atzeni...<br/><br/>
Si vous trouvez des images vous appartenant sur ce site et que vous n'êtes pas crédité, veuillez me faire part de votre demande par l'intérmédiaire de <strong>Contact</strong>.

<?php
finContent();
finCarte();
include("includes/copyright.php"); ?>