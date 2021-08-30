<DIV ID="rs_tlacitka">
<input name="www" type="button" value="Editace WWW stránek" onClick="location.href='index.php?app=rs'"
        <?php IF ($_SESSION ["sess_app"]=='rs') {echo 'disabled';}?>>
<?php
if ($sess_prava['edun']) {?>
        <input name="edun" type="button" value="Vzdělávací kurzy" onClick="location.href='index.php?app=edun'"
        <?php IF ($_SESSION ["sess_app"]=='edun') {echo 'disabled';}?>>
<?php
;}
?>
           
<?php
if ($sess_prava['staffer']) {?>
        <input name="staffer" type="button" value="Volné pracovní pozice" onClick="location.href='index.php?app=staffer'" 
        <?php IF ($_SESSION ["sess_app"]=='staffer') {echo 'disabled';}?>>
<?php
;}

?>        
       
</DIV>
