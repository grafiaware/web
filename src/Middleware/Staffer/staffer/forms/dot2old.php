<?php if(@$_GET['krok']!=3) {//dodělal Lukáš - aby se to u uložení nezobrazovalo ?>
<fieldset><legend>Kontaktní údaje</legend>
<p><?PHP text10_ ('titul',$pole['titul'],$krok);?> &nbsp;&nbsp;<?PHP text50 ('jmeno',$pole['jmeno'],$krok);?> &nbsp;&nbsp; <?PHP text50 ('prijmeni',$pole['prijmeni'],$krok);?>
</p><p><br>Adresa<br>
<?PHP text50 ('ulice',$pole['ulice'],$krok);?>&nbsp;<?PHP cislo_domu('cislo_pop',$pole['cislo_pop'],$krok);?>
<br><?PHP text50 ('mesto',$pole['mesto'],$krok);?>&nbsp;<?PHP psc ('psc',$pole['psc'],$krok);?>
</p>
<p><br><?PHP telefon ('telefon',$pole['telefon'],$krok);?> &nbsp;&nbsp;<?PHP email ('mail',$pole['mail'],$krok);?>
</p>
</fieldset>
<fieldset><legend>Životopis</legend>
<?PHP $pole['zivotopis'] = zivotopis ('zivotopis',$pole['zivotopis'],$krok);?>
</fieldset>
<fieldset>
<?PHP poznamka_ ('poznamka',$pole['poznamka'],$krok);?>
</fieldset>
<?php }//opět tvořil Lukáš ?>
