
<script>
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
