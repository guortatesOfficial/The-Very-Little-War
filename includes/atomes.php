<br/>  
<div style="text-align:center"><?php
           echo nombrePoints($autre['totalPoints']);
            $nb_molecules = 0;
            $ex = query('SELECT nombre FROM molecules WHERE proprietaire=\''.$_SESSION['login'].'\'');
            while($nb = mysqli_fetch_array($ex)){
                
                $nb_molecules += ceil($nb['nombre']);
            }

            echo nombreMolecules($nb_molecules);
            ?>
</div>