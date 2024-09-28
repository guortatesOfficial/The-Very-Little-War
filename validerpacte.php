<?php 
include("includes/basicprivatephp.php");

if(isset($_POST['idDeclaration'])) {
	$ex = mysqli_query($base,'SELECT count(*) AS existe FROM declarations WHERE id=\''.$_POST['idDeclaration'].'\' AND valide=0');
	$donnees = mysqli_fetch_array($ex);
	
	if($donnees['existe'] == 1) {
		if(isset($_POST['accepter'])) {
			mysqli_query($base,'UPDATE declarations SET valide=1 WHERE id=\''.$_POST['idDeclaration'].'\'');
		}
		else {
			mysqli_query($base,'DELETE FROM declarations WHERE id=\''.$_POST['idDeclaration'].'\'');
		}
	}
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<?php include("includes/meta.php"); ?>
<title>The Very Little War - Valider</title>
<link rel="stylesheet" type="text/css" href="style.css" >
</head>
<body>
<script LANGUAGE="JavaScript">
window.location= "rapports.php";
</script>
</body>
</html>