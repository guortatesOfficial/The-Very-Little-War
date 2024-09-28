<?php
include("connexion.php"); //connexion à la base

function pasDeRisques($chaine)
{
    return htmlspecialchars($chaine);
}

function query($a)
{
    global $base;
    $v = mysqli_query($base, $a) or die('Erreur SQL !<br />' . mysql_error());
    return $v;
}

if (isset($_POST['soumettre'])) {
    $correctionLieu = strtoupper(pasDeRisques($_POST['correctionLieu']));
    $lieu = strtoupper(pasDeRisques($_POST['lieu']));

    $correctionCommune = $correctionLieu = strtoupper(pasDeRisques($_POST['correctionCommune']));
    $commune = strtoupper(pasDeRisques($_POST['commune']));

    if (!empty($commune)) {
        query('INSERT INTO communes VALUES(default,"' . $commune . '","' . $correctionCommune . '")');
    }
    if (!empty($lieu)) {
        query('INSERT INTO lieux VALUES(default,"' . $lieu . '","' . $correctionLieu . '")');
    }
}
?>


<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Mise en forme CAJ</title> <!-- STYLE DU TABLEAU -->
    <style>
        table {
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <!-- FORMULAIRE HTML TRAITÉ EN JAVASCRIPT -->
    <h1>Formulaire de mise en forme</h1>
    <div id="formulaire">
        <!-- <form action="mise en forme.html" method="post"> -->
        <textarea id="texte" rows="60" cols="100"></textarea>
        <input type="submit" id="soumettre" value="Mettre en forme" />
        <!-- </form> -->
    </div>
    <div id="texteFinal">
        <table id="tableau">
            <tr>
                <th>UNA</th>
                <th>UNITE</th>
                <th>COMPAGNIE</th>
                <th>LIEU</th>
                <th>SEMAINE</th>
                <th>MOIS</th>
                <th>JOUR DEBUT</th>
                <th>JOUR FIN</th>
                <th>PERIODE</th>
                <th>CRENEAU</th>
                <th>COMMUNE</th>
                <th>VEHICULE SUSPECT</th>
                <th>INDIVIDU(S) SUSPECT(S)</th>
                <th>VEHICULE VOLE</th>
                <th>MANOP</th>
                <th>OBJETS VOLES</th>
                <th>AUTRES</th>
            </tr>
            <tr id="resultat">
            </tr>
            <tr id="correction">
                <form action="miseenforme.php" method="post">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="hidden" name="lieu" id="lieu" />
                        <label for="correctionLieu">Correction du lieu :</label>
                        <input type="text" id="correctionLieu" name="correctionLieu" />
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="hidden" name="commune" id="commune" />
                        <label for="correctionCommune">Correction de la commune :</label>
                        <input type="text" id="correctionCommune" name="correctionCommune" />
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><input type="submit" name="soumettre" value="Corriger" /></td>
                </form>
            </tr>
        </table>
    </div>
</body>

<script type="application/javascript">
    var textarea = document.getElementById("texte");
    document.getElementById("soumettre").addEventListener("click", function() {

        function siNonNul(regex) { //focntion pour éviter les erreurs si la regex trouve rien
            if (regex != null) {
                regex = regex[0];
            } else {
                regex = "";
            }

            return regex;
        }


        var motsCles = "";
        var valeurTexte = textarea.value; // la valeur de ce qu'il y a dans le textarea
        var fait = siNonNul(/Natinf.+(\n.+){3}/.exec(valeurTexte)); // le fait + lieu
        fait = fait.split("\n"); // on sépare pour avoir les différentes lignes
        var una = siNonNul(/UNA\s+[0-9]{5}\/[0-9]{5}\/[0-9]{4}/.exec(valeurTexte)); // UNA
        una = una.replace(/UNA\s{2}/, ""); // on obtient juste le numéro
        una = una.trim(); // on enlève les espaces inutiles

        var unite = siNonNul(/Unité saisie\s+.+\s/.exec(valeurTexte)); // unité saisie
        unite = unite.replace(/Unité saisie\s{2}/, "");
        unite = unite.toUpperCase();
        unite = unite.replace("-", " "); // on formate
        unite = unite.replace("SAINT", "ST"); //on prends en compte tous les cas possibles pour bien tout formater sous la forme COB/BTA NOMCOB NOMBP
        unite = unite.replace("BP", "");
        unite = unite.replace("BTPCL", "");
        unite = unite.replace("BTP", "");
        unite = unite.replace(/COMMUNAUT(É|E) DE BRIGADES DE/, "COB");
        unite = unite.replace("'", " ");
        unite = unite.replace("    ", " ");
        unite = unite.trim();

        <?php
        $ex = query("SELECT * FROM unites");
        while ($data = mysqli_fetch_array($ex)) {
            $data['unite'] = str_replace("COB", "", $data['unite']);
            $data['unite'] = str_replace("BTA", "", $data['unite']);
            $data['unite'] = trim($data['unite']);
            echo "if(/" . $data['unite'] . "/i.test(unite) == true) {
            unite = \"" . $data['unite'] . "\";
            compagnie = \"" . $data['compagnie'] . "\";
        }
        ";
        }

        $ex = query("SELECT * FROM unites WHERE sousunite!=''");
        while ($data = mysqli_fetch_array($ex)) {
            $data['sousunite'] = str_replace("BP", "", $data['sousunite']);
            $data['sousunite'] = trim($data['sousunite']);
            echo "if(/" . $data['sousunite'] . "/i.test(unite) == true){
            unite =\"" . $data['unite'] . "\";
            compagnie = \"" . $data['compagnie'] . "\";
        }";
        }
        ?>

        // type du fait
        var type = fait[0];
        type = type.replace(/Natinf \d+ - /, "");
        type = type.trim();

        // mise en place des mots-clés
        if (type == "TENTATIVE - VOL PAR EFFRACTION DANS UN LOCAL D'HABITATION OU UN LIEU D'ENTREPOT" || type == "TENTATIVE - VOL A L'AIDE D'UNE EFFRACTION") {
            motsCles = "TENTATIVE";
        }

        // on prends tous les types d'objets volés
        var listeVol = /Préjudice(.+\s+)+/.exec(valeurTexte);
        var vehVol = "";
        var vehUt = "";
        if (listeVol != null) {
            listeVol = listeVol[0];
            listeVol = listeVol.split("* ");
            var tabVol = [];
            var tabVeh = []
            for (var i = 1; i < listeVol.length; i++) {
                var add = siNonNul(/([a-z]|é| |è)+ \((volé|utilisé)\)/i.exec(listeVol[i])); // le premier mot avant la parenthèse
                add = add.trim();
                if (add == "Véhicule terrestre (Volé)" || add == "Véhicule terrestre (Utilisé)") { //si le véhicule est volé on prends le modéle, la couleur et la plaque
                    var typeVeh = siNonNul(/Nature : ([A-Z]| |[0-9])+/i.exec(listeVol[i]));
                    typeVeh = typeVeh.replace("Nature : ", "");

                    var plaque = siNonNul(/Numéro d'immatriculation : (([A-Z]{2}(-| )[0-9]{3}(-| )[A-Z]{2})|([0-9]{3}(-| )[A-Z]{3}(-| )[0-9]{2})|([0-9]{4}(-| )[A-Z]{2}(-| )[0-9]{2}))/i.exec(listeVol[i]));
                    plaque = plaque.replace("Numéro d'immatriculation : ", "");

                    var marque = siNonNul(/Marque : ([A-Z]| )+/i.exec(listeVol[i]));
                    marque = marque.replace("Marque : ", "");

                    var modele = siNonNul(/Modèle : ([A-Z]| )+/i.exec(listeVol[i]));
                    modele = modele.replace("Modèle : ", "");

                    var typeCommercial = siNonNul(/Type commercial : ([A-Z]| )+/i.exec(listeVol[i]));
                    typeCommercial = typeCommercial.replace("Type commercial : ", "");

                    var couleurPrincipale = siNonNul(/Couleur principale : ([A-Z]| )+/i.exec(listeVol[i]));
                    couleurPrincipale = couleurPrincipale.replace("Couleur principale : ", "");

                    if (typeCommercial == modele) { //on évite les doublons
                        typeCommercial = "";
                    }

                    var chaine = "- " + typeVeh + " " + marque + " " + modele + " " + typeCommercial + " " + couleurPrincipale + " " + plaque + "<br/>";
                    if (tabVeh.indexOf(chaine) == -1) {
                        if (add == "Véhicule terrestre (Volé)") {
                            vehVol += chaine;
                        } else if (add == "Véhicule terrestre (Utilisé)") {
                            vehUt += chaine;
                        }
                        tabVeh.push(chaine);
                    }

                }

                if (add != "Sensibilité médiatique" && add != "Type de victime" && add != "Mis" && add != "Interpellé" && add != "Placé" && add != "Prolongation garde" && add != "Déferrement judiciaire" && add != "Suite") { // s'il y a un suspect on l'enlève, on verra plus tard
                    add = add.toUpperCase();
                    if (tabVol.indexOf(add) == -1) {
                        tabVol.push(add);
                    }
                }
            }




            for (var i = 0; i < tabVol.length; i++) { // on insére les mots clés
                if (motsCles != "") {
                    motsCles = motsCles + " - " + tabVol[i];
                } else {
                    motsCles = tabVol[i];
                }
            }
        }

        // on cherche le lieu cambriolé


        var adresse = fait[3].replace("-", " "); // adresse de la commission de l'acte
        adresse = adresse.replace("SAINT", "ST");
        adresse = adresse.toUpperCase();
        adresse = adresse.trim();

        var adresseVictime = /Demeurant.+-/.exec(valeurTexte);
        if (adresseVictime != null) { // si la victime a une adresse
            adresseVictime = adresseVictime[0];
            adresseVictime = adresseVictime.split("-");
            adresseVictime = adresseVictime[0];
            adresseVictime = adresseVictime.replace("Demeurant", "");
            adresseVictime = adresseVictime.replace("SAINT", "ST");
            adresseVictime = adresseVictime.replace("-", " ");
            adresseVictime = adresseVictime.toUpperCase();
            adresseVictime = adresseVictime.trim();
        } else {
            adresseVictime = "SANS OBJET";
        }

        //TRAITEMENT DU LIEU

        var lieu = fait[2].replace("- ", "");
        lieu = lieu.trim();


        if (/r(é|e)sidence principale/i.test(valeurTexte)) {
            lieu = "RESIDENCE PRINCIPALE";
        }
        if (/r(é|e)sidence secondaire/i.test(valeurTexte)) {
            lieu = "RESIDENCE SECONDAIRE";
        }


        <?php
        $req = query("SELECT * FROM lieux");
        while ($data = mysqli_fetch_array($req)) {
            echo 'if(lieu == "' . $data['lieuCRPJ'] . '") {
            lieu = "' . $data['classification'] . '";
        }';
        }
        ?>

        if (lieu == "RESIDENCE") { // si l'adresse de la victime est celle de l'adresse du délit alors c'est une résidence principale
            if (adresseVictime == adresse) {
                lieu = "RESIDENCE PRINCIPALE";
            }
        }

        var date = fait[1];
        date = date.replace("Période du", ""); //si une date de fin et de début
        date = date.replace("Le", ""); //si une date unique
        date = date.split("au");


        var debut = date[0].trim();
        if (date[1] == null) {
            date[1] = date[0]; //si une date précise alors on met date de fin = date de début   
        }
        var fin = date[1].trim();

        var tabDebut = debut.split(" ");
        var tabFin = fin.split(" ");

        var jourDebut = tabDebut[0];
        var jourFin = tabFin[0];
        var periode;

        var tabJours = ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"];
        var tabMois = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
        if (jourDebut == jourFin) {
            periode = jourDebut;
        } else {
            var indexDebut = tabJours.indexOf(jourDebut);
            if ((indexDebut + 1) % 7 == tabJours.indexOf(jourFin)) { // le -7 car le % ne marche pas pour les valeurs négatives donc on met tout en négatif (pour le dimanche-lundi)
                periode = jourDebut + " - " + jourFin;
            } else {
                periode = "indéterminée";
            }
        }

        var momentJournee = "indéterminé";
        if (periode != "indéterminée") { // on determine le moment de la journée : matin, après-midi, soir, nuit
            var tableauMoments = [
                [6, 18, "jour", 12],
                [18, 30, "nuit"],
                [6, 12, "matin"],
                [12, 14, "midi"],
                [14, 18, "après-midi"],
                [18, 22, "soirée"]
            ];
            var heureDebut = Number(tabDebut[5]);
            var heureFin = Number(tabFin[5]);
            if (heureFin <= heureDebut) { // pour les calculs toujours heureFin < heureDebut
                heureFin = heureFin + 24;
            }
            var moyenne = ((heureDebut + heureFin) / 2);
            var duree = heureFin - heureDebut;
            console.log(moyenne);
            console.log(duree);
            for (var i = 0; i < tableauMoments.length; i++) {
                if (moyenne >= tableauMoments[i][0] && moyenne <= tableauMoments[i][1] && duree <= (tableauMoments[i][1] - tableauMoments[i][0])) {
                    momentJournee = tableauMoments[i][2];
                }
            }
        }



        var commune = siNonNul(/à([a-z]|-| )+[1-9]/i.exec(adresse));
        commune = commune.replace(/[1-9]/, "");
        commune = commune.replace(/à/i, "");
        commune = commune.trim();

        <?php
        $ex = query("SELECT * FROM communes");
        while ($data = mysqli_fetch_array($ex)) {
            echo "if(/" . $data['commune'] . "/i.test(commune) == true) {
            commune = \"" . $data['correctionCommune'] . "\";
        }
        ";
        }
        ?>

        //SCRIPT IMPORTE de codes-sources.commentcamarche.net/source/41485-numero-de-la-semaine
        Date.prototype.getYearDay = function() { //1 - 366
            var year = this.getFullYear();
            var month = this.getMonth();
            var day = this.getDate();

            var offset = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

            //l'année bissextile n'est utile qu'à partir de mars
            var bissextile = (month < 2) ? 0 : (year % 400 == 0 || (year % 4 == 0 && year % 100 != 0));

            return parseInt(day + offset[month] + bissextile);
        }

        Date.prototype.getMonday = function() {
            var offset = (this.getDay() + 6) % 7;
            return new Date(this.getFullYear(), this.getMonth(), this.getDate() - offset);
        }

        Date.prototype.getWeek = function() { //1 - 53
            var year = this.getFullYear();
            var week;

            //dernier lundi de l'année
            var lastMonday = new Date(year, 11, 31).getMonday();

            //la date est dans la dernière semaine de l'année
            //mais cette semaine fait partie de l'année suivante
            if (this >= lastMonday && lastMonday.getDate() > 28) {
                week = 1;
            } else {
                //premier lundi de l'année
                var firstMonday = new Date(year, 0, 1).getMonday();

                //correction si nécessaire (le lundi se situe l'année précédente)
                if (firstMonday.getFullYear() < year) firstMonday = new Date(year, 0, 8).getMonday();

                //nombre de jours écoulés depuis le premier lundi
                var days = this.getYearDay() - firstMonday.getYearDay();

                //window.alert(days);

                //si le nombre de jours est négatif on va chercher
                //la dernière semaine de l'année précédente (52 ou 53)
                if (days < 0) {
                    week = new Date(year, this.getMonth(), this.getDate() + days).getWeek();
                } else {
                    //numéro de la semaine
                    week = 1 + parseInt(days / 7);

                    //on ajoute une semaine si la première semaine
                    //de l'année ne fait pas partie de l'année précédente
                    week += (new Date(year - 1, 11, 31).getMonday().getDate() > 28);
                }
            }

            return parseInt(week);
        }

        //FIN DU SCRIPT
        var jFin = Number(tabFin[1]);
        var mFin = tabFin[2];
        mFin = tabMois.indexOf(mFin);
        var aFin = Number(tabFin[3]);
        var objDateFin = new Date(aFin, mFin, jFin);
        var numSem = objDateFin.getWeek();

        var ligne = "";

        function ajouterElement(variable) {
            ligne = ligne + "<td>" + variable + "</td>";
        }

        ajouterElement(una);
        ajouterElement(unite);
        ajouterElement(compagnie);
        ajouterElement(lieu);
        document.getElementById("lieu").value = lieu;
        ajouterElement(numSem);
        ajouterElement(tabMois[mFin]);
        ajouterElement(tabDebut[1] + "/" + tabMois.indexOf(tabDebut[2]) + "/" + tabDebut[3]);
        ajouterElement(jFin + "/" + mFin + "/" + aFin);
        ajouterElement(periode);
        ajouterElement(momentJournee);
        ajouterElement(commune);
        document.getElementById("commune").value = commune;
        ajouterElement(vehUt);
        ajouterElement(vehVol);

        var nodeLigne = document.getElementById("resultat");
        nodeLigne.innerHTML = ligne;

    });
</script>

</html>