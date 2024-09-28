<?php
include("includes/basicprivatephp.php");
$p = time().','.time().','.time().','.time().',';
mysqli_query($base,'UPDATE constructions SET generateurcarbone=20 WHERE login="a"');