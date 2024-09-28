<?php 
session_start();
$_SESSION['start'] = "start"; // Sert a savoir si il faut de nouveau ouvrir une nouvelle session ou non
if(isset($_SESSION['login']))
{
	include("includes/basicprivatephp.php");
}
else
{
	include("includes/basicpublicphp.php"); 
}

include("includes/tout.php");
debutCarte("Conditions d'Utilisation");
debutContent();
?>
<?php echo important('ARTICLE 1 : Objet'); ?>
Les présentes « conditions générales d'utilisation » ont pour objet l'encadrement juridique des modalités de mise à disposition des services du site The Very Little War et leur utilisation par « l'Utilisateur ».
Les conditions générales d'utilisation doivent être acceptées par tout Utilisateur souhaitant accéder au site. Elles constituent le contrat entre le site et l'Utilisateur. L’accès au site par l’Utilisateur signifie son acceptation des présentes conditions générales d’utilisation.<br/>
Éventuellement :
En cas de non-acceptation des conditions générales d'utilisation stipulées dans le présent contrat, l'Utilisateur se doit de renoncer à l'accès des services proposés par le site.
The Very Little War se réserve le droit de modifier unilatéralement et à tout moment le contenu des présentes conditions générales d'utilisation.<br/><br/>
<?php echo important('ARTICLE 2 : Mentions légales'); ?>
L'édition du site The Very Little War est assurée par Indebrow dont Le Directeur de la publication est Monsieur PRIE Enguerrand.<br/><br/>
<?php echo important('ARTICLE 3 : Définitions'); ?>
La présente clause a pour objet de définir les différents termes essentiels du contrat :<br/>
Utilisateur : ce terme désigne toute personne qui utilise le site ou l'un des services proposés par le site.<br/>
Contenu utilisateur : ce sont les données transmises par l'Utilisateur au sein du site.<br/>
Membre : l'Utilisateur devient membre lorsqu'il est identifié sur le site.<br/>
Identifiant et mot de passe : c'est l'ensemble des informations nécessaires à l'identification d'un Utilisateur sur le site. L'identifiant et le mot de passe permettent à l'Utilisateur d'accéder à des services réservés aux membres du site. Le mot de passe est confidentiel.<br/><br/>
<?php echo important('ARTICLE 4 : accès aux services'); ?>
Le site permet à l'Utilisateur un accès gratuit aux services suivants :
jeu ;
forum ;
cours de physique ;<br/>
Le site est accessible gratuitement en tout lieu à tout Utilisateur ayant un accès à Internet. Tous les frais supportés par l'Utilisateur pour accéder au service (matériel informatique, logiciels, connexion Internet, etc.) sont à sa charge.<br/>
Selon le cas :<br/>
L’Utilisateur non membre n'a pas accès aux services réservés aux membres. Pour cela, il doit s'identifier à l'aide de son identifiant et de son mot de passe.
Le site met en œuvre tous les moyens mis à sa disposition pour assurer un accès de qualité à ses services. L'obligation étant de moyens, le site ne s'engage pas à atteindre ce résultat.
Tout événement dû à un cas de force majeure ayant pour conséquence un dysfonctionnement du réseau ou du serveur n'engage pas la responsabilité de The Very Little War.
L'accès aux services du site peut à tout moment faire l'objet d'une interruption, d'une suspension, d'une modification sans préavis pour une maintenance ou pour tout autre cas. L'Utilisateur s'oblige à ne réclamer aucune indemnisation suite à l'interruption, à la suspension ou à la modification du présent contrat.<br/><br/>
<?php echo important('ARTICLE 5 : Propriété intellectuelle'); ?>
Les marques, logos, signes et tout autre contenu du site font l'objet d'une protection par le Code de la propriété intellectuelle et plus particulièrement par le droit d'auteur.
L'Utilisateur sollicite l'autorisation préalable du site pour toute reproduction, publication, copie des différents contenus.
L'Utilisateur s'engage à une utilisation des contenus du site dans un cadre strictement privé. Une utilisation des contenus à des fins commerciales est strictement interdite.
Tout contenu mis en ligne par l'Utilisateur est de sa seule responsabilité. L'Utilisateur s'engage à ne pas mettre en ligne de contenus pouvant porter atteinte aux intérêts de tierces personnes. Tout recours en justice engagé par un tiers lésé contre le site sera pris en charge par l'Utilisateur.
Le contenu de l'Utilisateur peut être à tout moment et pour n'importe quelle raison supprimé ou modifié par le site. L'Utilisateur ne reçoit aucune justification et notification préalablement à la suppression ou à la modification du contenu Utilisateur.<br/><br/>
<?php echo important('ARTICLE 6 : Données personnelles'); ?>
Les informations demandées à l’inscription au site sont nécessaires et obligatoires pour la création du compte de l'Utilisateur. En particulier, l'adresse électronique pourra être utilisée par le site pour l'administration, la gestion et l'animation du service.
Le site assure à l'Utilisateur une collecte et un traitement d'informations personnelles dans le respect de la vie privée conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés.
En vertu des articles 39 et 40 de la loi en date du 6 janvier 1978, l'Utilisateur dispose d'un droit d'accès, de rectification, de suppression et d'opposition de ses données personnelles. L'Utilisateur exerce ce droit via :
son espace personnel ;
un formulaire de contact ;
par mail à e.prie@yahoo.fr ;<br/><br/>
<?php echo important('ARTICLE 7 : Responsabilité et force majeure'); ?>
Les sources des informations diffusées sur le site sont réputées fiables. Toutefois, le site se réserve la faculté d'une non-garantie de la fiabilité des sources. Les informations données sur le site le sont à titre purement informatif. Ainsi, l'Utilisateur assume seul l'entière responsabilité de l'utilisation des informations et contenus du présent site.
L'Utilisateur s'assure de garder son mot de passe secret. Toute divulgation du mot de passe, quelle que soit sa forme, est interdite.
L'Utilisateur assume les risques liés à l'utilisation de son identifiant et mot de passe. Le site décline toute responsabilité.
Tout usage du service par l'Utilisateur ayant directement ou indirectement pour conséquence des dommages doit faire l'objet d'une indemnisation au profit du site.
Une garantie optimale de la sécurité et de la confidentialité des données transmises n'est pas assurée par le site. Toutefois, le site s'engage à mettre en œuvre tous les moyens nécessaires afin de garantir au mieux la sécurité et la confidentialité des données.
La responsabilité du site ne peut être engagée en cas de force majeure ou du fait imprévisible et insurmontable d'un tiers.<br/><br/>
<?php echo important('ARTICLE 8 : Liens hypertextes'); ?>
De nombreux liens hypertextes sortants sont présents sur le site, cependant les pages web où mènent ces liens n'engagent en rien la responsabilité de Thev Very Little War qui n'a pas le contrôle de ces liens.
L'Utilisateur s'interdit donc à engager la responsabilité du site concernant le contenu et les ressources relatives à ces liens hypertextes sortants.<br/><br/>
<?php echo important('ARTICLE 9 : Évolution du contrat'); ?>
Le site se réserve à tout moment le droit de modifier les clauses stipulées dans le présent contrat.<br/><br/>
<?php echo important('ARTICLE 10 : Durée'); ?>
La durée du présent contrat est indéterminée. Le contrat produit ses effets à l'égard de l'Utilisateur à compter de l'utilisation du service.<br/><br/>
<?php echo important('ARTICLE 11 : Droit applicable et juridiction compétente'); ?>
La législation française s'applique au présent contrat. En cas d'absence de résolution amiable d'un litige né entre les parties, seuls les tribunaux sont compétents.<br/><br/>
<?php echo important('ARTICLE 12 : Publication par l’Utilisateur'); ?>
Le site permet aux membres de publier des commentaires et d'envoyer des messages personnels.
Dans ses publications, le membre s’engage à respecter les règles de la Netiquette et les règles de droit en vigueur.
Le site exerce une modération a posteriori sur les publications et se réserve le droit de refuser leur mise en ligne, sans avoir à s’en justifier auprès du membre.
Le membre reste titulaire de l’intégralité de ses droits de propriété intellectuelle. Mais en publiant une publication sur le site, il cède à la société éditrice le droit non exclusif et gratuit de représenter, reproduire, adapter, modifier, diffuser et distribuer sa publication, directement ou par un tiers autorisé, dans le monde entier, sur tout support (numérique ou physique), pour la durée de la propriété intellectuelle. Le Membre cède notamment le droit d'utiliser sa publication sur internet et sur les réseaux de téléphonie mobile.
La société éditrice s'engage à faire figurer le nom du membre à proximité de chaque utilisation de sa publication.<br/><br/>
<?php echo important('ARTICLE 13 : Bug'); ?>
L'exploitation de tout bug découvert sur The Very Little War est interdite. Ce dernier doit être signalé à la section "Bug" du forum.

<?php
finContent();
finCarte();
include("includes/copyright.php"); ?>