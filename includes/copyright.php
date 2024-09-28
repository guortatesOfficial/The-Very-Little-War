<?php
debutCarte();
    debutContent();
        ?>
         &copy; Copyright<a href="index.php"> The Very Little War </a><?php echo date('Y'); ?> - <a href="version.php" class="lienVisible">V2.0.1.0</a><br/><a href="https://www.facebook.com/The-Very-Little-War-463377203736000/" class="external">Contact</a> - <a href="credits.php">Crédits</a>
        <?php
    finContent();
finCarte();
?>


            </div>
          </div>
        </div>
    </div>
</div>
</body>
</html>


  <script type="text/javascript" src="cordova.js"></script>
    <script type="text/javascript" src="js/notification.js"></script>
    <script type="text/javascript" src="js/PushNotification.js"></script>

    <script type="text/javascript" src="js/framework7.min.js"></script>
    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="js/loader.js"></script>
    <script type="text/javascript" src="js/aes.js"></script>
    <script type="text/javascript" src="js/aes-json-format.js"></script>
    
<script>
    document.getElementById('titre').style.marginLeft = window.innerWidth*0.32-105+"px";
    var myApp = new Framework7({swipePanel: 'left',ajaxLinks:'.ajax',animateNavBackIcon: true,material:true,smartSelectOpenIn:'picker',externalLinks:'.external',pushState:true,swipePanelActiveArea: 40});  //voir si PushState ne fait pas des bugs
    var $$ = Dom7;
    var mainView = myApp.addView('.view-main');
    var panel = myApp.addView('.panel-overlay');
    
    var calVacs = myApp.calendar({
        input: '#calVacs',
        dateFormat: 'dd/mm/yyyy',
        toolbarCloseText: 'Valider',
        monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
        monthNamesShort: ['Jan','Fév','Mars','Avr','Mai','Juin','Jui','Août','Sep','Oct','Nov','Déc'],
        dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
        dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam']
    }); 
    
    // Autocomplete
    <?php if(isset($_SESSION['login'])){ ?>
    var joueurs = [
        <?php
        $ex = query('SELECT login FROM membre WHERE login!=\''.$_SESSION['login'].'\'');
        while($noms = mysqli_fetch_array($ex)){
            echo '"'.$noms['login'].'",';
        }
        ?>
    ];
    <?php } ?>
    var autocompleteStandalonePopup = myApp.autocomplete({
        openIn: 'popup', //open in popup
        opener: $$('#labelInviter'), //link that opens autocomplete
        backOnSelect: true, //go back after we select something
        source: function (autocomplete, query, render) {
            var results = [];
            if (query.length === 0) {
                render(results);
                return;
            }
            // Find matched items
            for (var i = 0; i < joueurs.length; i++) {
                if (joueurs[i].toLowerCase().indexOf(query.toLowerCase()) >= 0) results.push(joueurs[i]);
            }
            // Render items by passing array with result items
            render(results);
        },
        onChange: function (autocomplete, value) {
            // Add item text value to item-after
            $$('#labelInviter').find('.item-after').text(value[0]);
            // Add item value to input value
            $$('#labelInviter').find('input').val(value[0]);
        }
    });

    <?php
        if(isset($erreur)){
            echo "myApp.alert(\"".$erreur."\",\"<span style='color:red;text-weight:bold'>Erreur</span>\");";
        }
    
        if(isset($information)){
            $information = addslashes($information);
            echo "myApp.addNotification({
                    message: '$information',
                    button: {
                        text: 'Fermer',
                        color: 'green'
                    }
                });";
        }
    ?>
    
    function deconnexion(){
        document.location.href="deconnexion.php";
    }
    
</script> 

<?php
if(isset($_GET['deployer'])){
    echo '<script>
        myApp.accordionOpen(document.getElementById("tutorielAccordion"));
    </script>';
}
?>

<script>
    function nFormatter(num) {
  var si = [
	{ value: 1E24, symbol: "Y" },
	{ value: 1E21, symbol: "Z" },
    { value: 1E18, symbol: "E" },
    { value: 1E15, symbol: "P" },
    { value: 1E12, symbol: "T" },
    { value: 1E9,  symbol: "G" },
    { value: 1E6,  symbol: "M" },
    { value: 1E3,  symbol: "K" }
  ];
  for (var i = 0; i < si.length; i++) {
    if (num >= si[i].value) {
      return Math.floor((num / si[i].value)*100)/100 + si[i].symbol;
    }
  }
  return Math.floor(num);
}
    
    function symboleEnNombre(chaine){
        var si = [
        { value: 1E24, symbol: "Y" },
        { value: 1E21, symbol: "Z" },
        { value: 1E18, symbol: "E" },
        { value: 1E15, symbol: "P" },
        { value: 1E12, symbol: "T" },
        { value: 1E9,  symbol: "G" },
        { value: 1E6,  symbol: "M" },
        { value: 1E3,  symbol: "K" }
      ];
        
        for(var i=0;i<chaine.length;i++){
            for(var j=0;j<si.length;j++){
                if(chaine[i] == si[j].symbol){
                    chaine.replace(si[j].symbol,si[j].value);
                    chaine = parseFloat(chaine)*si[j].value;
                }
            }
            
        }
        
        return chaine;
    }
    
	var consonnes = ['zh', 'zl', 'zr', 'zw', 'zv', 'rh', 'tr', 'th', 'tw', 'tl', 'tj', 'tt', 'ts', 'tz', 'tv', 'pr', 'pw', 'ph', 'pz', 'pl', 'pv', 'pj', 'ps', 'pf', 'ql', 'qr', 'qh', 'qv', 'qs', 'qz', 'qw'
	, 'sr', 'st', 'sp', 'sf', 'sh', 'sk', 'sw', 'sl', 'sm', 'sv', 'sb', 'sn', 'dz', 'dr', 'df', 'dh', 'dj', 'dl', 'dm', 'dv', 'dw', 'dn', 'fr', 'fs', 'fh', 'fl', 'fw', 'gz', 'gr', 'gs', 'gl', 'gw', 'gn'
	, 'jr', 'jp', 'jq', 'jd', 'jf', 'jh', 'jk', 'jl', 'jm', 'jw', 'jv', 'jb', 'jn', 'kr', 'ks', 'kf', 'kj', 'kl', 'kw', 'kv', 'll', 'lh', 'lw', 'lv', 'mr', 'mh', 'ml', 'mw', 'wr', 'wh', 'wl', 'xr', 'xd'
	, 'xl', 'xh', 'cr', 'ch', 'cl', 'cw', 'vr', 'vh', 'vl', 'br', 'bh', 'bl', 'bw', 'nf', 'nh', 'nl', 'nv', 'nw', 'z', 'r', 't', 'p', 'q', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'w', 'x', 'c', 'v', 'b', 'n'
	, 'z', 'r', 't', 'p', 'q', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'w', 'x', 'c', 'v', 'b', 'n', 'z', 'r', 't', 'p', 'q', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'w', 'x', 'c', 'v', 'b', 'n'];
	var voyelles = ['a', 'e', 'i', 'o', 'u', 'y',
	'e', 'e', 'e', 'e', 'a', 'a',
	'a', 'i', 'i', 'i', 'u', 'o'];
	var lettres = ['z', 'r', 't', 'p', 'q', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'w', 'x', 'c', 'v', 'b', 'n'];
	function generate() {
		var nbMots = Math.floor(Math.random()*5) + 2;
		var compteur = 0;
		var mot = '';
		var l1 = '';
		var l2 = '';
		var l3 = '';
		while(compteur <= nbMots) {
			var lettreGeneree;
			if(compteur == nbMots) {
				lettreGeneree = genererLettre(l1, l2, l3, 1);
			}
			else {
				lettreGeneree = genererLettre(l1, l2, l3, 0)
			}
			l1 = l2;
			l2 = l3;
			l3 = lettreGeneree;
			mot += lettreGeneree;
			compteur++;
		}
		var div = document.getElementById('login');
		div.value = mot;
	}
	
	function genererLettre(lettre1, lettre2, lettre3, compteur) {
		var lettre = '';
		var typel1 = 'rien';
		var typel2 = 'rien';
		var typel3 = 'rien';
		for (var i = 0, c = consonnes.length; i < c; i++) {
			if(lettre1 == consonnes[i]) {
				typel1 = 'consonne';
			}
			if(lettre2 == consonnes[i]) {
				typel2 = 'consonne';
			}
			if(lettre3 == consonnes[i]) {
				typel3 = 'consonne';
			}
		}
		for (var i = 0, c = voyelles.length; i < c; i++) {
			if(lettre1 == voyelles[i]) {
				typel1 = 'voyelle';
			}
			if(lettre2 == voyelles[i]) {
				typel2 = 'voyelle';
			}
			if(lettre3 == voyelles[i]) {
				typel3 = 'voyelle';
			}
		}
		
		if(compteur == 0) {
			if(typel3 == 'consonne') {
				lettre = genererConsonne(0, 1);
			}
			else if(typel3 == 'voyelle') {
				if(typel2 == 'voyelle') {
					lettre = genererConsonne(3, 3);	
				}
				else {
					lettre = genererConsonne(2, 3);
				}
			}
			else {
				lettre = genererConsonne(2, 3);
			}
		}
		else {
			if(typel3 == 'voyelle') {
				lettre = lettres[Math.floor(Math.random()*lettres.length)];
			}
			else {
				lettre = genererConsonne(0, 1);
			}
		}
		return lettre;
	}
	 
	function genererConsonne(nbChances, totalExperiences) {
		var aleatoire = Math.floor(Math.random()*totalExperiences ) + 1;
		if(aleatoire <= nbChances) {
			aleatoire = Math.floor(Math.random()*consonnes.length);
			lettre = consonnes[aleatoire];
		}
		else {
			aleatoire = Math.floor(Math.random()*voyelles.length);
			lettre = voyelles[aleatoire];
		}
		return lettre;
	}

</script>
