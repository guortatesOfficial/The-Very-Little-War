<div class="col-1">
<div class="panel panel-default margin-10 pattern-bg">
	<div class="panel-heading"><h2><img alt="pub" src="images/accueil/pub.png"  style="margin-right: 10px"/>Publicité</h2></div>
	<div class="panel-body">
	<!--<p style="text-align: center">
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<ins class="adsbygoogle"
	style="display:inline-block;width:336px;height:280px"
	data-ad-client="ca-pub-2378568784304627"
	data-ad-slot="3314456198"></ins>
	<script> 
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
	</p> -->
	</div>
	</div>
	<div class="panel panel-default margin-10 pattern-bg">
	<div class="panel-heading"><h2><img alt="news" src="images/accueil/news.png"  style="margin-right: 10px"/>News</h2></div>
	<div class="panel-body">
	<?php
	$retour = mysqli_query($base,'SELECT * FROM news ORDER BY id DESC LIMIT 0, 1');
	$nb = mysqli_num_rows($retour);
	if($nb == 0)
	{
		echo '<p>Aucune news pour l\'instant.</p>';
	}
	while ($donnees = mysqli_fetch_array($retour))
	{
		?>
		<span class="important"><?php echo $donnees['titre']; ?><em> le <?php echo date('d/m/Y Ã H\hi', $donnees['timestamp']); ?></em></span>  
		<p>
		<br/>
		<?php
		$contenu = nl2br(stripslashes($donnees['contenu']));
		echo $contenu;
		?></p><?php
	}
	?>    
	</div>
	</div>
	
	<div class="panel panel-default margin-10 pattern-bg">
	<div class="panel-heading"><h2><img alt="stats" src="images/accueil/statistiques.png"  style="margin-right: 10px"/>Statistiques</h2></div>
	<div class="panel-body">
	<p><img alt="user" src="images/accueil/user.png" style="margin-right: 5px"/>Inscrits :
	<?php $sql = 'SELECT count(*) AS c FROM membre';
	$req = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$inscrits = mysqli_fetch_array($req);
	echo $inscrits['c'];?>
	</p>
	<p><img alt="actifs" src="images/accueil/actifs.png" style="margin-right: 5px"/>Actifs :
	<?php
	$req = mysqli_query($base,'SELECT count(*) AS c FROM membre WHERE derniereConnexion >=\''.(time()-2678400).'\'');
	$actifs = mysqli_fetch_array($req);
	echo $actifs['c'];?>
	</p>
	<p><img alt="connectes" src="images/accueil/connectes.png" style="margin-right: 5px"/><a href="connectes.php">Connectés</a> : <?php $retour = mysqli_query($base,'SELECT COUNT(*) AS nbre_entrees FROM connectes');
	$connectes = mysqli_fetch_array($retour); 
	echo $connectes['nbre_entrees']; ?>
	</p> 
	</div>
	</div>
	
	<div class="panel panel-default margin-10 pattern-bg">
	<div class="panel-heading"><h2><img alt="screen" src="images/accueil/screen.png"  style="margin-right: 10px"/>Screenshots</h2></div>
	<div class="panel-body">
	<a href="images/screenshoots/screen1.png" rel="lightbox[screenshoots]"><img alt="screenMini1" src="images/screenshoots/screen1.png" class="img-square img-thumbnail" style="width:200;height:113;"/></a>
	<a href="images/screenshoots/screen2.png" rel="lightbox[screenshoots]"><img alt="screenMini2" src="images/screenshoots/screen2.png" class="img-square img-thumbnail" style="width:200;height:113;"/></a>
	<a href="images/screenshoots/screen3.png" rel="lightbox[screenshoots]"><img alt="screenMini3" src="images/screenshoots/screen3.png" class="img-square img-thumbnail" style="width:200;height:113;"/></a>
	<a href="images/screenshoots/screen4.png" rel="lightbox[screenshoots]"><img alt="screenMini3" src="images/screenshoots/screen4.png" class="img-square img-thumbnail" style="width:200;height:113;"/></a>
	</div>
	</div>
	</div>