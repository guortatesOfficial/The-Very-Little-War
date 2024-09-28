window.barreRing;window.listPartenaires=['The Very Little War', 'Pyromagnon'];
function Partenaire(e,t,n){
	this.id=e;
	this.nom=t;
	this.newsList=n;
	this.pointerNews=0;
}

function loadBarre(){
	loadBarreDOM();
	loadPartenaire();
	loadNewsPartenaire();
	var e=$(".nos_jeux"),t=$("#ring_barre_wrapper");
	e.on("click",function(){t.slideToggle(barreRing.timeAnimWrapper)});
	t.on("mouseover",function(){if(barreRing.timeoutWrapper)clearTimeout(barreRing.timeoutWrapper)});
	t.on("mouseout",function(){barreRing.timeoutWrapper=setTimeout(function(){t.slideUp(barreRing.timeAnimWrapper)},barreRing.timeBeforeHideWrapper)});
}

function loadBarreDOM(){
	var e=[{name:"The Very Little War",href:"http://www.theverylittlewar.com",src:"http://www.theverylittlewar.com/images/partenariat/theverylittlewar.png",alt:"The Very Little War",txt:"Jeu de stratégie sur le thème des molécules"},
	{name:"Pyromagnon",href:"http://www.pyromagnon.fr",src:"http://www.theverylittlewar.com/images/partenariat/pyromagnon.jpg",alt:"Pyromagnon",txt:"Jeu de gestion/stratégie préhistorique"}],t='<div class="ring_barre degrade_ring_barre">'+'	<div class="ico"></div>'+'	<p class="titre" style="color:#FFAF9B">Jeux par navigateur indépendants <span id="ring_news" style="color: white">'+'</span></p>	<p class="nos_jeux">Liste des jeux<span class="fleche_menu"></span></p>'+"</div>"+'<div id="ring_barre_wrapper">';
	
	for(i in e){
		t+='<div id="partenaire_'+i+'">'+'	<p><a href="'+e[i].href+'" target="_blank"><img class="img-responsive" src="'+e[i].src+'" alt="'+e[i].alt+'" /></a></p>'+'	<p><a href="'+e[i].href+'" target="_blank"><span style="font-weight: bold;">'+e[i].name+"</span> - "+e[i].txt+"</a></p>"+"</div>"
	}
	
	$("body").prepend(t)
}

function loadPartenaire(){
	for(i in listPartenaires){
		listPartenaires[i]=new Partenaire(i,listPartenaires[i],list_news[i])
	}
}

function loadNewsPartenaire(){
	listPartenaires=shuffle(listPartenaires);afficherNews(listPartenaires[0])
}
function afficherNews(e){
	if(e.newsList&&e.newsList[e.pointerNews]){
		var t=e.newsList[e.pointerNews];
		$("#ring_news").html('<span class="news"><span style="font-family: Verdana; font-size: 14px">: '+e.nom+'</span> - <a href="'+t.lien+'" target="_blank">'+t.titre+"</a></span>")
	}
	if(e.pointerNews+1>=e.newsList.length)e.pointerNews=0;
	else e.pointerNews++;
	
	setTimeout(function(){
					if(barreRing.pointerPartenaire+1>=listPartenaires.length)barreRing.pointerPartenaire=0;
					else barreRing.pointerPartenaire++;afficherNews(listPartenaires[barreRing.pointerPartenaire])}
				,barreRing.tempo_seconde)
}

function shuffle(e){
	for(var t,n,r=e.length;r;t=parseInt(Math.random()*r),n=e[--r],e[r]=e[t],e[t]=n);
	return e
}

window.barreRing;
window.listPartenaires=["The Very Little War","Pyromagnon"];
$(function(){barreRing={tempo_seconde:7500,timeoutWrapper:null,timeBeforeHideWrapper:1e3,timeAnimWrapper:500,pointerPartenaire:0,REF_AJAX:null};loadBarre()})