<body class="theme-black" style="font-weight:regular">
    <div class="panel-overlay"></div>
  <div class="panel panel-left panel-cover">
    <div style="display:block;width:100%;height:150px;color:white;font-size:20px;background-color: black;text-align: center">
    <br/>
    <p style="display:inline;">
    <img src="images/tvlw.png" style="vertical-align :middle;width:90px;height:90px" alt="icone"/>
    </p>
    </div>
    <?php include("statistiques.php"); 
    debutContent();
      debutListe();
        item(['media' => '<img alt="armee" src="images/menu/accueil.png" class="iconeMenu">', 'titre' => 'Accueil', 'link' => 'index.php', 'style' => 'color:black']);
        item(['media' => '<img alt="armee" src="images/menu/sinscrire.png" class="iconeMenu">', 'titre' => '<strong>S\'inscrire</strong>', 'link' => 'inscription.php', 'style' => 'color:black']);
        item(['media' => '<img alt="armee" src="images/menu/sinstruire.png" class="iconeMenu">', 'titre' => 'S\'instruire', 'link' => 'sinstruire.php', 'style' => 'color:black']);
        item(['media' => '<img alt="armee" src="images/menu/regles.png" class="iconeMenu">', 'titre' => 'CGU', 'link' => 'regles.php', 'style' => 'color:black']);
        item(['media' => '<img alt="armee" src="images/menu/classement.png" class="iconeMenu">', 'titre' => 'Classement', 'link' => 'classement.php?sub=0', 'style' => 'color:black']);
        item(['media' => '<img alt="armee" src="images/menu/forum.png" class="iconeMenu">', 'titre' => 'Forum', 'link' => 'forum.php', 'style' => 'color:black']);
      finListe();
    finContent();
    ?>
</div>
	

	

