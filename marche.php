<?php
include("includes/basicprivatephp.php");
include("includes/redirectionVacance.php");
//tableau d'échange de ressources

$req = mysqli_query($base, 'SELECT count(*) AS nbActifs FROM membre WHERE derniereConnexion >=\'' . (time() - 2678400) . '\'');
$actifs = mysqli_fetch_array($req);
$volatilite = 0.3 / $actifs['nbActifs'];


$ex = mysqli_query($base, 'SELECT * FROM cours ORDER BY timestamp DESC LIMIT 1'); // cours actuel
$val = mysqli_fetch_array($ex);
$tabCours = explode(",", $val['tableauCours']);


$bool = 1;
foreach ($nomsRes as $num => $ressource) {
    if (!(isset($_POST[$ressource . 'Envoyee']))) {
        $bool = 0;
    }
}
if (isset($_POST['energieEnvoyee']) and $bool == 1 and isset($_POST['destinataire'])) {
    if (!empty($_POST['destinataire'])) {
        $_POST['destinataire'] = antiXSS($_POST['destinataire']);

        $ipd = query('SELECT ip FROM membre WHERE login=\'' . $_POST['destinataire'] . '\'');
        $ipdd = mysqli_fetch_array($ipd);
        $ipm = query('SELECT ip FROM membre WHERE login=\'' . $_SESSION['login'] . '\'');
        $ipmm = mysqli_fetch_array($ipm);

        if ($ipmm['ip'] != $ipdd['ip']) {
            if (empty($_POST['energieEnvoyee'])) {
                $_POST['energieEnvoyee'] = 0;
            }
            $_POST['energieEnvoyee'] = transformInt($_POST['energieEnvoyee']);
            $_POST['energieEnvoyee'] = antiXSS($_POST['energieEnvoyee']);

            foreach ($nomsRes as $num => $ressource) {
                if (empty($_POST[$ressource . 'Envoyee'])) {
                    $_POST[$ressource . 'Envoyee'] = 0;
                }
                $_POST[$ressource . 'Envoyee'] = transformInt($_POST[$ressource . 'Envoyee']);
                $_POST[$ressource . 'Envoyee'] = antiXSS($_POST[$ressource . 'Envoyee']);
            }
            $bool = 1;
            foreach ($nomsRes as $num => $ressource) {
                if (!(preg_match("#^[0-9]*$#", $_POST[$ressource . 'Envoyee']))) {
                    $bool = 0;
                }
            }
            if (preg_match("#^[0-9]*$#", $_POST['energieEnvoyee']) and $bool == 1) {
                $ex = mysqli_query($base, 'SELECT count(*) AS joueurOuPas FROM membre WHERE login=\'' . $_POST['destinataire'] . '\'');
                $verification = mysqli_fetch_array($ex);
                if ($verification['joueurOuPas'] == 1) {
                    $ex = mysqli_query($base, 'SELECT * FROM ressources WHERE login=\'' . $_SESSION['login'] . '\'');
                    $ressources = mysqli_fetch_array($ex);

                    $bool = 1;
                    foreach ($nomsRes as $num => $ressource) {
                        if ($ressources[$ressource] < $_POST[$ressource . 'Envoyee']) {
                            $bool = 0;
                        }
                    }
                    if ($ressources['energie'] >= $_POST['energieEnvoyee'] and $bool == 1) {
                        $ex = mysqli_query($base, 'SELECT * FROM constructions WHERE login=\'' . $_POST['destinataire'] . '\'');
                        $constructionsJoueur = mysqli_fetch_array($ex);

                        if ($revenuEnergie >= revenuEnergie($constructionsJoueur['generateur'], $_POST['destinataire'])) {
                            $rapportEnergie = revenuEnergie($constructionsJoueur['generateur'], $_POST['destinataire']) / $revenuEnergie;
                        } else {
                            $rapportEnergie = 1;
                        }

                        $revenusJoueur = explode(";", $constructionsJoueur['pointsProducteur']);
                        foreach ($nomsRes as $num => $ressource) {
                            if ($revenu[$ressource] >= revenuAtome($revenusJoueur[$num], $_POST['destinataire'])) {
                                ${'rapport' . $ressource} = revenuAtome($revenusJoueur[$num], $_POST['destinataire']) / $revenu[$ressource];
                            } else {
                                ${'rapport' . $ressource} = 1;
                            }
                        }


                        $ressourcesEnvoyees = '';
                        $ressourcesRecues = '';

                        foreach ($nomsRes as $num => $ressource) {
                            $ressourcesEnvoyees = $ressourcesEnvoyees . $_POST[$ressource . 'Envoyee'] . ";";
                            $ressourcesRecues = $ressourcesRecues . (${'rapport' . $ressource} * $_POST[$ressource . 'Envoyee']) . ";";
                        }


                        $ex = mysqli_query($base, 'SELECT x,y FROM membre WHERE login=\'' . $_POST['destinataire'] . '\'');
                        $joueur = mysqli_fetch_array($ex);

                        $distance = pow(pow($membre['x'] - $joueur['x'], 2) + pow($membre['y'] - $joueur['y'], 2), 0.5);

                        $ressourcesEnvoyees = $ressourcesEnvoyees . $_POST['energieEnvoyee'];
                        $ressourcesRecues = $ressourcesRecues . $rapportEnergie * $_POST['energieEnvoyee'];

                        $distance =
                            query('INSERT INTO actionsenvoi VALUES(default,"' . $_SESSION['login'] . '","' . $_POST['destinataire'] . '","' . $ressourcesEnvoyees . '","' . $ressourcesRecues . '","' . (time() + round(3600 * $distance / $vitesseMarchands)) . '")');


                        $chaine = "";
                        foreach ($nomsRes as $num => $ressource) {
                            $plus = "";
                            if ($num < $nbRes) {
                                $plus = ",";
                            }
                            $chaine = $chaine . '' . $ressource . '=\'' . ($ressources[$ressource] - $_POST[$ressource . 'Envoyee']) . '\'' . $plus;
                        }
                        mysqli_query($base, 'UPDATE ressources SET energie=\'' . ($ressources['energie'] - $_POST['energieEnvoyee']) . '\',' . $chaine . ' WHERE login=\'' . $_SESSION['login'] . '\'');

                        $chaine = "";
                        foreach ($nomsRes as $num => $ressource) {
                            if ($_POST[$ressource . 'Envoyee'] > 0) {
                                $chaine = $chaine . ' ' . number_format($_POST[$ressource . 'Envoyee'], 0, ' ', ' ') . ' <img class="imageAide" src="images/' . $ressource . '.png" alt="' . $ressource . '"/>';
                            }
                        }
                        if ($_POST['energieEnvoyee'] > 0) {
                            $information = "Vous avez envoyé " . number_format($_POST['energieEnvoyee'], 0, ' ', ' ') . "<img class=\"imageAide\" src=\"images/energie.png\" alt=\"energie\"/> " . $chaine . ' à ' . $_POST['destinataire'];
                        } else {
                            $information = "Vous avez envoyé " . $chaine . " à " . $_POST['destinataire'];
                        }
                    } else {
                        $erreur = "Vous n'avez pas assez de ressources.";
                    }
                } else {
                    $erreur = "Le destinataire n'existe pas.";
                }
            } else {
                $erreur = "Seul des nombres entiers et positifs doivent être entrés.";
            }
        } else {
            $erreur = "Impossible d'envoyer des ressources à ce joueur. Même adresse IP.";
        }
    } else {
        $erreur = "Vous n'avez pas entré de destinataire.";
    }
}

if (isset($_POST['typeRessourceAAcheter']) and isset($_POST['nombreRessourceAAcheter'])) {
    $_POST['nombreRessourceAAcheter'] = antiXSS(transformInt($_POST['nombreRessourceAAcheter']));
    $_POST['typeRessourceAAcheter'] = antiXSS($_POST['typeRessourceAAcheter']);
    if (!empty($_POST['nombreRessourceAAcheter']) and preg_match("#^[0-9]*$#", $_POST['nombreRessourceAAcheter'])) {
        $bool = 1;
        $numRes = -1;
        foreach ($nomsRes as $num => $ressource) {
            if ($_POST['typeRessourceAAcheter'] == $ressource) {
                $bool = 0;
                $numRes = $num;
            }
        }
        if ($bool == 0) { // verification que c'est une ressource qui existe
            $coutAchat = round($tabCours[$numRes] * $_POST['nombreRessourceAAcheter']);
            $diffEnergieAchat = $ressources['energie'] - $coutAchat;
            if ($diffEnergieAchat >= 0) {
                $ex = query('UPDATE ressources SET energie=' . ($diffEnergieAchat) . ', ' . $nomsRes[$numRes] . '=' . ($ressources[$nomsRes[$numRes]] + $_POST['nombreRessourceAAcheter']) . ' WHERE login=\'' . $_SESSION['login'] . '\'');

                $chaine = '';
                foreach ($tabCours as $num => $cours) {
                    if ($num < sizeof($tabCours) - 1) {
                        $fin = ",";
                    } else {
                        $fin = "";
                    }

                    if ($numRes == $num) {
                        $ajout = $tabCours[$num] + $volatilite * $_POST['nombreRessourceAAcheter'] / $placeDepot;
                        $chaine = $chaine . $ajout . $fin;
                    } else {
                        $chaine = $chaine . $tabCours[$num] . $fin;
                    }
                }

                mysqli_query($base, 'INSERT INTO cours VALUES (default,"' . $chaine . '","' . time() . '")') or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));

                $information = "Vous avez acheté " . number_format($_POST['nombreRessourceAAcheter'], 0, ' ', ' ') . " <img class=\"imageAide\" src=\"images/" . $_POST['typeRessourceAAcheter'] . ".png\" alt=\"" . $_POST['typeRessourceAAcheter'] . "\"/> pour " . number_format(round($tabCours[$numRes] * $_POST['nombreRessourceAAcheter']), 0, ' ', ' ') . " <img class=\"imageAide\" src=\"images/energie.png\" alt=\"energie\"/>";

                $ex = mysqli_query($base, 'SELECT * FROM cours ORDER BY timestamp DESC LIMIT 0,1'); // cours actuel
                $val = mysqli_fetch_array($ex);
                $tabCours = explode(",", $val['tableauCours']);
            } else {
                $erreur = "Vous n'avez pas assez d'énergie.";
            }
        } else {
            $erreur = "Cette ressource n'existe pas.";
        }
    } else {
        $erreur = "Vous ne devez entrer que des nombre entiers et positifs.";
    }
}

if (isset($_POST['typeRessourceAVendre']) and isset($_POST['nombreRessourceAVendre'])) {
    $_POST['nombreRessourceAVendre'] = antiXSS(transformInt($_POST['nombreRessourceAVendre']));
    $_POST['typeRessourceAVendre'] = antiXSS($_POST['typeRessourceAVendre']);
    if (!empty($_POST['nombreRessourceAVendre']) and preg_match("#^[0-9]*$#", $_POST['nombreRessourceAVendre'])) {
        $bool = 1;
        $numRes = -1;
        foreach ($nomsRes as $num => $ressource) {
            if ($_POST['typeRessourceAVendre'] == $ressource) {
                $bool = 0;
                $numRes = $num;
            }
        }
        if ($bool == 0) { // verification que c'est une ressource qui existe
            if ($ressources[$nomsRes[$numRes]] >= $_POST['nombreRessourceAVendre']) {
                $ex = query('UPDATE ressources SET energie=' . ($ressources['energie'] + round($tabCours[$numRes] * $_POST['nombreRessourceAVendre'])) . ', ' . $nomsRes[$numRes] . '=' . ($ressources[$nomsRes[$numRes]] - $_POST['nombreRessourceAVendre']) . ' WHERE login=\'' . $_SESSION['login'] . '\'');

                $chaine = '';
                foreach ($tabCours as $num => $cours) {
                    if ($num < sizeof($tabCours) - 1) {
                        $fin = ",";
                    } else {
                        $fin = "";
                    }

                    if ($numRes == $num) {
                        $ajout = 1 / (1 / $tabCours[$num] + $volatilite * $_POST['nombreRessourceAVendre'] / $placeDepot);
                        $chaine = $chaine . $ajout . $fin;
                    } else {
                        $chaine = $chaine . $tabCours[$num] . $fin;
                    }
                }

                mysqli_query($base, 'INSERT INTO cours VALUES (default,"' . $chaine . '","' . time() . '")');

                $information = "Vous avez vendu " . number_format($_POST['nombreRessourceAVendre'], 0, ' ', ' ') . " <img class=\"imageAide\" src=\"images/" . $_POST['typeRessourceAVendre'] . ".png\" alt=\"" . $_POST['typeRessourceAVendre'] . "\"/> pour " . number_format(round($tabCours[$numRes] * $_POST['nombreRessourceAVendre']), 0, ' ', ' ') . " <img class=\"imageAide\" src=\"images/energie.png\" alt=\"energie\"/>";

                $ex = mysqli_query($base, 'SELECT * FROM cours ORDER BY timestamp DESC LIMIT 0,1'); // cours actuel
                $val = mysqli_fetch_array($ex);
                $tabCours = explode(",", $val['tableauCours']);
            } else {
                $erreur = "Vous n'avez pas assez d'atomes.";
            }
        } else {
            $erreur = "Cette ressource n'existe pas.";
        }
    } else {
        $erreur = "Vous ne devez entrer que des nombre entiers et positifs.";
    }
}

include("includes/tout.php");

if (!isset($_GET['sub'])) {
    $_GET['sub'] = 0;
}


$ex = mysqli_query($base, 'SELECT * FROM actionsenvoi WHERE envoyeur=\'' . $_SESSION['login'] . '\' OR receveur=\'' . $_SESSION['login'] . '\' ORDER BY tempsArrivee ASC');
$nb = mysqli_num_rows($ex); // pour ne pas voir l'espionnage

if ($nb > 0) {
    debutCarte();
    scriptAffichageTemps();
    echo '<div class="table-responsive"><table>';
    echo '<tr><th>Type</th><th>Joueur</th><th>Temps</th></tr>';

    while ($actionsenvoi = mysqli_fetch_array($ex)) {

        if ($_SESSION['login'] == $actionsenvoi['envoyeur']) { // faire si retour ou non
            echo '<tr><td><img src="images/rapports/envoi.png" class="imageChip" alt="fleche"/></td><td><a href="joueur.php?id=' . $actionsenvoi['receveur'] . '">' . $actionsenvoi['receveur'] . '</a></td><td id="affichage' . $actionsenvoi['id'] . '">' . affichageTemps($actionsenvoi['tempsArrivee'] - time()) . '</td></tr>';
        } else {
            echo '<tr><td><img src="images/rapports/retour.png" class="imageChip" alt="fleche"/></td><td><a href="joueur.php?id=' . $actionsenvoi['envoyeur'] . '">' . $actionsenvoi['envoyeur'] . '</a></td><td id="affichage' . $actionsenvoi['id'] . '">' . affichageTemps($actionsenvoi['tempsArrivee'] - time()) . '</td></tr>';
        }
        echo '
                <script>
                    var valeur' . $actionsenvoi['id'] . ' = ' . ($actionsenvoi['tempsArrivee'] - time()) . ';

                    function tempsDynamique' . $actionsenvoi['id'] . '(){
                        if(valeur' . $actionsenvoi['id'] . ' > 0){
                            valeur' . $actionsenvoi['id'] . ' -= 1;
                            document.getElementById("affichage' . $actionsenvoi['id'] . '").innerHTML = affichageTemps(valeur' . $actionsenvoi['id'] . ');
                        }
                        else {
                            document.location.href="marche.php";
                        }
                    }

                    setInterval(tempsDynamique' . $actionsenvoi['id'] . ', 1000);
                    </script>';
    }
    echo '</table></div>';
    finCarte();
}

if ($_GET['sub'] == 1) {
    debutCarte("Envoyer des ressources" . aide("envoyerRessources"));
    echo 'Cours d\'envoi : <span class="important">1</span> ' . popover("popover-envoiRes", "images/question.png");
    debutListe();
?>
    <form action="marche.php?sub=1" method="post" name="formEnvoyer">

        <?php
        item(['floating' => true, 'input' => '<input type="text" name="energieEnvoyee" id="energieEnvoyee" class="form-control"/>', 'titre' => 'Energie', 'after' => nombreEnergie(number_format($ressources['energie'], 0, ' ', ' '))]);

        foreach ($nomsRes as $num => $ressource) {
            item(['floating' => true, 'input' => '<input type="text" name="' . $ressource . 'Envoyee" id="' . $ressource . 'Envoyee" class="form-control"/>', 'titre' => ucfirst($nomsAccents[$num]), 'after' => nombreAtome($num, number_format($ressources['energie'], 0, ' ', ' '))]);
        }

        item(['floating' => true, 'input' => '<input type="text" name="destinataire" id="destinataire" class="form-control"/>', 'titre' => 'Destinataire']);
        item(['input' => submit(['form' => 'formEnvoyer', 'titre' => 'Envoyer'])]);
        finListe();
        finCarte();

        ?>
        <div class="popover popover-envoiRes">
            <div class="popover-angle"></div>
            <div class="popover-inner">
                <div class="content-block">
                    <p>Cela signifie que les ressources reçues seront égales au rapport entre les revenus des ressources de l'envoyeur et du receveur multiplié par ce coefficient de cours d'envoi.</p>
                </div>
            </div>
        </div>


    <?php
}

if ($_GET['sub'] == 0) {
    debutCarte("Cours");
    echo '<div class="table-responsive" style="overflow-y:hidden"><div id="curve_chart" style="width: 100%; height: 400px"></div></div>';
    finCarte();
    debutCarte("Acheter");
    ?>
        <form action="marche.php?sub=0" method="post" name="formAcheter">
            <?php
            debutListe();
            $options = "";
            foreach ($nomsRes as $num => $ressource) {
                $options = $options . '<option value="' . $ressource . '" data-option-color="' . $couleursSimples[$num] . '" data-option-image="images/petitesImages/' . $ressource . '.png">' . ucfirst($ressource) . '';
            }

            item(['floating' => false, 'select' => ["typeRessourceAAcheter", $options, "javascript" => 'onChange="majAchat(true)"', "hauteur" => 450], 'titre' => 'Atome']);
            item(['floating' => false, 'input' => '<input type="text" name="nombreRessourceAAcheter" id="nombreRessourceAAcheter" class="form-control" onChange="majAchat(true)"/>', 'titre' => 'Nombre']);
            item(['floating' => false, 'input' => '<input type="text" name="coutEnergieAchat" id="coutEnergieAchat" class="form-control" onChange="majAchat(false)"/>', 'titre' => 'Coût en énergie (' . chiffrePetit($ressources['energie']) . ')']);
            item(['input' => submit(['form' => 'formAcheter', 'titre' => 'Acheter', 'image' => 'images/marche/achat.png'])]);
            ?>
        </form>
        <?php
        finListe();
        finCarte();

        debutCarte("Vendre");
        ?>
        <form action="marche.php?sub=0" method="post" name="formVendre">
            <?php
            debutListe();
            $options = "";
            foreach ($nomsRes as $num => $ressource) {
                $options = $options . '<option value="' . $ressource . '" data-option-color="' . $couleursSimples[$num] . '" data-option-image="images/petitesImages/' . $ressource . '.png">' . ucfirst($ressource) . ' (' . chiffrePetit($ressources[$ressource]) . ')</option>';
            }

            item(['floating' => false, 'select' => ["typeRessourceAVendre", $options, "javascript" => 'onChange="majVente(true)"', "hauteur" => 450], 'titre' => 'Atome']);
            item(['floating' => false, 'input' => '<input type="text" name="nombreRessourceAVendre" id="nombreRessourceAVendre" class="form-control" onChange="majVente(true)"/>', 'titre' => 'Nombre']);
            item(['floating' => false, 'input' => '<input type="text" name="apportEnergieVente" id="apportEnergieVente" class="form-control" onChange="majVente(false)"/>', 'titre' => 'Apport en énergie']);
            item(['input' => submit(['form' => 'formVendre', 'titre' => 'Vendre', 'image' => 'images/marche/vente.png'])]);

            ?>
        </form>
        <?php
        finListe();
        finCarte();
        ?>
        <script type="text/javascript">
            // script de calcul des ressources en temps reel (pour l'échange)
            function majAchat(param) {
                var typeRessourceAAcheter = document.getElementById('typeRessourceAAcheter').value;
                var nombreRessourceAAcheter = symboleEnNombre(document.getElementById('nombreRessourceAAcheter').value);
                var coutEnergie = symboleEnNombre(document.getElementById('coutEnergieAchat').value);
                var echange = <?php echo json_encode($tabCours);

                                foreach ($nomsRes as $num => $ressource) { // on récupére le numéro dans le tableau de ressources des ressources que l'on échange
                                    echo '
		if("' . $ressource . '" == typeRessourceAAcheter){
			var numAchat = ' . $num . ';
		}
        ';
                                } ?>

                if (nombreRessourceAAcheter == "") {
                    nombreRessourceAAcheter = 0;
                }
                if (coutEnergie == "") {
                    coutEnergie = 0;
                }

                if (param) {
                    coutEnergie = Math.round(nombreRessourceAAcheter * echange[numAchat]);
                    document.getElementById("coutEnergieAchat").value = coutEnergie;
                } else {
                    nombreRessourceAAcheter = Math.round(coutEnergie / echange[numAchat]);
                    document.getElementById("nombreRessourceAAcheter").value = nombreRessourceAAcheter;
                }
            }

            function majVente(param) {
                var typeRessourceAVendre = document.getElementById('typeRessourceAVendre').value;
                var nombreRessourceAVendre = symboleEnNombre(document.getElementById('nombreRessourceAVendre').value);
                var apportEnergie = symboleEnNombre(document.getElementById('apportEnergieVente').value);
                var echange = <?php echo json_encode($tabCours);

                                foreach ($nomsRes as $num => $ressource) { // on récupére le numéro dans le tableau de ressources des ressources que l'on échange
                                    echo '
		if("' . $ressource . '" == typeRessourceAVendre){
			var numVente = ' . $num . ';
		}
        ';
                                } ?>

                if (nombreRessourceAVendre == "") {
                    nombreRessourceAVendre = 0;
                }
                if (apportEnergie == "") {
                    apportEnergie = 0;
                }

                if (param) {
                    apportEnergie = Math.round(nombreRessourceAVendre * echange[numVente]);
                    document.getElementById("apportEnergieVente").value = apportEnergie;
                } else {
                    nombreRessourceAVendre = Math.round(apportEnergie / echange[numVente]);
                    document.getElementById("nombreRessourceAVendre").value = nombreRessourceAVendre;
                }
            }
        </script>

    <?php
}
    ?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // affichage des cours
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Temps',
                    <?php
                    foreach ($nomsRes as $num => $res) {
                        echo '"' . ucfirst($res) . '",';
                    } ?>
                ],
                <?php
                $tot = '';
                $ex = query("SELECT * FROM cours ORDER BY timestamp DESC LIMIT 0,1000");
                $c = 1;
                $nb = mysqli_num_rows($ex);
                while ($cours = mysqli_fetch_array($ex)) {
                    if ($c != 1) {
                        $fin = ",";
                    } else {
                        $fin = "";
                    }
                    $tot =  '["",' . $cours['tableauCours'] . ']' . $fin . $tot;
                    $c++;
                }

                echo $tot;
                ?>
            ]);

            var options = {
                title: 'Evolution du coût en énergie des ressoures',
                backgroundColor: '#FAFAFA',
                curveType: 'function',
                colors: [<?php foreach ($couleurs as $num => $couleur) {
                                echo '"' . $couleur . '",';
                            } ?>],
                legend: {
                    position: 'bottom'
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }
    </script>


    <?php
    include("includes/copyright.php"); ?>