<!DOCTYPE html>
<html>
  <head>
    <?php include("includes/meta.php"); 
    include("includes/style.php"); ?>
  </head>
    <?php if (isset($_SESSION['login']))
    {
        include("includes/basicprivatehtml.php");
    }
    else
    {
        include("includes/basicpublichtml.php"); 
    }
    ?>
    
    <!-- commun au publique et au privé
    <div class="views tabs">
      <!-- Your main view, should have "view-main" class -->
      <div class="view tab view-main tab active " >
        <!-- Pages container, because we use fixed navbar and toolbar, it has additional appropriate classes-->
            <div class="navbar" style="box-shadow: -1px 2px 5px 2px rgba(0, 0, 0, 0.3); ">
              <div class="navbar-inner">
                <div class="left" id="leftMenu">
                  <a href="#" class="link icon-only open-panel">
                      <i class="icon icon-bars"></i>
                  </a>
                </div>
                <div class="center">
                <p>
                <img alt="banniere" src="images/banniere.png" id="titre" style="position:fixed;top:10px;left:15%;width:260px;height:27px;"/>
                </p>
                </div>
              </div>
            </div>
            
            <!-- AJOUT DES SOUS-MENUS -->
            <?php
            if(in_array("classement.php",explode("/",$_SERVER['PHP_SELF']))){
            ?>
        
            <div class="toolbar toolbar-bottom toolbarcustom">
            <div class="toolbar-inner">
                <a class="tab-link lienSousMenu" href="classement.php?sub=0"><img src="images/sous-menus/joueur.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Joueurs</span></a>
                <a class="tab-link lienSousMenu" href="classement.php?sub=1"><img src="images/sous-menus/alliance.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Equipes</span></a>
                <a class="tab-link lienSousMenu" href="classement.php?sub=2"><img src="images/sous-menus/swords.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Guerres</span></a>
                <a class="tab-link lienSousMenu" href="classement.php?sub=3"><img src="images/sous-menus/forum.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Forum</span></a>
                <a class="tab-link lienSousMenu" href="historique.php?sub=0"><img src="images/sous-menus/parchemin.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Archives</span></a>
            </div>
            </div>
            <?php } 
          
            if(in_array("marche.php",explode("/",$_SERVER['PHP_SELF']))){
            ?>
        
            <div class="toolbar toolbar-bottom toolbarcustom">
            <div class="toolbar-inner">
                <a class="tab-link lienSousMenu" href="marche.php?sub=0"><img src="images/sous-menus/back-forth.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Echanger</span></a>
                <a class="tab-link lienSousMenu" href="marche.php?sub=1"><img src="images/sous-menus/present.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Envoyer</span></a>
            </div>
            </div>
            <?php } 
          
            if(in_array("armee.php",explode("/",$_SERVER['PHP_SELF'])) && !isset($_POST['emplacementmoleculecreer'])){
            ?>
        
            <div class="toolbar toolbar-bottom toolbarcustom" >
            <div class="toolbar-inner">
                <a class="tab-link lienSousMenu" href="armee.php?sub=0"><img src="images/sous-menus/sword-spin.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Formation</span></a>
                <a class="tab-link lienSousMenu" href="armee.php?sub=1"><img src="images/sous-menus/rally-the-troops.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Vue d'ensemble</span></a>
            </div>
            </div>
            <?php }
          
            if(in_array("armee.php",explode("/",$_SERVER['PHP_SELF'])) && isset($_POST['emplacementmoleculecreer'])){
            ?>
            <div class="toolbar toolbar-bottom toolbarcustom">
            <div class="toolbar-inner" style="background-color:lightgray;overflow-x:auto;" >
                <?php
                echo chipInfo(attaque(0,$niveauoxygene,$_SESSION['login']),'images/molecule/sword.png','attaque');
                echo chipInfo(defense(0,$niveaucarbone,$_SESSION['login']),'images/molecule/shield.png','defense');
                echo chipInfo(pointsDeVieMolecule(0,$niveaubrome),'images/molecule/sante.png','vie').'<br/>';
                echo chipInfo(vitesse(0,$niveauchlore).' cases/h','images/molecule/vitesse.png','vitesse');
                echo chipInfo(potentielDestruction(0,$niveauhydrogene),'images/molecule/fire.png','destruction');
                echo chipInfo(affichageTemps(tempsFormation(0,$niveauazote,0,$_SESSION['login']),true),'images/molecule/temps.png','tempsFormation');
                echo chipInfo(pillage(0,$niveausoufre,$_SESSION['login']).' ressources','images/molecule/bag.png','pillage').'<br/>';
                echo nombreEnergie('<span style="color:green">+'.productionEnergieMolecule(0,$niveauiode).'/h</span>','productionIode');
                echo chipInfo(affichageTemps(demiVie($_SESSION['login'],0,1)),'images/molecule/demivie.png','demiVie');
                ?>
            </div>
            </div>
            <?php
            echo '
            <script>
                function actualiserStats(){
                    var totalAtomes = 0;
                    ';
                    foreach($nomsRes as $num => $res){
                        echo 'totalAtomes += Number(document.getElementById("'.$res.'").value);
                        ';
                    }
                    echo '
                    $.ajax({url: "api.php?id=attaque&joueur='.$_SESSION['login'].'&niveau='.$niveauoxygene.'&nombre="+document.getElementById(\'oxygene\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'attaque\').innerHTML = contenu.valeur;
                    }});
                    
                    $.ajax({url: "api.php?id=defense&joueur='.$_SESSION['login'].'&niveau='.$niveaucarbone.'&nombre="+document.getElementById(\'carbone\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'defense\').innerHTML = contenu.valeur;
                    }});
                    
                    $.ajax({url: "api.php?id=pointsDeVieMolecule&joueur='.$_SESSION['login'].'&niveau='.$niveaubrome.'&nombre="+document.getElementById(\'brome\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'vie\').innerHTML = contenu.valeur;
                    }});
                    
                    $.ajax({url: "api.php?id=potentielDestruction&joueur='.$_SESSION['login'].'&niveau='.$niveauhydrogene.'&nombre="+document.getElementById(\'hydrogene\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'destruction\').innerHTML = contenu.valeur;
                    }});
                    
                    $.ajax({url: "api.php?id=vitesse&joueur='.$_SESSION['login'].'&niveau='.$niveauchlore.'&nombre="+document.getElementById(\'chlore\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'vitesse\').innerHTML = contenu.valeur+" cases/h";
                    }});
                    
                    $.ajax({url: "api.php?id=pillage&joueur='.$_SESSION['login'].'&niveau='.$niveausoufre.'&nombre="+document.getElementById(\'soufre\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'pillage\').innerHTML = contenu.valeur;
                    }});
                    
                    $.ajax({url: "api.php?id=productionEnergieMolecule&joueur='.$_SESSION['login'].'&niveau='.$niveauiode.'&nombre="+document.getElementById(\'iode\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'productionIode\').innerHTML = "<span style=\"color:green\">+"+contenu.valeur+"/h</span>";
                    }});
                    
                    $.ajax({url: "api.php?id=tempsFormation&joueur='.$_SESSION['login'].'&nbTotalAtomes="+totalAtomes+"&niveau='.$niveauazote.'&nombre="+document.getElementById(\'azote\').value,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'tempsFormation\').innerHTML = contenu.valeur;
                    }});
                    
                    $.ajax({url: "api.php?id=demiVie&joueur='.$_SESSION['login'].'&nbTotalAtomes="+totalAtomes,
                    success: function(data){
                        var contenu = JSON.parse(data);
                        document.getElementById(\'demiVie\').innerHTML = contenu.valeur;
                    }});
                }
            </script>';
            }
            
            if(in_array("historique.php",explode("/",$_SERVER['PHP_SELF']))){ ?>
            <div class="toolbar toolbar-bottom toolbarcustom">
            <div class="toolbar-inner">
                <a class="tab-link lienSousMenu" href="historique.php?sub=0"><img src="images/sous-menus/joueur.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Joueurs</span></a>
                <a class="tab-link lienSousMenu" href="historique.php?sub=1"><img src="images/sous-menus/alliance.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Equipes</span></a>
                <a class="tab-link lienSousMenu" href="historique.php?sub=2"><img src="images/sous-menus/swords.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Guerres</span></a>
                <a class="tab-link lienSousMenu" href="classement.php?sub=0"><img src="images/sous-menus/podium.png" class="imageSousMenu"/><br/><span class="labelSousMenu">Partie en cours</span></a>
            </div>
            </div>
            <?php } ?>

        <div class="pages navbar-through">
          <!-- Page, "data-page" contains page name -->
          <div data-page="index" class="page">
 
            <!-- Top Navbar. In Material theme it should be inside of the page-->
            
            <!-- Scrollable page content -->
            <div class="page-content">
            <div style="height:63px"></div> <!-- pour éviter des problèmes avec la barre du menu -->
            <?php if (isset($_SESSION['login']))
            {
                include("includes/cardsprivate.php");
            }
            else
            {
                include("includes/cardspublic.php"); 
            }
            ?>