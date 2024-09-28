<?php 
session_start();
$_SESSION['start'] = "start"; 
if (isset($_SESSION['login']))
{
	include("includes/basicprivatephp.php");
}
else
{
	include("includes/basicpublicphp.php"); 
}

include("includes/tout.php");

if(isset($_GET['id'])) {
	$ex = mysqli_query($base,'SELECT * FROM declarations WHERE id=\''.$_GET['id'].'\' AND type=0');
	$guerre = mysqli_fetch_array($ex);
	$nbGuerres = mysqli_num_rows($ex);
	
	$ex = mysqli_query($base,'SELECT tag FROM alliances WHERE id=\''.$guerre['alliance1'].'\'');
	$alliance1 = mysqli_fetch_array($ex);
	
	$ex = mysqli_query($base,'SELECT tag FROM alliances WHERE id=\''.$guerre['alliance2'].'\'');
	$alliance2 = mysqli_fetch_array($ex);

	if($nbGuerres > 0) {
        debutCarte('<a href="alliance.php?id='.$alliance1['tag'].'" style="color:white"><span class="lienTitre">'.$alliance1['tag'].'</span></a> VS <a href="alliance.php?id='.$alliance2['tag'].'"><span class="lienTitre" style="color:white">'.$alliance2['tag'].'</span></a>');
		echo '
		<p>
		<span class="subimportant">Nombre de pertes totales : </span>'.number_format(($guerre['pertes1'] + $guerre['pertes2']), 0 , ' ', ' ').' molécules dont<br/>';
		if($guerre['pertes1'] + $guerre['pertes2'] > 0) {
			echo '
			'.number_format($guerre['pertes1'], 0 , ' ', ' ').' molécules pour <a href="alliance.php?id='.$alliance1['tag'].'">'.$alliance1['tag'].'</a> ('.round($guerre['pertes1']/($guerre['pertes1'] + $guerre['pertes2'])*100).'%)<br/>
			'.number_format($guerre['pertes2'], 0 , ' ', ' ').' molécules pour <a href="alliance.php?id='.$alliance2['tag'].'">'.$alliance2['tag'].'</a> ('.round($guerre['pertes2']/($guerre['pertes1'] + $guerre['pertes2'])*100).'%)<br/>
			';
		}
		else {
			echo '
			0 molécules pour <a href="alliance.php?id='.$alliance1['tag'].'">'.$alliance1['tag'].'</a> (0%)<br/>
			0 molécules pour <a href="alliance.php?id='.$alliance2['tag'].'">'.$alliance2['tag'].'</a> (0%)<br/>
			';
		}
		echo '<br/><span class="subimportant">Date de début de la guerre : </span>'.date('d/m/Y à H\hi', $guerre['timestamp']).'<br/>';
		
		if($guerre['fin'] > $guerre['timestamp']) {
			echo '<span class="subimportant">Date de fin de la guerre : </span>'.date('d/m/Y à H\hi', $guerre['fin']).'<br/>
			Cette guerre a donc duré '.round(($guerre['fin'] - $guerre['timestamp'])/86400).' jours.';
		}
		else {
			echo '<span class="subimportant">Date de fin de la guerre : </span>Non finie<br/>';
		}
		echo '</p>';
        finCarte();
	}
	else {
		echo "<p>Cette guerre n'a jamais existé.</p>";
	}
}
else {
	echo "<p>Stop ça petit troll !</p>";
}

include("includes/copyright.php");