<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();

if ($sess_prava[$stranka]) {
// TODO: Svoboda $use - hmm..
    if ($use) {echo '<p class=chyba>Tuto stránku právě upravuje uživatel <b>'.$use.'</b></p>' .
           '<p>Stránku budete moci editovat, až uživatel dokončí úpravy.</p>' .
           '<p><i>Pokud je stránka nedostupná i zjevně po ukončení práce ostatních uživatelů, ' .
           'pak se výše zmíněný uživatel neodhlásil ze systému. Stránka se odblokuje po 60-ti minutách.</i></p>';}
else {
$db_nazev = 'nazev_'.$lang;
$db_obsah = 'obsah_'.$lang;
$db_aktiv = 'aktiv_'.$lang;
$db_aktivstart = 'aktiv_'.$lang.'start';
$db_aktivstop = 'aktiv_'.$lang.'stop';
$db_keywords = 'keywords_'.$lang;

$statement = $handler->query("SELECT $db_nazev,$db_obsah,$db_aktiv,$db_aktivstart,$db_aktivstop,$db_keywords, aut_gen, editor FROM stranky WHERE list='$stranka'");
$statement->execute();
$zaznam = $statement->fetch(PDO::FETCH_ASSOC);

?>
<H3><?php echo $zaznam[$db_nazev];?></h3>
<?php
  if ($zobraz_prvek['multi_lang']) {
    IF ($lang == 'lan1') {echo '<H4>'.$lan1[1].'</H4>';}
    IF ($lang == 'lan2') {echo '<H4>'.$lan2[1].'</H4>';}
    IF ($lang == 'lan3') {echo '<H4>'.$lan3[1].'</H4>';}
  }

if (($zaznam['aut_gen'] == 0)  or ($zaznam['aut_gen'] == 2)) {   //EDITOVAT
//if (1) {
                                                                //hodnota 0 - "obycejne stranky"  - chci editovat
                                                                //hodnota 1 - stranka generovana (puvodne jen pro planovane kurzy (vytvoreny harmonogram)) - neni mozno editovat
                                                                //hodnota 2 - "kmenova" stranka pro katalog - chci editovat
                                                                //21,22,23.... - "podstranky" katalogu - nechci editovat
//echo "<br>***list***" . $list;  // *SEL*

$urlEditorCss = Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/css/editor.css';
$urlPrefixTemplatesTinyMce = Middleware\Rs\AppContext::getPublicDirectory().'tiny_templates/';

$urlSemanticCss = Web\Middleware\Web\AppContext::getAppPublicDirectory().'semantic/dist/semantic.min.css';
$urlZkouskaCss = Web\Middleware\Web\AppContext::getAppPublicDirectory().'grafia/css/zkouska_less.css';

// TODO: Svoboda - zřejmě se POST proměnná edit nikde nepoužívá (sloupec editor se nečetl z db) - navíc jde o bezpečnostní riziko - v prohlížeči vidím uživatelské jméno
// řádek 253 a totéž na 286: <input type="hidden" name="edit" value="<?php echo $zaznam['editor' ...

?>
<form method="POST" action="index.php?app=rs&list=stranky&language=<?php echo $lang;?>&stranka=<?php echo $stranka;?>">
<!-- ZACATEK STAVU STRANKY -->
<fieldset>
<legend>Stav stránky</legend>
<input type="radio" name="aktiv" value="0" <?php IF ($zaznam[$db_aktiv] == 0) {echo 'checked';}?>> <span class="rs_obdobi_neuverejnene">Neuveřejněná</span><br>
<input type="radio" name="aktiv" value="1" <?php IF ($zaznam[$db_aktiv] == 1) {echo 'checked';}?>> <span class="rs_obdobi_uverejnene">Uveřejněná</span><br>
<input type="radio" name="aktiv" value="2" <?php IF ($zaznam[$db_aktiv] == 2) {echo 'checked';}?>> <span class="rs_obdobi_oddo">Uveřejnit v období <i>(rok-měsíc-den)</i></span>
<?php include Middleware\Rs\AppContext::getScriptsDirectory().'contents/kalendar.php';?>
<DIV class="rs_float_r"><input type="submit" value="ULOŽIT STAV"></DIV>
</fieldset>
<!-- KONEC STAVU STRANKY -->
<!-- ZACATEK NAZVU STRANKY -->
<fieldset>
<legend>Název stránky</legend>
<input type="text" name="nazev" size="76" value="<?php echo $zaznam[$db_nazev];?>" maxlength="100">
</fieldset>
<!-- KONEC NAZVU STRANKY -->
<!-- ZACATEK KEYWORDS STRANKY -->
<fieldset>
<legend>Klíčová slova - <i>jednotlivá slova oddělujte čárkou</i></legend>
<input type="text" name="keywords" size="76" value="<?php echo $zaznam[$db_keywords];?>" maxlength="500">
</fieldset>
<!-- KONEC ZACATEK KEYWORDS STRANKY -->


<!-- ZACATEK EDITORU -->
<!-- Nastaveni EDITORU -->

<script language="javascript" type="text/javascript" src=" <?= Middleware\Rs\AppContext::getPublicDirectory().'tinymce/tinymce.min.js'; ?>"></script>
<script language="javascript" type="text/javascript">

tinymce.init({
  selector: 'textarea',
  schema : 'html5',
  language : 'cs',

  plugins: [
       "advlist autolink lists link  charmap  preview hr anchor pagebreak image code", // codesample print  //
       "searchreplace wordcount visualblocks visualchars code fullscreen",
       "insertdatetime  nonbreaking save autosave table contextmenu directionality",
       "template paste textcolor colorpicker textpattern searchreplace"
   ],
  //menubar: "tools...."; //pak vyjmenovat

  toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent |" +
            " hr | nonbreaking | link image ",
  toolbar2: " preview  | forecolor backcolor " + " | fontselect  fontsizeselect | code | searchreplace template",  // charmap",
  textcolor_map: [
    "000000", "Black",
    "808080","Gray",
    "999999","Medium gray",
    "FFFFFF","White",
    "EB1B4C",'Grafia-červená',
    '000080','Grafia-modrá' ],
/*'D7EF73','PersonalService-sv.zelená', '9DA677','PersonalService-tm.zelená',
'005799','Alternativní práce-modrá',  '7DAC37','Alternativní práce-zelená',  'C1011D','Alternativní práce-červená',
'55AB26', 'Cristal-zelená' */

  font_formats:
        "Arial=arial,helvetica,sans-serif;"+
        "Arial Black=arial black,avant garde;"+
        "Impact=impact,chicago;"+
        //"Symbol=symbol;"+
        "Verdana=verdana,geneva;",
        //"Webdings=webdings;"+
        //"Wingdings=wingdings,zapf dingbats",
  fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
  insertdatetime_formats: [ "%d.%m.%Y", "%H:%M:%S" ],

  image_advtab: true,
  image_title: true,
  //imagetools_toolbar: "flipv fliph | editimage imageoptions",
  image_class_list: [    //classy se po vybrani neskladaji
        {title: 'Vyberte styl obrázku (Vyp.)', value: ''},
        {title: 'Obrázek obtékaný zprava', value: 'image_vlevo'},
        {title: 'Obrázek obtékaný zleva', value: 'image_vpravo'},
        {title: 'Rámeček 1px černý', value: 'image_ramecek'},
        {title: 'Obtékaný zprava a rámeček', value: 'image_vlevo_ramecek'},
        {title: 'Obtékaný zleva a rámeček', value: 'image_vpravo_ramecek'}
  ],
  link_class_list: [
        {title: 'Odkaz jako tlačítko (Vyp.)', value: ''},
        {title: 'Primární tlačítko', value: 'ui primary button'},
        {title: 'Sekundární tlačítko', value: 'ui secondary button'}
  ],

    //image_prepend_url: "http://www.tinymce.com/images/",   //???
    //plugins: "image imagetools",
    //imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions",
    //imagetools_cors_hosts : "www.grafia.cz";//???

    //image_prepend_url: "localhost/WWWweby/www_grafia/", //???

//    color_picker_callback: function(callback, value) {
//    callback('#FF00FF');
//    } ,

    content_css :    ["<?=$urlEditorCss?>", "<?=$urlSemanticCss?>", "<?=$urlZkouskaCss?>"],

    templates: [
        { title: 'Kontakt', description: 'Grafia web - kontakt',       url: '<?=$urlPrefixTemplatesTinyMce?>kontakt.html'}, //vztaženo k rootu RS, tam kde je index redakčního s.
        { title: 'Publikace', description: 'Grafia web - publikace',   url: '<?=$urlPrefixTemplatesTinyMce?>publikace.html'},
        { title: 'Bloky na titulku', description: 'Grafia web - bloky na titulku', url: '<?=$urlPrefixTemplatesTinyMce?>titulka.html'},
        { title: 'Bloky na titulku Semantic', description: 'Grafia web - bloky na titulku', url: '<?=$urlPrefixTemplatesTinyMce?>titulka_1.html'},
        { title: 'Stránka', description: 'Grafia web - stránka',        url: '<?=$urlPrefixTemplatesTinyMce?>stranka.html'},
        { title: '----',    description: 'oddelovac',  url: "" },
        { title: "Odkaz(y) celoplošné na střed", description: "HTML bubliny." ,    url: '<?=$urlPrefixTemplatesTinyMce?>odkaz_bubliny_stred_flex.html' },
        { title: "Odkaz(y) 3D tlačítka na střed", description: "HTML bubliny." ,   url: '<?=$urlPrefixTemplatesTinyMce?>odkaz_3D_tlacitka_stred_flex.html' },
        { title: "Blok na střed", description: "Blok na střed." ,     url: '<?=$urlPrefixTemplatesTinyMce?>box_stred_flex.html' },
        { title: "Blok s legendou", description: "Blok s legendou." ,     url: '<?=$urlPrefixTemplatesTinyMce?>blok_s_legendou.html' },
        { title: '----',    description: 'oddelovac',  url: "" },
        { title: 'Obrázek vpravo a text', description: 'Bez obtékání. Dva sloupce', url: '<?=$urlPrefixTemplatesTinyMce?>obrazekVpravo_blok.html'},
        { title: 'Obrázek vlevo a text', description: 'Bez obtékání. Dva sloupce', url: '<?=$urlPrefixTemplatesTinyMce?>obrazekVlevo_blok.html'},
        { title: 'Publikace - 2', description: 'Vložení publikací na stránku', url: '<?=$urlPrefixTemplatesTinyMce?>eshop_radka.html'},
        { title: 'Publikace - 1', description: 'Vložení publikace na stránku', url: '<?=$urlPrefixTemplatesTinyMce?>eshop_nove.html'},
        { title: '---Tvorba šablon---',    description: 'oddelovac',  url: "" },
        { title: "Nutné k vytvoření šablon", description: "Vložte nejprve tuto šablonu a do ní vkládejte ostatní prvky této sekce" , url: '<?=$urlPrefixTemplatesTinyMce?>grid.html' },
        { title: 'Ohraničený blok', description: 'Univerzální šablona pro vytvoření bloků', url: '<?=$urlPrefixTemplatesTinyMce?>ohraniceny_blok.html'},
        { title: 'Neohraničený blok', description: 'Univerzální šablona pro vytvoření bloků', url: '<?=$urlPrefixTemplatesTinyMce?>neohraniceny_blok.html'},

       // { title: "Odkazy 3D tlačítka PAR", description: "HTML bubliny." ,   url: '<?=$urlPrefixTemplatesTinyMce?>odkazy_3D_tlacitka_flexPAR.html' },
       // { title: "Odkaz celoplošný - vlevo (DIV)", description: "HTML bublina.",  url: '<?=$urlPrefixTemplatesTinyMce?>odkaz_bublina_vlevo.html'} ,

        //{ title: "2-sloupcovy blok", description: "2-sloupcový blok" , url: '<?=$urlPrefixTemplatesTinyMce?>2-sloupcovy_blok.html' },
        //{ title: 'Divy a itemy',    description: '',  url: '<?=$urlPrefixTemplatesTinyMce?>divy_itemy.html' },

        //{ title: "Bubliny v textu vlevo a vpravo" ,  description: "HTML bubliny.",  url: '<?=$urlPrefixTemplatesTinyMce?>bubliny_vtextu_vlevo_a_vpravo.html' },
        //{ title: "Box" ,  description: "HTML bublina.",  url: '<?=$urlPrefixTemplatesTinyMce?>bublina.html' },
        //{ title: 'Box 100% šířka',description: "HTML bublina.",  url: '<?=$urlPrefixTemplatesTinyMce?>bublina100.html' },
//        { title: 'Šablona cvičná 1 - z .php kódu', description: 'Šablona cvičná 1 - z .php kódu' , url: '<?=$urlPrefixTemplatesTinyMce?>tem1.php' },
//        [ title: "Simple snippet", description: "Simple HTML snippet.", url: '<?=$urlPrefixTemplatesTinyMce?>snippet1.htm'  ],
//	  ["Layout", '<?=$urlPrefixTemplatesTinyMce?>layout1.htm', "HTML Layout."],
//        [ title: "Mustr",  description: "HTML mustr." ,       url: '<?=$urlPrefixTemplatesTinyMce?>mustr.html' ],
//        [ title: "Odkazy bubliny na stred (DIV fl)", "HTML bubliny." ,  url: '<?=$urlPrefixTemplatesTinyMce?>odkazy_bubliny_stred_flex.html' ],
//        [ title: "Odkaz bublina vlevo (DIV)", description: "HTML bublina.",  url: '<?=$urlPrefixTemplatesTinyMce?>odkaz_bublina_vlevo.html'],  //  class .odkaz_vtextu_bublina_vlevo tez styl
//        [ title: "Bublina 3D",  description: "HTML bubliny.",                url: '<?=$urlPrefixTemplatesTinyMce?>bublina_3D.html'],
//        ["4 bubliny nestred DIV", '<?=$urlPrefixTemplatesTinyMce?>bubliny_4.html', "HTML bubliny."],

    ],

    style_formats_merge: true,
    style_formats : [   //ovlivni Formaty (Formats)
        //{title : '-Bold text', inline : 'b'},
        //{title : '-Zeleny text', inline : 'span', styles : {color : 'green'} },
        {title: "Headers", items: [
            {title: "Header 1", format: "h1"},
            {title: "Header 2", format: "h2"},
            {title: "Header 3", format: "h3"},
            {title: "Header 4", format: "h4"},
            {title: "Header 5", format: "h5"},
            {title: "Header 6", format: "h6"}
        ]},

        {title: "Box", items: [
             {title : 'Vytvoř box', block:'div', classes: 'bub_inline' , wrapper:true },    //wrapper:true
             {title : 'Vytvoř box 100% šířka', block:'div', classes: 'bub_block', wrapper:true },  //wrapper:true
             {title : 'Zap./Vyp. box ohraničení - šedá', selector:'div', classes: 'bub_kulate_ohraniceni_seda' },     //vice selectoru neslo
             {title : 'Zap./Vyp. box ohraničení - hlavní barva', selector:'div', classes: 'bub_kulate_ohraniceni_hlavni_barva' },
            // {title : 'Box text výrazný - hlavní barva', selector:'div', classes: 'bub_text_vyrazny_hlavni_barva' },
            // {title : 'Box text výrazný - bílá', selector:'div', classes: 'bub_text_vyrazny_bila' },
            // {title : 'Box pozadí - hlavní barva', selector:'div', classes: 'bub_back_hlavni_barva' },
             {title : 'Zap./Vyp. box pozadí - sv.šedá', selector:'div', classes: 'bub_back_stribrna' },
             {title : 'Zap./Vyp. box pozadí - šedá',    selector:'div', classes: 'bub_back_seda' },
             {title : 'Zap./Vyp. odsuň box doprava, obtékaný zleva', selector:'div', classes: 'styl_vpravo' },
             {title : 'Zap./Vyp. odsuň box doleva, obtékaný zprava', selector:'div', classes: 'styl_vlevo' },

            // {title : 'Vytvoř box bez wrapperu', block:'div', classes: 'bub_inline'  },
            // {title : 'Box ohraničení oblé zelená', block:'div', classes: 'bub_kulate_ohraniceni_zelena', exact: true },  //, exact: true
            // {title : 'Box ohraničení oblé oranž', block:'div', classes: 'bub_kulate_ohraniceni_oranzova',exact: true },
            // {title : 'Box ohraničení oblé modra', block:'div', classes: 'bub_kulate_ohraniceni_modra',exact: true },
          ]},

         {title: "Blok na střed", items: [
               {title: 'Blok na střed -  vytvořte pomocí šablony!',    description: 'oddelovac'},
               {title : 'Zap./Vyp. ohraničení blok na střed - šedá',      selector:'p',   classes: 'ohraniceni_stredoboxu_seda' },
               {title : 'Zap./Vyp. ohraničení blok na střed - hlavní barva', selector:'p',classes: 'ohraniceni_stredoboxu_hlavni' },
               {title : 'Zap./Vyp. pozadí blok na střed - sv.šedá',       selector:'p',   classes: 'pozadi_stredoboxu_stribrna' },
               {title : 'Zap./Vyp. pozadí blok na střed - šedá',          selector:'p',   classes: 'pozadi_stredoboxu_seda' },
         ]},

       {title: "Blok s legendou", items: [
          //{title : 'Zap./Vyp. blok s legendou - bílý', selector:'fieldset', classes: 'blok_fieldset_bila' },
           {title: 'Blok s legendou - vytvořte pomocí šablony!',    description: 'oddelovac'},
           {title : 'Zap./Vyp. blok s legendou - sv.šedý', selector:'fieldset', classes: 'blok_fieldset_seda' },
           {title : 'Zap./Vyp. blok s legendou - sv.šedý+výrazná legenda', selector:'fieldset', classes: 'blok_fieldset_legenda_vyrazna' },
           {title : 'Zap./Vyp. odsuň blok doprava, obtékaný zleva', selector:'fieldset', classes: 'styl_vpravo' },
           {title : 'Zap./Vyp. odsuň blok doleva, obtékaný zprava', selector:'fieldset', classes: 'styl_vlevo' },
       ]},

            //{title: 'Obrázek vlevo, obtékání vpravo', selector: 'img',  styles: {  float: 'left',  margin: '10px 10px 10px 10px' }   },
            //{title: 'Obrázek vpravo, obtékání vlevo', selector: 'img',    styles: {  float: 'right',   margin: '10px 10px 10px 10px' }  } ,
        {title: 'Obrázek - rámeček - 1px černý',    selector: 'img',    styles: {  border: '1px black solid' }  } ,

             //  {title : 'Zap./Vyp. odkaz v textu - oranžový (a)',  selector: 'a' ,   classes : 'odkaz_vtextu_kulaty_orange'}  , //inline : 'span'
        {title : 'Zap./Vyp. odkaz v textu - kulatý červený',  selector: 'a',  classes : 'odkaz_vtextu_kulaty_cerveny'}  , //'inline : 'span'
        {title : 'Zap./Vyp. odkaz v textu - kulatý šedý',     selector: 'a',  classes : 'odkaz_vtextu_kulaty_sedy'} ,    //'inline : 'span'

            // {title : 'xxxNa odkaz v textu - celoplošný', block : 'div', classes : 'odkaz_celoplosny'} ,
            // {title : 'Bublina v textu vlevo',     block : 'div',        classes : 'bublina_vtextu_vlevo'} ,     //css ve styles_contents
            // {title : 'Bublina v textu vpravo',    block : 'div',        classes : 'bublina_vtextu_vpravo'} ,     //css ve styles_contents

        {title : ' Zap./Vyp. dva sloupce', block:'div', classes: 'sloupce' },
    ],


    custom_undo_redo_levels: 80,
    nonbreaking_force_tab : true,  //povoluje vlozit 3x&nbsp; tabulatorovym tlacitkem
    // přidali jsme povolení zachovat elementy <i> při uložení obsahu v tinyMce
    extended_valid_elements :  "iframe[src|width|height|name|align],i[*]",

});
</script>


<!-- Konec nastaveni EDITORU -->
<!-- KONEC EDITORU -->
<!-- sirkatextarea bylo100% -->



<fieldset>
<legend>Obsah stránky</legend>
<textarea id="rs_ed_pole1" name="obsah"  rows="30"  cols="70"  style="width: 100%" >

<?php echo $zaznam[$db_obsah];?>

</textarea>
</fieldset>

<p><input type="submit" value="ULOŽIT ZMĚNY"> <input type="submit" value="Importovat soubor" name="newfile"></p>
<input type="hidden" name="stranka" value="<?php echo $stranka;?>">
<input type="hidden" name="edit" value="<?php echo $zaznam['editor'];?>">

<br>
</form>
<?php
}  //konec editovatelnych

else   //stranka je "NEEDITOVATELNA" = publikacní
{ ?>
  <form method="POST" action="index.php?app=rs&list=stranky_publ&language=<?php echo $lang;?>&stranka=<?php echo $stranka;?>">
  <!-- ZACATEK STAVU STRANKY -->
  <fieldset>
  <legend>Stav stránky</legend>
  <input type="radio" name="aktiv" value="0" <?php IF ($zaznam[$db_aktiv] == 0) {echo 'checked';}?>> <span clas="rs_obdobi_neuverejnene">Neuveřejněná</span><br>
  <input type="radio" name="aktiv" value="1" <?php IF ($zaznam[$db_aktiv] == 1) {echo 'checked';}?>> <span clas="rs_obdobi_uverejnene">Uveřejněná</span><br>
  <input type="radio" name="aktiv" value="2" <?php IF ($zaznam[$db_aktiv] == 2) {echo 'checked';}?>> <span clas="rs_obdobi_oddo">Uveřejnit v období <i>(rok-měsíc-den)</i></span>
  <?php include Middleware\Rs\AppContext::getScriptsDirectory().'contents/kalendar.php';?>
  <DIV class="rs_float_r"><input type="submit" value="ULOŽIT STAV"></DIV>
  </fieldset>
  <!-- KONEC STAVU STRANKY -->
  <!-- ZACATEK NAZVU STRANKY -->
  <fieldset>
  <legend>Název stránky</legend>
  <input type="text" name="nazev" size="76" value="<?php echo $zaznam[$db_nazev];?>" maxlength="100" disabled>
  </fieldset>
  <!-- KONEC NAZVU STRANKY -->
  <br>
  <fieldset>
  <legend>Obsah stránky</legend>
  <?php echo $zaznam[$db_obsah];?>
  </fieldset>

  <input type="hidden" name="stranka" value="<?php echo $stranka;?>">
  <input type="hidden" name="edit" value="<?php echo $zaznam['editor'];?>">

  <br>
  </form>

<?php
}

;}
;}
else {echo '<p class=chyba><br>Nemáte oprávnění k editaci!</p>';}?>
