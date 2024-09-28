<?php $sql = 'SELECT count(*) AS c FROM membre';
	$req = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$inscrits = mysqli_fetch_array($req);

    $retour = mysqli_query($base,'SELECT COUNT(*) AS c FROM connectes');
	$connectes = mysqli_fetch_array($retour); 
?>
   

<div style="text-align:center">
    <br/>
    <?php
    echo chip($inscrits['c'],'<img src="images/accueil/man-user.png" alt="user" style="width:20px;height:20px">',"black");
    echo chip(compterActifs(),'<img src="images/accueil/man-user.png" alt="user" style="width:20px;height:20px">',"red");
    echo chip('<a href="connectes.php" class="lienVisible">'.$connectes['c'].'</a>','<img src="images/accueil/man-user.png" alt="user" style="width:20px;height:20px">',"green");
    ?>
</div>