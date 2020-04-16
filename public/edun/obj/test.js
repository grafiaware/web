var obj;
var Pocitadlo = new Array();

function init(itemname,codes) {
    var i;
    var j;
    Pocitadlo[itemname]=0;
    var sub_select = document.getElementById("sub_"+itemname);
    for(i=0;i<codes.length;i++) {
        for(j=0;j<sub_select.options.length;j++) {
            if(sub_select.options[j].value == codes[i]) {
                sub_select.selectedIndex = j;
                add_to_div(itemname);
                break;
            }
        }
    }
}

function add_to_div(itemname) {
    var k;
    var active_div = document.getElementById('div_'+itemname);
    var new_div_num = ++Pocitadlo[itemname];
    var new_div = document.createElement('div');
    var new_div_IdName = 'div_'+itemname+'_'+new_div_num;
    var sub_select = document.getElementById("sub_"+itemname);
    var button = document.getElementById("button_"+itemname);
    var new_kod = sub_select.value;
    var new_text = sub_select.options[sub_select.options.selectedIndex].text;
    var last_kod = true;
    var disabled_kod = false;
    sub_select.options[sub_select.options.selectedIndex].disabled=true;
    if(sub_select.options[sub_select.selectedIndex].isDisabled) {
        alert("Položka již byla vybrána zvolte jinou !");
        new_div=false;
        disabled_kod = true;
    }
    else {
        new_div.setAttribute('id',new_div_IdName);
        new_div.innerHTML = "<a href=\"javascript:;\" onclick=\"remove_from_div(\'"+new_div_IdName+"\',\'"+sub_select.id+"\',"+sub_select.selectedIndex+",\'"+button.id+"\')\">  <img src=\"img/b_drop.png\" alt=\"Odstranit\" title=\"Odstranit\" border=\"0\"></a>  "+new_text;
        new_div.innerHTML = new_div.innerHTML+"<INPUT type=\"hidden\" name=\""+itemname+"[]\" id=\""+itemname+"\" value=\""+new_kod+"\">";
    }
    for(k=0;k<sub_select.options.length;k++) {
        if(!sub_select.options[k].isDisabled) {
            sub_select.selectedIndex = k;
            last_kod = false;
            break;
        }
    }
    if(last_kod) {
        sub_select.selectedIndex = -1;
        button.disabled=true;
    }
    if(!disabled_kod) {
        active_div.appendChild(new_div);
    }
}

function remove_from_div(div_name,sub_select_id,index,button_id) {
    var button = document.getElementById(button_id);
    var sub_select = document.getElementById(sub_select_id);
    sub_select.options[index].disabled=false;
    if(button.disabled) {
        button.disabled=false;
        for(i=0;i<sub_select.options.length;i++) {
            if(!sub_select.options[i].isDisabled) {
                sub_select.selectedIndex = i;
                last_kod = false;
                break;
            }
        }
    }
    var olddiv = document.getElementById(div_name);
    myDiv = olddiv.parentNode;
    myDiv.removeChild(olddiv);
}

