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
<p><?PHP text10_ ('narozeni',$pole['narozeni'],$krok);?>  &nbsp;&nbsp;&nbsp;&nbsp;<?PHP stav ('stav',$pole['stav'],$krok);?> &nbsp;&nbsp;<?PHP vzdelani ('vzdelani',$pole['vzdelani'],$krok);?> &nbsp;&nbsp;<?PHP ridicak ('ridicak',$pole['ridicak'],$krok);?>
</p>
<p><br><?PHP pc ('pc',$pole['pc'],$krok);?> &nbsp;&nbsp;&nbsp;&nbsp;<?PHP jazyk ('jazyk',$pole['jazyk'],$krok);?> &nbsp;&nbsp;&nbsp;&nbsp;<?PHP jazyk_uroven ('jazyk_uroven',$pole['jazyk_uroven'],$krok);?>
</p>
<br>
<p>Absolvované kurzy a školení<br>
<?PHP kurz ('kurz1',$pole['kurz1'],$krok);?><br>
<?PHP kurz ('kurz2',$pole['kurz2'],$krok);?><br>
<?PHP kurz ('kurz3',$pole['kurz3'],$krok);?><br>
</p>
<p><br>Dosavadní zaměstnání<br>
<b>1.</b> <?PHP text255_ ('firma1',$pole['firma1'],$krok);?> &nbsp;&nbsp;<?PHP text255_ ('pozice1',$pole['pozice1'],$krok);?><br>
&nbsp;<?PHP text10_ ('zac1',$pole['zac1'],$krok);?> &nbsp;&nbsp;<?PHP text10_ ('kon1',$pole['kon1'],$krok);?><br><br>
<b>2.</b> <?PHP text255_ ('firma2',$pole['firma2'],$krok);?> &nbsp;&nbsp;<?PHP text255_ ('pozice2',$pole['pozice2'],$krok);?><br>
&nbsp;<?PHP text10_ ('zac2',$pole['zac2'],$krok);?> &nbsp;&nbsp;<?PHP text10_ ('kon2',$pole['kon2'],$krok);?><br><br>
<b>3.</b> <?PHP text255_ ('firma3',$pole['firma3'],$krok);?> &nbsp;&nbsp;<?PHP text255_ ('pozice3',$pole['pozice3'],$krok);?><br>
&nbsp;<?PHP text10_ ('zac3',$pole['zac3'],$krok);?> &nbsp;&nbsp;<?PHP text10_ ('kon3',$pole['kon3'],$krok);?><br><br>
</p>
</fieldset>
<fieldset>
<?PHP poznamka_ ('poznamka',$pole['poznamka'],$krok);?>
</fieldset>
<?php }//opět tvořil Lukáš ?>
