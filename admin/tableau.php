<?php
include("debut.php");
?>
                 <!-- Banner ends here -->


                 <!-- Main content starts here -->
                <div class="banner">
                    
                </div>
                
                <div class="featured-blocks">
                    <div class="row-fluid">
                    <div class="featured-heading">
                        <h1>Tableau</h1>
                        <h5>Voici la page de votre tableau sélectionné</h5>
                    </div>
                    </div>
                    <div class="row-fluid">
                        
                            
                                <div class="span4">
                            <div class="block">
                                <div class="block-title">
                                    <h1>1</h1>
                                    <h2>Obtenir la source <img src="img/folded-paper.png" alt="folded" style="float: right;margin-top: -30px"/></h2>
                                </div>
                                <div class="block-content">
                                    <p>Ouvrez le CRPJ que vous voulez insérer dans le tableau et cliquez sur le bouton en haut à droit du contenu "Autres actions" puis ssélectionnez dans la liste déroulante "Afficher la source". Une fenêtre s'ouvre : appuyer alors sur Ctrl+A pour sélectionner tout le contenu puis Ctrl+C pour le copier.</p>
                                </div>
                            </div>
                        </div>
                            
                            
                                <div class="span4">
                            <div class="block">
                                <div class="block-title">
                                    <h1>2</h1>
                                    <h2>Transformer le CRPJ <img src="img/rapidshare-arrow.png" alt="arrow" style="float: right;margin-top: -30px"/></h2>
                                </div>
                                <div class="block-content">
                                    <p>Rendez-vous ensuite sur cette page juste en bas de ce tutoriel dans la grande zone de texte et appuyez sur Ctrl+V pour coller la source du CRPJ. Appuyez ensuite sur le bouton "Mettre en forme" en bas de la grande zone de texte. Vous obtenez alors un tableau un peu plus bas. Copiez ce qui vous intéresse (Ctrl+C).</p>
                                </div>
                            </div>
                        </div>
                            
                            
                                <div class="span4">
                            <div class="block">
                                <div class="block-title">
                                    <h1>3</h1>
                                    <h2>Compléter le tableau <img src="img/checklist.png" alt="check" style="float: right;margin-top: -30px"/></h2>
                                </div>
                                <div class="block-content">
                                    <p>C'est presque fini ! Il reste seulement à ouvrir le fichier Calc et à appuyer sur Ctrl+V au niveau de l'UNA de la ligne qui vous intéresse.</p>
                                </div>
                            </div>
                        </div>  
                        
                        
                        
                        
                    </div>
                </div>
                

                    <!-- Featured slider starts here -->
                <div class="featured-slider">
                    <?php if(isset($_GET['id'])) {
	                    $_GET['id'] = antiXSS($_GET['id']);
                        $ex = query('SELECT *, count(*) AS nb FROM tableaux WHERE id=\''.$_GET['id'].'\'');
                        $data = mysqli_fetch_array($ex);
                        if($data['nb'] >= 1){
                            ?>
                            <h1>Tableau <?php echo $data['nom'] ?></h1>
                            <br/><br/>
                            <p>
                            <textarea id="texte" rows="20" style="width:100%"></textarea><br/><br/>
                            <input type="submit" id="soumettre" value="METTRE EN FORME" />
                            </div>
                            <div class="featured-slider" id="englobeResultat">
                            </div>
                            <div id="crpj"></div>
                            </p>
                            
                                
                            <script type="application/javascript">

                            document.getElementById("soumettre").addEventListener("click", function () {
                                var textarea = document.getElementById("texte");
                                function siNonNul(regex){ //focntion pour éviter les erreurs si la regex trouve rien
                                    if(regex != null){
                                        regex = regex[0];
                                    }
                                    else {
                                        regex = "";
                                    }

                                    return regex;
                                }

                                function siNonNul2(req){
                                    if(typeof req[0] != "undefined" && typeof req != "undefined"){
                                        req = req[0].innerHTML;
                                    }
                                    else {
                                        req = "";
                                    }

                                    return req;
                                }
                                function ajouterZero(nombre, nombreDeChiffres){ // mettre le mois sous la forme 03 au lieu de 3
                                    nombre = nombre+"";
                                    nombre = nombre.trim();
                                    var caracLen = nombre.length;
                                    for (var i=0;i<(nombreDeChiffres-caracLen);i++){
                                        nombre = "0"+nombre;
                                    }
                                    return nombre;
                                }

                                function ajouterElement(variable){
                                    ligneResultat = ligneResultat+"<td>"+variable+"</td>";
                                }
                                
                                function ajouterTitre(variable){
                                    ligneTitre = ligneTitre+"<th>"+variable+"</th>";
                                }

                                var valeurTexte = textarea.value; // la valeur de ce qu'il y a dans le textarea

                                // il y a un problème d'encodage ISO si ce n'est pas une pièce jointe donc on prévient ce cas
                                valeurTexte = valeurTexte.replace(/3D/g,"");
                                valeurTexte = valeurTexte.replace(/=\n/g,"");
                                valeurTexte = valeurTexte.replace(/=C3=A0/g,"à");
                                valeurTexte = valeurTexte.replace(/=C3=A9/g,"é");
                                valeurTexte = valeurTexte.replace(/=C3=A8/g,"è");
                                valeurTexte = valeurTexte.replace(/=C3=8E/g,"I");
                                valeurTexte = valeurTexte.replace(/=C3=A7/g,"ç");
                                valeurTexte = valeurTexte.replace(/=E2=82=AC/g,"€");
                                valeurTexte = valeurTexte.replace(/=C3=AA/g,"ê");
                                valeurTexte = valeurTexte.replace(/=C2=B0/g,"°");
                                valeurTexte = valeurTexte.replace(/=C3=B4/g,"ô");
                                valeurTexte = valeurTexte.replace(/=E2=80=99/g,"ô");
                                valeurTexte = valeurTexte.replace(/ISO-8859-1/,"UTF-8");


                                valeurTexte = valeurTexte.replace(/\s/g," ");
                                valeurTexte = siNonNul(/(<div class="moz-forward-container">|<body>)[^]*(<\/div>|<\/body>)/.exec(valeurTexte));
                                document.getElementById("crpj").innerHTML = valeurTexte;
                                
                                var ligneResultat ="";
                                var ligneTitre ="";
    
                                document.getElementById("englobeResultat").innerHTML = '<h1>Résultat</h1><p><div id="texteFinal" style="width:100%;display:block;overflow:auto;"><table id="tableau"><tr id="colTitre"></tr><tr id="colResultat"></tr></table></div></p>';
                                var tabMois = ["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
                                var tabJours = ["lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche"];
                                var natinf="";
                                var ligne;
                                var ligneLieu = "";
                                var tabFait = siNonNul2(document.getElementsByClassName("crpj_pgFaits")); // le fait + lieu
                                tabFait = tabFait.split("<br><br>");


                                var unite =  siNonNul2(document.getElementsByClassName("crpj_pgUniteSaisie"));// unité saisie
                                var compagnie;
                                unite = unite.toUpperCase();
                                unite = unite.replace(/-/g," "); // on formate
                                unite = unite.replace(/SAINT/ig, "ST"); //on prends en compte tous les cas possibles pour bien tout formater sous la forme COB/BTA NOMCOB NOMBP
                                unite = unite.replace(/BP/ig, "");
                                unite = unite.replace(/BTPCL/ig,"");
                                unite = unite.replace(/BTP/ig,"");
                                unite = unite.replace(/COMMUNAUT(É|E) DE BRIGADES DE/g,"COB");
                                unite = unite.replace(/'/ig, " ");
                                unite = unite.trim();

                                var listeVolTotale = siNonNul2(document.getElementsByClassName("crpj_pgPrejudice"));
                                var vehVol = "";
                                var vehUt = "";
                                var typeVol="";
                                var typeUt="";
                                var plaqueVol="";
                                var plaqueUt="";
                                var couleurVol="";
                                var couleurUt="";    
                                var marqueVol="";
                                var marqueUt="";
                                var modeleVol="";
                                var modeleUt="";
                                var numSerieVol="";
                                var numSerieUt="";
                                
                                var vehicules = siNonNul2(document.getElementsByClassName("crpj_pgVehicule"));
                                vehicules = vehicules.split("<br>");
                                for(var k=0;k<vehicules.length;k++){
                                    listeVolTotale= listeVolTotale+"<br>"+vehicules[k];
                                }
                                console.log(listeVolTotale);
                                // VERIFIER SI BONNE SYNTAXE POUR VEHICULE
                                if(listeVolTotale!=""){
                                    listeVolTotale = listeVolTotale.replace(/\*/g,"");
                                    var listeVol = listeVolTotale.split(/<br>/g);
                                    var tabVol = [];
                                    var tabVeh = [];
                                    for(var i=0;i<listeVol.length;i++){
                                        var chaine="";
                                        listeVol[i]=listeVol[i].trim();
                                        var add = siNonNul(/([a-z]|é| |è)+ \((volé|utilisé|saisi|découvert)\)/i.exec(listeVol[i])); // le premier mot avant la parenthèse
                                        add = add.trim();
                                        if(add=="Véhicule terrestre (Volé)" || add=="Véhicule terrestre (Utilisé)" || add=="Véhicule terrestre (Saisi)" || add=="Véhicule terrestre (Découvert)"){
                                            console.log(listeVol[i]);//si le véhicule est volé on prends le modéle, la couleur et la plaque
                                            var typeVeh = siNonNul(/Nature : ([A-Z]| |[0-9])+/i.exec(listeVol[i]));
                                            typeVeh = typeVeh.replace("Nature : ","");
                                            
                                            var plaque = siNonNul(/Numéro d'immatriculation : (([A-Z]{2}(-| )?[0-9]{3}(-| )?[A-Z]{2})|([0-9]{3}(-| )?[A-Z]{2,3}(-| )?[0-9]{2})|([0-9]{4}(-| )?[A-Z]{2}(-| )?[0-9]{2}))/i.exec(listeVol[i]));
                                            plaque = plaque.replace("Numéro d'immatriculation : ","");
                                            
                                            var marque = siNonNul(/Marque : ([A-Z0-9]| )+/i.exec(listeVol[i]));
                                            marque = marque.replace("Marque : ","");
                                            
                                            var modele = siNonNul(/Modèle : ([A-Z0-9]| )+/i.exec(listeVol[i]));
                                            modele = modele.replace("Modèle : ","");

                                            var couleurPrincipale = siNonNul(/Couleur principale : ([A-Z0-9]| )+/i.exec(listeVol[i]));
                                            couleurPrincipale = couleurPrincipale.replace("Couleur principale : ","");
                                            
                                            var dominanceCouleur = siNonNul(/Dominance couleur : ([A-Z0-9]| )+/i.exec(listeVol[i]));
                                            dominanceCouleur = dominanceCouleur.replace("Dominance couleur : ","");
                                            couleurPrincipale = couleurPrincipale+" "+dominanceCouleur;
                                            
                                            var numSerie = siNonNul(/Numéro : ([A-Z0-9]| )+/i.exec(listeVol[i]));
                                            numSerie = numSerie.replace("Numéro : ","");
                                            
                                            chaine += plaque+marque;
                                            
                                            if(tabVeh.indexOf(chaine) == -1){
                                                console.log(add);
                                                if(add=="Véhicule terrestre (Volé)"){
                                                    typeVol += "<br/> - "+typeVeh;
                                                    plaqueVol += "<br/> - "+plaque;
                                                    couleurVol += "<br/> - "+couleurPrincipale;
                                                    marqueVol += "<br/> - "+marque;
                                                    modeleVol += "<br/> - "+modele;
                                                    numSerieVol += "<br/> - "+numSerie;
                                                }
                                                else if(add=="Véhicule terrestre (Utilisé)"){
                                                    typeUt += "<br/> - UTILISE "+typeVeh;
                                                    plaqueUt += "<br/> - "+plaque;
                                                    couleurUt += "<br/> - "+couleurPrincipale;
                                                    marqueUt += "<br/> - "+marque;
                                                    modeleUt += "<br/> - "+modele;
                                                    numSerieUt += "<br/> - "+numSerie;
                                                }
                                                else if(add=="Véhicule terrestre (Saisi)"){
                                                    typeUt += "<br/> - SAISI - "+typeVeh;
                                                    plaqueUt += "<br/> - "+plaque;
                                                    couleurUt += "<br/> - "+couleurPrincipale;
                                                    marqueUt += "<br/> - "+marque;
                                                    modeleUt += "<br/> - "+modele;
                                                    numSerieUt += "<br/> - "+numSerie;
                                                }
                                                else if(add=="Véhicule terrestre (Découvert)"){
                                                    typeUt += "<br/> - DECOUVERT - "+typeVeh;
                                                    plaqueUt += "<br/> - "+plaque;
                                                    couleurUt += "<br/> - "+couleurPrincipale;
                                                    marqueUt += "<br/> - "+marque;
                                                    modeleUt += "<br/> - "+modele;
                                                    numSerieUt += "<br/> - "+numSerie;
                                                }
                                                tabVeh.push(chaine);
                                                console.log(tabVeh);
                                            }
                                            
                                        }
                                    }
                                }
                                
                                <?php // on teste toutes les unites et sous-unites dans la base de données pour retrouver la compagnie associée
                                $ex2 = query("SELECT * FROM unites");
                                while($data1 = mysqli_fetch_array($ex2)) {
                                    $data1['unite'] = str_replace("COB","",$data1['unite']);
                                    $data1['unite'] = str_replace("BTA","",$data1['unite']);
                                    $data1['unite'] = trim($data1['unite']);
                                    echo "if(/".$data1['unite']."/i.test(unite) == true) {
                                        unite = \"".$data1['unite']."\";
                                        compagnie = \"".$data1['compagnie']."\";
                                    }
                                    ";
                                }
                                
                                $ex2 = query("SELECT * FROM unites WHERE sousunite!=''");
                                while($data1 = mysqli_fetch_array($ex2)){
                                    $data1['sousunite'] = str_replace("BP","",$data1['sousunite']);
                                    $data1['sousunite'] = trim($data1['sousunite']);
                                    echo "if(/".$data1['sousunite']."/i.test(unite) == true){
                                        unite =\"".$data1['unite']."\";
                                        compagnie = \"".$data1['compagnie']."\";
                                    }";
                                }
                                

                                $colonnes = explode(";",$data['composition']);
                                for($i=0;$i<sizeof($colonnes)-1;$i++){
                                    // on enlève 1 pour ne pas avoir l'espace à la fin
                                    
                                echo'
                                console.log(tabFait);
                                ligne ="";
                                ligneLieu = "";
                                for(var i=0;i<tabFait.length;i++) {
                                    var fait = tabFait[i].split("<br>");
                                    ';

                                echo 'var date = fait[1];
                                date = date.replace("Période du", ""); //si une date de fin et de fin
                                date = date.replace("Le",""); //si une date unique
                                date = date.split("au");

                                var lieu = fait[2].replace(/- /g,"");
                                lieu = lieu.toUpperCase();
                                ligneLieu += lieu+"<br/><br/>";


                                var debut = date[0].trim();
                                if(date[1]==null){
                                    date[1] = date[0]; //si une date précise alors on met date de fin = date de fin   
                                }
                                var fin = date[1].trim();

                                var tabDebut = debut.split(" ");
                                var tabFin = fin.split(" ");
                                var jourDebut = tabDebut[0];
                                var jourFin = tabFin[0];';

                                    
                                switch($colonnes[$i]){ 
                                    case "Natinf": 
                                            echo 'var type = fait[0];
                                            type = type.replace(/Fait [0-9]:/,""); // on enlève tout ça
                                            type = type.trim();
                                            ligne += type+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                        break;
                                            
                                        case "Type (Residence, Commerce, Autres)": 
                                            $req = query("SELECT * FROM lieux");
                                            $correction = "";
                                            while($data = mysqli_fetch_array($req)){
                                                $correction = $correction.'if(/'.$data['lieuCRPJ'].'/i.test(lieu) == true) {
                                                    lieu = "'.$data['classification'].'";
                                                }';
                                            }

                                            echo 'var lieu = fait[2].replace(/- /g,"");
                                            lieu = lieu.toUpperCase();
                                            lieu = lieu.trim();
                                            '.$correction.'
                                            ligne += lieu+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            
                                        break;
                                         
                                        case "Commune" :
                                            echo 'var adresse = fait[3].replace(/-/g," "); 
                                            adresse = adresse.toUpperCase();
                                            adresse = adresse.replace(/SAINT/g,"ST");
                                            adresse = adresse.trim();

                                            var commune = siNonNul(/à([a-z]|-| |\')+[0-9]{5}/i.exec(adresse));
                                            commune = commune.replace(/à/i,"");
                                            commune = commune.replace(/-/ig," ");
                                            commune = commune.trim();
                                            ligne += commune+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                        break;
                                        
                                        case "Adresse" :
                                            echo 'var adresse = fait[3].replace(/-/g," "); 
                                            adresse = adresse.toUpperCase();
                                            adresse = adresse.replace(/SAINT/g,"ST");
                                            adresse = adresse.trim();
                                            ligne += adresse+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;
                                        
                                        case "Jour début" :
                                            echo '
                                            var jourDebut = tabDebut[0];
                                            ligne += jourDebut+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;
                                        
                                        case "Date début" :
                                            echo'
                                            var dateDebut = tabDebut[1]+"/"+ajouterZero(tabMois.indexOf(tabDebut[2])+1,2)+"/"+tabDebut[3];
                                            ligne += dateDebut+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;
                                        
                                        case "Heure début" :
                                            echo'
                                            var heure = tabDebut[5]+":"+tabDebut[7];
                                            ligne += heure+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;
                                        
                                        case "Jour fin" :
                                            echo '
                                            var jourFin = tabFin[0];
                                            ligne += jourFin+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;
                                        
                                        case "Date fin" :
                                            echo '
                                            var dateFin = tabFin[1]+"/"+ajouterZero(tabMois.indexOf(tabFin[2])+1,2)+"/"+tabFin[3];
                                            ligne += dateFin+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;
                                        
                                        case "Heure fin" :
                                            echo '
                                            var heure = tabFin[5]+":"+tabFin[7];
                                            ligne += heure+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;

                                        case "Semaine" :
                                            echo '
                                            //SCRIPT IMPORTE de codes-sources.commentcamarche.net/source/41485-numero-de-la-semaine
                                            Date.prototype.getYearDay = function() { //1 - 366
                                                var year  = this.getFullYear();
                                                var month = this.getMonth();
                                                var day   = this.getDate();

                                                var offset = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

                                                //l\'année bissextile n\'est utile qu\'à partir de mars
                                                var bissextile = (month < 2) ? 0 : (year % 400 == 0 || (year % 4 == 0 && year % 100 != 0));

                                                return parseInt(day + offset[month] + bissextile);
                                            }

                                            Date.prototype.getMonday = function() {
                                            var offset = (this.getDay() + 6) % 7;
                                            return new Date(this.getFullYear(), this.getMonth(), this.getDate()-offset);
                                            }

                                                Date.prototype.getWeek = function() { //1 - 53
                                                    var year = this.getFullYear();
                                                    var week;

                                                    //dernier lundi de l\'année
                                                    var lastMonday = new Date(year, 11, 31).getMonday();

                                                    //la date est dans la dernière semaine de l\'année
                                                    //mais cette semaine fait partie de l\'année suivante
                                                    if(this >= lastMonday && lastMonday.getDate() > 28) {
                                                        week = 1;
                                                    }
                                                    else {
                                                        //premier lundi de l\'année
                                                        var firstMonday = new Date(year, 0, 1).getMonday();

                                                        //correction si nécessaire (le lundi se situe l\'année précédente)
                                                        if(firstMonday.getFullYear() < year) firstMonday = new Date(year, 0, 8).getMonday();

                                                        //nombre de jours écoulés depuis le premier lundi
                                                        var days = this.getYearDay() - firstMonday.getYearDay();

                                                        //window.alert(days);

                                                        //si le nombre de jours est négatif on va chercher
                                                        //la dernière semaine de l\'année précédente (52 ou 53)
                                                        if(days < 0) {
                                                            week = new Date(year, this.getMonth(), this.getDate()+days).getWeek();
                                                        }
                                                        else {
                                                            //numéro de la semaine
                                                            week = 1 + parseInt(days / 7);

                                                            //on ajoute une semaine si la première semaine
                                                            //de l\'année ne fait pas partie de l\'année précédente
                                                            week += (new Date(year-1, 11, 31).getMonday().getDate() > 28);
                                                        }
                                                    }

                                                    return parseInt(week);
                                                }
                                                
                                                //FIN DU SCRIPT
                                                var jFin = Number(tabFin[1]);
                                                var mFin = tabFin[2];
                                                mFin = tabMois.indexOf(mFin);
                                                var aFin = Number(tabFin[3]);
                                                var objDateFin = new Date(aFin,mFin,jFin);
                                                var numSem = objDateFin.getWeek();
                                                ligne += numSem+"<br/><br/>";
                                                }
                                                ajouterElement(ligne);';
                                                break;

                                        case "Période (début-fin)" :
                                            echo'
                                            var periode;
                                            if(tabDebut[0]+tabDebut[1]+tabDebut[2] == tabFin[0]+tabFin[1]+tabFin[2]){
                                                periode = jourDebut;
                                            }
                                            else {
                                                var indexDebut = tabJours.indexOf(jourDebut);
                                                if((indexDebut+1)%7 == tabJours.indexOf(jourFin) && ((Number(tabDebut[1])+1)%30 == Number(tabFin[1]) || (Number(tabDebut[1])+1)%31 == Number(tabFin[1]))){ // le -7 car le % ne marche pas pour les valeurs négatives donc on met tout en négatif (pour le dimanche-lundi) et les jours se suivent
                                                    periode = jourDebut+"-"+jourFin;
                                                }
                                                else {
                                                    periode = "indéterminée";
                                                }
                                            }
                                            ligne += periode+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;

                                        case "Créneau (jour, nuit...)" :
                                            echo 'var periode;
                                            if(tabDebut[0]+tabDebut[1]+tabDebut[2] == tabFin[0]+tabFin[1]+tabFin[2]){
                                                periode = jourDebut;
                                            }
                                            else {
                                                var indexDebut = tabJours.indexOf(jourDebut);
                                                if((indexDebut+1)%7 == tabJours.indexOf(jourFin) && ((Number(tabDebut[1])+1)%30 == Number(tabFin[1]) || (Number(tabDebut[1])+1)%31 == Number(tabFin[1]))){ // le -7 car le % ne marche pas pour les valeurs négatives donc on met tout en négatif (pour le dimanche-lundi) et les jours se suivent
                                                    periode = jourDebut+"-"+jourFin;
                                                }
                                                else {
                                                    periode = "indéterminée";
                                                }
                                            }
                                            var momentJournee = "indéterminé";
                                            if(periode != "indéterminée") { // on determine le moment de la journée : matin, après-midi, soir, nuit
                                                var tableauMoments = [[6,18,"jour",12],[18,30,"nuit"],[0,6,"nuit"],[6,12,"matin"],[12,14,"midi"],[14,18,"après-midi"],[18,22,"soirée"]];
                                                var heureDebut = Number(tabDebut[5]);
                                                var heureFin = Number(tabFin[5]);
                                                if(heureFin < heureDebut){ // pour les calculs toujours heureFin < heureDebut
                                                    heureFin = heureFin+24;
                                                }
                                                var moyenne = ((heureDebut+heureFin)/2);
                                                var duree = heureFin-heureDebut;
                                                console.log(moyenne,duree);
                                                for(var i=0;i<tableauMoments.length;i++){
                                                    if(moyenne >= tableauMoments[i][0] && moyenne <= tableauMoments[i][1] && duree<=(tableauMoments[i][1]-tableauMoments[i][0])){
                                                        momentJournee = tableauMoments[i][2];
                                                    }
                                                }
                                            }
                                            ligne += momentJournee+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;

                                        case "Mois" :
                                            echo 'var mFin = tabFin[2];
                                            ligne += mFin+"<br/><br/>";
                                            }
                                            ajouterElement(ligne);';
                                            break;

                                        case "Lieu (appartement, cave etc...)" :
                                            echo '                                            
                                            }
                                            ajouterElement(ligneLieu);
                                            ';
                                            break;
                                            
                                        default :
                                            echo '}';
                                            break;
                                    }
                                    
                                    switch($colonnes[$i]){
                                        case "Una":
                                            echo 'var una = siNonNul2(document.getElementsByClassName("crpj_pgUpva")); // UNA
                                            una = una.replace(/UNA/,""); // on obtient juste le numéro

                                            una = una.split("/"); //on la remet en forme
                                            var codeU = una[0];
                                            var codeN = una[1];
                                            var codeA = una[2];
                                            una = ajouterZero(codeU,8)+"-"+ajouterZero(codeN,5)+"-"+ajouterZero(codeA,4);
                                            
                                            ajouterElement(una);
    
                                            ';
                                            break;
                                        
                                        case "Manop":
                                            echo 'var manop = siNonNul2(document.getElementsByClassName("crpj_pgManiereOperer"));
                                            ajouterElement(manop);';
                                            break;

                                        case "Unité":
                                            echo'
                                            ajouterElement(unite);';
                                            break;

                                        case "Compagnie":
                                            echo'
                                            ajouterElement(compagnie);';
                                            break;

                                        case "Individus":
                                            echo '
                                            var signalementTotal = siNonNul(/Manière d\'opérer(.+\s+)+/i.exec(valeurTexte));
                                            var individus = "";
                                            var auteursNonIdentifies = siNonNul2(document.getElementsByClassName("crpj_pgAuteursNonIdentifies"));
                                            if(auteursNonIdentifies != ""){
                                                individus = auteursNonIdentifies;
                                            }
                                            else {
                                                 // on remplace les mots inutiles (a voir)
                                                var tableauSignalements = [];
                                                signalementTotal = signalementTotal.replace(/( de | ou | des | d\'| que )/g," ");';
                                                
                                                $ex1 = query('SELECT * FROM signalement');
                                                while($data = mysqli_fetch_array($ex1)){
                                                    echo "tableauSignalements.push(/ ".$data['motCle']." /ig);
                                                    ";
                                                }
                                                
                                                echo'
                                                var mis;
                                                var avant = false;
                                                var avantAvant = false;
                                                var mettre = [];
                                                var signalement = signalementTotal.split(/\s/);
                                                for(var i=0;i<signalement.length;i++){
                                                    if(signalement[i]!="" && signalement!= "-"){
                                                        mis = false;
                                                        for(var j=0;j<tableauSignalements.length;j++){

                                                            if(tableauSignalements[j].test(" "+signalement[i]+" ") == true){
                                                                mettre.push(signalement[i]);
                                                                mis = true;
                                                                break;
                                                            }
                                                        }
                                                        if(mis == false && avant == true && avantAvant == false){
                                                            mettre.pop();
                                                            mettre.push("\n");
                                                        }
                                                        avantAvant = avant;
                                                        avant = mis;
                                                    }  
                                                }

                                                for(var i=0; i<mettre.length;i++){
                                                    individus+=mettre[i]+" ";
                                                }
                                            }
                                            ajouterElement(individus);';
                                            break;

                                        case "Objets" :
                                            echo 'var listeVolTotale = siNonNul2(document.getElementsByClassName("crpj_pgPrejudice"));
                                            var objets = "";
                                            listeVolTotale = listeVolTotale.split("*");
                                            console.log(listeVolTotale);
                                            for(var i=0;i<listeVolTotale.length;i++) {
                                                var nature = siNonNul(/Nature : ([a-z]|[0-9]| |\')*/i.exec(listeVolTotale[i]));
                                                nature = nature.replace("Nature : ","");
                                                console.log(nature);
                                                objets += nature+" - ";
                                            }
                                            ajouterElement(objets);
                                            ';
                                            break;

                                        case "Type (vus)" :
                                            echo 'ajouterElement(typeUt);';
                                            break;

                                        case "Modèle (vus)" :
                                            echo 'ajouterElement(modeleUt);';
                                            break;

                                        case "Marque (vus)" :
                                            echo 'ajouterElement(marqueUt);';
                                            break;

                                        case "Couleur (vus)" :
                                            echo 'ajouterElement(couleurUt);';
                                            break;

                                        case "Immatriculation (vus)" :
                                            echo 'ajouterElement(plaqueUt);';
                                            break;

                                        case "Numéro de série (vus)" :
                                            echo 'ajouterElement(numSerieUt);';
                                            break;

                                        case "Type (volés)" :
                                            echo 'ajouterElement(typeVol);';
                                            break;

                                        case "Modèle (volés)" :
                                            echo 'ajouterElement(modeleVol);';
                                            break;

                                        case "Marque (volés)" :
                                            echo 'ajouterElement(marqueVol);';
                                            break;

                                        case "Couleur (volés)" :
                                            echo 'ajouterElement(couleurVol);';
                                            break;

                                        case "Immatriculation (volés)" :
                                            echo 'ajouterElement(plaqueVol);';
                                            break;

                                        case "Numéro de série (volés)" :
                                            echo 'ajouterElement(numSerieVol);';
                                            break;

                                        case "Véhicules" :
                                            echo 'typeUt = typeUt.replace(/(<br\/>|-)/g,"");
                                            marqueUt = marqueUt.replace(/(<br\/>|-)/g,"");
                                            modeleUt = modeleUt.replace(/(<br\/>|-)/g,"");
                                            couleurUt = couleurUt.replace(/(<br\/>|-)/g,"");
                                            plaqueUt = plaqueUt.replace(/(<br\/>|-)/g,"");';
                                            echo 'ajouterElement(typeUt+" "+marqueUt+" "+modeleUt+" "+couleurUt+" "+plaqueUt+" "+typeVol+" "+marqueVol+" "+modeleVol+" "+couleurVol+" "+plaqueVol);';
                                            break;

                                        case "Mots clés" :
                                            echo 'var listeVolTotale = siNonNul2(document.getElementsByClassName("crpj_pgPrejudice")); // pour les objets
                                            var objets = "";
                                            listeVolTotale = listeVolTotale.split("*");
                                            console.log(listeVolTotale);
                                            for(var i=0;i<listeVolTotale.length;i++) {
                                                var nature = siNonNul(/Nature : ([a-z]|[0-9]| |\')*/i.exec(listeVolTotale[i]));
                                                nature = nature.replace("Nature : ","");
                                                console.log(nature);
                                                objets += nature+" - ";
                                            }
                                            ajouterElement(ligneLieu+" "+objets);
                                            ';


                                    }
                                    echo 'ajouterTitre("'.$colonnes[$i].'");';
                                }
                                ?>
                                
                                var nodeLigneTitre = document.getElementById("colTitre");
                                nodeLigneTitre.innerHTML = ligneTitre;
                                
                                var nodeLigneRes = document.getElementById("colResultat");
                                nodeLigneRes.innerHTML = ligneResultat;
                                });
                                </script>
                                <?php
                                
                                
                        }
                        else {
                            $erreur = erreur("Il n'existe aucun tableau correspondant à cet id.");
                            echo $erreur;
                        }
                    }
                    ?>
                    
                </div>

            </div>
            
        <?php
        include("footer.php");
        ?>
        



   </body>
</html>
