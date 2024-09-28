<?php
include("includes/connexion.php");
include("includes/constantesBase.php");
include("includes/fonctions.php");

if(isset($_GET['id'])){
    $_GET['id'] = antiXSS($_GET['id']);
    $param = ['nombre','niveau','joueur','nbTotalAtomes'];
    foreach($param as $num=>$val){
        if(isset($_GET[$val])){
            $_GET[$val] = antiXSS($_GET[$val]);
        }
        else {
            $_GET[$val] = 0;
        }
    }
    
    if($_GET['id'] == "attaque"){
        exit(json_encode(array("valeur" => attaque($_GET['nombre'],$_GET['niveau'],$_GET['joueur']))));
    }
    if($_GET['id'] == "defense"){
        exit(json_encode(array("valeur" => defense($_GET['nombre'],$_GET['niveau'],$_GET['joueur']))));
    }
    if($_GET['id'] == "pointsDeVieMolecule"){
        exit(json_encode(array("valeur" => pointsDeVieMolecule($_GET['nombre'],$_GET['niveau']))));
    }
    if($_GET['id'] == "potentielDestruction"){
        exit(json_encode(array("valeur" => potentielDestruction($_GET['nombre'],$_GET['niveau']))));
    }
    if($_GET['id'] == "pillage"){
        exit(json_encode(array("valeur" => pillage($_GET['nombre'],$_GET['niveau'],$_GET['joueur']))));
    }
    if($_GET['id'] == "productionEnergieMolecule"){
        exit(json_encode(array("valeur" => productionEnergieMolecule($_GET['nombre'],$_GET['niveau']))));
    }
    if($_GET['id'] == "vitesse"){
        exit(json_encode(array("valeur" => vitesse($_GET['nombre'],$_GET['niveau']))));
    }
    if($_GET['id'] == "tempsFormation"){
        exit(json_encode(array("valeur" => affichageTemps(tempsFormation($_GET['nombre'],$_GET['niveau'],$_GET['nbTotalAtomes'],$_GET['joueur']),true))));
    }
    if($_GET['id'] == "demiVie"){
        exit(json_encode(array("valeur" => affichageTemps(demiVie($_GET['joueur'],$_GET['nbTotalAtomes'],1)))));
    }
}
?>