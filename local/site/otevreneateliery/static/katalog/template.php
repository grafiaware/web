<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */


$institutionsSite = '6671583cb7df7';
$personsSite = '667153b3adac6';
$thisSite = '6697cf115d654';


$katalog=[]; // podle nazev-instituce rozpoznavat instituce/osoba
$katalog[]= array ( 'prijmeni' => 'Ateliér s duší', 'jmeno' => '', 'nazev-instituce' => 'Ateli&eacute;r s du&scaron;&iacute; ', 'instituce' => '1', 
                    'web-anchor' => 'atelier-s-dusi'  );
$katalog[]= array ( 'prijmeni' => 'Arteterapie Plzeň', 'jmeno' => '', 'nazev-instituce' => 'Arteterapie Plzeň', 'instituce' => '1',
                    'web-anchor' => 'arteterapie-plzen'  );
$katalog[]= array ( 'prijmeni' => 'Ateliér Dorido - Petra Fenclová', 'jmeno' => 'Ateli&eacute;r Dorido - Petra Fenclov&aacute;', 'nazev-instituce' => '', 'instituce' => '1',
                    'web-anchor' => 'atelier-dorido-petra-fenclova'  );

$katalog[]= array ( 'prijmeni' => 'Pankrác', 'jmeno' => 'Marketa  ',  'nazev-instituce' => '', 'instituce' => '0',
                    'web-anchor' => ''  );
$katalog[]= array ( 'prijmeni' => 'Lišková Mašková', 'jmeno' => 'Jaroslava','nazev-instituce' => '', 'instituce' => '0',
                    'web-anchor' => 'Li&scaron;kov&aacute; Ma&scaron;kov&aacute; Jaroslava'  );
$katalog[]= array ( 'prijmeni' => 'Liska Nordlinder', 'jmeno' => 'Magdalena',  'nazev-instituce' => '', 'instituce' => '0',
                    'web-anchor' => 'Liska Nordlinder Magdalena'  );

$katalog[]= array ( 'prijmeni' => '', 'jmeno' => '', 'nazev-instituce' => '', 'instituce' => '0','web-anchor' => ''  );


//Arteterapie Plzeň
//<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#arteterapie-plzen">Arteterapie Plzeň</a></p>
//Ateliér Dorido - Petra Fenclová
//Ateliér P.M. Markéta Pangrác
//Ateliér s duší 
//<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#atelier-dorido-petra-fenclova">Ateli&eacute;r Dorido - Petra Fenclov&aacute;</a></p>
//<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#atelier-p-m-marketa-pankrac">Ateli&eacute;r P.M. Mark&eacute;ta Pangr&aacute;c </a></p>
//<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#atelier-s-dusi">-->Ateli&eacute;r s du&scaron;&iacute; <!--</a>--></p>
//Liska Nordlinder Magdalena
//Lišková Mašková Jaroslava
//Lomičková Zuzana
//Lorencová Žaneta
//<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#magdalena-liska-nordliner">Liska Nordlinder Magdalena </a></p>
//<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#liskova-maskova-jaroslava">Li&scaron;kov&aacute; Ma&scaron;kov&aacute; Jaroslava</a></p>
//<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#lomickova-zuzana">-->Lomičkov&aacute; Zuzana<!--</a>--></p>
//<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#zaneta-lorencova">-->Lorencov&aacute; Žaneta<!--</a>--></p>
//Marketa Pankrác 


$volume  = array_column($katalog, 'prijmeni');
array_multisort($volume, SORT_ASC, $katalog);

$s=1;

$first=''; $chSet = [];
                        $institutions = [];$persons = [];
$chBlock = [];
foreach ($katalog as $client) {
    if ($client['nazev-instituce']) {
        if (substr($client['nazev-instituce'],0,1) != $first) {   //str_starts_with
            $first = substr($client['nazev-instituce'],0,1);                
            $chSet[] =  [ 'chNazev' => substr($client['nazev-instituce'],0,1), 'thisSite' => $thisSite  ] ;
            
            $chBlock [$first] =  $client;
            
            $chBlo[$first]['clients'] = $client;
            $chBlo[$first]['char'] = $first;
            ///??????
            $chBlo[]['clients'] = $client;
            $chBlo[]['char'] = $first;
            
        } else {
            $chBlock [$first] =  $client;
        }
    } else {    
        if (substr($client['prijmeni'],0,1) != $first) {   //str_starts_with
            $first = substr($client['prijmeni'],0,1);                
            $chSet[] =  [ 'chNazev' => substr($client['prijmeni'],0,1), 'thisSite' => $thisSite  ] ;
            
            $chBlock [$first] =  $client;
        } else {
            $chBlock [$first] =  $client;
        }
        
    } //nazev-instituce / prijmeni

//                        //---------------- // na nic
//                        if ($client['instituce']) {
//                            $institutions[] = $client;
//                        } else {
//                            $persons [] = $client;           
//                        }              
//                        //-------------------------
}
 $s=1; 

 
?>



<h2 style="text-align: center; margin-bottom: 30px;">*Umělci, kteří Vám představí svoji tvorbu.*</h2>

<div>
    <?= $this->repeat(__DIR__.'/katalog-chset.php', $chSet) ?>
</div>

<div>
    <?= $this->repeat(__DIR__.'/katalog-blockset.php', ) ?>
</div>










---------------------------
<h2 style="text-align: center; margin-bottom: 30px;">Umělci, kteř&iacute; V&aacute;m představ&iacute; svoji tvorbu.</h2>
<p>Mapa Plzně a okol&iacute; se zapln&iacute; zast&aacute;vkami, kde to 28. - 29. z&aacute;ř&iacute; 2024 ožije uměn&iacute;m. Pojďme se pod&iacute;vat, kdo otevře sv&eacute; ateli&eacute;ry! Přin&aacute;&scaron;&iacute;me V&aacute;m malou ochutn&aacute;vku jejich děl. Pokud zde nenajdete profily v&scaron;ech umělců, je to proto, že někter&yacute;m jejich boh&eacute;msk&aacute; du&scaron;e dosud nedopř&aacute;la čas k odesl&aacute;n&iacute; podkladů pro tento web. ;-)</p>




<p style="margin-top: 20px; text-align: center;">
    <a href="web/v1/page/item/66681f58db5e9#A"> A </a>|
    <a href="web/v1/page/item/66681f58db5e9#B"> B </a>| <a href="web/v1/page/item/66681f58db5e9#C"> C </a>|
    <a href="web/v1/page/item/66681f58db5e9#D"> D </a>|<a href="web/v1/page/item/66681f58db5e9#E"> E </a>|<a href="web/v1/page/item/66681f58db5e9#F"> F </a>|<a href="web/v1/page/item/66681f58db5e9#G"> G </a>|<a href="web/v1/page/item/66681f58db5e9#H"> H </a>|<a href="web/v1/page/item/66681f58db5e9#CH"> CH </a>|<a href="web/v1/page/item/66681f58db5e9#I"> I </a>|<a href="web/v1/page/item/66681f58db5e9#J"> J </a>|<a href="web/v1/page/item/66681f58db5e9#K"> K </a>|<a href="web/v1/page/item/66681f58db5e9#L"> L </a>|<a href="web/v1/page/item/66681f58db5e9#M"> M </a>|<a href="web/v1/page/item/66681f58db5e9#N"> N </a>|<a href="web/v1/page/item/66681f58db5e9#O"> O </a>|<a href="web/v1/page/item/66681f58db5e9#P"> P </a>| Q |<a href="web/v1/page/item/66681f58db5e9#R"> R, Ř </a>|<a href="web/v1/page/item/66681f58db5e9#S"> S, &Scaron; </a>|<a href="web/v1/page/item/66681f58db5e9#T"> T </a>|<a href="web/v1/page/item/66681f58db5e9#U"> U </a>|<a href="web/v1/page/item/66681f58db5e9#V"> V </a>|<a href="web/v1/page/item/66681f58db5e9#W"> W </a>| X | Y |
    <a href="web/v1/page/item/66681f58db5e9#Z"> Z </a></p>




<div style="display: inline-block; width: 25%;">
<p id="A" style="margin-top: 30px;"><strong>A</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#ambasador-atelier-markety-pangrac">Ambasador atelier Mark&eacute;ty Pangr&aacute;c</a></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#arteterapie-plzen">Arteterapie Plzeň</a></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#atelier-dorido-petra-fenclova">Ateli&eacute;r Dorido - Petra Fenclov&aacute;</a></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#atelier-p-m-marketa-pankrac">Ateli&eacute;r P.M. Mark&eacute;ta Pangr&aacute;c </a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#atelier-s-dusi">-->Ateli&eacute;r s du&scaron;&iacute; <!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#atelier-u-fandy">-->Ateli&eacute;r U Fandy<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#azyl-galerie">-->Azyl Galerie<!--</a>--></p>
<p id="B" style="margin-top: 10px;"><strong>B</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#bartipanova-daniela">-->Bařtip&aacute;nov&aacute; Daniela <!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#anna-bergerova">-->Bergerov&aacute; Anna<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#michaela-blechova">-->Blechov&aacute; Michaela<!--</a>--></p>
<p id="C" style="margin-top: 10px;"><strong>C, Č</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#cepnik-roubalova-michaela">Cepn&iacute;k Roubalov&aacute; Michaela</a></p>








<p id="D" style="margin-top: 10px;"><strong>D</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#dagmar-egyudova">Dagmar Egyudova </a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#sups-a-zus-zamecek">-->D&iacute;lna SUP&Scaron; Z&aacute;meček U Zvonu - kamenosochaři a dal&scaron;&iacute; uměleck&eacute; obory<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#domaci-galerie-nad-udolim">-->Dom&aacute;c&iacute; galerie Nad &uacute;dol&iacute;m<!--</a>--></p>
<p id="F" style="margin-top: 10px;"><strong>F</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#fakulta-designu-a-umeni-ladislava-sutnara">Fakulta designu a uměn&iacute; Ladislava Sutnara</a></p>
<p id="G" style="margin-top: 10px;"><strong>G</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#galerie-bonifac">Galerie Bonif&aacute;c</a></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#galerie-evropskeho-domu-svk-pk">Galerie Evropsk&eacute;ho domu SVK PK</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#galerie-jiriho-trnky-galeria-dominikanska-12">-->Galerie Jiř&iacute;ho Trnky/ Galerie Dominik&aacute;nsk&aacute; 12<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#galerie-ladistava-sutnara">-->Galerie Ladislava Sutnara<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#galeria-u-andelicka">-->Galerie u Anděl&iacute;čka, z. s.<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#galerie-u-bileho-jednorozce">-->Galerie U B&iacute;l&eacute;ho jednorožce<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#galerie-ve-stodole">-->Galerie Ve stodole<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#gamp">-->GAMP<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#weronika-gray">-->Gray Weronika<!--</a>--></p>
<p id="H" style="margin-top: 10px;"><strong>H</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#hana-novackova-carovna-zahrada">-->Hana Nov&aacute;čkov&aacute; - Čarovn&aacute; zahrada<!--</a>--></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#anastasia-hurianova">Hurianova Anastasiia </a></p>
<p id="I" style="margin-top: 10px;"><strong>I</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#informacni-a-vzdelavaci-centrum-plzen-a-cvt-z-s">Informačn&iacute; a vzděl&aacute;vac&iacute; centrum Plzeň a CVT z.s.</a></p>
<p id="K" style="margin-top: 10px;"><strong>K</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#kavarna-dama-a-kral">-->Kav&aacute;rna DAMA a KRAL<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#jeremy-king">-->King Jeremy<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#klub-novych-komunikaci">-->Klub nov&yacute;ch komunikac&iacute;. KULTURKA - Z&aacute;padočesk&aacute; univerzita v Plzni<!--</a>--></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#knihovna-mesta-plzne">Knihovna města Plzně</a></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#marcela-kotesovcova">Kotě&scaron;ovcov&aacute; Marcela</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#eliska-marie-kozlikova">-->Kozl&iacute;kov&aacute; Eli&scaron;ka Marie<!--</a>--></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#lada-kratochvilova">Kratochv&iacute;lov&aacute; Lada</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#kroslakova-tereza-ayama">-->Kro&scaron;l&aacute;kov&aacute; Tereza Ayama<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#kulturka">-->Kulturka ŽČU<!--</a>--></p>
<p id="L" style="margin-top: 10px;"><strong>L</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#zdenek-langmajer">-->Langmajer Zdeněk<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#kulturka">-->Le Consulat<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#le-consulat">-->Le Consulat<!--</a>--></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#magdalena-liska-nordliner">Liska Nordlinder Magdalena </a></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#liskova-maskova-jaroslava">Li&scaron;kov&aacute; Ma&scaron;kov&aacute; Jaroslava</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#lomickova-zuzana">-->Lomičkov&aacute; Zuzana<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#zaneta-lorencova">-->Lorencov&aacute; Žaneta<!--</a>--></p>
<p id="M" style="margin-top: 10px;"><strong>M</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#ambasador-atelier-markety-pangrac">Marketa Pankr&aacute;c </a></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#mato-tattoo-romana-zabkova">Matto Tattoo - Romana Žabkov&aacute;</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#mladova-barbora">-->Ml&aacute;dov&aacute; Barbora <!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#jana-myslikova">-->Mysl&iacute;kov&aacute; Jana<!--</a>--></p>
<p id="N" style="margin-top: 10px;"><strong>N</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#na-brehu-rhony">-->Na Břehu Rh&ocirc;ny<!--</a>--></p>
<p id="O" style="margin-top: 10px;"><strong>O</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#optik-studio-svarc">OPTIK STUDIO &Scaron;varc</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#ozzmanovo-malovani-hasicarna-v-obci-zahradka-plzen-sever">-->Ozzmenovo malov&aacute;n&iacute; Hasič&aacute;rna v obci Zahr&aacute;dka Plzen sever<!--</a>--></p>
<p id="P" style="margin-top: 10px;"><strong>P</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#papirna-plzen">-->Pap&iacute;rna Plzeň<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#pavilon-skla-klatovy-pask">-->Pavilon skla Klatovy PASK <!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#prostory-sdh-svrcovec">-->Prostory SDH Svrčovec<!--</a>--></p>
<p id="R" style="margin-top: 10px;"><strong>R, Ř</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#radka-nova">-->Radka Nov&aacute;<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#raunerova-dana">-->Raunerov&aacute; Dana<!--</a>--></p>
<p id="S" style="margin-top: 10px;"><strong>S, &Scaron;</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#ivana-sedmerova">Sedmerov&aacute; Ivana</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#simona-schmiedhuberova">-->Schmiedhuberov&aacute; Simona<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#sojkatelier">-->SojkAtelier<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#jan-stehule">-->Stěhule Jan<!--</a>--></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#stredni-odborne-uciliste-stavebni">Středn&iacute; odborn&eacute; učili&scaron;tě stavebn&iacute;</a></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#studio-jiras-slunecni-atelier">-->Studio Jiras / Slunečn&iacute; ateli&eacute;r z.s.<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#ivana-siplakova">-->&Scaron;ipl&aacute;kov&aacute; Ivana<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#jana-snajdrova">-->&Scaron;najdrov&aacute; Jana<!--</a>--></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/667153b3adac6#jiri-svarny">&Scaron;varn&yacute; Jiř&iacute; </a></p>
<p id="V" style="margin-top: 10px;"><strong>V</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#vladimira-pisarova-zivna">-->Vladim&iacute;ra P&iacute;sařov&aacute; Živn&aacute;<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#vytvarno">-->V&yacute;tvarno z.s.<!--</a>--></p>
<p id="Z" style="margin-top: 10px;"><strong>Z, Ž</strong></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#zapadoceska-galerie-v-plzni">-->Z&aacute;padočesk&aacute; galerie v Plzni <!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/667153b3adac6#renata-zitkova">-->Z&iacute;tkov&aacute; Ren&aacute;ta<!--</a>--></p>
<p style="margin-top: 5px;"><!--<a href="web/v1/page/item/6671583cb7df7#zus-zamecek">-->ZU&Scaron; Z&aacute;meček <!--</a>--></p>
</div>
<div style="float: right; margin-top: 30px; margin-right: 5px;"><img src="_files/otevreneateliery/files/2132088.png" alt="kol&aacute;ž umělců" width="550"></div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>