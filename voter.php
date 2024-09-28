<?php
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Origin: *");
$base = mysqli_connect ('localhost', 'theveryl_admin', 'mno33d65e') ; 
mysqli_select_db ($base,'theveryl_testconnexion')or die ('Erreur de connexion a la base de données'.mysql_error()); 
mysqli_query($base,"SET NAMES 'utf8'");

if(isset($_GET['reponse']) && isset($_GET['login'])){
	if(!empty($_GET['login']) && !empty($_GET['reponse'])){
		$ex = mysqli_query($base,'SELECT * FROM sondages ORDER BY date DESC');
		$data = mysqli_fetch_array($ex);

		$ex = mysqli_query($base,'SELECT count(*),id AS nb  FROM reponses WHERE login=\''.$_GET['login'].'\' AND sondage=\''.$data['id'].'\'');
		$reponse = mysqli_fetch_array($ex);

		if($reponse['nb'] == 0){
			mysqli_query($base,'INSERT INTO reponses VALUES(default,"'.$_GET['login'].'","'.$data['id'].'","'.$_GET['reponse'].'")');
			exit(json_encode(array("erreur" => false,"dejaRepondu" => false)));
		}
		else {
			if(!isset($_GET['pasDeVote'])){
				mysqli_query($base,'UPDATE reponses SET reponse=\''.$_GET['reponse'].'\' WHERE login=\''.$_GET['login'].'\' AND sondage=\''.$data['id'].'\'');
			}
			exit(json_encode(array("erreur" => false,"dejaRepondu" => true)));
		}
	}
	else {
		exit(json_encode(array("erreur" => true)));
	}
}
