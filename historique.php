<?php
session_start();
$_SESSION['start'] = "start"; // Sert a savoir si il faut de nouveau ouvrir une nouvelle session ou non
if (isset($_SESSION['login']))
{
	include("includes/basicprivatephp.php");
}
else
{
	include("includes/basicpublicphp.php"); 
}

include("includes/tout.php");

if(isset($_GET['sub'])) {
	$_GET['sub'] = mysqli_real_escape_string($base,stripslashes(antihtml($_GET['sub'])));
}
if(isset($_POST['numeropartie'])) {
	$_POST['numeropartie'] = mysqli_real_escape_string($base,stripslashes(antihtml($_POST['numeropartie'])));
    $_SESSION['numeropartie'] = $_POST['numeropartie'];
}

if(!isset($_GET['sub'])) { $_GET['sub'] = 0;}
if(!isset($_POST['numeropartie'])) { if(!isset($_SESSION['numeropartie'])){$_POST['numeropartie'] = 1;}else {$_POST['numeropartie'] = $_SESSION['numeropartie'];}}
$nArchive = "";
if(isset($_GET['sub']) and isset($_POST['numeropartie'])) { 
    $ex = query('SELECT debut FROM parties WHERE id=\''.$_POST['numeropartie'].'\'');
    $data = mysqli_fetch_array($ex);
	$nArchive = ' - '.date('m/Y',$data['debut']);
}

debutCarte();
    echo important("Sélection de la partie").'<br/>';
    debutListe();
    echo '<form action="historique.php?sub='.$_GET['sub'].'" method="post" name="formHistorique">';

    $sql2 = 'SELECT * FROM parties ORDER BY id DESC';
    $ex2 = mysqli_query($base,$sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
    $options = "";
    while ($data = mysqli_fetch_array($ex2)) {
        $s = "";
        if($_POST['numeropartie'] == $data['id']) {$s = "selected";}
        $options = $options.'<option value='.$data['id'].' '.$s.'>'.date('m/Y',$data['debut']).'</option>';
    }

    item(['select' => ["numeropartie",$options]]);
    item(['input' => submit(['form' => 'formHistorique', 'titre' => 'Séléctionner'])]);

    echo '</form>';
    finListe();
finCarte();

debutCarte('Archives'.$nArchive);
?>
<div class="table-responsive">
<?php
if(isset($_POST['numeropartie'])) { 
	if(isset($_GET['sub']) AND $_GET['sub'] == 0) {
		?>
		<table class="table table-striped table-bordered">
		<thead>
		<tr>
		
        <th><img src="images/classement/up.png" alt="up" class="imageSousMenu"/><br/><span class="labelClassement">Rang</span></th>
        <th><img src="images/classement/joueur.png" alt="joueur" title="Joueur" class="imageSousMenu"/><br/><span class="labelClassement">Joueur</span></th>
        <th><a href="historique.php?sub=0"><img src="images/classement/points.png" alt="points" title="Points" class="imageSousMenu"/><br/><span class="labelClassement">Points</span></a></th>
        <th><img src="images/classement/alliance.png" alt="alliance" title="Equipe" class="imageSousMenu"/><br/><span class="labelClassement">Equipe</span></th>
        <th><a href="historique.php?sub=0&clas=5"><img src="images/classement/museum.png" alt="pointCs" title="Points de construction" class="imageSousMenu"/><br/><span class="labelClassement">Constructions</span></a></th>
        <th><a href="historique.php?sub=0&clas=2"><img src="images/classement/sword.png" alt="att" title="Attaque" class="imageSousMenu"/><br/><span class="labelClassement">Attaque</span></a></th>
        <th><a href="historique.php?sub=0&clas=3"><img src="images/classement/shield.png" alt="def" title="Défense" class="imageSousMenu"/><br/><span class="labelClassement">Défense</span></a></th>
        <th><a href="historique.php?sub=0&clas=4"><img src="images/classement/bag.png" alt="bag" title="Pillage" class="imageSousMenu"/><br/><span class="labelClassement">Pillage</span></a></th>
        <th><a href="historique.php?sub=0&clas=1"><img src="images/classement/victoires.png" alt="victoires" title="Points de victoire" class="imageSousMenu"/><br/><span class="labelClassement">Victoire</span></a></th>
	
		</tr>
		</thead>
		<tbody>
		<?php 
		$ex = mysqli_query($base,'SELECT * FROM parties WHERE id=\''.$_POST['numeropartie'].'\'');
		$data = mysqli_fetch_array($ex);
		$tab = explode("[",$data['joueurs']);
		foreach($tab as $compteur => $chaine){
			if($compteur > 0){ //on ne prends pas le premier carcatere qui est un delimiteur
				$valeurs = explode(",",$chaine);
				?> 
				<tr>
				<td><?php echo imageClassement($compteur) ; ?></td>
				<td><a href="joueur.php?id=<?php echo $valeurs[0];?>"><?php echo $valeurs[0]; ?></a></td>
				<td><?php echo $valeurs[1]; ?></td>
				<td><?php echo $valeurs[2]; ?></td>
				<td><?php echo alliance($valeurs[3]); ?></td>
				<td><?php echo $valeurs[4];?></td>
				<td><?php echo $valeurs[5];?></td>
				<td><?php echo $valeurs[6];?></td>
				<td><?php echo $valeurs[7];?></td>
				</tr>
			<?php
			}
		}
		?>
		</tbody>
		</table>
		<?php	
	}
	elseif (isset($_GET['sub']) AND mysqli_real_escape_string($base,stripslashes(antihtml($_GET['sub']))) == 1){
		
		?>
		<table class="table table-striped table-bordered">
		<thead>
		<tr>
		<th><img src="images/classement/up.png" alt="up" title="Classement" class="imageSousMenu"/><br/><span class="labelClassement">Rang</span></th>
        <th><img src="images/classement/post-it.png" alt="post" class="imageSousMenu"/><br/><span class="labelClassement">TAG</span></th>
        <th><img src="images/classement/alliance.png" alt="alliance" title="Nombre de joueurs" class="imageSousMenu"/><br/><span class="labelClassement">Membres</span></th>
        <th><a href="historique.php?sub=1"><img src="images/classement/points.png" alt="points" title="Points totaux" class="imageSousMenu"/><br/><span class="labelClassement">Points</span></a></th>
        <th><img src="images/classement/sum-sign.png" alt="post" class="imageSousMenu"/><br/><span class="labelClassement">Moyenne</span></th>
        <th><a href="historique.php?sub=1&clas=1"><img src="images/classement/museum.png" alt="pointCs" title="Points de construction" class="imageSousMenu"/><br/><span class="labelClassement">Constructions</span></a></th>
        <th><a href="historique.php?sub=1&clas=2"><img src="images/classement/sword.png" alt="att" title="Attaque" class="imageSousMenu"/><br/><span class="labelClassement">Attaque</span></a></th>
        <th><a href="historique.php?sub=1&clas=3"><img src="images/classement/shield.png" alt="def" title="Défense" class="imageSousMenu"/><br/><span class="labelClassement">Défense</span></a></th>
        <th><a href="historique.php?sub=1&clas=4"><img src="images/classement/bag.png" alt="bag" title="Pillage" class="imageSousMenu"/><br/><span class="labelClassement">Pillage</span></a></th>
        <th><a href="historique.php?sub=1&clas=5"><img src="images/classement/victoires.png" alt="bag" title="Points de victoire" class="imageSousMenu"/><br/><span class="labelClassement">Victoire</span></a></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$ex = mysqli_query($base,'SELECT * FROM parties WHERE id=\''.$_POST['numeropartie'].'\'');
		$data = mysqli_fetch_array($ex);
		$tab = explode("[",$data['alliances']);

		foreach($tab as $compteur => $chaine){
			if($compteur > 0){ //on ne prends pas le premier carcatere qui est un delimiteur
				$valeurs = explode(",",$chaine);
				?> 
				<tr>
				<td><?php echo imageClassement($compteur) ; ?></td>
				<td><?php echo alliance($valeurs[0]); ?></td>
				<td><?php echo $valeurs[1]; ?></td>
				<td><?php echo $valeurs[2]; ?></td>
				<td><?php echo $valeurs[3]; ?></td>
				<td><?php echo $valeurs[4]; ?></td>
				<td><?php echo $valeurs[5]; ?></td>
				<td><?php echo $valeurs[6]; ?></td>
				<td><?php echo $valeurs[7]; ?></td>
				<td><?php echo $valeurs[8]; ?></td>
				</tr> 
				<?php
			}
		}
		?>
		</tbody>
		</table>
		<?php	
	}
	else {
		?>
		<table class="table table-striped table-bordered">
		<thead>
		<tr>
		<th><img src="images/classement/up.png" alt="up" title="Classement" class="imageSousMenu"/><br/><span class="labelClassement">Rang</span></th>
        <th><img src="images/classement/adversaires.png" alt="adversaires" title="Adversaires" class="imageSousMenu"/><br/><span class="labelClassement">Adversaires</span></th>
        <th><img src="images/classement/morts.png" alt="morts" title="Nombre de molécules perdues" class="imageSousMenu"/><br/><span class="labelClassement">Pertes</span></th>
        <th><img src="images/classement/calendrier.png" alt="calendrier" title="Durée (jours)" class="imageSousMenu"/><br/><span class="labelClassement">Durée</span></th>
        <th><img src="images/classement/copy.png" alt="copy" class="imageSousMenu"/><br/><span class="labelClassement">Détails</span></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$ex = mysqli_query($base,'SELECT * FROM parties WHERE id=\''.$_POST['numeropartie'].'\'');
		$data = mysqli_fetch_array($ex);
		$tab = explode("[",$data['guerres']);
		foreach($tab as $compteur => $chaine){
			if($compteur > 0){ //on ne prends pas le premier carcatere qui est un delimiteur
				$valeurs = explode(",",$chaine);
				?> 
				<tr>
				<td><?php echo imageClassement($compteur) ; ?></td>
				<td><?php echo $valeurs[0]; ?></td>
				<td><?php echo $valeurs[1]; ?></td>
				<td><?php echo $valeurs[2]; ?></td>
				<td><?php echo '<a href="guerre.php?id='.$valeurs[3].'" class="lienVisible"><img src="images/classement/details.png" alt="details" title="Détails"/></a>';?></td>
				</tr> 
				<?php
			}
		}
		?>
		</tbody>
		</table>
		<?php
	}
}
else {
	debutContent();
    echo 'Aucune partie séléctionnée<br/><br/>';
    finContent();
}

finCarte();
include("includes/copyright.php"); ?>