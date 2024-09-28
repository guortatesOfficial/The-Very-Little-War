<?php 
include("includes/basicprivatephp.php");

include("includes/tout.php");

debutCarte("Messages envoyés"); 
?>
<div class="table-responsive">
<?php 
$sql = 'SELECT * FROM messages WHERE expeditaire=\''.$_SESSION['login'].'\' ORDER BY timestamp DESC';
$ex = mysqli_query($base,$sql) or die('Erreur SQL !<br/>'.$sql.'<br/>'.mysql_error());
$nb_messages = mysqli_num_rows($ex);
if($nb_messages > 0) {
	echo '<table class="table table-striped table-bordered">
	<thead>
	<tr>
	<th>Titre</th>
	<th>Auteur</th>
	<th>Date</th>
	</tr></thead><tbody>';
	while($message = mysqli_fetch_array($ex)) {
		echo '<tr><td><a href="messages.php?message='.$message['id'].'">'.$message['titre'].'</a></td>';
		echo '<td><a href=joueur.php?id='.$message['destinataire'].'>'.$message['destinataire'].'</a></td>';
		echo '<td><em>'.date('d/m/Y à H\hi', $message['timestamp']).'</em></td></tr>';
	}
	echo '</tbody></table>';
}
else {
	echo "Vous n'avez envoyé aucun messages.";
}
?>
</div>

<?php 
finCarte();
include("includes/copyright.php"); ?>
