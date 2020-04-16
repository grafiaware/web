<?php 
if(@$_GET['krok']!=3) {//dodělal Lukáš - aby se to u uložení nezobrazovalo 
//echo "**dot1*";
//echo "<br>**pole*";
//print_r ( $pole);
//echo "**<br>**";
//print_r ( $pole['souhlas']);
?>
<p><b>Údaje označené )* jsou povinné!</b></p>

<fieldset><legend>Kontaktní údaje</legend>
<p><?PHP text10_ ('titul',$pole['titul'],$krok, 'staffer_tabelator1');?> &nbsp;&nbsp;
   <?PHP text50 ('jmeno',$pole['jmeno'],$krok);?> &nbsp;&nbsp; 
   <?PHP text50 ('prijmeni',$pole['prijmeni'],$krok);?>
</p>
<p><br>Adresa<br>
    <?PHP text50 ('ulice',$pole['ulice'],$krok,'staffer_tabelator1');?>&nbsp;
    <?PHP cislo_domu('cislo_pop',$pole['cislo_pop'],$krok, 'staffer_tabelator1');?>
<br><?PHP text50 ('mesto',$pole['mesto'],$krok,'staffer_tabelator1');?>&nbsp;
    <?PHP psc ('psc',$pole['psc'],$krok,'staffer_tabelator1' );?>
</p>
<p><br><?PHP telefon ('telefon',$pole['telefon'],$krok,'staffer_tabelator1');?> &nbsp;&nbsp;
       <?PHP email ('mail',$pole['mail'],$krok);?>
</p>
</fieldset>


<fieldset><legend>Životopis</legend>
<p>
    Tyto údaje jsou zcela dobrovolné. Přihlášku můžete odeslat i bez jejich vyplnění.<br>
</p>    
<br>
<p><?PHP text10_ ('narozeni',$pole['narozeni'],$krok,'staffer_tabelator2');?>  &nbsp;&nbsp;&nbsp;&nbsp;
   <?PHP stav ('stav',$pole['stav'],$krok);?> &nbsp;&nbsp;<br>
   <?PHP vzdelani ('vzdelani',$pole['vzdelani'],$krok,'staffer_tabelator2');?> &nbsp;&nbsp;
   <?PHP ridicak ('ridicak',$pole['ridicak'],$krok);?>
</p>
<br>
<p><?PHP pc ('pc',$pole['pc'],$krok);?> &nbsp;&nbsp;&nbsp;&nbsp;<br><br>
    
    <?PHP jazyk ('jazyk1',$pole['jazyk1'],$krok);?> &nbsp;&nbsp;&nbsp;&nbsp;
    <?PHP jazyk_uroven ('jazyk_uroven1',$pole['jazyk_uroven1'],$krok);?><br>
    <?PHP jazyk ('jazyk2',$pole['jazyk2'],$krok);?> &nbsp;&nbsp;&nbsp;&nbsp;
    <?PHP jazyk_uroven ('jazyk_uroven2',$pole['jazyk_uroven2'],$krok);?><br>
    <?PHP jazyk ('jazyk3',$pole['jazyk3'],$krok);?> &nbsp;&nbsp;&nbsp;&nbsp;
    <?PHP jazyk_uroven ('jazyk_uroven3',$pole['jazyk_uroven3'],$krok);?>
</p>
<br>
<p>Absolvované kurzy a školení<br>
<?PHP kurz ('kurz1',$pole['kurz1'],$krok);?><br>
<?PHP kurz ('kurz2',$pole['kurz2'],$krok);?><br>
<?PHP kurz ('kurz3',$pole['kurz3'],$krok);?><br>
</p>
<br>
<p>Dosavadní zaměstnání<br>
<b>1.</b> <?PHP text255_ ('firma1',$pole['firma1'],$krok);?> &nbsp;&nbsp;<br>
    <?PHP text255_ ('pozice1',$pole['pozice1'],$krok,'staffer_tabelator3');?><br>
    <?PHP text10_ ('zac1',$pole['zac1'],$krok,'staffer_tabelator3');?> &nbsp;&nbsp;
    <?PHP text10_ ('kon1',$pole['kon1'],$krok);?><br><br>
<b>2.</b> <?PHP text255_ ('firma2',$pole['firma2'],$krok);?> &nbsp;&nbsp;<br>
    <?PHP text255_ ('pozice2',$pole['pozice2'],$krok,'staffer_tabelator3');?><br>
    <?PHP text10_ ('zac2',$pole['zac2'],$krok,'staffer_tabelator3');?> &nbsp;&nbsp;
    <?PHP text10_ ('kon2',$pole['kon2'],$krok);?><br><br>
<b>3.</b> <?PHP text255_ ('firma3',$pole['firma3'],$krok);?> &nbsp;&nbsp;<br>
    <?PHP text255_ ('pozice3',$pole['pozice3'],$krok,'staffer_tabelator3');?><br>
    <?PHP text10_ ('zac3',$pole['zac3'],$krok,'staffer_tabelator3');?>&nbsp;&nbsp;
    <?PHP text10_ ('kon3',$pole['kon3'],$krok);?><br><br>
</p>
</fieldset>


<p>
<fieldset><legend>Životopis-příloha</legend>
<?PHP $pole['zivotopis_nepovinny'] = zivotopis_nepovinny ('zivotopis_nepovinny',$pole['zivotopis_nepovinny'],$krok);?>
</fieldset>
</p>

<p>
<?PHP poznamka_ ('poznamka',$pole['poznamka'],$krok);?><br>
</p>
    
<p>    
<?PHP checkBpovinny ('souhlas',$pole['souhlas'],$krok);
?>
<br><br>
Kliknutím na níže uvedené tlačítko pro odeslání souhlasím se zpracováním a uchováním veškerých mých osobních údajů,
které poskytuji prostřednictvím internetové služby spravované společností GRAFIA, s.r.o.<br> 
Osobní údaje budou zpracovány v souladu s rozhodnutím Komise EU ze dne 15. června 2001 a zákonem 101/2000 Sb.<br>
Tyto údaje poskytuji společnosti GRAFIA, s.r.o. výhradně za účelem získání zaměstnání a to nejdéle však na 1 rok od jejich zaslání.
Kdykoli budu moci požádat společnost GRAFIA, s.r.o. o smazání nebo o opravu osobních údajů.
<br> Dne  
<?php  echo date ("d.m.Y H:i"); ?>.
</p>


<?php } ?>
