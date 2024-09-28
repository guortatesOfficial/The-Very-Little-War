<?php
$nomsRes = array("carbone","azote","hydrogene","oxygene","chlore","soufre","brome","iode"); 
$nomsAccents = array("carbone","azote","hydrogène","oxygène","chlore","soufre","brome","iode");
$couleurs = ['black','blue','gray','red','green','#D07D00','#840000','#BB6668'];
$couleursSimples = ['black','blue','gray','red','green','orange','brown','pink'];
$utilite = array("Défense","Temps de formation","Dégâts aux bâtiments","Attaque","Vitesse de déplacement","Capacité de pillage","Points de vie","Produit de l'énergie");
$lettre = array("C", "N", "H", "O", "Cl","S","Br","I");
$aidesAtomes = ['Le carbone augmente la défense de votre molécule. Ce sont les dégâts que votre molécule infligera aux molécules adverses lorsque l\'on vous attaquera.',
               'L\'azote diminue le temps de formation de votre molécule : plus il y a d\'azote dans votre molécule, moins cela prendra de temps pour créer une de ces molécules.',
               'L\'hydrogène inflige des dégâts aux bâtiments adverses lors de vos attaques. Cela vous permettra d\'affaiblir la production adverses.',
               'L\'oxygène augmente l\'attaque de votre molécule. Ce sont les dégâts que votre molécule infligera aux molécules adverses lorsque vous attaquerez.',
               'Le chlore augmente la vitesse de déplacement de vos molécules sur la carte. Il vous faudra des molécules rapides si vous voulez prendre par surprise un adversaire loin de vous.',
               'Le soufre vous permet de piller l\'adversaire lors d\'une des vos attaques. Vous récupérez ainsi une partie de ses ressources pour vous.',
               'Le brome augmente les points de vie de vos molécules. Lors d\'une attaque, les dégâts infligés par les molécules adverses seront comparés à la vie de vos molécules pour déterminer le nombre de morts.',
               'L\'iode est particulier, cela permet de produire de l\'énergie. Ces molécules seront plutôt destinées à rester chez vous mais devront être défendues par des molécules carbonées pour éviter que vous perdiez toute votre production sur une attaque surprise.'];
$nbRes = sizeof($nomsRes)-1;
$nbClasses = 4;
$nbPointsVictoire = 1000;

$paliersMedailles = ['Bronze','Argent','Or','Emeraude','Saphir','Rubis','Diamant','Diamant Rouge'];
$imagesMedailles = ['medaillebronze.png','medailleargent.png','medailleor.png','emeraude.png','saphir.png','rubis.png','diamant.png','diamantrouge.png'];

$bonusMedailles = [1,3,6,10,15,20,30,50];
$bonusForum = ['insigne bronze','insigne argent','insigne or','insigne emeraude','insigne saphir','insigne rubis','insigne diamant','insigne diamant rouge'];
$bonusTroll = ['Rien','Rien','Rien','Rien','Rien','Rien','Rien','Rien'];

$paliersTerreur = [5,15,30,60,120,250,500,1000];
$paliersAttaque = [100,1000,5000,20000,100000,500000,2000000,10000000];
$paliersDefense = [100,1000,5000,20000,100000,500000,2000000,10000000];
$paliersPillage = [1000,10000,50000,200000,1000000,5000000,20000000,100000000];
$paliersPipelette = [10,25,50,100,200,500,1000,5000];
$paliersPertes = [10,100,500,2000,10000,50000,200000,1000000];
$paliersEnergievore = [100,500,3000,20000,100000,2000000,10000000,1000000000];
$paliersConstructeur = [5,10,15,25,35,50,70,100];
$paliersBombe = [1,2,3,4,5,6,8,12];
$paliersTroll = [0,1,2,3,4,5,6,7];

//EQUIPES
$joueursEquipe = 20;

//MARCHE

$vitesseMarchands = 20;

//ESPIONNAGE
$vitesseEspionnage = 20;
$coutNeutrino = 50;
?>