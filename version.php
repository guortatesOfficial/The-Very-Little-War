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

debutCarte("Historique des versions");
echo important('Version 2.0.1.0'); ?>
<p>
- ajout des points d'attaque, de défense et de pillage<br/>
- ajustement des caractéristiques des atomes<br/> 
- ajustement de la demi-vie des atomes<br/>
- révisions des points d'attaque
- ajout des points de victoire pour les joueurs et les alliances
- la partie recommence maintenant tous les mois et n'est plus selon une limite de points 
- les joueurs inactifs ne sont plus placés sur la carte et indiqués en gris dans le classement
- prévisualisation des caractéristiques de la molécule lors de sa création
- formation de molécules en moins d'une seconde autorisé
<br/>
</p>
<?php
echo important('Version 2.0.0.0'); ?>
<p>
Améliorations : <br/>
- ajout de temps de construction des batiments<br/>
- ajout de temps de formation des molécules<br/>
- ajout de la carte et des coordonnées<br/>
- ajout de temps de trajet des attaques<br/>
- attaquer ne coute plus d'énergie<br/>
- attaquer ne donne plus de points<br/>
- révision de l'ensemble des constructions<br/>
- création de l'application pour Android<br/>
- révision totale du design du site<br/>
- nouveau tutoriel<br/>
- ajustements des revenus d'énergie et d'atomes<br/>
- révision des seuils des médailles<br/>
- ajout de plusieurs grades de médailles : bronze, or, argent, emeraude, saphir, rubis, diamant, diamant rouge<br/>
- suppression du terrain<br/>
- bulles d'aide<br/>
- ajout du batiment "Lieur"<br/>
- le nombre de joueurs par équipe est limité à 20<br/>
<br/>
</p>
<?php echo important('Version 1.7.0.0'); ?>
(~50h de travail)<br/><br/>
<p>Bugs corrigés : <br/>
- bug sur les attaques qui ne mettait pas bien à jour les ressources du défenseur<br/>
- bug sur l'actualisation des ressources<br/>
<br/>
Améliorations : <br/>
- changement de l'image de fond<br/>
- changement des images <br/>
- changement de la police d'écriture<br/>
- changement du style des boutons et des zones de saisie<br/>
- changement de l'organisation de la page d'accueil (dernière news sur le côté, pas de pubs, images sur le côté)<br/>
- ajout de l'indication du nombre de joueurs actifs sur la page d'accueil<br/>
- ajout de la surveillance de multi-comptes sur la page de modération<br/>
- l'hydrogène sert désormais à détruire les bâtiments adverses au lieu d'être de la capcaité de pillage<br/>
- ajout de points de vie pour les bâtiments<br/>
- changement du texte des tutoriels<br/><br/>
</p> <?php
echo important('Version 1.6.0.0'); ?>
<p>Bugs corrigés : <br/>
- bug l'actualisation des ressources<br/>
- bug sur le marché qui ne marchait pas correctement ;)<br/>
- bug sur le temps des parties enregistrées dans les archives<br/>
<br/>
Améliorations : <br/>
- changement du système de combat avec ajout de points de vie<br/>
- ajout du gain de ressources par heure dans la barre des ressources<br/>
- ajout du brome (points de vie de la molécule)<br/>
- ajout de l'iode (génération d'énergie)<br/>
- baisse du cout de l'attaque et de l'espionnage dans le but de promouvoir les combats (jeu trop statique)<br/>
<br/>
</p>
<?php echo important('Version 1.5.0.0'); ?>
(~50h de travail)<br/><br/>
<p>Bugs corrigés : <br/>
- bug sur les attaques qui ne mettait pas bien à jour les ressources du défenseur<br/>
- bug sur l'actualisation des ressources<br/>
<br/>
Améliorations : <br/>
- changement de l'image de fond<br/>
- changement des images <br/>
- changement de la police d'écriture<br/>
- changement du style des boutons et des zones de saisie<br/>
- changement de l'organisation de la page d'accueil (dernière news sur le côté, pas de pubs, images sur le côté)<br/>
- ajout de l'indication du nombre de joueurs actifs sur la page d'accueil<br/>
- ajout de la surveillance de multi-comptes sur la page de modération<br/>
- l'hydrogène sert désormais à détruire les bâtiments adverses au lieu d'être de la capcaité de pillage<br/>
- ajout de points de vie pour les bâtiments<br/>
- changement du texte des tutoriels<br/><br/>
</p>
<?php echo important('Version 1.4.3.0'); ?>
<p>Bugs corrigés : <br/>
- bug de la sélection des joueurs pour une attaque<br/>
- plus de deconnexion lorsque l'on souhaite voir le descriptif des versions<br/>
- correction de la formule de calcul du maximum de terrain que l'on peut conquérir<br/>
<br/>
Améliorations : <br/>
- création d'un bouton "max" pour remplir le champ de construction des molécules<br/>
- amélioration du forum (nombre de messages, nombre de sujets, mise en valeur des sujets avec un nouveau message)<br/>
- ajout de fonctions de modération du forum (et du statut de modérateur)<br/>
- récapitulatif des ressources sur la page marché
</p>
<?php echo important('Version 1.4.2.0'); ?>
<p>Bugs corrigés : <br/>
- bug du marché fou<br/>
- bug au niveau de recherche du joueur<br/>
- résolution de methodes de triche<br/>
<br/>
Améliorations : <br/>
- fonction rechercher un joueur dans le classement<br/>
- tableau de résumé des troupes dans la page "Armée"<br/>
- possibilité de supprimer tous les messages<br/>
- passage de 5 classe de molécules à 4<br/>
- dans le forum, les sujets verouillés se placent à la fin<br/>
- affichage de notre place dans le forum en haut<br/>
- message à tous les membres de l'alliance du joueur<br/>
- rajout des espaces entre les chiffres<br/>
- rajout d'un batiment spécial pour les alliances et du don pour les alliances<br/>
- rajout des images pour les constructions<br/>
- rajout des pactes et des guerres entre alliances
</p>
<?php echo important('Version 1.4.1.0'); ?>
<p>Bugs corrigés : <br/>
- on se déconnectait lorsque l'on essayait de poster un message sur le forum
<br/><br/>
Améliorations : <br/>
- les attaques peuvent maintenant rapporter des points variables allant de 1 à 5 en fonction de l'adversaire<br/>
- les messages du forum peuvent être éditer<br/>
- ajout de l'espionnage<br/>
- ajout de la médaille l'explosif<br/>
- ajout des bonus des médailles<br/>
- ajout du troll de diamant<br/>
- les sous-chefs sont créés et peuvent aussi inviter<br/>
</p>
<?php echo important('Version 1.4.0.0'); ?>
<p>Bugs corrigés : <br/>
- certaines alliance ne se supprimaient pas<br/><br/>

Améliorations : <br/>
- ajout de la médaille du troll chosie au hasard au début<br/>
- ajout dans le classement du plus haut bâtiment de chaque joueur<br/>
- ajout d'un nouveau bâtiment : le stabilisateur permettant de réduire le pourcentage de pertes de molécules<br/>
- ajout du marché permettant d'envoyer des ressources et d'en échanger<br/>
- ajout de [Réponse] directement dans le titre lors d'une réponse<br/>
- ajout de l'indication sujet lu/non-lu pour les joueurs connectés
</p>
<?php echo important('Version 1.3.0.0'); ?>
<p>Bugs corrigés : <br/>
- le trop-plein de ressources après validation du tutoriel n'était mis à zéro qu'une fois la page rafraichie<br/>
- dans la liste des derniers connectés s'affichait aussi la liste des derniers attaqués<br/>
- on ne pouvait pas supprimer son alliance ou son profil<br/><br/>

Améliorations : <br/>
- on ne peut pas supprimer son compte dans la première semaie après l'inscription<br/>
- la police des titres à été changée
- ajout des médailles
- ajout du bbcode
- la mission en cours s'affiche maintenant en haut en dessous des ressources
</p>
<?php echo important('Version 1.2.0.0'); ?>
<p>
Bugs corrigés : <br/>
Aucun<br/><br/>

Améliorations : <br/>
- mise en place de tableaux dans les messages, les forums et les rapports<br/>
- indication du coût d'une molécule<br/>
- indication du nombre maximum de molécules pouvant être créées<br/>
- indication du nombre maximum de terrain pouvant être conquis<br/>
- icônes des ressources changées<br/>
- on ne peut plus flood de messages (dédicace spéciale à Virious)<br/>
- création d'un rapport indiquant le nombre de molécules perdues<br/>
- puissance d'attaque et de défense exponentielle<br/>
- baisse générale de toutes les ressources, des coûts et des productions
</p>
<?php echo important('Version 1.1.1.0'); ?>
<p>
Bugs corrigés : <br/>
- on ne pouvait pas attaquer sous firefox<br/>
- bug au niveau de l'envoi des messages<br/>
- bug des combats (pertes négatives dans certains cas et aucune dans d'autres)<br/><br/>

Améliorations : <br/>
- la date de dernière connexion est affichée sur le profil du joueur<br/>
- paragraphe "comprendre le jeu" dans le tutoriel rajouté<br/>
- image de fond changée<br/>
- on ne se déconnecte plus en allant sur des pages publiques<br/>
- ajout du chapitre "Les molécules" dans S'instruire<br/>
- créer une classe de molécule est plus cher<br/>
- ajout de la page des dix derniers connectés<br/>
- image de fond ajoutée pour les titres
</p>
<?php echo important('Version 1.1.0.0'); ?>
<p>
Bugs corrigés : <br/>
- bug au niveau du combat quand les unités n'avaient aucune attaque ou aucune défense<br/>
- bug au niveau de la suppression du compte<br/>
- bug au niveau de la deconnexion<br/>
- dates mauvaises<br/><br/>

Améliorations : <br/>
- tutoriel mis en place<br/>
- charte de confidentialité<br/>
- pagination des messages et ils peuvent maintenant être supprimés<br/>
- pagination des rapports et ils peuvent maintenant être supprimés<br/>
- on peut choisir les classes de molécules envoyées au combat<br/>
- mise en place de bulles d'aide<br/>
- si les ressources dépassent 1000 elles sont mises en K (1000 : 1K)<br/>
- les attaques coûtent deux fois moins chères<br/>
- images ajoutées<br/>
- refonte compléte du design<br/>
- changement de la description sur la page d'accueil
</p>
<?php echo important('Version 1.0.0.0'); ?>
<p>
Cette version est la version de base.
</p>
<?php
finCarte();
include("includes/copyright.php"); ?>
