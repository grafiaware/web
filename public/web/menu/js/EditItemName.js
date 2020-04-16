document.addEventListener('keydown', function (event) {
    var escPressed = event.which === 27,
    nlPressed = event.which === 13,
    targetElement = event.target,
    acceptedElement = targetElement.nodeName !== 'INPUT' && targetElement.nodeName !== 'TEXTAREA',
    location, url,
    data = {};

    if (acceptedElement) {
        if (escPressed) {
            // restore state
            document.execCommand('undo');
            targetElement.blur();
        } else if (nlPressed) {
            // data title z innerText, ostatní z data- atributů - zde musí být shoda jmen s html šablonou pro item!
            data['title'] = targetElement.innerText; // innerHTML obsahuje ivložený <br/> tag vzhiklý po stisku enter klávesy
            data['original-title'] = targetElement.getAttribute('data-oroginaltitle');
            data['uid'] = targetElement.getAttribute('data-uid');
            // url = window.location až k poslednímu lomítku + / + routa
            location = window.location.toString();
            url = location.substr(0, location.lastIndexOf('/')) + '/nodes/' + data['uid'] + '/';
            // odeslání ajax requestu
            // .ajax vrací Deferred Object - .done a .fail jsou metody Deferred Objectu (a samy vracejí Deferred Object)
            $.ajax({
                    url: url,
                    data: data,
                    type: 'post'
                    })
                    .done(function(data, textStatus, jqXHR) {
                    alert( "Provedeno: " + data );
                    })
                    .fail(function(jqXHR, textStatus, errorThrown){
                    alert( "Selhalo: " + errorThrown );
                });

            log(JSON.stringify(data));

            targetElement.blur();
            event.preventDefault();
        }
    }
}, true);

function log(s) {
  document.getElementById('debug').innerHTML = 'value changed to: ' + s;
}

//The order of events for a dblclick is:
//
//    mousedown
//    mouseup
//    click
//    mousedown
//    mouseup
//    click
//    dblclick
//
//The one exception to this rule is (of course) Internet Explorer with their custom order of:
//
//    mousedown
//    mouseup
//    click
//    mouseup
//    dblclick



//<div onclick="doubleclick(this, function(){alert('single')}, function(){alert('double')})">click me</div>
  //      function doubleclick(el, onsingle, ondouble) {
function doubleclick(el, onsingle, ondouble) {
    if (el.getAttribute("data-dblclick") == null) {
        el.setAttribute("data-dblclick", 1);
        setTimeout(function () {
            if (el.getAttribute("data-dblclick") == 1) {
                onsingle();
            }
            el.removeAttribute("data-dblclick");
        }, 300);
    } else {
        el.removeAttribute("data-dblclick");
        ondouble();
    }
}
onsingle = function (e) {
    e.preventDefault();
};
ondouble = function (e) {
    this.contentEditable = true;
    this.focus();
    this.style.backgroundColor = '#E0C0C0';
    this.style.border = '1px dotted black';
};

var list = document.getElementsByClassName("item editable");
for (var i = 0; i < list.length; i++) {
    elem = list[i];

    elem.onclick = function (e) {
        if (this.getAttribute("data-firstclick") === null) {
            this.setAttribute("data-firstclick", 1);
            setTimeout(function () {
                if (this.getAttribute("data-firstclick") === 1) {
                    e.preventDefault();
                }
                this.removeAttribute("data-firstclick");
            }, 900);
        } else {
            this.removeAttribute("data-firstclick");
            e.preventDefault();
        }
    };

    elem.ondblclick = function (e) {
        ondouble(e);
    };

    elem.onmouseout = function () {
       // this.style.backgroundColor = '#ffffff';
        this.style.border = '';
        this.contentEditable = false;
    };

}
