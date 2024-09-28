<?php
$base = mysql_connect('localhost', 'root', '');
mysql_select_db('theveryl_theverylittlewar', $base) or die('Erreur de connexion a la base de données' . mysql_error());
mysqli_query($base, "SET NAMES 'utf8'");
