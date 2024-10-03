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

include("includes/bbcode.php");

include("includes/tout.php");

if (isset($_GET['id'])) {
	$_GET['id'] = antiXSS($_GET['id']);
	$sql = 'SELECT * FROM membre WHERE login="'.$_GET['id'].'"';
	$req = mysqli_query($base,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$membre = mysqli_fetch_array($req);
	$nb = mysqli_num_rows($req);

	$sql1 = 'SELECT * FROM autre WHERE login="'.mysqli_real_escape_string($base,stripslashes(antihtml($membre['login']))).'"';
	$req1 = mysqli_query($base,$sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
	$donnees1 = mysqli_fetch_array($req1);
	
	$sql3 = 'SELECT idalliance FROM autre WHERE login="'.mysqli_real_escape_string($base,stripslashes(antihtml($membre['login']))).'"';
	$req3 = mysqli_query($base,$sql3) or die('Erreur SQL !<br />'.$sql3.'<br />'.mysql_error());
	$donnees3 = mysqli_fetch_array($req3);
	
	$sql2 = 'SELECT tag FROM alliances WHERE id="'.mysqli_real_escape_string($base,stripslashes(antihtml($donnees3['idalliance']))).'"';
	$req2 = mysqli_query($base,$sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
	$donnees2 = mysqli_fetch_array($req2);
	
	$sql4 = 'SELECT nombre FROM molecules WHERE proprietaire=\''.$membre['login'].'\' AND nombre!=0';
	$req4 = mysqli_query($base,$sql4) or die('Erreur SQL !<br />'.$sql4.'<br />'.mysql_error());
	$nb_molecules = 0;
	while($donnees4 = mysqli_fetch_array($req4)) {
		$nb_molecules = $nb_molecules + $donnees4['nombre'];
	}
    
	if($nb > 0 ) {
        debutCarte($membre['login']);
        $titre = 'Joueur';
        if(statut($membre['login']) == 0){
            $titre = $titre." <span style=\"color:darkgray\">Inactif</span>";
        }
        echo important($titre);
		?>
		<br/>
		<img style="margin-right: 20px; float: right; border-radius: 10px;width:80px;" alt="profil" src="images/profil/<?php echo $donnees1['image']; ?>"/>
       <?php if($donnees3['idalliance'] > 0) { $alliance = alliance($donnees2['tag']); } else { $alliance = "Pas d'alliance";}
        
        $rangQuery = query('SELECT login FROM autre ORDER BY totalPoints DESC');
        $rang = 1;
                
        while($rangEx = mysqli_fetch_array($rangQuery)){
            if($rangEx['login'] == $membre['login']){
                break;
            }
            $rang++;
        }
        echo chipInfo('<span class="important">Rang : </span>'.imageClassement($rang),'images/alliance/up.png').'<br/>';
        echo chip('<span class="important">Nom : </span>'.$membre['login'],'<img alt="coupe" src="images/classement/joueur.png" class="imageChip" style="width:25px;border-radius:0px;"/>',"white",false,true).'<br/>';
        echo chip('<span class="important">Equipe : </span>'.$alliance,'<img alt="coupe" src="images/classement/alliance.png" class="imageChip" style="width:25px;border-radius:0px;"/>',"white",false,true).'<br/>';
        echo nombrePoints('<span class="important">Points : </span>'.$donnees1['totalPoints']).'<br/>';
		echo chip('<span class="important">Victoires : </span>'.$donnees1['victoires'],'<img alt="coupe" src="images/classement/victoires.png" class="imageChip" style="width:25px;border-radius:0px;"/>',"white",false,true).'<br/>';
        if($membre['x'] != -1000){
            echo chip('<span class="important">Position : </span>'.'<a href="attaquer.php?x='.$membre['x'].'&y='.$membre['y'].'">'.$membre['x'].';'.$membre['y'].'</a>','<img alt="coupe" src="images/attaquer/map.png" class="imageChip" style="width:25px;border-radius:0px;"/>',"white",false,true);
        }

        $fin = false;
		if(isset($_SESSION['login'])) {
            if($membre['x'] != -1000 && $_SESSION['login'] != $membre['login']){
			     $fin = '<a href="attaquer.php?id='.$membre['login'].'&type=1" class="lienSousMenu"><img src="images/classement/adversaires.png" class="imageSousMenu" alt="attaquer" title="Attaquer"/><br/><span class="labelSousMenu"  style="color:black">Attaquer</span></a>
                <a href="attaquer.php?id='.$membre['login'].'&type=2" class="lienSousMenu"><img src="images/rapports/binoculars.png" class="imageSousMenu" alt="attaquer" title="Espionner"/><br/><span class="labelSousMenu"  style="color:black">Espionner</span></a>';
            }

            $fin = $fin.'<a href="ecriremessage.php?destinataire='.$membre['login'].'" class="lienSousMenu"><img src="images/message_ferme.png" class="imageSousMenu" alt="attaquer" title="Ecrire un message"/><br/><span class="labelSousMenu"  style="color:black">Message</span></a>
            <a href="medailles.php?login='.$membre['login'].'" class="lienSousMenu"><img src="images/medailles.png" class="imageSousMenu" alt="attaquer" title="Médailles"/><br/><span class="labelSousMenu"  style="color:black">Médailles</span></a>';
            echo '<br/><br/>'.important("Actions");
		}
        
		finCarte($fin);
        
        if(isset($_SESSION['login'])) {
            debutCarte("Description");
                echo '<div class="table-responsive">';
		      echo BBcode($donnees1['description']);
                echo '</div>';
            finCarte();
        }
	} else {
        $membre['login'] = "Joueur inexistant";
        debutCarte("Erreur");
        debutContent();
		echo  "Ce joueur n'existe pas !";
        finContent();
        finCarte();
	}
} 
include("includes/copyright.php"); ?>