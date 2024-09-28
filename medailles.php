<?php 
include("includes/basicprivatephp.php");
include("includes/tout.php");

if((isset($_GET['login']) AND !empty($_GET['login'])) OR isset($_SESSION['login']))  {
	if(isset($_GET['login']) AND $_GET['login'] != $_SESSION['login']) {
		$_GET['login'] = mysqli_real_escape_string($base,stripslashes(antihtml($_GET['login'])));
		$joueur = $_GET['login'];
	}
	else {
		$joueur = $_SESSION['login'];
	}
	
	$ex = mysqli_query($base,'SELECT count(*) AS ok FROM membre WHERE login=\''.$joueur.'\'');
	$donnees = mysqli_fetch_array($ex);
	
	if($donnees['ok'] == 1) {
        
        $ex = mysqli_query($base,'SELECT nbattaques FROM autre WHERE login=\''.$joueur.'\'');
		$donnees = mysqli_fetch_array($ex);
        
        $ex = mysqli_query($base,'SELECT count(*) AS nbmessages FROM reponses WHERE auteur=\''.$joueur.'\'');
		$donnees1 = mysqli_fetch_array($ex);
        
        $ex = mysqli_query($base,'SELECT * FROM autre WHERE login=\''.$joueur.'\'');
		$donnees2 = mysqli_fetch_array($ex);
        
        $ex = mysqli_query($base,'SELECT energieDepensee FROM autre WHERE login=\''.$joueur.'\'');
		$donnees3 = mysqli_fetch_array($ex);
        
        $ex = mysqli_query($base,'SELECT * FROM constructions WHERE login=\''.$joueur.'\'');
		$donnees4 = mysqli_fetch_array($ex);
        $plusHaut = batMax($donnees4['login'],$nomsRes,$nbRes);
        
        $ex = mysqli_query($base,'SELECT troll FROM membre WHERE login=\''.$joueur.'\'');
		$troll = mysqli_fetch_array($ex);
        
        $ex = mysqli_query($base,'SELECT bombe FROM autre WHERE login=\''.$joueur.'\'');
		$bombe = mysqli_fetch_array($ex);
            
	    debutCarte("Médailles");
        debutListe();
            $listeMedailles = [['Terreur',$donnees['nbattaques'],$paliersTerreur,'Attaques','% de diminution du coût d\'attaque',$bonusMedailles],
                               ['Attaquant',floor($donnees2['pointsAttaque']),$paliersAttaque,'Points d\'attaque','% d\'attaque supplémentaire',$bonusMedailles],
                               ['Defenseur',floor($donnees2['pointsDefense']),$paliersDefense,'Points de défense','% de défense supplémentaire',$bonusMedailles],
                               ['Pilleur',floor($donnees2['ressourcesPillees']),$paliersPillage,'Ressources pillées','% de pillage supplémentaire',$bonusMedailles],
                               ['Pertes',floor($donnees2['moleculesPerdues']),$paliersPertes,'Pertes','% de stabilisation des molécules',$bonusMedailles],
                               ['Energievore',$donnees3['energieDepensee'],$paliersEnergievore,'Energie dépensée','% de production d\'énergie',$bonusMedailles],
                               ['Constructeur',$plusHaut,$paliersConstructeur,'Niveau de bâtiment','% de réduction du coût des bâtiments',$bonusMedailles],
                               ['Pipelette',$donnees1['nbmessages'],$paliersPipelette,'Messages sur le forum','',$bonusForum],
                               ['Explosif',$bombe['bombe'],$paliersBombe,'Jeu de la bombe','',$bonusTroll],
                               ['Aléatoire',$troll['troll'],$paliersTroll,'Aléatoire','',$bonusTroll]];
            foreach($listeMedailles as $nbMedaille => $infos){
                
                $nomMedaille = $infos[0];
                $bonus = $infos[5];
                $medaille = false;
                $progression = $infos[1].'/'.$infos[2][0]; // pas de médaille
                $imageMedaille = '<img alt="vide" src="images/classement/vide.png" />';
                $bonusActuel = "Aucun bonus";
                $bonusSuivant = '<img alt="medaille" class="imageAide" src="images/classement/'.$imagesMedailles[0].'"/> <strong>'.$paliersMedailles[0].'</strong> : '.$bonus[0].$infos[4];
                $objetProgression = '<span class="important">'.$infos[3].'</span>';
            
                foreach($paliersMedailles as $num => $palier){
                    if($infos[1] >= $infos[2][$num]){
                        $medaille = $num;
                        
                        $bonusActuel = '<img alt="medaille" class="imageAide" src="images/classement/'.$imagesMedailles[$num].'"/> <strong>'.$paliersMedailles[$num].'</strong> : '.$bonus[$num].$infos[4];
                        
                        if($num+1 < sizeof($infos[2])){
                            $progression = $infos[1].'/'.$infos[2][$num+1];
                            $bonusSuivant = '<img alt="medaille" class="imageAide" src="images/classement/'.$imagesMedailles[$num+1].'"/> <strong>'.$paliersMedailles[$num+1].'</strong> : '.$bonus[$num+1].$infos[4];
                        }
                        else { // gestion du diamant rouge
                            $progression = $infos[1].' - Niveau maximal';
                            $bonusSuivant = '<strong>Niveau maximal</strong>';
                        }
                        
                        $imageMedaille = '<img alt="medaille" style="vertical-align:middle;width:40px;height:40px;" src="images/classement/'.$imagesMedailles[$num].'" />';
                    }
                }
                
                item(['accordion' => important('Bonus actuel').$bonusActuel.'<br/><br/>'.important('Bonus au prochain niveau').$bonusSuivant, 'titre' => $objetProgression, 'media' => $imageMedaille,'soustitre' => $progression]);
            }
        finListe();
        finCarte();
	}
	else {
        debutCarte("Amusant");
        debutContent();
		echo 'A un moment faut s\'arréter de jouer avec la barre URL.';
        finContent();
        finCarte("Mais pas suffisant.");
	}
	
}

include("includes/copyright.php"); ?>
