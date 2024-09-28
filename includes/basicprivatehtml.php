<?php 

$exMess = query('SELECT count(*) as nbMess FROM messages WHERE expeditaire=\''.$_SESSION['login'].'\'');
$nbMess = mysqli_fetch_array($exMess);

$exClasses = query('SELECT count(*) as nbClasses FROM molecules WHERE proprietaire=\''.$_SESSION['login'].'\' AND formule!=\'Vide\'');
$nbClassesTuto = mysqli_fetch_array($exClasses);

$listeMissions = [
    ['titre' => 'Changer la description', 'contenu' => 'Pour vous démarquer des autres joueurs, changez la description publique de votre profil dans <strong>Mon compte</strong>','atomes' => 30,'resultat' => $autre['description'] != "Pas de description",'icone' => '<img alt="des" src="images/missions/description.png" class="w32"/>'],
    
    ['titre' => 'Producteur niveau 3', 'contenu' => 'Il vous faut plus d\'atomes pour plus de molécules ! ','energie' => 50,'resultat' => $constructions['producteur'] >= 3,'icone' => '<img alt="des" src="images/batiments/producteur.png" class="w32"/>'],
    
    ['titre' => 'Envoyer un message', 'contenu' => 'Vous pouvez communiquer avec les autres joueurs à l\'aide de la messagerie. Dans <strong>Messages</strong>, envoyez un message à un autre joueur !','atomes' => 35,'resultat' => $nbMess['nbMess'] > 0,'icone' => '<img alt="des" src="images/missions/message.png" class="w32"/>'],
    
    ['titre' => 'Générateur niveau 3', 'contenu' => 'Il va falloir produire de l\'énergie rapidement si vous voulez pouvoir attaquer vos ennemis, améliorez donc le générateur','energie' => 50, 'atomes'=>20, 'resultat' => $constructions['generateur'] >= 3,'icone' => '<img alt="des" src="images/batiments/generateur.png" class="w32"/>'],
    
    ['titre' => 'Découvrez les médailles', 'contenu' => 'Les médailles sont des bonus permanents que vous obtenez en franchissant des paliers : par exemple, pour un certain nombre d\'attaques, vous aurez la médaille d\'attaquant bronze qui donne une réduction du coût de l\'attaque. Il existe de nombreux seuils, certains disent que le seuil ultime serait celui du <span style="color:red;text-style:italic">diamant rouge</span>...<br/><br/>Rendez-vous sur la page des <strong>Médailles</strong>','energie' => 100, 'atomes'=>100, 'resultat' => in_array("medailles.php",explode("/",$_SERVER['PHP_SELF'])),'icone' => '<img alt="des" src="images/menu/medailles.png" class="w32"/>'],
    
    ['titre' => 'Stabilisateur niveau 3', 'contenu' => 'Le stabilsateur améliore la stabilité de vos molécules, votre armée se détruira moins rapidement et cela vous évitera de mauvaises surprises au beau matin.','energie' => 150,'resultat' => $constructions['stabilisateur'] >= 3,'icone' => '<img alt="des" src="images/batiments/stabilisateur.png" class="w32"/>'],
    
    ['titre' => 'Ionisateur niveau 5', 'contenu' => 'L\'ionisateur sera d\'une aide précieuse pour détruire les molécules adverses, en leur donnant de l\'attaque supplémentaire.','oxygene' => 200,'resultat' => $constructions['ionisateur'] >= 5,'icone' => '<img alt="des" src="images/batiments/ionisateur.png" class="w32"/>'],
    
    ['titre' => 'Découvrez le marché', 'contenu' => 'Le marché permet de vendre et d\'acheter des atomes contre de l\'énergie. Faites attention, les cours d\'échanges sont variables !. Rendez-vous sur la page du <strong>Marché</strong>','energie' => 120, 'atomes'=>120, 'resultat' => in_array("marche.php",explode("/",$_SERVER['PHP_SELF'])),'icone' => '<img alt="des" src="images/menu/marche.png" class="w32"/>'],
    
    ['titre' => 'Lieur niveau 7', 'contenu' => 'Le lieur va vous permettre de limiter grandement le temps de formation de vos molécules, il est primordial pour pouvoir se remettre sur pied rapidement après une défaite.','azote' => 600,'resultat' => $constructions['lieur'] >= 7,'icone' => '<img alt="des" src="images/batiments/lieur.png" class="w32"/>'],
    
    ['titre' => 'Attaque', 'contenu' => 'Il est temps de passer à l\'action ! Attaquez un ennemi ! ','atomes'=>200, 'resultat' => $autre['nbattaques'] > 0,'icone' => '<img alt="des" src="images/menu/attaquer.png" class="w32"/>'],
    
    ['titre' => 'Pillage', 'contenu' => 'Le pillage est une bonne manière de gagner des ressources sur le dos des autres pour progresser, pillez donc au moins 2000 atomes.','soufre'=>600, 'resultat' => $autre['ressourcesPillees'] >= 2000,'icone' => '<img alt="des" src="images/molecule/bag.png" class="w32"/>'],
    
    ['titre' => 'Condenseur niveau 10', 'contenu' => 'Le condenseur permet d\'améliorer les effets de chacun des atomes, il est très utile lorsque la partie commence à devenir longue... ','energie'=>1000, 'atomes' => 500, 'resultat' => $constructions['condenseur'] >= 10,'icone' => '<img alt="des" src="images/batiments/condenseur.png" class="w32"/>'],
    
    ['titre' => 'Ecrivez sur le forum', 'contenu' => 'Le forum est un lieu d\'échange avec la communauté des autres joueurs. Vous pouvez y poster vos suggestions, reporter les bugs ou vous présenter voir jouer à des jeux !  <strong>Ecrivez un message</strong> sur le forum','atomes'=>600, 'resultat' => $autre['nbMessages'] > 0, 'icone' => '<img alt="des" src="images/menu/forum.png" class="w32"/>'],
    
    ['titre' => 'Champ de force niveau 10', 'contenu' => 'Le champ de force protège toutes les constructions de niveau inférieur contre les attaques ennemies, augmentez le afin d\'éviter de mauvaises surprises à base d\'hydrogène ennemi.','carbon' => 800,'resultat' => $constructions['champdeforce'] >= 10,'icone' => '<img alt="des" src="images/batiments/champdeforce.png" class="w32"/>'],
    
    ['titre' => 'Stockage niveau 10', 'contenu' => 'Pour pouvoir stocker plus de ressources et avoir de la réserve en cas de défaite, il vous faut un bon stockage.','energie' => floor(placeDepot(10)/2), 'atomes'=>floor(placeDepot(10)/3), 'resultat' => $constructions['depot'] >= 10,'icone' => '<img alt="des" src="images/batiments/depot.png" class="w32"/>'],
    
    ['titre' => 'Producteur niveau 15', 'contenu' => 'Avec ça, les points d\'attaque seront faciles !','tout' => 700,'resultat' => $constructions['producteur'] >= 15,'icone' => '<img alt="des" src="images/batiments/producteur.png" class="w32"/>'],
    
    ['titre' => 'Deuxième classe', 'contenu' => 'Pour avoir une armée plus diversifiée, il faut une classe de molécules supplémentaire, utilisez-en une pour la production d\'énergie et une autre pour la défense par exemple (la défense en première ligne) !','energie' => 1500, 'iode' => 400,'resultat' => $nbClassesTuto['nbClasses'] >= 2,'icone' => '<img alt="des" src="images/missions/molecules.png" class="w32"/>'],
    
    ['titre' => '100 points', 'contenu' => 'Obtenez un total de cent points pour débloquer la récompense.','tout' => 3000,'resultat' => $autre['totalPoints'] >= 100,'icone' => '<img alt="des" src="images/points.png" class="w32"/>'],
    
    ['titre' => '50 points d\'attaque', 'contenu' => 'Attaquez d\'autres joueurs et infligez lui des pertes pour obtenir des points d\'attaque','oxygene' => 2000,'resultat' => $autre['pointsAttaque'] >= 50,'icone' => '<img alt="des" src="images/molecule/sword.png" class="w32"/>'],
    
    ['titre' => 'Toutes les classes', 'contenu' => 'Utilisez toutes les classes pour enfin avoir une armée digne de ce nom !','energie' => 30000, 'tout' => 15000,'resultat' => $nbClassesTuto['nbClasses'] >= 4,'icone' => '<img alt="des" src="images/missions/molecules.png" class="w32"/>']
    ];

$sql1 = 'SELECT * FROM ressources WHERE login=\''.htmlentities(mysqli_real_escape_string($base,stripslashes($_SESSION['login']))).'\'';
$req1 = mysqli_query($base,$sql1);
$ressources = mysqli_fetch_array($req1);

$sql2 = 'SELECT nombre FROM molecules WHERE proprietaire=\''.$_SESSION['login'].'\' AND nombre!=0';
$req2 = mysqli_query($base,$sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
$nb_moleculesJoueur = 0;
while($moleculesJoueur = mysqli_fetch_array($req2)) {
	$nb_moleculesJoueur = $nb_moleculesJoueur + $moleculesJoueur['nombre'];
}

$requete = mysqli_query($base,'SELECT * FROM constructions WHERE login=\''.$_SESSION['login'].'\'');
$depot = mysqli_fetch_array($requete);
$ressourcesMax = chiffrePetit(placeDepot($depot['depot'])); 

// gestion de la validation des missions du tutoriel

if(isset($_GET['suivreTuto']) and $autre['niveaututo'] == 1){ // si on a commencé le tuto on passe à l'autre niveau
	mysqli_query($base,'UPDATE autre SET niveaututo=2 WHERE login=\''.$_SESSION['login'].'\'');
}

if($autre['niveaututo'] == 2 and in_array("classement.php",explode("/",$_SERVER['PHP_SELF']))){
	mysqli_query($base, 'UPDATE autre SET niveaututo=3 WHERE login=\''.$_SESSION['login'].'\'');
	mysqli_query($base, 'UPDATE ressources SET energie=\''.($ressources['energie']+50).'\' WHERE login=\''.$_SESSION['login'].'\'');
    
    $information = "Bien, voyons la suite !";
    echo '<script>document.location.href="classement.php?sub=0&deployer=true&information='.$information.'";</script>';
    
}

if($autre['niveaututo'] == 3 and $depot['producteur'] >= 2){
	mysqli_query($base, 'UPDATE autre SET niveaututo=4 WHERE login=\''.$_SESSION['login'].'\'');
    $chaine = '';
    foreach($nomsRes as $num => $res){
        if($num < sizeof($nomsRes)-1){
            $plus = ",";
        }
        else {
            $plus = '';
        }
        $chaine = $chaine.$res.'='.($ressources[$res]+20).$plus;    
    }
    
	mysqli_query($base, 'UPDATE ressources SET '.$chaine.' WHERE login=\''.$_SESSION['login'].'\'');
    
    $information = 'Félicitations pour votre première construction !';
    echo '<script>document.location.href="constructions.php?deployer=true&information='.$information.'";</script>';
}

if($autre['niveaututo'] == 4 and $constructions['pointsProducteur'] != '1;1;1;1;1;1;1;1'){
	mysqli_query($base, 'UPDATE autre SET niveaututo=5 WHERE login=\''.$_SESSION['login'].'\'');
	mysqli_query($base, 'UPDATE ressources SET energie=\''.($ressources['energie']+80).'\' WHERE login=\''.$_SESSION['login'].'\'');
    
    $information = "Bravo, votre production augmente !";
    echo '<script>document.location.href="constructions.php?deployer=true&information='.$information.'";</script>';
    
}

if($autre['niveaututo'] == 5 and in_array("armee.php",explode("/",$_SERVER['PHP_SELF']))){
	mysqli_query($base, 'UPDATE autre SET niveaututo=6 WHERE login=\''.$_SESSION['login'].'\'');
	
    $information = 'Bien !';
    echo '<script>document.location.href="armee.php?deployer=true&information='.$information.'";</script>';
}

$ex = mysqli_query($base,'SELECT * FROM molecules WHERE proprietaire=\''.$_SESSION['login'].'\'');
$numClasse = -1;
$nombre = 0;
while($data = mysqli_fetch_array($ex)){
	if($data['formule'] != "Vide"){
		$numClasse = $data['numeroclasse'];
		$nombre = $data['nombre'];
        
        $nombreAtomes = 0;
        foreach($nomsRes as $num=>$ressource){
            $nombreAtomes+=$data[$ressource];
        }
        
        $aAjouter = round(1000/$nombreAtomes);
	}
}
if($autre['niveaututo'] == 6 and $numClasse!= -1){
	mysqli_query($base, 'UPDATE molecules SET nombre=\''.($nombre+$aAjouter).'\' WHERE proprietaire=\''.$_SESSION['login'].'\' and numeroclasse=\''.$numClasse.'\'');
	mysqli_query($base, 'UPDATE autre SET niveaututo=7 WHERE login=\''.$_SESSION['login'].'\''); 
	mysqli_query($base, 'UPDATE ressources SET energie=\''.($ressources['energie']+50).'\' WHERE login=\''.$_SESSION['login'].'\'');
    
    $information = 'Bravo, votre armée augmente !';
    echo '<script>document.location.href="attaquer.php?deployer=true&information='.$information.'";</script>';
}

if($autre['niveaututo'] == 7 and in_array("joueur.php",explode("/",$_SERVER['PHP_SELF']))){
	mysqli_query($base, 'UPDATE molecules SET nombre=\''.($nombre+$aAjouter).'\' WHERE proprietaire=\''.$_SESSION['login'].'\' and numeroclasse=\''.$numClasse.'\'');
    mysqli_query($base, 'UPDATE autre SET niveaututo=8 WHERE login=\''.$_SESSION['login'].'\''); 
    
    $information = 'Parfait, passons à la suite !';
    echo '<script>document.location.href="alliance.php?deployer=true&information='.$information.'";</script>';
}

$idalliance = query('SELECT idalliance FROM autre WHERE login=\''.$_SESSION['login'].'\'');
$idalliance = mysqli_fetch_array($idalliance);

if($autre['niveaututo'] == 8 and $idalliance['idalliance']!=0){
	augmenterBatiment("champdeforce",$_SESSION['login']);
    mysqli_query($base, 'UPDATE ressources SET energie=\''.($ressources['energie']+50).'\' WHERE login=\''.$_SESSION['login'].'\'');
	mysqli_query($base, 'UPDATE autre SET niveaututo=9 WHERE login=\''.$_SESSION['login'].'\'');
    
    $information = 'Bien joué, vous serez bien plus en sécurité.';
    echo '<script>document.location.href="constructions.php?deployer=true&information='.$information.'";</script>';
}
if($autre['niveaututo'] == 9 and isset($_POST['finir'])){
    
	mysqli_query($base, 'UPDATE autre SET niveaututo=10 WHERE login=\''.$_SESSION['login'].'\'');
}


$ex = mysqli_query($base,'SELECT niveaututo,missions FROM autre WHERE login=\''.$_SESSION['login'].'\'');
$tuto = mysqli_fetch_array($ex);

// VERIFICATION DES MISSIONS
if($tuto['missions'] != ""){ // initialisation du tableau des missions
    $missions = explode(";",$tuto['missions']);
    $c = 0;
    $chaine = '';
    
    foreach($listeMissions as $num => $mission){
        $temp = $missions[$num].';'; // par défaut on ne change pas le statut de la mission
        if($c < 3){ // on vérifie que les trois premires missions non réalisées
            if($missions[$num] == 0){ //si c'est pas fait
                if($mission['resultat']){ // si les conditions sont remplies
                    $information = "Mission ".$mission['titre']." réussie";
                    $temp = '1;'; // mission réussie dans la base de données
                    
                    if(array_key_exists("energie",$mission)){
                         ajouter('energie','ressources',$mission['energie'],$_SESSION['login']);
                    }
                    if(array_key_exists("atomes",$mission)){
                         foreach($nomsRes as $num1 => $res){
                            ajouter($res,'ressources',$mission['atomes'],$_SESSION['login']);
                        }
                    }
                    foreach($nomsRes as $num1 => $res){
                        if(array_key_exists($res,$mission)){
                            ajouter($res,'ressources',$mission[$res],$_SESSION['login']);
                        }
                    }
                    
                }
                $c++;
            }
        }
        $chaine = $chaine.$temp;
    }
    
    query('UPDATE autre SET missions=\''.$chaine.'\' WHERE login=\''.$_SESSION['login'].'\'');
}
else { // si cela n'a pas été initialisé à la première connexion
    $chaine = '';
    foreach($listeMissions as $num){
        $chaine=$chaine."0;";
    }
    query('UPDATE autre SET missions=\''.$chaine.'\' WHERE login=\''.$_SESSION['login'].'\'');
}


?>

<body class="theme-black" style="font-weight:regular">
    <div class="panel-overlay"></div>
  <div class="panel panel-left panel-cover">
    <div style="display:block;width:100%;height:70px;color:white;font-size:20px;background-color: black;box-shadow: 5px 2px 5px 5px rgba(0, 0, 0, 0.2);">
    <br/>
    <p style="display:block;font-family:magmawave_capsbold;margin-top:-20px;">
        <img src="images/profil/<?php echo $autre['image']; ?>" style="max-width:50px;max-height:50px;vertical-align:middle;margin-left:10px;" alt="profil"/> <a style="color:white" href="joueur.php?id=<?php echo $_SESSION['login']; ?>"><?php echo $_SESSION['login']; ?></a>
    </p>
    </div>
    <?php
    include("atomes.php");
    debutContent();
      debutListe();
        item(['media' => '<img src="images/menu/power-button.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Déconnexion', 'link' => 'deconnexion.php', 'style' => 'color:black']);
				// On vérifie si l'utilisateur connecté est un modérateur
				$sql3 = 'SELECT moderateur FROM membre WHERE login=\''.$_SESSION['login'].'\'';
				$ex3 = mysqli_query($base,$sql3) or die ('Erreur SQL !<br />'.$sql3.'<br />'.mysql_error());
				$joueur = mysqli_fetch_array($ex3);
				if($joueur['moderateur']) {
                    item(['media' => '<img src="images/menu/forum.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Modération', 'link' => 'moderationForum.php', 'style' => 'color:red']);
				}
      
      $constructionsPlus = "";
        if($constructions['pointsProducteurRestants'] != 0) {
            $constructionsPlus =  '<span class="badge bg-green" >'.($constructions['pointsProducteurRestants']+$constructions['pointsCondenseurRestants']).'</span>';
        }
    
        item(['media' => '<img src="images/menu/constructions.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Constructions '.$constructionsPlus, 'link' => 'constructions.php', 'style' => 'color:black']);
      
        item(['media' => '<img src="images/menu/armee.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Armée', 'link' => 'armee.php', 'style' => 'color:black']);
        item(['media' => '<img src="images/menu/attaquer.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Carte', 'link' => 'attaquer.php', 'style' => 'color:black']);
        item(['media' => '<img src="images/menu/marche.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Marché', 'link' => 'marche.php', 'style' => 'color:black']);

        $sql = 'SELECT invite FROM invitations WHERE invite=\''.$_SESSION['login'].'\'';
        $ex = mysqli_query($base,$sql);
        $invitations = mysqli_num_rows($ex);
        $alliancePlus = "";

			$ex1 = mysqli_query($base,'SELECT idalliance FROM autre WHERE login=\''.$_SESSION['login'].'\'');
			$alliance = mysqli_fetch_array($ex1);
			if($alliance['idalliance'] == 0 && $invitations != 0) {
				$alliancePlus = '<span class="badge bg-red" >'.$invitations.'</span>'; 
			}
        item(['media' => '<img src="images/menu/alliance.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Equipe '.$alliancePlus, 'link' => 'alliance.php', 'style' => 'color:black']);
        item(['media' => '<img src="images/menu/classement.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Classement', 'link' => 'classement.php?sub=0', 'style' => 'color:black']);
        
      $sql = 'SELECT destinataire FROM messages WHERE destinataire=\''.$_SESSION['login'].'\' AND statut=0';
			$ex = mysqli_query($base,$sql);
            $messagePlus = "";
			$nb_messages_nonlus = mysqli_num_rows($ex);
            if($nb_messages_nonlus != 0) {
                $messagePlus =  '<span class="badge bg-red" >'.$nb_messages_nonlus.'</span>';
            }
        item(['media' => '<img src="images/menu/message.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Messages '.$messagePlus, 'link' => 'messages.php', 'style' => 'color:black']);
      
      $sql = 'SELECT destinataire FROM rapports WHERE destinataire=\''.$_SESSION['login'].'\' AND statut=0';
			$ex = mysqli_query($base,$sql);
            $rapportPlus = "";
			$nb_messages_nonlus = mysqli_num_rows($ex);
            if($nb_messages_nonlus != 0) {
                $rapportPlus =  '<span class="badge bg-red" >'.$nb_messages_nonlus.'</span>';
            }
        item(['media' => '<img src="images/menu/rapports.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Rapports '.$rapportPlus, 'link' => 'rapports.php', 'style' => 'color:black']);
        item(['media' => '<img src="images/menu/compte.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Mon compte', 'link' => 'compte.php', 'style' => 'color:black']);
      
        $ex2 = mysqli_query($base,'SELECT count(*) AS nbSujets FROM sujets WHERE statut = 0');
	   $nbSujets = mysqli_fetch_array($ex2);
		$ex1 = mysqli_query($base,'SELECT count(*) AS nbLus FROM statutforum WHERE login=\''.$_SESSION['login'].'\'');
		$statutForum = mysqli_fetch_array($ex1);
        if($nbSujets['nbSujets'] - $statutForum['nbLus'] > 0) {
            $messagePlus =  '<span class="badge bg-grey" >'.($nbSujets['nbSujets'] - $statutForum['nbLus']).'</span>';
        }
        else {
            $messagePlus = '';
        }
        item(['media' => '<img src="images/menu/forum.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Forum '.$messagePlus, 'link' => 'forum.php', 'style' => 'color:black']);
        item(['media' => '<img src="images/menu/medailles.png" alt="checklist" style="width:25px;height:25px;">', 'titre' => 'Médailles', 'link' => 'medailles.php', 'style' => 'color:black']);

      finListe();
      finContent();
    ?>
    </div>
    
    <?php
    // liste des aides
    
    $listeRessources = '<center>';
    foreach($nomsRes as $num => $ressource) { 
        $listeRessources = $listeRessources.nombreAtome($num,'<span id="affichage'.$ressource.'" style="font-size:12px;">'.chiffrePetit($ressources[$ressource]).'/'.$ressourcesMax.'</span><span style="color:green;margin-left:3px;font-size:10px;">+'.chiffrePetit(revenuAtome($num,$_SESSION['login'])).'/h</span> <span style="color:'.$couleurs[$num].';font-size:10px;font-style:italic;margin-left:10px">'.$utilite[$num].'</span> ').'<br/>';
    } 
    $listeRessources = $listeRessources.'</center>';
    
    echo '
        <div class="popover popover-ressources">
            <div class="popover-angle"></div>
            <div class="popover-inner">
                <div class="content-block-title titreAide">Atomes</div>
                <div class="content-block">
                <p>'.$listeRessources.'</p>
                </div>
            </div>
        </div>';
    
	$bonus = 0;
	$prodMedaille = '';
    foreach($paliersEnergievore as $num => $palier){
        if($autre['energieDepensee'] >= $palier){
            $bonus = $bonusMedailles[$num];
            $prodMedaille = '<tr><td>Médaille '.$paliersMedailles[$num].'<strong> +'.$bonus.'%</td><td style="color:green">'.revenuEnergie($constructions['generateur'],$_SESSION['login'],2).'/h</td></tr>';
        }
    }
    
    $ex = mysqli_query($base,'SELECT idalliance FROM autre WHERE login=\''.$_SESSION['login'].'\'');
	$idalliance = mysqli_fetch_array($ex);
	$bonusDuplicateur = 1;
	if($idalliance['idalliance'] > 0) {
		$ex = mysqli_query($base,'SELECT duplicateur FROM alliances WHERE id=\''.$idalliance['idalliance'].'\'');
		$duplicateur = mysqli_fetch_array($ex);
		$bonusDuplicateur = 1+((0.1*$duplicateur['duplicateur'])/100);
        
        $prodDuplicateur = '<tr><td>Duplicateur <strong>niveau '.$duplicateur['duplicateur'].' (+'.(bonusDuplicateur($duplicateur['duplicateur'])*100).'%)</strong></td><td style="color:green">'.revenuEnergie($constructions['generateur'],$_SESSION['login'],1).'/h</td></tr>';
	}
    else {
        $prodDuplicateur = ''; 
    }
    
    $prodIode = '';
    if((revenuEnergie($constructions['generateur'],$_SESSION['login'],3)-revenuEnergie($constructions['generateur'],$_SESSION['login'],4)) > 0){
        $prodIode = '<tr><td>Molécules <strong style="color:green">+'.(revenuEnergie($constructions['generateur'],$_SESSION['login'],3)-revenuEnergie($constructions['generateur'],$_SESSION['login'],4)).'/h</strong><td style="color:green">'.revenuEnergie($constructions['generateur'],$_SESSION['login'],3).'/h</td></tr>';
    }
    
    $prodBase = '<tr><td>Générateur niveau '.$constructions['generateur'].' <strong style="color:green">+'.revenuEnergie($constructions['generateur'],$_SESSION['login'],4).'/h</strong><td style="color:green">'.revenuEnergie($constructions['generateur'],$_SESSION['login'],4).'/h</td></tr>';
    $prodProducteur = '<tr><td>Producteur niveau '.$constructions['producteur'].' <strong style="color:red">-'.drainageProducteur($constructions['producteur']).'/h</strong><td style="color:green">'.revenuEnergie($constructions['generateur'],$_SESSION['login']).'/h</td></tr>';
    
    // aide des atomes
    
    foreach($nomsRes as $num => $ressource){
        echo '
        <div class="popover popover-'.$ressource.'">
            <div class="popover-angle"></div>
            <div class="popover-inner">
                <div class="content-block-title magma" style="color:'.$couleurs[$num].';text-align:center;font-size:18px;">'.ucfirst($nomsAccents[$num]).'</div>
                <div class="content-block"><hr/>
                '.$aidesAtomes[$num].'
                </div>
            </div>
        </div>
        ';    
    }
    
    echo '
        <div class="popover popover-detailsEnergie">
            <div class="popover-angle"></div>
            <div class="popover-inner">
                <div class="content-block-title titreAide">Production d\'énergie</div>
                <div class="content-block">
                <table>
                '.$prodBase.'
                '.$prodIode.'
                '.$prodMedaille.'
                '.$prodDuplicateur.'
                '.$prodProducteur.'
                <tr><td>Production totale</td><td style="color:green">'.revenuEnergie($constructions['generateur'],$_SESSION['login']).'/h</td></tr>
                </table>
                </div>
            </div>
        </div>';

    $listeAides = ['armee' => 'Votre armée est composée de <strong>molécules</strong>. Vous choisissez les caractéristiques de ces molécules (attaque, défense, vitesse etc...) en indiquant leur composition en atomes. Vous pouvez créer <strong>quatre compositions différentes</strong> aussi appelées classes. Pour en créér une nouvelle et pouvoir former le début de votre armée, appuyez sur <img src="images/plus.png" class="imageAide" alt="plus"/><br/><br/>Une fois la classe créée, vous pouvez former des molécules en l\'indiquant dans la zone prévue à cet effet.<br/><br/>
    <img src="images/danger.png" alt="danger" class="imageAide"/><span style="color:red">La première classe de molécules (n°1) est celle qui prendra en premier les attaques ennemies. Il est donc judicieux d\'en faire une classe <strong>défensive</strong></span>',
                   
    'vueEnsemble' => 'Sur cette page se trouve un résumé de l\'ensemble de votre armée. En cliquant sur le nom (la formule) de la molécule, vous pouvez voir ses statistiques',
                   
    'composition' => 'Vous créez ici le <strong>plan de base</strong> d\'une de vos classes de molécules. Vous pourrez après <strong>créer des molécules ayant la composition que vous allez indiquer</strong>. Plus il y a certains atomes dans cette molécule, plus la caractéristique indiquée sera augmentée.<br/><br/><img src="images/danger.png" class="imageAide"/> <span style="color:red">Plus il y a d\'atomes dans votre molécule, plus elle sera <strong>instable</strong> (elle s\'auto-détruira plus rapidement au cours du temps). </span>',
        
    'demivie' => 'Cela signifie que la moitié des molécules de cette classe seront mortes au bout de ce temps là.',
                   
    'coursEchange' => 'C\'est dans ce tableau que se trouvent <strong>les cours actuels d\'échange</strong> d\'une ressource en une autre. Ces cours <strong>varient dans le temps</strong> en fonction de ce que demandent les joueurs. Il faut lire le tableau à l\'horizontale : 1 '.imageEnergie().' = 0.75 '.image(2).' par exemple.',
                   
    'echangerRessources' => 'Renseignez dans les champs ci-dessous quelle <strong>type de ressource</strong> (énergie, hydrogène, azote, etc...) et <strong>quelle quantité</strong> vous voulez échanger. Il faut aussi donner la sortie, c\'est à dire le type de ressource que vous souhaitez obtenir. La quantité obtenue de cette ressource sera calculée à partir du <strong>tableau des cours d\'échange</strong> plus bas sur cette page.',
                   
    'envoyerRessources' => 'Vous pouvez envoyer des ressources à d\'autres personne pour les aider. Par contre, la quantité reçue par l\'ami ne sera pas celle envoyée car il y a des <strong>pertes en chemin</strong>. Ces pertes sont déterminées par le coefficient de cours d\'envoi çi-dessous',
                   
    'carte' => 'Pour attaquer un ennemi, <strong>cliquez sur un joueur</strong> sur la carte puis cliquez sur <strong>Attaquer</strong> en dessous de son profil.<br/><br/>Vos troupes ne peuvent se déplacer que de joueurs en joueurs sur la carte et ne <strong>peuvent pas se déplacer à des emplacements innocupés</strong>. Une fois l\'attaque terminée, vos molécules rentreront automatiquement chez vous.',
    
    'bbcode' => 'Le <strong>BBcode</strong> permet de <strong>styliser</strong> votre texte. Il suffit d\'écrire son texte entre deux balises spécifiques pour que sa forme change (gras, souligné, couleur)<br/><br/>
    [b]Gras[/b] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> <strong>Gras</strong><br/>
    [u]Souligné[/u] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> <span style="text-decoration:underline">Souligné</span><br/>
    [i]Italique[/i] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> <span style="font-style:italic">Italique</span><br/>
    Texte[sup]Puissance[/sup] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> Texte<sup>Puissance</sup><br/>
    Texte[sub]Indice[/sub] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> Texte<sub>Indice</sub><br/>
    [center]Texte centré[/center] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> <center>Texte centré</center><br/>[color=#cc0000]
    <nobr>[title]Titre[/title] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> <span style="font-size: 25px;font-weight:bold">Titre</span></nobr><br/>
    [color=red]Italique[/color] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> <span style="color:red">Rouge</span><br/>
    [color=blue]Italique[/color] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> <span style="color:blue">Bleu</span><br/>
    [joueur=Guortates/] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> '.joueur("Guortates").'<br/>
    [alliance=Equipe/] <img alt="fleche" src="images/attaquer/arrow.png" style="vertical-align:middle" class="w16"/> '.alliance("Equipe").'<br/>
    ',
    
    'neutrinos' => 'Les neutrinos permettent à la fois <strong>d\'espionner vos ennemis</strong> et le <strong>contre-espionnage</strong> au sein de votre armée. Ainsi, les neutrinos ennemis pourront être repérés et tués quand ils tenteront de vous espionner.'
    ];

    // génération des popover

    foreach($listeAides as $num => $contenu){
        echo '
        <div class="popover popover-'.$num.'">
            <div class="popover-angle"></div>
            <div class="popover-inner">
                <div class="content-block-title titreAide">Aide</div>
                <div class="content-block">
                <p>'.$contenu.'</p>
                </div>
            </div>
        </div>';
    }
    ?>

<script> // affichage des variables en temps reel
// voir temps de début pour passer au bon moment
// faire les autres ressources
// molecules aussi


var revenuJSEnergie=<?php echo revenuEnergie($constructions['generateur'],$_SESSION['login'])/3600;?> //incrementer tous ces nombres de secondes
var valeur = <?php echo $ressources['energie']; ?>;
function energieDynamique(){
	document.getElementById("affichageenergie").innerHTML = nFormatter(Math.floor(valeur))+'/<?php echo $ressourcesMax; ?>';
	if(valeur+revenuJSEnergie < <?php echo placeDepot($constructions['depot']); ?>){
		valeur = valeur+revenuJSEnergie;
	}
	else {
		valeur = <?php echo placeDepot($constructions['depot']); ?>;
	}
}

<?php
foreach($nomsRes as $num => $ressource) { 
	echo'
	var revenuJS'.$ressource.'='.(revenuAtome($num,$_SESSION['login'])/3600).'
	var valeur'.$ressource.' = '.$ressources[$ressource].'
	function '.$ressource.'Dynamique(){
		document.getElementById("affichage'.$ressource.'").innerHTML = nFormatter(Math.floor(valeur'.$ressource.'))+\'/'.$ressourcesMax.'\'
		if(valeur'.$ressource.'+revenuJS'.$ressource.' < '.placeDepot($depot['depot']).'){
			valeur'.$ressource.' = valeur'.$ressource.'+revenuJS'.$ressource.'
		}
		else {
			valeur'.$ressource.' = '.placeDepot($constructions['depot']).'
		}
	}
	setInterval('.$ressource.'Dynamique, 1000);';
}
?>
setInterval(energieDynamique, 1000);
</script>

