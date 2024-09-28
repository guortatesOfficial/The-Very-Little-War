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


if(isset($_GET['clas'])) {
	$_GET['clas'] = mysqli_real_escape_string($base,stripslashes(antihtml($_GET['clas'])));
}

if(isset($_GET['clas'])) {
    switch($_GET['clas']) {
        case 0:
            $order = 'batmax';
            break;
        case 1:
            $order = 'victoires';
            break;
        case 2:
            $order = 'pointsAttaque';
            break;
        case 3:
            $order = 'pointsDefense';
            break;
        case 4:
            $order = 'ressourcesPillees';
            break;
        case 5:
            $order = 'points';
            break;
        default :
            $order = 'totalPoints';
            break;
    }
}			
else {
    $order = 'totalPoints';
}
	
if(isset($_POST['joueurRecherche']) AND !empty($_POST['joueurRecherche'])) {
	$ex = mysqli_query($base,'SELECT count(*) AS joueurExiste FROM autre WHERE login=\''.$_POST['joueurRecherche'].'\'');
	$recherche = mysqli_fetch_array($ex);
	
	if($recherche['joueurExiste'] == 1) {
		$ex = mysqli_query($base,'SELECT * FROM autre ORDER BY '.$order.' DESC');
		$compteur = 1;
		while($donnees = mysqli_fetch_array($ex)) {
			$_POST['joueurRecherche'] = lcfirst($_POST['joueurRecherche']);
			if($donnees['login'] == $_POST['joueurRecherche']) {
				$place = $compteur;
				$pageParDefaut = ceil($place/20);
				
			}
			$_POST['joueurRecherche'] = ucfirst($_POST['joueurRecherche']);
			if($donnees['login'] == $_POST['joueurRecherche']) {
				$place = $compteur;
				$pageParDefaut = ceil($place/20);
				
			}
			$compteur++;
		}
		$pasTrouve = 1;
	}
	else {
		$erreur = "Ce joueur n'existe pas.";
		$pasTrouve = 0;
	}
}
	
include("includes/tout.php");

$_GET['sub'] = mysqli_real_escape_string($base,stripslashes(antihtml($_GET['sub'])));
debutCarte("Classement"); ?>
<div class="table-responsive">
<?php
if(isset($_GET['sub']) AND $_GET['sub'] == 0) {

	$nombreDeJoueursParPage = 20; 
	
		if(isset($_SESSION['login'])) {
			if(isset($pasTrouve) AND $pasTrouve==1) {
			}
			else {
				// recherche de la place du joueur
				$ex = mysqli_query($base,'SELECT * FROM autre ORDER BY '.$order.' DESC');
				$compteur = 1;
				while($donnees = mysqli_fetch_array($ex)) {
					if($donnees['login'] == $_SESSION['login']) {
						$place = $compteur;
						$pageParDefaut = ceil($place/$nombreDeJoueursParPage);
					}
					$compteur++;
				}
			}
		}
		else {
			$pageParDefaut = 1;
		}
		
	
	
	
	$retour = mysqli_query($base,'SELECT COUNT(*) AS nb_joueurs FROM autre');
	$donnees = mysqli_fetch_array($retour);
	$totalDesJoueurs = $donnees['nb_joueurs'];
	$nombreDePages  = ceil($totalDesJoueurs / $nombreDeJoueursParPage); // Calcul du nombre de pages créées
	// Puis on fait une boucle pour écrire les liens vers chacune des pages
	
	if (isset($_GET['page']) AND $_GET['page'] <= $nombreDePages AND $_GET['page'] > 0 AND preg_match("#\d#",$_GET['page']))// Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
	{
        $page = $_GET['page']; // Récuperation du numéro de la page
	}
	else // La variable n'existe pas, c'est la première fois qu'on charge la page
	{
        $page = $pageParDefaut;
	}

	$premierJoueurAafficher = ($page - 1) * $nombreDeJoueursParPage;
    
	$classement = mysqli_query($base,'SELECT * FROM autre ORDER BY '.$order.' DESC LIMIT ' . $premierJoueurAafficher . ', ' . $nombreDeJoueursParPage)or die('Erreur SQL !<br />'.mysql_error());   
	$compteur = $nombreDeJoueursParPage*($page-1)+1;
	
	if(isset($_SESSION['login'])) {
		$exAllianceJoueur = mysqli_query($base,'SELECT idalliance FROM autre WHERE login=\''.$_SESSION['login'].'\'');
		$idalliance = mysqli_fetch_array($exAllianceJoueur);
	
		$exAllianceJoueur = mysqli_query($base,'SELECT * FROM alliances WHERE id=\''.$idalliance['idalliance'].'\'');
		$allianceJoueur = mysqli_fetch_array($exAllianceJoueur);
	}
	?>
	<table class="table table-striped table-bordered">
	<thead>
	<tr>
	<th><img src="images/classement/up.png" alt="up" class="imageSousMenu"/><br/><span class="labelClassement">Rang</span></th>
    <th><img src="images/classement/joueur.png" alt="joueur" title="Joueur" class="imageSousMenu"/><br/><span class="labelClassement">Joueur</span></th>
	<th><a href="classement.php?sub=0"><img src="images/classement/points.png" alt="points" title="Points" class="imageSousMenu"/><br/><span class="labelClassement">Points</span></a></th>
	<th><img src="images/classement/alliance.png" alt="alliance" title="Equipe" class="imageSousMenu"/><br/><span class="labelClassement">Equipe</span></th>
	<th><a href="classement.php?sub=0&clas=5"><img src="images/classement/museum.png" alt="pointCs" title="Points de construction" class="imageSousMenu"/><br/><span class="labelClassement">Constructions</span></a></th>
	<th><a href="classement.php?sub=0&clas=2"><img src="images/classement/sword.png" alt="att" title="Attaque" class="imageSousMenu"/><br/><span class="labelClassement">Attaque</span></a></th>
	<th><a href="classement.php?sub=0&clas=3"><img src="images/classement/shield.png" alt="def" title="Défense" class="imageSousMenu"/><br/><span class="labelClassement">Défense</span></a></th>
	<th><a href="classement.php?sub=0&clas=4"><img src="images/classement/bag.png" alt="bag" title="Pillage" class="imageSousMenu"/><br/><span class="labelClassement">Pillage</span></a></th>
	<th><a href="classement.php?sub=0&clas=1"><img src="images/classement/victoires.png" alt="victoires" title="Points de victoire" class="imageSousMenu"/><br/><span class="labelClassement">Victoire</span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php 
	while($donnees = mysqli_fetch_array($classement)){
		$enGuerre = "#2C2C2C";
		if($donnees['idalliance'] > 0){
			$sql = 'SELECT tag, id FROM alliances WHERE id=\'' . $donnees['idalliance'] . '\'';
			$req = mysqli_query($base,$sql) or die('Erreur SQL !'.$sql.'<br />'.mysql_error());
			$alliance = mysqli_fetch_array($req);
		}
		$sql1 = 'SELECT id FROM membre WHERE login =\'' . addslashes($donnees['login']) . '\'';
		$req1 = mysqli_query($base,$sql1) or die('Erreur SQL !'.$sql1.'<br />'.mysql_error());
		$donnees1 = mysqli_fetch_array($req1);
		
		$sql4 = 'SELECT nombre FROM molecules WHERE proprietaire=\''.$donnees['login'].'\' AND nombre!=0';
		$req4 = mysqli_query($base,$sql4) or die('Erreur SQL !<br />'.$sql4.'<br />'.mysql_error());
		$nb_molecules = 0;
		while($donnees4 = mysqli_fetch_array($req4)) {
			$nb_molecules = $nb_molecules + $donnees4['nombre'];
		}
		$enGuerre = "";
		if(isset($_SESSION['login'])) {
			if($_SESSION['login'] == $donnees['login'] || (isset($_POST['joueurRecherche']) && $_POST['joueurRecherche']==$donnees['login'])) {
					$enGuerre = "160,160,160";
			}
		}
			
		if(isset($_SESSION['login']) AND $donnees['idalliance'] > 0) {
			$exGuerre = mysqli_query($base,'SELECT count(*) AS estEnGuerre FROM declarations WHERE type=0 AND ((alliance1=\''.$alliance['id'].'\' AND alliance2=\''.$allianceJoueur['id'].'\') OR (alliance2=\''.$alliance['id'].'\' AND alliance1=\''.$allianceJoueur['id'].'\')) AND fin=0');
			$guerre = mysqli_fetch_array($exGuerre);
			if($guerre['estEnGuerre'] != 0 AND $donnees['idalliance'] != $idalliance['idalliance'] AND $donnees['idalliance'] != 0) {
				$enGuerre = "254,130,130";
			}
			$exPacte = mysqli_query($base,'SELECT count(*) AS estEnPacte FROM declarations WHERE type=1 AND ((alliance1=\''.$alliance['id'].'\' AND alliance2=\''.$allianceJoueur['id'].'\') OR (alliance2=\''.$alliance['id'].'\' AND alliance1=\''.$allianceJoueur['id'].'\')) AND valide!=0');
			$pacte = mysqli_fetch_array($exPacte);
			if(($pacte['estEnPacte'] != 0 OR $donnees['idalliance'] == $idalliance['idalliance']) AND $donnees['idalliance'] != 0 AND $donnees['login'] != $_SESSION['login']) {
				$enGuerre = "156,255,136";
			}
		}
		?> 
		<tr style="background-color: rgba(<?php if(isset($enGuerre)) { echo $enGuerre.",0.6)"; }?>;">
		<td ><?php echo imageClassement($compteur) ; ?></td>
		<td><?php echo joueur($donnees['login']); ?></td>
		<td><?php echo number_format($donnees['totalPoints'], 0 , ' ', ' '); ?></td>
		<td><?php if($donnees['idalliance'] > 0 ) {  echo alliance($alliance['tag']); } ?></td>
		<td><?php echo chiffrePetit($donnees['points']); ?></td>
		<td><?php echo chiffrePetit(pointsAttaque($donnees['pointsAttaque'])); ?></td>
		<td><?php echo chiffrePetit(pointsDefense($donnees['pointsDefense'])); ?></td>
		<td><?php echo chiffrePetit($donnees['ressourcesPillees']); ?></td>
		<td><?php $victoires = mysqli_fetch_array(mysqli_query($base,'SELECT victoires FROM autre WHERE login=\''.$donnees['login'].'\''));
		echo $victoires['victoires'].' <span style="font-style:italic;font-size:10px">+'.pointsVictoireJoueur($compteur).'</span>'; ?></td>
		</tr>	
		<?php $compteur++; 
	}
	?>
	</tbody>
	</table>
	
	<?php
	$adresse = "classement.php?sub=0&";
        $premier = '';
        if($page > 2){
            $premier = '<a href="'.$adresse.'page=1">1</a>';
        }
        $pointsD = '';
        if($page > 3){
            $pointsD = '...';
        }
        $precedent = '';
        if($page > 1){
            $precedent = '<a href="'.$adresse.'page='.($page-1).'">'.($page-1).'</a>';
        }
        $suivant = '';
        if($page+1 <= $nombreDePages){
            $suivant = '<a href="'.$adresse.'page='.($page+1).'">'.($page+1).'</a>';
        }
        $pointsF = '';
        if($page+3 <= $nombreDePages){
            $pointsF = '...';
        }
        $dernier = '';
        if($page+2 <= $nombreDePages){
            $dernier = '<a href="'.$adresse.'page='.$nombreDePages.'">'.$nombreDePages.'</a>';
        }
        $pages = $premier.' '.$pointsD.' '.$precedent.' <strong>'.$page.'</strong> '.$suivant.' '.$pointsF.' '.$dernier;
	?></p>
	<?php
	if(isset($_SESSION['login'])) { 
	$plus = '';
	if(isset($_GET['clas'])) {
		$plus = '&clas='.$_GET['clas'];
	}
    debutListe();
        echo important("Rechercher");
        item(['floating' => true, 'form' => ['classement.php?sub=0'.$plus, "rechercher"], 'titre' => 'Nom du joueur', 'input' => '<input type="text" name="joueurRecherche" id="joueurRecherche" class="form-control"/>']);
        echo '<br/>'.submit(['form' => 'rechercher', 'titre' => 'Rechercher', 'image' => 'images/boutons/rechercher.png']);
	finListe();
	}
}
elseif (isset($_GET['sub']) AND mysqli_real_escape_string($base,stripslashes(antihtml($_GET['sub']))) == 1){
	$sql = 'SELECT id FROM alliances';
	$req =  mysqli_query($base,$sql) or die('Erreur SQL !'.$sql.'<br />'.mysql_error());
	
	while($donnees = mysqli_fetch_array($req)) {
		$sql1 = 'SELECT * FROM autre WHERE idalliance =\''.$donnees['id'].'\'';
		$req1 =  mysqli_query($base,$sql1) or die('Erreur SQL !'.$sql1.'<br />'.mysql_error());
		$pointstotaux = 0;
        $cTotal = 0;
        $aTotal = 0;
        $dTotal = 0;
        $pTotal = 0;
		while($donnees1 = mysqli_fetch_array($req1)){
			$pointstotaux = $donnees1['totalPoints'] + $pointstotaux;
            $cTotal += $donnees1['points'];
            $aTotal += pointsAttaque($donnees1['pointsAttaque']);
            $dTotal += pointsDefense($donnees1['pointsDefense']);
            $pTotal += $donnees1['ressourcesPillees'];
		}
	
		$sql2 = 'UPDATE alliances SET pointstotaux =\''.$pointstotaux.'\',totalConstructions=\''.$cTotal.'\',totalAttaque=\''.$aTotal.'\', totalDefense=\''.$dTotal.'\', totalPillage=\''.$pTotal.'\' WHERE id =\''.$donnees['id'].'\'';
		$req2 =  mysqli_query($base,$sql2) or die('Erreur SQL !'.$sql2.'<br />'.mysql_error());
	}
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_SESSION['login'])) {
		$exAllianceJoueur = mysqli_query($base,'SELECT idalliance FROM autre WHERE login=\''.$_SESSION['login'].'\'');
		$idalliance = mysqli_fetch_array($exAllianceJoueur);
		
		$exAllianceJoueur = mysqli_query($base,'SELECT * FROM alliances WHERE id=\''.$idalliance['idalliance'].'\'');
		$allianceJoueur = mysqli_fetch_array($exAllianceJoueur);
	}
	else {
		$allianceJoueur['id'] = -1; // pour ne pas avoir d'erreurs et ne pas mettre de if partout
	}
	
	$nombreDeAlliancesParPage = 20; 
	$retour = mysqli_query($base,'SELECT COUNT(*) AS nb_alliances FROM alliances');
	$donnees = mysqli_fetch_array($retour);
	$totalDesAlliances = $donnees['nb_alliances'];
	$nombreDePages  = ceil($totalDesAlliances / $nombreDeAlliancesParPage); // Calcul du nombre de pages créées
	// Puis on fait une boucle pour écrire les liens vers chacune des pages
	
	if (isset($_GET['page']) AND $_GET['page'] <= $nombreDePages AND $_GET['page'] > 0 AND preg_match("#\d#",$_GET['page']))// Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
	{
        $page = $_GET['page']; // Récuperation du numéro de la page
	}
	else // La variable n'existe pas, c'est la première fois qu'on charge la page
	{
        $page = 1; // On se met sur la page 1 (par défaut)
	}
 
	// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
	$premiereAllianceAafficher = ($page - 1) * $nombreDeAlliancesParPage;
    
    if(isset($_GET['clas'])) {
		switch($_GET['clas']) {
			case 1:
				$order = 'totalConstructions';
				break;
            case 2:
				$order = 'totalAttaque';
				break;
            case 3:
				$order = 'totalDefense';
				break;
            case 4:
				$order = 'totalPillage';
				break;
            case 5 :
                $order = 'pointsVictoire';
                break;
			default :
				$order = 'pointstotaux';
				break;
		}
	}			
	else {
		$order = 'pointstotaux';
	}
	
	$classement = mysqli_query($base,'SELECT * FROM alliances ORDER BY '.$order.' DESC LIMIT ' . $premiereAllianceAafficher . ', ' . $nombreDeAlliancesParPage);   
	$compteur = $nombreDeAlliancesParPage*($page-1)+1;
	
	?>
	<table class="table table-striped table-bordered">
	<thead>
	<tr>
	<th><img src="images/classement/up.png" alt="up" title="Classement" class="imageSousMenu"/><br/><span class="labelClassement">Rang</span></th>
	<th><img src="images/classement/post-it.png" alt="post" class="imageSousMenu"/><br/><span class="labelClassement">TAG</span></th>
	<th><img src="images/classement/alliance.png" alt="alliance" title="Nombre de joueurs" class="imageSousMenu"/><br/><span class="labelClassement">Membres</span></th>
    <th><a href="classement.php?sub=1"><img src="images/classement/points.png" alt="points" title="Points totaux" class="imageSousMenu"/><br/><span class="labelClassement">Points</span></a></th>
	<th><img src="images/classement/sum-sign.png" alt="post" class="imageSousMenu"/><br/><span class="labelClassement">Moyenne</span></th>
	<th><a href="classement.php?sub=1&clas=1"><img src="images/classement/museum.png" alt="pointCs" title="Points de construction" class="imageSousMenu"/><br/><span class="labelClassement">Constructions</span></a></th>
	<th><a href="classement.php?sub=1&clas=2"><img src="images/classement/sword.png" alt="att" title="Attaque" class="imageSousMenu"/><br/><span class="labelClassement">Attaque</span></a></th>
	<th><a href="classement.php?sub=1&clas=3"><img src="images/classement/shield.png" alt="def" title="Défense" class="imageSousMenu"/><br/><span class="labelClassement">Défense</span></a></th>
	<th><a href="classement.php?sub=1&clas=4"><img src="images/classement/bag.png" alt="bag" title="Pillage" class="imageSousMenu"/><br/><span class="labelClassement">Pillage</span></a></th>
	<th><a href="classement.php?sub=1&clas=5"><img src="images/classement/victoires.png" alt="bag" title="Points de victoire" class="imageSousMenu"/><br/><span class="labelClassement">Victoire</span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	while($donnees = mysqli_fetch_array($classement)){
		
		$exGuerre = mysqli_query($base,'SELECT count(*) AS estEnGuerre FROM declarations WHERE type=0 AND ((alliance1=\''.$donnees['id'].'\' AND alliance2=\''.$allianceJoueur['id'].'\') OR (alliance2=\''.$donnees['id'].'\' AND alliance1=\''.$allianceJoueur['id'].'\')) AND fin=0');
		$enGuerre = "";
		$guerre = mysqli_fetch_array($exGuerre);
		if($guerre['estEnGuerre'] != 0 AND $donnees['id'] != $idalliance['idalliance']) {
			$enGuerre = "254,130,130";
		}
		$exPacte = mysqli_query($base,'SELECT count(*) AS estEnPacte FROM declarations WHERE type=1 AND ((alliance1=\''.$donnees['id'].'\' AND alliance2=\''.$allianceJoueur['id'].'\') OR (alliance2=\''.$donnees['id'].'\' AND alliance1=\''.$allianceJoueur['id'].'\')) AND valide!=0');
		$pacte = mysqli_fetch_array($exPacte);
		if($pacte['estEnPacte'] != 0 AND $donnees['id'] != $idalliance['idalliance']) {
			$enGuerre = "156,255,136";
		}
		if($donnees['id'] == $allianceJoueur['id']) {
			$enGuerre = "160,160,160";
		}
		$sql1 = 'SELECT login FROM autre WHERE idalliance="'.$donnees['id'].'"';
		$req1 = mysqli_query($base,$sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
		$nbjoueurs = mysqli_num_rows($req1);
		if ($nbjoueurs != 0) { // Pour éviter la division par zéro
			?> 
			<tr style="background-color: rgba(<?php if(isset($enGuerre)) { echo $enGuerre.",0.6)"; }?>">
			<td><?php echo imageClassement($compteur) ; ?></td>
			<td><?php echo alliance($donnees['tag']); ?></td>
			<td><?php echo $nbjoueurs; ?></td>
			<td><?php echo number_format($donnees['pointstotaux'], 0 , ' ', ' ');?></td>
			<td><?php echo number_format(round($donnees['pointstotaux']/$nbjoueurs), 0 , ' ', ' '); ?></td>
			<td><?php echo number_format($donnees['totalConstructions'], 0 , ' ', ' ');?></td>
			<td><?php echo number_format($donnees['totalAttaque'], 0 , ' ', ' ');?></td>
			<td><?php echo number_format($donnees['totalDefense'], 0 , ' ', ' ');?></td>
			<td><?php echo number_format($donnees['totalPillage'], 0 , ' ', ' ');?></td>
            <td><?php echo $donnees['pointsVictoire'].' <span style="font-style:italic;font-size:10px">+'.pointsVictoireAlliance($compteur).'</span></td>'; ?></td>
			</tr> 
			<?php $compteur++;
		}
		else {
			$sql3 = 'DELETE FROM alliances WHERE id=\''.$donnees['id'].'\'';
			$ex3 = mysqli_query($base,$sql3) or die('Erreur SQL !<br />'.$sql3.'<br />'.mysql_error());
	
			$sql5 = 'DELETE FROM invitations WHERE idalliance=\''.$donnees['id'].'\'';
			$ex5 = mysqli_query($base,$sql5) or die('Erreur SQL !<br />'.$sql5.'<br />'.mysql_error());
		}
	}
	?>
	</tbody>
	</table>
	<?php
	$adresse = "classement.php?sub=1&";
        $premier = '';
        if($page > 2){
            $premier = '<a href="'.$adresse.'page=1">1</a>';
        }
        $pointsD = '';
        if($page > 3){
            $pointsD = '...';
        }
        $precedent = '';
        if($page > 1){
            $precedent = '<a href="'.$adresse.'page='.($page-1).'">'.($page-1).'</a>';
        }
        $suivant = '';
        if($page+1 <= $nombreDePages){
            $suivant = '<a href="'.$adresse.'page='.($page+1).'">'.($page+1).'</a>';
        }
        $pointsF = '';
        if($page+3 <= $nombreDePages){
            $pointsF = '...';
        }
        $dernier = '';
        if($page+2 <= $nombreDePages){
            $dernier = '<a href="'.$adresse.'page='.$nombreDePages.'">'.$nombreDePages.'</a>';
        }
        $pages = $premier.' '.$pointsD.' '.$precedent.' <strong>'.$page.'</strong> '.$suivant.' '.$pointsF.' '.$dernier;
}
elseif(isset($_GET['sub']) AND mysqli_real_escape_string($base,stripslashes(antihtml($_GET['sub']))) == 2) {
	$nombreDeGuerresParPage = 20; 
	$retour = mysqli_query($base,'SELECT COUNT(*) AS nb_guerres FROM declarations WHERE pertesTotales!=0 AND type=0 AND fin!= 0');
	$donnees = mysqli_fetch_array($retour);
	$totalDesGuerres = $donnees['nb_guerres'];
	$nombreDePages  = ceil($totalDesGuerres / $nombreDeGuerresParPage); // Calcul du nombre de pages créées
	// Puis on fait une boucle pour écrire les liens vers chacune des pages
	
	if (isset($_GET['page']) AND $_GET['page'] <= $nombreDePages AND $_GET['page'] > 0 AND preg_match("#\d#",$_GET['page']))// Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
	{
        $page = $_GET['page']; // Récuperation du numéro de la page
	}
	else // La variable n'existe pas, c'est la première fois qu'on charge la page
	{
        $page = 1; // On se met sur la page 1 (par défaut)
	}
 
	// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
	$premiereGuerreAafficher = ($page - 1) * $nombreDeGuerresParPage;  
	$compteur = $nombreDeGuerresParPage*($page-1)+1;
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
	$ex = mysqli_query($base,'SELECT id, pertes1, pertes2 FROM declarations WHERE type=0');
	while($alliance = mysqli_fetch_array($ex)) {
		mysqli_query($base,'UPDATE declarations SET pertesTotales=\''.($alliance['pertes1'] + $alliance['pertes2']).'\' WHERE id=\''.$alliance['id'].'\''); 
	}
	$ex = mysqli_query($base,'SELECT * FROM declarations WHERE pertesTotales!=0 AND type=0 AND fin!= 0 ORDER BY pertesTotales DESC LIMIT ' . $premiereGuerreAafficher . ', ' . $nombreDeGuerresParPage.'');
	while ($donnees = mysqli_fetch_array($ex)) {
		$ex1 = mysqli_query($base,'SELECT tag FROM alliances WHERE id=\''.$donnees['alliance1'].'\'');
		$alliance1 = mysqli_fetch_array($ex1);
		
		$ex2 = mysqli_query($base,'SELECT tag FROM alliances WHERE id=\''.$donnees['alliance2'].'\'');
		$alliance2 = mysqli_fetch_array($ex2);
		?>
		<tr>
		<td><?php echo imageClassement($compteur) ; ?></td>
		<td><?php echo alliance($alliance1['tag']); ?> contre <?php echo alliance($alliance2['tag']); ?></td>
		<td><?php echo number_format($donnees['pertesTotales'], 0 , ' ', ' '); ?></td>
		<td><?php echo round(($donnees['fin'] - $donnees['timestamp'])/86400);?></td>
		<td><a href="guerre.php?id=<?php echo $donnees['id']; ?>" class="lienVisible"><img src="images/classement/details.png" alt="details" title="Détails"/></a></td>
		</tr> 
		<?php
		$compteur++;
	}
	?>
	</tbody>
	</table>
	<?php $adresse = "classement.php?sub=2&";
        $premier = '';
        if($page > 2){
            $premier = '<a href="'.$adresse.'page=1">1</a>';
        }
        $pointsD = '';
        if($page > 3){
            $pointsD = '...';
        }
        $precedent = '';
        if($page > 1){
            $precedent = '<a href="'.$adresse.'page='.($page-1).'">'.($page-1).'</a>';
        }
        $suivant = '';
        if($page+1 <= $nombreDePages){
            $suivant = '<a href="'.$adresse.'page='.($page+1).'">'.($page+1).'</a>';
        }
        $pointsF = '';
        if($page+3 <= $nombreDePages){
            $pointsF = '...';
        }
        $dernier = '';
        if($page+2 <= $nombreDePages){
            $dernier = '<a href="'.$adresse.'page='.$nombreDePages.'">'.$nombreDePages.'</a>';
        }
        $pages = $premier.' '.$pointsD.' '.$precedent.' <strong>'.$page.'</strong> '.$suivant.' '.$pointsF.' '.$dernier;
	
}
else {
	$nombreDeForumParPage = 20; 
	$ex = mysqli_query($base,'SELECT count(*) AS nbMembres FROM membre');
	$nbMembres = mysqli_fetch_array($ex);
	$totalDesMembres = $nbMembres['nbMembres'];
	$nombreDePages  = ceil($totalDesMembres / $nombreDeForumParPage); // Calcul du nombre de pages créées
	// Puis on fait une boucle pour écrire les liens vers chacune des pages
	
	if (isset($_GET['page']) AND $_GET['page'] <= $nombreDePages AND $_GET['page'] > 0 AND preg_match("#\d#",$_GET['page']))// Quelques vérifications comme si la variable ne contient qu'une suite de chiffres
	{
        $page = $_GET['page']; // Récuperation du numéro de la page
	}
	else // La variable n'existe pas, c'est la première fois qu'on charge la page
	{
        $page = 1; // On se met sur la page 1 (par défaut)
	}
 
	// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
	$premierForumAafficher = ($page - 1) * $nombreDeForumParPage;  
	$compteur = $nombreDeForumParPage*($page-1)+1;
	?>
	<table class="table table-striped table-bordered">
	<thead>
	<tr>
	<th><img src="images/classement/up.png" alt="up" title="Classement" class="imageSousMenu"/><br/><span class="labelClassement">Rang</span></th>
	<th><img src="images/classement/joueur.png" alt="joueur" title="Joueur" class="imageSousMenu"/><br/><span class="labelClassement">Joueur</span></th>
	<th><a href="classement.php?sub=3"><img src="images/classement/reponses.png" alt="reponses" title="Nombre de réponses" class="imageSousMenu"/><br/><span class="labelClassement">Réponses</span></a></th>
	<th><img src="images/classement/sujets.png" alt="reponses" title="Nombre de sujets" class="imageSousMenu"/><br/><span class="labelClassement">Sujets</span></th>
	<th><a href="classement.php?sub=3&clas=0"><img src="images/classement/bombe.png" alt="bombe" title="Nombre de bombes gagnées" class="imageSousMenu"/><br/><span class="labelClassement">Bombe</span></a></th>
	<th><a href="classement.php?sub=3&clas=1"><img src="images/classement/alea.png" alt="alea" title="Médaille aléatoire" class="imageSousMenu"/><br/><span class="labelClassement">Aléatoire</span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$plus = '';
	if(isset($_GET['clas'])) {
		$plus = '&clas='.$_GET['clas'];
	}

	if(isset($_GET['clas'])) {
		$_GET['clas'] = mysqli_real_escape_string($base,addslashes(antihtml(trim($_GET['clas']))));
		switch($_GET['clas']) {
			case 0:
				$order = 'bombe';
				$table = 'autre';
				break;
			case 1:
				$order = 'troll';
				$table = 'membre';
				break;
			default :
				$order = 'nbMessages';
				$table = 'autre';
				break;
		}
	}			
	else {
		$order = 'nbMessages';
		$table = 'autre';
	}
	
	$ex = mysqli_query($base,'SELECT login FROM '.$table.' ORDER BY '.$order.' DESC LIMIT ' . $premierForumAafficher . ', ' . $nombreDeForumParPage.'');
	while ($donnees = mysqli_fetch_array($ex)) {
		$ex1 = mysqli_query($base,'SELECT nbMessages,bombe FROM autre WHERE login=\''.$donnees['login'].'\'');
		$donnees1 = mysqli_fetch_array($ex1);
		
		$ex2 = mysqli_query($base,'SELECT troll FROM membre WHERE login=\''.$donnees['login'].'\'');
		$troll = mysqli_fetch_array($ex2);
		switch($troll['troll']) {
			case 0 :
				$troll['troll'] = "medaillebronze";
				break;
			case 1 :
				$troll['troll'] = "medailleargent";
				break;
			case 2 :
				$troll['troll'] = "medailleor";
				break;
			case 3:
				$troll['troll'] = "emeraude";
				break;
            case 4:
				$troll['troll'] = "saphir";
				break;
            case 5:
				$troll['troll'] = "rubis";
				break;
            case 6:
				$troll['troll'] = "diamant";
				break;
			default :
				$troll['troll'] = "diamantrouge";
				break;
		}
		
		switch($donnees1['bombe']) {
			case 0 :
				$donnees1['bombe'] = "Rien";
				break;
			case 1 :
				$donnees1['bombe'] = "Bronze";
				break;
			case 2 :
				$donnees1['bombe'] = "Argent";
				break;
			case 3 :
				$donnees1['bombe'] = "Or";
				break;
			case 4:
				$donnees1['bombe'] = "Platine";
				break;
			default :
				$donnees1['bombe'] = "Diamant";
				break;
		}
		
		$ex3 = mysqli_query($base,'SELECT count(*) AS nbSujets FROM sujets WHERE auteur=\''.$donnees['login'].'\'');
		$nbSujets = mysqli_fetch_array($ex3);
		
		$enGuerre = "";
		if(isset($_SESSION['login'])) {
			if($_SESSION['login'] == $donnees['login']) {
					$enGuerre = "160,160,160";
			}
		}
		
		?>
		<tr style="background-color:rgba(<?php echo $enGuerre; ?>,0.6)">
		<td><?php echo imageClassement($compteur) ; ?></td>
		<td><?php echo joueur($donnees['login']); ?></td>
		<td><?php echo $donnees1['nbMessages']; ?></td>
		<td><?php echo $nbSujets['nbSujets']; ?></td>
		<td><img alt="bombe" style="width:40px;height:40px" src="images/medailles/bombe<?php echo $donnees1['bombe'];?>.png"/></td>
		<td><img alt="troll" style="width:40px;height:40px" src="images/classement/<?php echo $troll['troll'];?>.png"/></td>
		</tr> 
		<?php
		$compteur++;
	}
	?>
	</tbody>
	</table>
	<?php $adresse = "classement.php?sub=3&";
        $premier = '';
        if($page > 2){
            $premier = '<a href="'.$adresse.'page=1">1</a>';
        }
        $pointsD = '';
        if($page > 3){
            $pointsD = '...';
        }
        $precedent = '';
        if($page > 1){
            $precedent = '<a href="'.$adresse.'page='.($page-1).'">'.($page-1).'</a>';
        }
        $suivant = '';
        if($page+1 <= $nombreDePages){
            $suivant = '<a href="'.$adresse.'page='.($page+1).'">'.($page+1).'</a>';
        }
        $pointsF = '';
        if($page+3 <= $nombreDePages){
            $pointsF = '...';
        }
        $dernier = '';
        if($page+2 <= $nombreDePages){
            $dernier = '<a href="'.$adresse.'page='.$nombreDePages.'">'.$nombreDePages.'</a>';
        }
        $pages = $premier.' '.$pointsD.' '.$precedent.' <strong>'.$page.'</strong> '.$suivant.' '.$pointsF.' '.$dernier;
	
}
echo '</div>';
finCarte($pages);

include("includes/copyright.php"); ?>