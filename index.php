<?php
session_start();
$_SESSION['start'] = "start"; // Sert a savoir si il faut de nouveau ouvrir une nouvelle session ou non
if (isset($_SESSION['login'])) {
    include("includes/basicprivatephp.php");
} else {
    include("includes/basicpublicphp.php");
}


if (isset($_GET['inscrit'])) {
    $_GET['inscrit'] = mysqli_real_escape_string($base, (stripslashes(antihtml(trim($_GET['inscrit'])))));
    if ($_GET['inscrit'] == 1) {
        $information = "Vous avez bien été inscrit";
    } else {
        $erreur = "Ca t'amuse de changer la barre URL ?";
    }
}

if (isset($_GET['att'])) {
    $_GET['att'] = mysqli_real_escape_string($base, (stripslashes(antihtml(trim($_GET['att'])))));
    if ($_GET['att'] == 1) {
        $erreur = "Un visiteur s'est inscrit il y a moins d'une minute, veuillez attendre s'il vous plait puis réessayez (anti-bot)";
    } else {
        $erreur = "Ca t'amuse de changer la barre URL ?";
    }
}

include("includes/tout.php");

if (!isset($_SESSION['login'])) {
    debutCarte("Connexion", "background-color:#8A0000");
    debutListe();
    echo '<form action="index.php?noAutoConnexion=1" method="post" name="connexion">';
    item(['floating' => true, 'titre' => 'Login', 'media' => '<img src="images/accueil/player.png" alt="user" class="w32"/>', 'input' => '<input type="text" name="loginConnexion" id="loginConnexion">']);
    item(['floating' => true, 'titre' => 'Mot de passe', 'media' => '<img src="images/accueil/door-key.png" alt="lock" class="w32"/>', 'input' => '<input type="password" name="passConnexion" id="passConnexion">']);
    finListe();
    echo '<br/><p class="buttons-row">' . submit(['form' => 'connexion', 'titre' => 'Connexion', 'id' => 'boutonConnexion']) . submit(['link' => 'comptetest.php?inscription=1', 'titre' => 'Tester']);
    echo '</p>';
    echo '</form>';
    finCarte();
}

$retour = mysqli_query($base, 'SELECT * FROM news ORDER BY id DESC LIMIT 0, 1');
$nb = mysqli_num_rows($retour);
if ($nb == 0) {
    $donnees['titre'] = "Aucun news";
    $contenuNews = 'Aucune news pour l\'instant.';
}
$donnees = mysqli_fetch_array($retour);
$contenuNews = nl2br(stripslashes($donnees['contenu']));

debutCarte();
debutAccordion();
itemAccordion($donnees['titre'], '<img src="images/accueil/newspaper.png" width="44">', $contenuNews);
finAccordion();
finCarte();

debutCarte("The Very Little War", "", 'images/accueil/wallpaper.jpg');
debutContent(); ?>
<center>Depuis la nuit des temps, les atomes se livrent une guerre sans fin...</center><br /><br />
<img alt="so" src="images/accueil/azote.png" class="imageAtome" /><img alt="so" src="images/accueil/carbone.png" style="float:right" class="imageAtome" />
<div style="text-align:left;"><span style="color:#0024A7" class="atome">azote</span></div>
<div style="text-align:right;"><span class="atome">carbone</span></div>

<div style="margin-left:15%"><span style="color:#AF0000;" class="atome">oxygene</span><img alt="so" src="images/accueil/oxygene.png" class="imageAtome" /></div><img alt="so" src="images/accueil/hydrogene.png" style="float:right" class="imageAtome" />
<div style="text-align:right"><span style="color:lightGray;" class="atome">Hydrogene</span></div><br /><br />
<div style="margin-right:15%"><span style="color:#F9B106;" class="atome">soufre</span><img alt="so" src="images/accueil/soufre.png" class="imageAtome" /></div>

<br />
<div style="margin-left:5%;text-align:middle"><span style="color:#087625;" class="atome">chlore</span><img alt="so" src="images/accueil/chlore.png" class="imageAtome" /></div>

<br />
<div style="margin-left:40%">
    <span style="color:#FF3C54;" class="atome">iode</span> <span style="color:#693C25;" class="atome">brome</span>
</div>
<div style="margin-left:40%">
    <img alt="so" src="images/accueil/iode.png" style="float:left;" class="imageAtome" />
    <img alt="so" src="images/accueil/brome.png" style="text-align:middle;" class="imageAtome" />
</div>
<br />
<br /><br /><br />
<center>Prenez part à ce combat éternel en contrôlant votre propre armée de molécules !<br /><br /><img src="images/icone.png" style="height:50px;width:50px" alt="atome" /><br /><br />
    Rejoignez une communauté investie autour d'un jeu complétement <strong>gratuit</strong>, seule votre stratégie pourra vous sauver ! <br /><br />
    <?php echo submit(['link' => 'inscription.php', 'titre' => 'S\'inscrire']); ?><br />
</center><?php
            finContent();
            finCarte();

            debutCarte();
            debutContent(); ?>
<center><img src="images/accueil/molecules.png" alt="2" class="w32" /></center><br />Créez vos propres molécules à partir des différents atomes : <strong>carbone</strong> pour la défense, <span style="color:red">oxygène</span> pour l'attaque, reste à découvrir les capacités du <span style="color:orange">soufre</span>, <span style="color:marroon">brome</span>, <span style="color:lightGray">hydrogène</span>, <span style="color:fuschia">iode</span> et <span style="color:blue">azote</span> !
<?php
finContent();
finCarte();

debutCarte();
debutContent(); ?>
<center><img src="images/accueil/deal.png" alt="alliance" class="w32" /></center><br /><strong>Alliez-vous</strong> avec d'autres joueurs afin d'obtenir des bonus grâce au duplicateur : l'union fait la force !
<?php
finContent();
finCarte();

debutCarte();
debutContent(); ?>
<center><img src="images/accueil/crown.png" alt="victoire" class="w32" /></center><br />Prenez la tête des 4 différents classements en détruisant vos ennemis et <strong>remportez la victoire</strong> au bout du mois ! Une nouvelle partie recommencera tous les premiers du mois pour permettre de repartir sur un pied d'égalité...<br /><br />
<?php
finContent();
finCarte();

debutCarte();
debutContent(); ?>
<center><img src="images/accueil/agenda.png" alt="1" class="w32" /></center><br />Découvrez le bon côté de la physique, <strong>aucune connaissance scientifique</strong> n'est requise pour ce jeu ! Vous pouvez quand même en apprendre plus grâce aux cours <strong><a href="sinstruire.php">S'instruire</a></strong>.
<?php
finContent();
finCarte();

if (!isset($_SESSION['login']) && !isset($_GET['noAutoConnexion'])) {
?>
    <script>
        document.getElementById("loginConnexion").value = localStorage.getItem("login");
        document.getElementById("passConnexion").value = localStorage.getItem("mdp");

        if (localStorage.getItem("login") != null && localStorage.getItem("mdp") != null) {
            document.connexion.submit();
        }
    </script>
<?php
}
include("includes/copyright.php");
?>