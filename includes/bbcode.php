<script language="Javascript">
var isMozilla = (navigator.userAgent.toLowerCase().indexOf('gecko')!=-1) ? true : false;
var regexp = new RegExp("[\r]","gi");

function storeCaret(selec)
{
	if (isMozilla) 
	{
	// Si on est sur Mozilla

		oField = document.forms['news'].elements['newst'];

		objectValue = oField.value;

		deb = oField.selectionStart;
		fin = oField.selectionEnd;

		objectValueDeb = objectValue.substring( 0 , oField.selectionStart );
		objectValueFin = objectValue.substring( oField.selectionEnd , oField.textLength );
		objectSelected = objectValue.substring( oField.selectionStart ,oField.selectionEnd );

	//	alert("Debut:'"+objectValueDeb+"' ("+deb+")\nFin:'"+objectValueFin+"' ("+fin+")\n\nSelectionné:'"+objectSelected+"'("+(fin-deb)+")");
			
		oField.value = objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]" + objectValueFin;
		oField.selectionStart = strlen(objectValueDeb);
		oField.selectionEnd = strlen(objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]");
		oField.focus();
		oField.setSelectionRange(
			objectValueDeb.length + selec.length + 2,
			objectValueDeb.length + selec.length + 2);
	}
	else
	{
	// Si on est sur IE
		
		oField = document.forms['news'].elements['newst'];
		var str = document.selection.createRange().text;

		if (str.length>0)
		{
		// Si on a selectionné du texte
			var sel = document.selection.createRange();
			sel.text = "[" + selec + "]" + str + "[/" + selec + "]";
			sel.collapse();
			sel.select();
		}
		else
		{
			oField.focus(oField.caretPos);
		//	alert(oField.caretPos+"\n"+oField.value.length+"\n")
			oField.focus(oField.value.length);
			oField.caretPos = document.selection.createRange().duplicate();
			
			var bidon = "%~%";
			var orig = oField.value;
			oField.caretPos.text = bidon;
			var i = oField.value.search(bidon);
			oField.value = orig.substr(0,i) + "[" + selec+ "][/" + selec + "]" + orig.substr(i, oField.value.length);
			var temp = orig.substr(0,i);
			var nbretour =0;
			for (var cpt=0;cpt<temp.length;cpt=cpt+1)
			{
			if(temp.charAt(cpt)=="\n")
			{
			nbretour=nbretour+1;
			}
			}
			//Prise en compte des retour chariots dans le placement du curseur
			pos = i + 2 + selec.length - nbretour;

			//Fin de la modif

			//placer(document.forms['news'].elements['newst'], pos);
			var r = oField.createTextRange();
			var r = 0;
			for(n = 0; n < i; n++)
			{if(regexp.test(oField.value.substr(n,2)) == true){r++;}};
			pos = i + 2 + selec.length - r;
			//placer(document.forms['news'].elements['newst'], pos);
			var r = oField.createTextRange();
			r.moveStart('character', pos);
			r.collapse();
			r.select();

		}
	}
}

function storeCaret(ao_txtfield,as_mf){
var isIE = (document.all);
if(!isIE){
//mozilla
//recuperation du txt selectionné	
oField = ao_txtfield;
oFieldValue = oField.value;
deb = oField.selectionStart;
fin = oField.selectionEnd;
Deb = oFieldValue.substring( 0 , oField.selectionStart );
Fin = oFieldValue.substring( oField.selectionEnd , oField.textLength );
Sel = oFieldValue.substring( oField.selectionStart ,oField.selectionEnd );	
oField.value = Deb + '[' + as_mf + "]" + Sel + "[/" + as_mf + ']' + Fin;
oField.selectionStart = Deb.length;
tmps =Deb + '[' + as_mf+']' + Sel +"[/" + as_mf + ']'
oField.selectionEnd = tmps.length+2;
oField.setSelectionRange(Deb.length+as_mf.length+2,tmps.length-as_mf.length-3);
oField.focus();
}else{
//IE
var str = document.selection.createRange().text;
if (str.length>0){
//recuperation du txt selectionné
var select = document.selection.createRange();
select.text = '[' + as_mf + ']' + str + "[/" + as_mf + ']';
select.collapse();
select.select();
}else{
ao_txtfield.focus(ao_txtfield.caretPos);
ao_txtfield.focus(ao_txtfield.value.length);
ao_txtfield.caretPos = document.selection.createRange().duplicate();	
var bidon = "%~%";
var original = ao_txtfield.value;
ao_txtfield.caretPos.text = bidon;
var i = ao_txtfield.value.search(bidon);
ao_txtfield.value = original.substr(0,i) + '[' + as_mf + "][/" + as_mf + ']' + original.substr(i, ao_txtfield.value.length);
var temp = original.substr(0,i);
var nbretour =0;
for (var cpt=0;cpt<temp.length;cpt=cpt+1){
if(temp.charAt(cpt)=="\n"){
nbretour=nbretour+1;
}
}
pos = i + 2 + as_mef.length - nbretour;
var r = oField.createTextRange();
var r = ao_txtfield.createTextRange();
r.moveStart('character', pos);
r.collapse();
r.select();
}
}
}

function storeCaretNoValueInto(ao_txtfield,as_mf,as_url){
var isIE = (document.all);
if(!isIE){
//mozilla
//recuperation du txt selectionné
oField = ao_txtfield;
oFieldValue = oField.value;
deb = oField.selectionStart;
fin = oField.selectionEnd;
Deb = oFieldValue.substring( 0 , oField.selectionStart );
Fin = oFieldValue.substring( oField.selectionEnd , oField.textLength );	
oField.value = Deb + '[' + as_mf+'='+as_url+'/]' + Fin;
oField.selectionStart = Deb.length;
tmps =Deb + '[' + as_mf+'=' +as_url+'/]';
oField.selectionEnd = tmps.length+3;
oField.setSelectionRange(Deb.length+(as_mf.length*2)+as_url.length+4,tmps.length-as_mf.length-3);
oField.focus();
}else{
//IE
var str = document.selection.createRange().text;
if (str.length>0){
//recuperation du txt selectionné
var select = document.selection.createRange();
select.text = '[' + as_mf +'='+as_url+'/]';
select.collapse();
select.select();
}else{
ao_txtfield.focus(ao_txtfield.caretPos);
ao_txtfield.focus(ao_txtfield.value.length);
ao_txtfield.caretPos = document.selection.createRange().duplicate();	
var bidon = "%~%";
var original = ao_txtfield.value;
ao_txtfield.caretPos.text = bidon;
var i = ao_txtfield.value.search(bidon);
ao_txtfield.value = original.substr(0,i) + '[' + as_mf +'='+as_url+'/]' + original.substr(i, ao_txtfield.value.length);
var temp = original.substr(0,i);
var nbretour =0;
for (var cpt=0;cpt<temp.length;cpt=cpt+1){
if(temp.charAt(cpt)=="\n"){
nbretour=nbretour+1;
}
}
pos = i + 2 + as_mef.length - nbretour;
var r = oField.createTextRange();
var r = ao_txtfield.createTextRange();
r.moveStart('character', pos);
r.collapse();
r.select();
var r = ao_txtfield.createTextRange();
r.moveStart('character', pos);
r.collapse();
r.select();
}
}
}

function storeCaretValue(ao_txtfield,as_mf,as_url){
var isIE = (document.all);
if(!isIE){
//mozilla
//recuperation du txt selectionné
oField = ao_txtfield;
oFieldValue = oField.value;
deb = oField.selectionStart;
fin = oField.selectionEnd;
Deb = oFieldValue.substring( 0 , oField.selectionStart );
Fin = oFieldValue.substring( oField.selectionEnd , oField.textLength );
Sel = oFieldValue.substring( oField.selectionStart ,oField.selectionEnd );	
oField.value = Deb + '[' + as_mf+'='+as_url+']' + Sel + '[/' + as_mf + ']' + Fin;
oField.selectionStart = Deb.length;
tmps =Deb + '[' + as_mf+'=' +as_url+']' + Sel +'[/' + as_mf + ']';
oField.selectionEnd = tmps.length+3;
oField.setSelectionRange(Deb.length+(as_mf.length*2)+as_url.length+4,tmps.length-as_mf.length-3);
oField.focus();
}else{
//IE
var str = document.selection.createRange().text;
if (str.length>0){
//recuperation du txt selectionné
var select = document.selection.createRange();
select.text = '[' + as_mf +'='+as_url+']' + str + '[/' + as_mf + ']';
select.collapse();
select.select();
}else{
ao_txtfield.focus(ao_txtfield.caretPos);
ao_txtfield.focus(ao_txtfield.value.length);
ao_txtfield.caretPos = document.selection.createRange().duplicate();	
var bidon = "%~%";
var original = ao_txtfield.value;
ao_txtfield.caretPos.text = bidon;
var i = ao_txtfield.value.search(bidon);
ao_txtfield.value = original.substr(0,i) + '[' + as_mf +'='+as_url+'][/' + as_mf + ']' + original.substr(i, ao_txtfield.value.length);
var temp = original.substr(0,i);
var nbretour =0;
for (var cpt=0;cpt<temp.length;cpt=cpt+1){
if(temp.charAt(cpt)=="\n"){
nbretour=nbretour+1;
}
}
pos = i + 2 + as_mef.length - nbretour;
var r = oField.createTextRange();
var r = ao_txtfield.createTextRange();
r.moveStart('character', pos);
r.collapse();
r.select();
var r = ao_txtfield.createTextRange();
r.moveStart('character', pos);
r.collapse();
r.select();
}
}
}

function storeCaretIMG(ao_txtfield,as_mf,as_url,ab_img){
var isIE = (document.all);
if(!isIE){
//mozilla
//recuperation du txt selectionné	
oField = ao_txtfield;
oFieldValue = oField.value;
deb = oField.selectionStart;
fin = oField.selectionEnd;
Deb = oFieldValue.substring( 0 , oField.selectionStart );
Fin = oFieldValue.substring( oField.selectionEnd , oField.textLength );
Sel = oFieldValue.substring( oField.selectionStart ,oField.selectionEnd );	
oField.value = Deb + '[' + as_mf +'='+as_url+ ']' + Sel + Fin;
tmps =Deb + '[' + as_mf +'='+as_url+ ']';
oField.selectionStart = tmps.length;
tmps =Deb +'['+ as_mf+'='+as_url+']'+Sel;
oField.selectionEnd = tmps.length;
oField.setSelectionRange(Deb.length+as_mf.length+4,tmps.length);
oField.focus();
}else{
//IE
var str = document.selection.createRange().text;
if (str.length>0){
//recuperation du txt selectionné
var select = document.selection.createRange();
select.text = '[' + as_mf +'='+as_url+ ']' + str;
select.collapse();
select.select();
}else{
ao_txtfield.focus(ao_txtfield.caretPos);
ao_txtfield.focus(ao_txtfield.value.length);
ao_txtfield.caretPos = document.selection.createRange().duplicate();	
var bidon = "%~%";
var original = ao_txtfield.value;
ao_txtfield.caretPos.text = bidon;
var i = ao_txtfield.value.search(bidon);
ao_txtfield.value = original.substr(0,i) + '[' + as_mf +'='+as_url+ ']' + original.substr(i, ao_txtfield.value.length);
var temp = original.substr(0,i);
var nbretour =0;
for (var cpt=0;cpt<temp.length;cpt=cpt+1){
if(temp.charAt(cpt)=="\n"){
nbretour=nbretour+1;
}
}
pos = i + 2 + as_mef.length - nbretour;
var r = oField.createTextRange();
var r = ao_txtfield.createTextRange();
r.moveStart('character', pos);
r.collapse();
r.select();
var r = ao_txtfield.createTextRange();
r.moveStart('character', pos);
r.collapse();
r.select();
}
}
}

</script>
<?php
function BBCode($text,$javascript=false){
    
$text = htmlentities($text);
    
$text = preg_replace('!localStorage.getItem\(("|\')mdp!isU', '', $text);
$text = preg_replace('!0:(-)?\)!isU', '<img alt="angel" src="images/smileys/icon_angel.gif"/>', $text);
$text = preg_replace('!\[b\](.+)\[/b\]!isU', '<span style="font-weight: bold">$1</span>', $text);
$text = preg_replace('!\[elfique\](.+)\[/elfique\]!isU', '<span style="font-family: quenya;font-size:2em">$1</span>', $text);
$text = preg_replace('!\[i\](.+)\[/i\]!isU', '<span style="font-style: italic">$1</span>', $text);
$text = preg_replace('!\[u\](.+)\[/u\]!isU', '<span style="text-decoration:underline;">$1</span>', $text);
$text = preg_replace('!\[sup\](.+)\[/sup\]!isU', '<sup>$1</sup>', $text);
$text = preg_replace('!\[sub\](.+)\[/sub\]!isU', '<sub>$1</sub>', $text);
$text = preg_replace('!\[center\](.+)\[/center\]!isU', '<div style="text-align: center;">$1</div>', $text);
$text = preg_replace('!\[title\](.+)\[/title\]!isU', '<span style="font-size: 130%;">$1</span>', $text);
$text = preg_replace('!\[joueur=([a-z0-9_-]{3,16})/\]!isU', '<a href="joueur.php?id=$1">$1</a>', $text);
$text = preg_replace('!\[alliance=([a-z0-9_-]{3,16})/\]!isU', '<a href="alliance.php?id=$1">$1</a>', $text);
$text = preg_replace('!\[url=((https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?)\](.+)\[/url\]!isU', '<a href="$1">$6</a>', $text);
$text = preg_replace('!\[img=(https?:\/\/(.*)\.(gif|png|jpg|jpeg))\]!isU', '<img alt="undefinded" src="$1">', $text);
$text = preg_replace('!\[color=(blue|red|green|white|black|beige|brown|cyan|yellow|orange|gray|purple|maroon)\](.+)\[/color\]!isU', '<span style="color:$1;">$2</span>', $text);

$text = preg_replace('!\[latex\](.+)\[/latex\]!isU', '\$\$$1\$\$', $text);

$text = preg_replace('!:arrow:!isU', '→', $text);
$text = preg_replace('!:(-)?D!isU', '😃', $text);
$text = preg_replace('!xD!isU', '😆', $text);
$text = preg_replace('!:(-)?s!isU', '😖', $text);
$text = preg_replace('!B(-)?\)!isU', '😎', $text);
$text = preg_replace('!:\'(-)?\(!isU', '😢', $text);
$text = preg_replace('!O_o!sU', '😮', $text);
$text = preg_replace('!o_O!sU', '😮', $text);
$text = preg_replace('!3:(-)?\)!isU', '😈', $text);
$text = preg_replace('!:idea:!isU', '💡', $text);
$text = preg_replace('!lol!isU', '😁', $text);
$text = preg_replace('!=/!isU', '😕', $text);
$text = preg_replace('!:green:!isU', '😷', $text);
$text = preg_replace('!:(-)?(\||l)!isU', '😐', $text);
$text = preg_replace('!:(-)?p!isU', '😛', $text);
$text = preg_replace('!:emotion:!isU', '😳', $text);
$text = preg_replace('!8(-)?\(!isU', '🙄', $text);
$text = preg_replace('!:(-)?\(!isU', '😟', $text);
$text = preg_replace('!:(-)?\)!isU', '😊', $text);
$text = preg_replace('!:(-)?o!isU', '😲', $text);
$text = preg_replace('!;(-)?\)!isU', '😉', $text);
$text = preg_replace('!:chainhappy:!isU', '<img alt="chainhappy" src="images/smileys/chainhappy.gif"/>', $text);
$text = preg_replace('!:want:!isU', '<img alt="want" src="images/smileys/want.gif"/>', $text);
$text = preg_replace('!:facepalm:!isU', '<img alt="facepalm" src="images/smileys/facepalm.gif"/>', $text);
$text = preg_replace('!:bye:!isU', '<img alt="bye" src="images/smileys/bye.gif"/>', $text);
$text = preg_replace('!:music:!isU', '<img alt="music" src="images/smileys/music.gif"/>', $text);
$text = preg_replace('!:what:!isU', '<img alt="what" src="images/smileys/what.gif"/>', $text);
return $text;
}
if(isset($_POST["newst"])){
$newss = $_POST["newst"];
echo replaceBBCode($newss);
}
?>