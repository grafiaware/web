<?php if(@$_GET['krok']!=3) {//dodělal Lukáš - aby se to u uložení nezobrazovalo ?>
<fieldset><legend>Kontaktní údaje</legend>
<?PHP text50 ('jmeno',$pole['jmeno'],$krok);?> &nbsp;&nbsp; <?PHP text50 ('prijmeni',$pole['prijmeni'],$krok);?>
</fieldset>
<?php }//opět tvořil Lukáš ?>
