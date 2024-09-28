        <div style="text-align:center">
           <?php
            echo '<a href="#" data-popover=".popover-ressources" class="open-popover">'.chipInfo('Atomes','images/atom.png').'</a>';
            echo '<a href="#" data-popover=".popover-detailsEnergie" class="open-popover">'.nombreEnergie('<span id="affichageenergie">'.chiffrePetit($ressources['energie']).'/'.$ressourcesMax.'</span> <span style="color:green;margin-left:10px"> +'.chiffrePetit(revenuEnergie($constructions['generateur'],$_SESSION['login'])).'/h').'</a>';
            ?>
        </div>


