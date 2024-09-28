<?php
include("ressources.php");


$sql= 'SELECT niveaututo FROM autre WHERE login=\''.$_SESSION['login'].'\'';
$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
$niveaututo = mysqli_fetch_array($ex);

$sql1 = 'SELECT * FROM tutoriel WHERE niveau=\''.$niveaututo['niveaututo'].'\'';
$ex1 = mysqli_query($base,$sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
$tutoriel = mysqli_fetch_array($ex1);
 // soit on affiche les missions ou le tutoriel
 //if($niveaututo['niveaututo'] <= 8 and !(isset($_GET['tuto']))) { echo '<br/><form method="post" action="tutoriel.php"><span class="important">Mission n°'.$tutoriel['niveau'].' : '.$tutoriel['titre'].' - 
	//<input type="submit" name="valider" value="Valider" class="bouton"/></span>
	//</form>'; }	

if($tuto['niveaututo'] < 10) { 
	if($tuto['niveaututo'] == 1) { 
        $image = 'images/tutoriel/question.png';
        $titreTuto = 'Tutoriel';
        $contenuTuto = important("Bienvenue sur The Very Little War").'<img alt="carbone" src="images/icone.png" style="height:50px;width:50px;float:left;margin-right:10px">
        Ce tutoriel va vous guider à travers le jeu afin que vous le découvriez. Pour vous motiver, des <strong>bonus</strong> seront distribués au fur et à mesure que les missions données seront complétées. Lorsqu\'une chose est peu claire, n\'hésitez pas à demander sur le <a href="forum.php"><strong>Forum</strong></a>, cliquer sur les <img alt="interro" src="images/question.png" class="imageAide"/> présents en haut de certaines pages ou encore vous rendre dans <a href="tutoriel.php"><strong>Aide</strong></a>.<br/><br/>
        <a href="constructions.php?suivreTuto=true&deployer=true" class="button button-fill button-raised external">Suivre le tutoriel</a>';
        
        $objectif = false;
        $recompense = false;
    }
    elseif($tuto['niveaututo'] == 2) {
        $image = 'images/tutoriel/question.png';
        $titreTuto = 'But du jeu';
        $contenuTuto = 'Le but du jeu est d\'être le joueur à obtenir le plus de points un jour avant la fin du mois. Après quoi, votre position dans le classement vous donnera un certain nombre de points de victoire et une nouvelle partie recommencera un jour plus tard, le 1<sup>er</sup> du mois. Vous trouverez votre nombre de points pour cette partie en cliquant sur le menu en haut à gauche. Pour gagner des points, vous pouver<ul><li><img alt="msusem" src="images/classement/museum.png" class="imageAide"/> <strong>Construire</strong> des bâtiments</li><li><img alt="attaquer" src="images/classement/sword.png" class="imageAide"/> <strong>Attaquer</strong> des ennemis</li><li><img alt="shield" src="images/classement/shield.png" class="imageAide"/> <strong>Défendre</strong> contre vos ennemis</li><li><img alt="msusem" src="images/classement/bag.png" class="imageAide"/> <strong>Piller</strong> les adversaires</li></ul><br/><br/>
        Vous pourrez attaquer les ennemis grâce à votre future armée de molécules ! Essayez d\'explorer et de découvrir le jeu en passant par <strong>le menu en haut à gauche</strong>.<br/><br/>
        Afin de montrer que vous avez bien compris l\'interface du jeu, rendez-vous sur la page <nobr><img src="images/menu/classement.png" alt="checklist" class="imageAide"> <strong>Classement</strong></nobr>. Vous obtiendrez alors votre récompense.';
        
        $objectif = 'Rendez-vous sur le <strong>Classement</strong>';
        $recompense = nombreEnergie(50).'';
    }
    elseif($tuto['niveaututo'] == 3) {
        $image = 'images/tutoriel/atom.png';
        $media = '';
        $texte = "";
        foreach($nomsRes as $num => $ressource){ $texte = $texte.' '.nombreAtome($num,ucfirst($ressource));}
        $titreTuto = 'Les atomes';
        $contenuTuto = 'Les ressources principales du jeu sont les atomes : '.$texte.'<br/><br/>
        La quantité dont vous en disposez peut être trouvée en <strong>haut de ce tutoriel</strong>, en cliquant sur <br/><br/><center>'.chipInfo('Atomes','images/atom.png').'</center><br/><br/>
        Ces atomes sont la brique de base pour vos futures <strong>molécules</strong>. Ce sera avec ces molécules que vous pourrez attaquer et vous défendre contre vos ennemis !<br/><br/> Pour produire des atomes, il faut augmenter le <nobr><img alt="prod" src="images/batiments/producteur.png" class="imageAide2"/> <strong>Producteur</strong></nobr> dans les <nobr><img src="images/menu/constructions.png" alt="checklist" class="imageAide"> <strong>Constructions</strong></nobr><br/><br/>
        Une fois augmenté au niveau supérieur, vous aurez obtenu <strong>'.sizeof($nomsRes).' points de production</strong> qu\'il vous faudra placer.
        ';
        
        $objectif = 'Augmenter le producteur au <span class="important">niveau 2</span>';
        $recompense = coutTout(20);
    }
    elseif($tuto['niveaututo'] == 4) {
        $image = 'images/tutoriel/thunder.png';
        $titreTuto = 'L\'énergie';
        $contenuTuto = 'Il existe une autre ressource que les atomes<br/><br/><br/><center>'.nombreEnergie("Energie").'</center> <br/><br/>Elle est indispensable pour pouvoir <strong>attaquer</strong>, <strong>construire</strong> ou même <strong>acheter</strong> des atomes sur le marché.<br/>Vous pouvez trouver l\'énergie que vous possédez juste au <strong>dessus de ce tutoriel</strong>. Exemple : <br/><br/>
        <center>'.nombreEnergie(floor($ressources['energie']).'/'.$placeDepot.' <span style="color:green;margin-left:10px"> +'.chiffrePetit(revenuEnergie($constructions['generateur'],$_SESSION['login'])).'/h').'</center> <br/>signifie que vous possédez <strong>'.floor($ressources['energie']).'</strong> sur les <strong>'.$placeDepot.'</strong> que vous pouvez posséder au maximum. De plus vous produisez <strong>'.revenuEnergie($constructions['generateur'],$_SESSION['login']).'</strong> d\'énergie par heure.<br/><br/><hr/><br/>
        Il existe deux manières de produire de l\'énergie : augmenter le <nobr><img alt="prod" src="images/batiments/generateur.png" class="imageAide2"/> <strong>Générateur</strong></nobr> dans les constructions ou <strong>créer des molécules</strong>. Pour qu\'une molécule produise de l\'énergie, il faut qu\'elle soit composée <strong style="color:pink">d\'iode</strong>. Pour produire de l\'iode, il faut placer les <strong>points de production</strong> obtenus.<br/><br/>Voici comment faire : 
        <ul><li><img alt="msusem" src="images/batiments/producteur.png" class="imageAide"/> <strong>Cliquez</strong> sur le Producteur dans les <strong>Constructions</strong>.</li>
        <li><img alt="plus" src="images/add.png" class="imageAide"/> <strong>Cliquez</strong> sur <img alt="plus" src="images/add.png" class="imageAide"/> à côté de l\'atome dont vous voulez aumenter la production (<img style="vertical-align:middle;width:20px;height:20px;" alt="Energie" src="images/iode.png" alt="iode" title="Iode" /> est l\'iode)</li> 
        <li>- <strong>Répétez</strong> cette action <strong>'.(sizeof($nomsRes)-1).' fois</strong> (sur différents atomes).</li>
        <li><img alt="validate" src="images/yes.png" class="imageAide"/> <strong>Sauvegardez</strong> ensuite cette production en cliquant sur <img alt="validate" src="images/yes.png" class="imageAide"/></li></ul>
        ';
        
        $objectif = 'Placez vos <strong>Points de production</strong>';
        $recompense = nombreEnergie(80);
    }
     elseif($tuto['niveaututo'] == 5) {
        $image = 'images/tutoriel/molecules.png';
        $texte = "";
        foreach($nomsRes as $num => $ressource){ $texte = $texte.' '.nombreAtome($num,'<span style="color:'.$couleurs[$num].';font-size:12px;">'.ucfirst($ressource).'</span><span style="color:'.$couleurs[$num].';font-size:10px;font-style:italic;margin-left:10px">'.$utilite[$num].'</span>');} 
        $titreTuto = 'L\'armée';
        $contenuTuto = 'Les atomes sont la brique de base pour vos <strong>molécules</strong>. Ces molécules, comme des <strong>soldats</strong> permettront d\'attaquer ou de vous défendre contre d\'autres joueurs.<br/><br/>
        La particularité de The Very Little War est que <strong>vous choisissez les caractéristiques de vos soldats (ou molécules)</strong>. La <strong>composition</strong> de votre molécule en atomes déterminera ses caractéristiques au combat. Chaque atome possède sa caractéristique propre : '.$texte.'<br/><br/>
        Pour créer votre molécule, cliquez sur <nobr><img alt="c" src="images/menu/armee.png" class="imageAide"/> <strong>Armée</strong></nobr> dans le menu.';
         
        $objectif = 'Aller sur la page <strong>Armée</strong>';
        $recompense = 'Ma gratitude';
    }
    elseif($tuto['niveaututo'] == 6) {
        $image = 'images/tutoriel/molecules.png';
        $titreTuto = 'L\'armée';
        $contenuTuto = 'Sur cette page se trouvent <strong>quatre lignes</strong> numérotées. Ce sont <strong>des futures classes de molécule</strong>. Une classe est un <strong>modèle de molécule</strong>, elle vous permet ainsi de faire plusieurs molécules possédant les mêmes caractéristiques.<br/><br/>
        Pour créer une classe appuyez sur un des <img class="imageAide" alt="plus" src="images/plus.png"/><br/>
        Renseignez ensuite la composition de votre classe en terme d\'atomes. Plus il y d\'atomes d\'un type, plus cela améliorera la caractéristique indiquée. Vous pouvez mettre <strong>200 atomes</strong> d\'un type au maximum.<br/><br/>
        <strong style="color:red">Attention, plus il y a d\'atomes dans votre molécules, moins elle sera stable (elle s\'auto-détruira plus rapidement).</strong>';
        
        $objectif = 'Créer une classe de molécule';
        $recompense = '<span class="important">quelques</span> molécules de cette classe '.nombreEnergie(50);
    }
    elseif($tuto['niveaututo'] == 7) {
        $image = 'images/tutoriel/map.png';
        $titreTuto = 'Carte';
        $contenuTuto = 'Votre armée sera bientôt prête ! Maintenant, sélectionnez <nobr><img alt="c" src="images/menu/attaquer.png" class="imageAide"/> <strong>Carte</strong></nobr> dans le menu à gauche.<hr/><br/>
        Vous vous trouvez alors devant une <strong>carte</strong> que vous pouvez déplacer. Votre emplacement est entouré <strong style="color:orange">d\'orange</strong>, <strong style="color:blue">votre équipe en bleue</strong>, vos <strong style="color:green">alliés (pacte) en vert</strong> et vos <strong style="color:red">adversaires (guerre) en rouge</strong>.<br/><br/>
        <strong>Cliquez sur un joueur</strong> sur la carte : vous tombez alors sur son profil. Vous pouvez effectuer différentes actions telles que <strong>l\'espionner</strong> <img alt="att" src="images/rapports/binoculars.png" class="imageAide"/>, lui <strong>envoyer un message</strong> <img alt="att" src="images/message_ferme.png" class="imageAide"/>, voir ses <strong>médailles</strong> <img alt="att" src="images/medailles.png" class="imageAide"/> et bientôt <strong>l\'attaquer</strong> <img alt="att" src="images/rapports/sword.png" class="imageAide"/>. Malheureusement, vous êtes encore sous <strong>protection débutant</strong> pour <strong>deux jours</strong>, retenez vos pulsions meurtrières jusque là !';
        
        $objectif =  'Cliquer sur un joueur';
        $recompense = '<span class="important">quelques molécules</span>';
    }
    elseif($tuto['niveaututo'] == 8) {
        $image = 'images/tutoriel/team.png';
        $titreTuto = 'Equipe';
        $contenuTuto = 'En attendant, afin d\'être plus puissant face aux autres joueurs, rejoindre une équipe est une bonne manière de fonctionner.<br/>Rendez-vous sur la page  <nobr><img alt="c" src="images/menu/alliance.png" class="imageAide"/> <strong>Equipe</strong></nobr>.<br/><br/><strong>Deux possibilités</strong> : en créer une nouvelle ou demander à un membre d\'une équipe déjà existante de vous inviter dans la sienne.';
        
        $objectif = 'Rejoindre une équipe';
        $recompense = 'Champ de force <span class="important">niveau 1</span> et '.nombreEnergie(50);
    }
    elseif($tuto['niveaututo'] == 9) {
        $image = 'images/tutoriel/question.png';
        $titreTuto = 'Fin du tutoriel';
        $contenuTuto = 'Voilà les bases du jeu mais il reste encore beaucoup à découvrir, explorez le menu ! Le <nobr><img alt="c" src="images/menu/classement.png" class="imageAide"/> <strong>Classement</strong></nobr> vous permettra de vous évaluer par rapport aux autres, les <nobr><img alt="c" src="images/menu/medailles.png" class="imageAide"/> <strong>Médailles</strong></nobr> vous donnent des étapes pour obtenir des bonus, le <nobr><img alt="c" src="images/menu/marche.png" class="imageAide"/> <strong>Marché</strong></nobr> vous permet d\'échanger et d\'envoyer des ressources, le <nobr><img alt="c" src="images/menu/forum.png" class="imageAide"/> <strong>Forum</strong></nobr> vous permettra d\'échanger et de proposer des améliorations ou des bugs...<br/><br/>
        Bon jeu et bonne chance sur <span class="important">The Very Little War !</span>';
        
        if(preg_match("#Visiteur[0-9]+#",$_SESSION['login'])) {
            $contenuTuto = $contenuTuto.'<br/><hr/><br/>Il est désormais temps de vous inscrire sur le jeu.<br/><br/>
            <form action="comptetest.php" method="post" name="inscription">'.
                debutListe(true).
                    item(['retour' => true, 'floating' => true, 'media' => '<img alt="login" src="images/accueil/player.png" class="w32"/>', 'titre' => 'Login', 'input' => '<input type="text" name="login" id="login" maxlength="13" value="Login">', 'after' => submit(['link' => 'javascript:generate()', 'titre' => 'Générer'])]).
                    item(['retour' => true,'floating' => true, 'media' => '<img alt="login" src="images/accueil/email.png" class="w32"/>', 'titre' => 'E-mail', 'input' => '<input type="text" name="email" id="email" maxlength="25" class="form-control">']).
                    item(['retour' => true,'floating' => true, 'media' => '<img alt="login" src="images/accueil/door-key.png" class="w32"/>', 'titre' => 'Mot de passe', 'input' => '<input type="password" name="pass" id="pass" class="form-control">']).
                    item(['retour' => true,'floating' => true, 'media' => '<img alt="login" src="images/accueil/door-key.png" class="w32"/>', 'titre' => 'Confirmation', 'input' => '<input type="password" name="pass_confirm" id="pass_confirm" class="form-control">']).
                    '<p style="margin-left:5px">En vous inscrivant vous acceptez nos <a href="regles.php" class="external lien">Conditions Générales d\'Utilisation</a></p>'.
                    item(['input' => submit(['form' => 'inscription','titre' => 'Inscription']),'retour' => true]).
                finListe(true).
            '</form>';
        }
        else {
            $contenuTuto = $contenuTuto.'<form method="post" name="finirTuto" action="tutoriel.php" style="display: block;margin: 0 auto;text-align: center;" >'.submit(['titre' => 'Finir le tutoriel', 'form' => 'finirTuto', 'nom' => 'finir']).'</form>';
        }
        
        $objectif = false;
        $recompense = false;
        
    }
    
    debutCarte("Tutoriel","background-color:#8A0000");
        debutAccordion();
            if(!$objectif){
                $objectif = '';
            }
            else {
                $objectif = important('Objectif').$objectif.'<br/><br/>';
            }
    
            if(!$recompense){
                $recompense = '';
            }
            else {
                $recompense = important('Récompense').$recompense;
            }
            itemAccordion($titreTuto,'<img src="'.$image.'" class="imageMedia">',$contenuTuto.'<br/><br/>'.$objectif.$recompense,"tutorielAccordion");
        finListe();
    finCarte();
    }
    else { // s'il n'y a plus de tuto on affiche les missions
        $plusQueZero = false;
        if($autre['missions'] != ""){ // initialisation du tableau des missions
            $missions = explode(";",$autre['missions']);
            foreach($missions as $num => $mission){
                if($mission == 0 && $mission != ""){
                    $plusQueZero = true;
                }
            }
        }
        else {
            $missions = [];
            for($i=0;$i<sizeof($listeMissions);$i++){
                $missions[] = 0;
            }
            
            $plusQueZero = true;
        }
        
        if($plusQueZero){
            debutCarte("Missions","background-color:#8A0000");
            debutAccordion();
            $c = 0;
            foreach($listeMissions as $num => $mission){
                
                $recompense = '';
                if(array_key_exists("energie",$mission)){
                    $recompense = $recompense.nombreEnergie($mission['energie']);    
                }
                if(array_key_exists("atomes",$mission)){
                    $recompense = $recompense.nombreTout($mission['atomes']);
                }
                foreach($nomsRes as $num1 => $res){
                    if(array_key_exists($res,$mission)){
                        $recompense = $recompense.nombreAtome($num1,$mission[$res]);
                    }
                }
                
                
                if($missions[$num] == 0 && $c < 3){
                    item(['titre' => $mission['titre'],'accordion'=>$mission['contenu'].'<br/><br/>'.important("Récompense").'<br/>'.$recompense.'<br/>','media'=>$mission['icone']]);
                    $c++;
                }
            }
            finListe();
            finCarte();
        }
    }
?>
	
