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

$cours=["Introduction","Description de l'univers","De l'atome à l'élément chimique","Les molécules","L'élément chimique","Quantité d'espèce chimique"];

if(!isset($_GET['cours']) || $_GET['cours'] <0 || $_GET['cours'] > sizeof($cours)) {
	$_GET['cours'] = 0;
}

debutCarte($cours[$_GET['cours']],"",'images/accueil/wallpaper.jpg');
debutContent(); 
if($_GET['cours'] == 1) {
	?>
	<div class="partie">I) Description de l'univers</div><hr/>
	<div class="sousPartie">A) Vers l'infiniment petit</div><hr/>
	<p><img alt="atom" src="images/accueil/atom1.png" style="float: left;width:50px ;heigth:50px;margin: 5px 5px 5px 5px">Dans l'infiniment petit, la matière est constituée d'<span class="important">atomes.</span> Ces atomes peuvent s'assembler pour former des <span class="important">molécules.</span> Un atome est constitué d'un noyau autour duquel les éléctrons sont en mouvement.
	L'atome est 100000 fois plus grand que le noyau.<img alt="atom" src="images/accueil/atom5.png" style="float: right; width:50px ;heigth:50px; margin: 5px 5px 5px 5px"><br/><br/><br/>
	</p>
	<div class="sousPartie">B) Dans l'infiniment grand</div><hr/>
	<p>Dans l'infiniment grand on retiendra les <span class="important">étoiles</span> (dont le soleil), le système solaire qui contient le Soleil et <span class="important">huit planètes</span> (Mercure, Venus, Terre, Mars, Jupiter, Saturne, Uranus, Neptune)
	, les satellites (comme la Lune), les astéroïdes et les comètes. Entre les planètes et les étoiles, il y a du vide. C'est un système lacunaire. Les galaxies sont un amas d'étoiles.
	</p>
	<div class="table-responsive"><img src="images/cours/systeme.png" alt="univers" class="imageCours"/></div><hr/>
	<div class="partie">II) Longueurs dans l'univers</div><hr/>
	<div class="sousPartie">A) Unités de longueur</div><hr/>
	<p>L'unité de longueur dans le système international (SI) est le mètre. On utilise souvent les sous-multiples.<br/><br/>
	<div class="table-responsive">
	<table class="table table-striped table-bordered">
	<tr><td>10<sup>9</sup></td><td>10<sup>6</sup></td><td>10<sup>3</sup></td><td>10<sup>0</sup></td><td>10<sup>-3</sup></td><td>10<sup>-6</sup></td><td>10<sup>-9</sup></td><td>10<sup>-12</sup></td><td>10<sup>-15</sup></td></tr>
	<tr><td>1 Gm</td><td>1 Mm</td><td>1 km</td><td>1 m</td><td>1 mm</td><td>1 µm</td><td>1 nm</td><td>1 pm</td><td>1 fm</td></tr>
	<tr><td>Gigamètre</td><td>Mégamètre</td><td>kilomètre</td><td>mètre</td><td>millimétre</td><td>micromètre</td><td>nanomètre</td><td>picomètre</td><td>femtomètre</td></tr>
	</table>
	</div>
	</p>
	<div class="sousPartie">B) Ecriture scientifique</div><hr/>
	<p>La notation scientifique est l'écriture d'un nombre sous la forme d'un produit du type a*10<sup>n</sup> avec a un nombre décimal tel que 1 inférieur ou égal à 1 et strictement supérieur à 10.
	et n étant un nombre entier.<br/>
	Ex : Rayon de la Terre : 6378 km = 6.378*10<sup>3</sup> km
	</p>
	<div class="sousPartie">C) Ordre de grandeur</div><hr/>
	<p>L'ordre de grandeur d'une valeur est la puissance de 10 la plus proche de cette valeur.<br/>
	Ex : soit 1.52*10<sup>4</sup> = 1*10<sup>4</sup> => ordre de grandeur : 10<sup>4</sup><br/>
	soit 8.2*10<sup>4</sup> = 10*10<sup>4</sup> = 1*10<sup>5</sup> => ordre de grandeur : 10<sup>5</sup><br/>
	soit 8.2*10<sup>-3</sup> = 10*10<sup>-3</sup> = 10<sup>1-3</sup> => ordre de grandeur : 10<sup>-2</sup><br/>
	Pour les valeurs de a strictement inférieures à 5, on arrondit à 1 puis on ajoute la puissance.<br/>
	Pour les valeurs de a supérieures ou égales à 5, on arrondit à 10 puis on ajoute la puissance.
	</p>
	<div class="partie">III) L'année lumière</div><hr/>
	<div class="sousPartie">A) Propagation et vitesse de la lumière</div><hr/>
	<p>La lumière se propage en ligne droite dans un milieu homogène et transparent. La lumière se propage à vitesse finie. La vitesse de la lumière dans le vide ou dans l'air est de : <span class="important">c = 3.00*10<sup>8</sup> m/s</span>
	</p>
	<div class="sousPartie">B) L'année lumière</div><hr/>
	<p>L'année lumière est la <span class="important">distance parcourue par la lumière en une année.</span><br/>
	v = d/t => d = v*t<br/>
	t = une année = 365*24*3600<br/>
	d = 3*10<sup>8</sup>*(365*24*3600)<br/>
	d = 9.47*10<sup>15</sup> m<br/>
	L'ordre de grandeur de l'année lumiére est de 10<sup>16</sup>m ou 10<sup>13</sup>km
	</p>
	<div class="sousPartie">C) Voir loin, c'est voir dans le passé</div><hr/>
	<p>L'étoile polaire est située à 440 a.l (années lumiéres) de la Terre. Cette lumière a voyagé pendant 440 ans avant d'arriver sur Terre. Donc on la voit telle qu'elle était il y a 440 ans.
	Pour des galaxies éloignées, nous les voyons telles qu'elles étaient il y a 12 milliards d'années.
	</p>
	<?php
}
elseif($_GET['cours'] == 2) {
	?>
	<div class="partie">I) L'atome</div><hr/>
	<div class="sousPartie">A) Description du modele de l'atome</div><hr/>
	<div class="sousSousPartie">1) Constitution de l'atome</div><hr/>
	<p>Un atome est constitué d'un noyau chargé + et d'éléctrons chargés - en mouvement autour du noyau. L'atome est <span class="important">éléctriquement neutre.</span></p>
	<div class="sousSousPartie">2) Noyau de l'atome</div><hr/>
	<p>Un noyau est constitué de particules appelées nucléons de deux types : - des  <span class="important">neutrons</span> (neutres)<br/>
	- des  <span class="important">protons</span> (chargés +)<br/>
	Comme l'atome est éléctriquement neutre, il y autant de charges + que de charges -. Il y a donc autant d'éléctrons que de protons. La charge éléctrique portée par le proton est appelée charge élémentaire (e).
	e = 1.6*10<sup>-19</sup> J
	</p>
	<div class="sousSousPartie">3) Representation symbolique de l'atome</div><hr/>
	<p><img src="images/cours/xaz.png" alt="representationatome" style="float: left; margin-left: 20px;"/>
	A : nombre de nucléons (protons et neutrons)<br/>
	Z : numero atomique (nombre de protons donc nombre d'éléctrons)<br/>
	Si N est le nombre de neutrons :<br/> 
	A = Z + N<br/>
	</p>
	<div class="sousPartie">B) Caracteristique de l'atome</div><hr/>
	<div class="sousSousPartie">1) Structure lacunaire de l'atome</div><hr/>
	<p>La matière constituant un atome est essentiellement concentrée dans son noyau. Les distances séparant le noyau des éléctrons sont très grandes. Ainsi, la plus grande partie de l'atome est constituée de vide.
	C'est une  <span class="important">structure lacunaire.</span><br/>
	Dimensions de l'atome d'hydrogène : <br/>
	diamètre du noyau : d<sub>N</sub> = 2.4*10<sup>-15</sup> m<br/>
	diamétre de l'atomes : d<sub>A</sub> = 1.1*10<sup>-10</sup> m<br/>
	Le diamètre de l'atome est donc 100 000 fois plus grand que celui de son noyau.
	</p>
	<div class="sousSousPartie">2) Masse de l'atome</div><hr/>
	<p>La masse de l'atome est égale au nombre de nucléons (A) multipliée par leur masse qui est la même pour les protons et les neutrons. On ne prends pas en compte la masse des éléctrons dans le calcul 
	car leur masse est négligable.
	</p>
	<div class="sousPartie">C) Repartition electronique</div><hr/>
	<div class="sousSousPartie">1) Les couches electroniques</div><hr/>
	<p>La répartition des éléctrons n'est pas homogène. Dans un modèle simplifié, on peut considérer qu'ils se concentrent sur des couches appelées couches éléctroniques. Trois couches suffisent pour répartir
	les éléctrons au lyçée (Z <= 18). Chaque couche est repérée par une lettre : K, L, M.
	</p>
	<div class="sousSousPartie">2) Remplissage des couches electroniques</div><hr/>
	<p>Une couche ne peut contenir qu'un nombre limité d'éléctrons. La couche K (la plus proche) peut contenir deux éléctrons maximum. Les couches L et M peuvent contenir huit éléctrons au maximum.
	Une couche éléctronique est saturée lorsqu'elle contient son nombre maximum d'éléctrons. Le remplissage des couches commence par la couche K puis L puis M.
	</p>
	<div class="sousSousPartie">3) Structure electronique</div><hr/>
	<p>La couche la plus éloignée qui contient des éléctrons est appelée couche externe. Les éléctrons de cette couche externe sont les éléctrons périphériques.<br/>
	Exemple : Structure éléctronique du sodium<br/>
	<sub>11</sub>Na : (K)<sup>2</sup> (L)<sup>8</sup> (M)<sup>1</sup> : les couches entre parenthéses et le nombre d'éléctrons en exposant.
	</p>
	<div class="partie">II) L'element chimique</div><hr/>
	<div class="sousPartie">A) Les ions monoatomiques</div><hr/>
	<p>Lorqu'un atome perd ou gagne un ou plusieurs éléctrons, il devient un  <span class="important">ion monoatomique.</span> Cette transformation concerne uniquement les éléctrons. Un atome et l'ion qui en dérive sont caractérisés par la même
	valeur de Z. Un atome éléctriquement neutre qui gagne des éléctrons deviendra un ion négatif (ou anion). Un atomes éléctriquement neutre qui perd des éléctrons deviendra un ion positif (ou cathion). On met en haut à droite
	la charge de l'ion : <br/>Cl<sup>-</sup> (gagne un éléctron)<br/>
	Fe<sup>3+</sup> (perdu trois éléctrons)
	</p>
	<div class="sousPartie">B) L'element chimique</div><hr/>
	<p>Toutes les entités chimiques, atomes ou ions, possédant le même Z appartiennent au même élément chimique. Tout élément chimique est représenté par un symbole qui permet de l'identifier.
	Le symbole est composé d'une lettre majuscule (ex : Iode : I) ou d'une majuscule suivie d'une minuscule (magnesium : Mg).
	</p>
	<div class="sousPartie">C) Classification periodique des elements</div><hr/>
	<p>La classification périodique des éléments est un tableau qui regroupe tous les éléments connus, classés par numéro atomique croissant.<br/>
	</p>
	<div class="sousPartie">D) Les isotopes</div><hr/>
	<p>On appelle atomes isotopes les ensembles d'atomes caractérisés par le même Z mais un nombre de nucléons (A) différent. Ce sont des atomes qui ne différent que par leur nombre de neutrons.
	</p>
	<?php
}
elseif($_GET['cours'] == 3) {
	?>
	<div class="partie">I) Les molécules</div><hr/>
	<div class="sousPartie">A) Les liaisons entre les molécules</div><hr/>
	<div class="sousSousPartie">1) La molécule</div><hr/>
	<p>Les atomes existent très rarement sous forme isolée. Spontanément, ils s'assemblent entre eux pour former des  <span class="important">molécules.</span> Une molécule est un édifice chimique éléctriquement neutre formé d'un nombre
	limité  <span class="important">d'atomes liés les uns les autres.</span>
	</p>
	<div class="sousSousPartie">2) La liaison</div><hr/>
	<p>La liaison entre atomes est symbolisée par un tiret ex: H-H<br/>
	Lorsque les atomes subissent des transformations (en ions monoatomiques ou lorsqu'ils établissent des liaisons entre eux), ils le font de manière à saturer leur couche externe. Les atomes dont la couche 
	externe est déjà saturée ne donnent pas d'ions monoatomiques et ne pourront pas se lier et donc faire de molécules. Ils sont dits chimiquement stables. Le nombre de liaisons que peut former un atome
	est  <span class="important">égal au nombre d'éléctrons qu'il doit acquérir pour saturer sa couche externe.</span> 
	</p>
	<div class="sousPartie">B) Représentation de la molécule</div><hr/>
	<p><span class="important">La formule brute</span> d'une molécule est l'écriture la plus compacte décrivant les atomes et leurs nombres.<br/>
	Ex: CH<sub>4</sub><br/>
	<span class="important">La formule développée</span> indique l'ordre des atomes et les liaisons sont représentées par des tirets et les doubles liaisons par des doubles tirets.<br/>
	<img src="images/cours/developpee.gif" alt="developpee"/><br/>
	<span class="important">La formule semi-développée</span> : les liaisons avec les atomes d'hydrogène ne sont pas représentées. Le nombre d'atomes d'hydrogène est indiqué en bas à droite.<br/>
	Ex: CH<sub>2</sub>=CH<sub>2</sub>	   
	</p>
	<div class="sousPartie">C) Isomères</div><hr/>
	<p>A une formule brute peut correspondre plusieurs formules développées (ou semi-développées). Ce sont des isomères. Les molécules de ces formules développées différentes n'ont pas les mêmes propriétés physqiues et chimiques.<br/>
	Ex: CH<sub>3</sub>-CH<sub>2</sub>-OH et CH<sub>3</sub>-O-CH<sub>3</sub>
	</p>
	<div class="partie">II) Structure des molécules organiques</div><hr/>
	<p>Les molécules organiques sont essentiellement constituées de carbone et d'hydrogène (éventuellement d'oxygène et d'azote). Une molécule organique est constituée d'une chaîne d'atomes de carbone
	appelée chaîne carbonée sur laquelle se fixent les autres atomes. La chaîne carbonée peut être linéaire, cyclique ou ramifiée. Le type de chaînbe influe sur les propriétés physiques et chimiques.<br/>
	<span class="important">Chaîne linéaire</span> : <img src="images/cours/lineaire.gif" alt="lineaire"/><br/>
	<span class="important">Chaîne cyclique</span> : <img src="images/cours/cyclique.gif" alt="cyclique"/><br/>
	<span class="important">Chaîne ramifiée</span> : <img src="images/cours/ramifiee.gif" alt="ramifiee"/><br/>
	<h5>Source des trois images ci-dessus : http://www.web-sciences.com</h5>
	</p>
	<?php
}
elseif($_GET['cours'] == 4) {
	?>
	<div class="partie">I) Les éléments chimiques de l'univers</div><hr/>
	<div class="sousPartie">A) L'origine des éléments</div><hr/>
	<p>Selon la théorie du Big Bang, les 1ers éléments, hydrogène et helium, se sont assemblés il y a 13.7 milliards d'années. Les éléments ayant un Z plus élevé comme le carbone, l'oxygène et le fer
	sont ensuite nés au coeur d'étoiles dans des conditions extrèmes de température et de pression. En fin de vie, les étoiles les plus massives explosent et dispersent les éléments sous forme de gaz.
	</p>
	<div class="sousPartie">B) Conservation des éléments</div><hr/>
	<p>Les réactions chimiques se font sans apparition ni perte d'éléments. Les éléments mis en jeu peuvent éventuellement changer de forme. C'est à dire passer d'un atome à un ion où se combiner
	avec d'autres atomes. Il y a toujours <span class="important">conservation des éléments au cours d'une réaction chimique.</span>
	</p>
	<div class="partie">II) Classification periodique</div><hr/>
	<div class="sousPartie">A) Classification historique de Mendeleïv</div><hr/>
	<p>
	Mendeleïv eut l'idée de classer les éléments, connus à son époque en colonnes et en lignes par ordre de masse croissante de façon à ce que les éléments ayant des <span class="important">propriétés chimiques semblables</span>
	figurent dans une <span class="important">même ligne.</span> Il prédit l'existence de certains éléments inconnus à son époque pour expliquer certaines cases vides dans son classement.
	</p>
	<div class="sousPartie">B) Classification moderne</div><hr/>
	<p>Elle est constituée de 18 colonnes et 7 lignes. Les 112 éléments chimiques naturels ou artificiels sont rangés en ligne par ordre croissant. Le remplissage progressif d'une ligne correspond au
	remplissage progressif d'une couche éléctronique. Un changement de ligne s'effectue quand la couche est saturée. Les lignes sont appelées des periodes. Dans une même colonne, les atomes ont le
	<span class="important">même nombre d'éléctrons sur leur couche éléctronique.</span><br/>
	<div class="table-responsive"><img alt="tableau_periodiqueMini" src="images/cours/tableau_periodique.png"/></div>
	</p>
	<div class="partie">III) Utilisation de la classification</div><hr/>
	<div class="sousPartie">A) Notion de famille chimique</div><hr/>
	<p>Les propriétés chimiques des éléments dépendent essentiellement du nombre d'éléctrons sur la couche externe. Or les éléments d'une même colonne ont le même nombre d'éléctrons sur leur couche externe.
	Ils auront donc les mêmes propriétés. On dit qu'une colonne constitue une famille chimique.<br/>
	Les familles chimiques sont les suivantes : <br/><br/>
	<div class="table-responsive">
	<table class="table table-striped table-bordered">
	<tr><th>Colonne</th><td>I</td><td>II</td><td>III</td><td>IV</td><td>V</td><td>VI</td><td>VII</td><td>VIII</td></tr>
	<tr><th>Famille</th><td>Métaux alcalins (sauf hydrogène)</td><td>Métaux alcalino-terreux</td><td>Famille du bore</td><td>Famille du carbone</td><td>Famille de l'azote</td><td>Famille de l'oxygène</td><td>Halogènes</td><td>Gaz nobles</td></tr>
	<tr><th>Nombre d'éléctrons périphériques</th><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8 (sauf helium)</td></tr>
	</table>
	</div>
	</p>
	<div class="sousPartie">B) Prévision de la charge d'un ion monoatomique</div><hr/>
	<p>Les structures éléctroniques en <span class="important">duet</span> (couche K saturée) ou en <span class="important">octet</span> (couche L ou M saturée) sont particulièrement stables. La famille des gaz rares possède une structure éléctronique en duet
	ou en octet et est inerte.<br/>
	Règle du duet (Z compris entre 2 et 4 inclus) : Ces atomes tendent à avoir une structure en duet pour avoir la couche K saturée.<br/>
	Règle de l'octet (Z compris entre 4 exclu et 20 inclu) : Ces atomes tendent à avoir une structure en octet pour avoir la couche L ou M saturée.<br/><br/>
	Pour trouver la charge des ions : <br/>
	1ere colonne : <sub>3</sub>Li (K)<sup>2</sup> (L)<sup>1</sup> => perd 1 éléctron de la couche L pour être en duet => Li<sup>+</sup><br/>
	2e colonne : <sub>9</sub>F (K)<sup>2</sup> (L)<sup>7</sup> => gagne 1 éléctron de la couche L pour être en octet => F<sup>-</sup>
	</p>
	<?php
}
elseif($_GET['cours'] == 5) {
	?>
	<div class="partie">I) Quantité de matière</div><hr/>
	<div class="sousPartie">A) Du microscopique au macroscopique</div><hr/>
	<p>Dans un morceau de carbone, il y a un très grand nombre d'atomes de carbone. L'utilisation de trés grand nombre n'étant pas très aisée, les chimistes effectuent un changement d'échelle
	et introduisent une nouvelle grandeur : la quantité de matière et son unité qui est <span class="important">la mole.</span>
	</p>
	<div class="sousPartie">B) La mole</div><hr/>
	<p>Pour faciliter le comptage d'un grand nombre d'objets identiques, on les regroupe en paquets. De même en chimie, les atomes, les molécules, les ions sont regroupés en "paquets". Chacun de ces 
	paquets contient <span class="important">6.02*10<sup>23</sup> particules</span> et on appelle ce paquet une mole. Donc dans une mole d'atomes, il y a 6.02*10<sup>23</sup>. La quantité de matière est notée n et la mol est l'unité.
	</p>
	<div class="sousPartie">C) Constante d'Avogadro</div><hr/>
	<p>Le nombre de particules par mole est la constante d'Avogadro notée N<sub>A</sub><br/>
	N<sub>A</sub> = 6.02*10<sup>23</sup> mol<sup>-1</sup><br/>
	Le nombre d'atomes N est proportionnel à la quantité de matière n.<br/>
	N = n*N<sub>A</sub>
	</p>
	<div class="partie">II) Quantité de matière et masse</div><hr/>
	<div class="sousPartie">A) Masse molaire atomique</div><hr/>
	<p>La masse molaire atomique est la masse d'une mole d'atomes de cet élément. Elle s'exprime en g/mol. Les valeurs sont données dans le tableau periodique. Les proportions des isotopes sont telles
	que celles trouvées dans la nature.<br/>
	Ex : M(O) = 16g/mol
	</p>
	<div class="sousPartie">B) Masse molaire moléculaire</div><hr/>
	<p>La masse molaire moléculaire est égale à la <span class="important">somme des masses molaires atomiques</span> de tous les atomes présents dans la molécule.<br/>
	Ex : M(H<sub>2</sub>0) = 2*M(H) + M(0) = 2*1 + 16 = 18 g/mol
	</p>
	<div class="sousPartie">C) Relation entre la masse et la quantité de matière</div><hr/>
	<p><span class="important">n = m/M</span> avec<br/>
	m : masse de l'échantillon en g<br/>
	M : masse molaire en g/mol<br/>
	Ex : Un comprimé de vitamine C contient 60.0 mg de vitamine C de formule C<sub>6</sub>H<sub>8</sub>O<sub>6</sub><br/>
	M(C<sub>6</sub>H<sub>8</sub>O<sub>6</sub>) = 6*12 + 8*1 + 6*16 = 176 g/mol<br/>
	n = m/M = 0.06/176 = 3.41*10<sup>-4</sup>
	</p>
	<div class="partie">III) Cas des solutions</div><hr/>
	<div class="sousPartie">A) Définition</div><hr/>
	<p>Une solution est obtenue par dissolution d'une espèce chimique dans <span class="important">un solvants</span> (un liquide). Si le solvant est l'eau, la solution est appelée <span class="important">solution aqueuse.</span> Une fois dissoute, l'espèce chimique
	s'appelle <span class="important">le soluté.</span> Une solution peut contenir plusieurs solutés différents.
	</p>
	<div class="sousPartie">B) Dissolution d'une espèce moléculaire ou ionique</div><hr/>
	<p>La formule chimique se note différement selon que l'espèce est en solution ou non. Avant la dissolution, on écrit la formule chimique en la faisant suivre de son état physique entre parenthèses :
	soit (s) pour les solides, (l) pour les liquides et (g) pour les gaz. Après la dissolution, si le solvant est l'eau, on obtient une <span class="important">solution aqueuse.</span> La formule des espèces est alors suivie de (aq).<br/>
	Ex : le glucose C<sub>6</sub>H<sub>12</sub>0<sub>6</sub> (s), une solution de glucose s'écrit C<sub>6</sub>H<sub>12</sub>0<sub>6</sub> (aq).<br/>
	Une solution ionique est neutre globalement. Cela implique l'égalité du nombre de charges + et - dans l'échantillon, ce dont on tient compte dans l'écriture ionique.<br/>
	Ex : Une solution de chlorure de sodium contient les ions Na<sup>+</sup> et Cl<sup>-</sup>, la solution s'écrit Na<sup>+</sup> (aq) + Cl<sup>-</sup> (aq)<br/>
	Une solution de chlorure de calcium contient les ions Ca<sup>2+</sup> et Cl<sup>-</sup>, la solution s'écrit Ca<sup>2+</sup> (aq) + 2Cl<sup>-</sup> (aq)
	</p>
	<div class="sousPartie">C) Solution saturée</div><hr/>
	<p>Pour une température donnée, il existe une masse maximale que l'on peut dissoudre pour préparer 1L de solution. Au delà de cette masse, le soluté ne se dissous plus et la solution est saturée.
	</p>
	<div class="sousPartie">D) Concentration</div><hr/>
	<p>Les liquides corporels (ex : le sang) sont des solutions aqueuses qui contiennent de nombreux solutés ioniques et moléculaires. Ils jouent tous un rôle déterminant pour le bon fonctionnement des organes
	et donc sur la santé. Il est donc important de pouvoir mesurer leur quantité. Ainsi, on utilise deux grandeurs : la concentration molaire et la concentration massique.
	</p>
	<div class="sousSousPartie">1) La concentration massique</div><hr/>
	<p>La concentration massique est donnée par C = m/V avec c la concentration massique (g/L), m la masse (g) et V le volume (L)
	</p>
	<div class="sousSousPartie">2) La concentration molaire</div><hr/>
	<p>La concentration massique est donnée par C = n/V avec c la concentration molaire (g/L), n la masse (g) et V le volume (L)
	</p>
	<?php
}
else {
	?>
	<img src="images/cours/galaxie.png" alt="univers" class="imageCours" style="float: right; width:200px; height:200px;"/>
	<em>Rien n'est indifférent, rien n'est impuissant dans l'univers ; un atome peut tout dissoudre, un atome peut tout sauver !</em><br/><br/><br/><br/>
	<img src="images/cours/atome.png" alt="atome" class="imageCours" style="float: left;width:100px; height:100px;margin-right:20px"/>
	L'infiniment petit constitue l'infiniment grand, tout part des ces strucutures : <span class="important">les atomes</span>. Ces atomes peuvent s'assembler pour former des molécules.<br/>
	Un atome est constitué d'un noyau autour duquel les éléctrons sont en mouvement.<br/>
	<br/>L'atome est 100 000 fois plus grand que le noyau. Entre les éléctrons et le noyau, il y a du vide. On dit que l'atome a une <span class="important">structure lacunaire</span>.<br/><br/>
	Le monde dans lequel nous vivont est donc principalement du vide.
	<?php
}
finContent();
finCarte();

debutCarte();
debutListe();

$options = "";
foreach($cours as $num=>$titre){
    $selected = "";
    if(isset($_GET['cours']) && $num == $_GET['cours']){
        $selected="selected";
    }
    $options = $options."<option value=\"?cours=$num\" $selected>$titre</option>";
}
item(['select' => ['numeroCours',$options,'javascript' => 'onchange="document.location = \'sinstruire.php\' + this.options[this.selectedIndex].value;"']]);
echo '<div class="row">';
if($_GET['cours'] > 0) {
	echo '<div class="col-50"><a href="sinstruire.php?cours='.($_GET['cours'] - 1).'"><img src="images/prev.png" alt="prev" style="margin-right: 10px"/></a></div>';
}

if($_GET['cours'] < 5) {
	echo '<div class="col-50"><a href="sinstruire.php?cours='.($_GET['cours'] + 1).'"><img src="images/next.png" alt="next" style="margin-left: 10px"/></a></div>';
}
echo '</div>';
finListe();
finCarte();

include("includes/copyright.php"); ?>