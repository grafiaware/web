/**
 * JavaScript ClientSide Controler for multiselect
 * Its call and add to page by php class c_webform_cselect_multi
 *
 * @name cselect_multi.js
 * @author Tomas Cerny <it@grafia.cz>
 * @version 1.0
 * @package webform_obj
 */
// ---- Parameters ---- //
//path to drop item image
var img_drop = "img/b_drop.png";
var img_ordh = "img/b_ordh.png";
var img_ordl = "img/b_ordl.png";

// ---- /Parameters ---- //

/**
 * cselect_multi_init
 * function init - preset the element do default state accords to passed codes
 * @param string $itemname id of parent cselecet_multi element
 * @param array(string) $codes array of codes which will be displayed after form load or form reset
 */
function cselect_multi_init(itemname,codes) {
    var i;  //counter
    var j;  //counter
    var reset_function_text;
    var sub_select = document.getElementById("sub_"+itemname); //get select object
    var button = document.getElementById("button_"+itemname); //get button object
    if(divCounter[itemname] != undefined) {    //new initialization of form
        for(jj=0;jj<=divCounter[itemname];jj++) { //
            cselect_multi_remove_from_div('div_'+itemname+'_'+jj,itemname,1); //removing before added divs
        }
        for(j=0;j<sub_select.options.length;j++) {
            sub_select.options[j].disabled=false;       //enabling before disabled options
            }
        button.diabled=false; //enabling add-button
    }

    divCounter[itemname]=0; //preset divCounter
    //search for index by code in select object options array
    //alert (codes);
    if(codes != undefined) {
        for(i=0;i<codes.length;i++) {
            for(j=0;j<sub_select.options.length;j++) {
                if(sub_select.options[j].value == codes[i]) {
                    sub_select.selectedIndex = j; //presetting the select element for add_to_div function
                    cselect_multi_add_to_div(itemname);   //call the function for displaying items in div
                    break;
                }
            }
        }
    }
}

/**
 * cselect_multi_add_to_div
 * function cselect_multi_add_to_div - function found select elemenent by getted itemname, then read the selected index
 * and then create new and put into it the text description from select element and drop image with
 * setted call the remove_div function
 * The hidden array element is also putted into the new div
 * @param string $itemname id of parent cselecet_multi element
 */
function cselect_multi_add_to_div(itemname) {
    var k; //counter
    var active_div = document.getElementById('div_'+itemname); //div with multiselect component
    var new_div_num = ++divCounter[itemname]; //new serial number of div
    var new_div = document.createElement('div');
    var new_div_IdName = 'div_'+itemname+'_'+new_div_num;
    var sub_select = document.getElementById("sub_"+itemname);
    var button = document.getElementById("button_"+itemname);
    var new_kod = sub_select.value;

    var new_text = sub_select.options[sub_select.options.selectedIndex].text ;
    var last_kod = true;
    var disabled_kod = false;
    if (sub_select.options[sub_select.selectedIndex].isDisabled) {  //For IE - IE not accpeting options.disable functions, for this moment is alert displayed
        alert("Položka již byla vybrána zvolte jinou !");
        new_div=false;
        disabled_kod = true;
    }
    else {  //create content for new div - clicable drop picture + text
         //alert ("ADD* " + new_div_IdName );

        new_div.setAttribute('id',new_div_IdName);
        new_div.innerHTML = "<a href=\"javascript:;\" onclick=\"cselect_multi_remove_from_div(\'"+new_div_IdName+"\',\'"+itemname+"\',"+sub_select.selectedIndex+")\">" +
                                         "<img src=\'"+img_drop+"\' alt=\"Odstranit\" title=\"Odstranit\" border=\"0\"></a>  " +

                            "<a href=\"javascript:;\" onclick=\"cselect_multi_poradi_niz(\'"+ itemname+"\',"+ new_div_num  + ")\">" +
                                         "<img src=\'"+img_ordl +"\' alt=\"Posunout níž\" title=\"Posunout níž\" border=\"0\"></a>  " +

                            "<a href=\"javascript:;\" onclick=\"cselect_multi_poradi_vys(\'"+ itemname+"\',"+ new_div_num  + ")\">" +
                                         "<img src=\'"+img_ordh +"\' alt=\"Posunout výš\" title=\"Posunout výš\" border=\"0\"></a>  " +

                            "<span id=\'span_" + itemname+ "_" + new_div_num + "\' >" +
                            new_text + "</span> ";
        new_div.innerHTML = new_div.innerHTML+"<INPUT type=\"hidden\" readonly size=3 name=\""+itemname+"[]\" id=\""+itemname+'_'+new_div_num+"\" value=\""+new_kod+"\">"; //

    }
    sub_select.options[sub_select.options.selectedIndex].disabled=true;  //disabling selected items - not allow to add them twice
    for(k=0;k<sub_select.options.length;k++) {
        //alert("Stav položky je:"+sub_select.options[k].disabled);
        if(!sub_select.options[k].disabled) { //looking for new not disabled index
            sub_select.selectedIndex = k;
            last_kod = false;
            //alert ("Nový index bude:"+sub_select.options[sub_select.options.selectedIndex].text);
            break;
        }
    }
    if(last_kod) { //if all codes in select is disabled, the add button will be disabled too
        sub_select.selectedIndex = -1;
        button.disabled=true;
    }
    if(!disabled_kod) {
        active_div.appendChild(new_div);
    }
//alert ("ADD* " + new_div_IdName );
}

/**
 * cselect_multi_remove_from_div
 * function cselect_multi_remove_from_div - function will remove div by defiined div_name, is called by presing drop image
 * or from init function
 * @param string $div_name id of div to be dropped
 * @param string $itemname id of parent cselecet_multi element
 * @param int $index index number of displayed items
 */
function cselect_multi_remove_from_div(div_name,itemname,index) {
    var button = document.getElementById("button_"+itemname);
    var sub_select = document.getElementById("sub_"+itemname);
    sub_select.options[index].disabled=false;
    if(button.disabled) {
        button.disabled=false;
        for(i=0;i<sub_select.options.length;i++) {
            if(!sub_select.options[i].disabled) {
                sub_select.selectedIndex = i;
                last_kod = false;
                break;
            }
        }
    }
    var olddiv = document.getElementById(div_name);
    if(olddiv != null) {
        myDiv = olddiv.parentNode;
        myDiv.removeChild(olddiv);
    }
}

function  cselect_multi_poradi_vys(itemname, num) {
    // alert ( "itemname*" + itemname + "\n" +"num*" + num);

    var cislo = num;
    var cislo_predchozi = cislo-1;
    //alert("predchozi*" + cislo_predchozi);

    if (cislo_predchozi<=0) {
        return;
    } else {
        var idspan = 'span_' + itemname+'_'+ cislo;
        var idspan_predchozi = 'span_' + itemname+'_'+ cislo_predchozi;
        //alert ("idspan*" + idspan);

        var spanelem = document.getElementById(idspan);
        var spanelem_predchozi = document.getElementById(idspan_predchozi);
        //alert("spanelem.id*" + spanelem.id + "\n" +    "spanelem.textContent*" +"\n" + spanelem.textContent);

        var idinput = itemname+'_'+ cislo;
        var idinput_predchozi =  itemname+'_'+ cislo_predchozi;
        //alert ("idinput*" + idinput);

        var inputelem = document.getElementById(idinput);
        var inputelem_predchozi = document.getElementById(idinput_predchozi);
        //alert("---inputelem.id* " + inputelem.id + "\n" +  "---inputelem.value*" +"\n" + inputelem.value);
        //alert("---inputelem_predchozi.id* " + inputelem_predchozi.id + "\n" + "--inputelem_predchozi.value*" +"\n" + inputelem_predchozi.value);


        //vymena
        var pomhtml = spanelem_predchozi.innerHTML;  //textContent
        var pomtext = spanelem_predchozi.textContent;
        var pomvalue = inputelem_predchozi.value;

        //alert ("*******innerHTML* " +"\n" + pomhtml);
        //alert ("*******textContent* " +"\n" + pomtext);

        spanelem_predchozi.innerHTML = spanelem.innerHTML;
        spanelem_predchozi.textContent = spanelem.textContent;
        inputelem_predchozi.value = inputelem.value;

        spanelem.innerHTML = pomhtml;
        spanelem.textContent = pomtext;
        inputelem.value = pomvalue;
    }
    return;
}


function  cselect_multi_poradi_niz(itemname, num) {
    // alert ( "itemname*" + itemname + "\n" +"num*" + num);

    var cislo = num;
    var cislo_nasledujici = cislo+1;
    //alert("predchozi*" + cislo_predchozi);

    //nadrazeny  vsech  je div
    var nadrazeny = document.getElementById('div_'+ itemname);
    var pocet_elementu = nadrazeny.childElementCount;
    //alert("nadrazeny.id* " + nadrazeny.id );
    //alert("pocet elementu* " + nadrazeny.childElementCount);

    if (cislo_nasledujici>pocet_elementu) {
        return;
    } else {
        var idspan = 'span_' + itemname+'_'+ cislo;
        var idspan_nasledujici = 'span_' + itemname+'_'+ cislo_nasledujici;
        //alert ("idspan*" + idspan);

        var spanelem = document.getElementById(idspan);
        var spanelem_nasledujici = document.getElementById(idspan_nasledujici);
        //alert("spanelem.id*" + spanelem.id + "\n" +    "spanelem.textContent*" +"\n" + spanelem.textContent);
        //var q = spanelem.parentNode.id;

        var idinput = itemname+'_'+ cislo;
        var idinput_nasledujici =  itemname+'_'+ cislo_nasledujici;
        //alert ("idinput*" + idinput);

        var inputelem = document.getElementById(idinput);
        var inputelem_nasledujici = document.getElementById(idinput_nasledujici);
        //alert("---inputelem.id* " + inputelem.id + "\n" +
        //     "---inputelem.value*" +"\n" + inputelem.value);
        //alert("--inputelem_nasledujici.id* " + inputelem_nasledujici.id + "\n" +
        //    "--inputelem_nasledujici.value*" +"\n" + inputelem_nasledujici.value);

        //vymena
        var pomhtml = spanelem_nasledujici.innerHTML;  //textContent
        var pomtext = spanelem_nasledujici.textContent;
        var pomvalue = inputelem_nasledujici.value;

        //alert ("*******innerHTML* " +"\n" + pomhtml);
        //alert ("*******textContent* " +"\n" + pomtext);

        spanelem_nasledujici.innerHTML = spanelem.innerHTML;
        spanelem_nasledujici.textContent = spanelem.textContent;
        inputelem_nasledujici.value = inputelem.value;

        spanelem.innerHTML = pomhtml;
        spanelem.textContent = pomtext;
        inputelem.value = pomvalue;
    }
    return;
}

