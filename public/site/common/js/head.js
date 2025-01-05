function removeDisabled(elementId){
   document.getElementById(elementId).removeAttribute('disabled');
}

function eventsEnableButtonsOnForm(event) {
    form = event.currentTarget;
//    input = event.target;
//    list = form.closest(".list");
//    let buttons = list.querySelectorAll("button");
//    buttons.forEach((button) => {button.disabled = true;});
    eventsEnableSave(form);
    event.stopPropagation();
}

function eventsDisableButtonsOnForm(event) {
    form = event.currentTarget;
    eventsDisableSave(form);
    event.stopPropagation();
}

function eventsEnableButtonsOnInput(event) {
    form = event.target.closest('form');
    eventsEnableSave(form);
}

function eventsEnableButtonsOnTinyMCE(form) {
    eventsEnableSave(form);
}

function eventsResetButton(event) {
    form = event.target.closest('form');
    eventsDisableSave(form);
}

function eventsEnableSave(form) {
    editButton = form.querySelector('#edit_'+form.id);
    if(editButton) {
       editButton.disabled = false;
    }
    addButton = form.querySelector('#add_'+form.id);
    if(addButton) {
       addButton.disabled = false;
    }
    resetButton = form.querySelector('#reset_'+form.id);
    if(resetButton) {
       resetButton.style.display = "inline";
    }  
}

function eventsDisableSave(form) {
    editButton = form.querySelector('#edit_'+form.id);
    if(editButton) {
       editButton.disabled = true;
    }
    addButton = form.querySelector('#add_'+form.id);
    if(addButton) {
       addButton.disabled = true;
    }
    resetButton = form.querySelector('#reset_'+form.id);
    if(resetButton) {
       resetButton.style.display = "none";
    }
}


//funkce se volaji v souboru svislemenu.php
function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

$(window).scroll(function(){
    // Get number of pixels of scroll.
    var pixel = $(window).scrollTop();
    var headerElement = document.getElementById("header");
    if (headerElement) {
        var header = headerElement.offsetHeight;
        //console.log(pixel);
        // When the scroll exceeds 300px, give the [fixed-menu] class.
        if(pixel > header){
            $('.mobile-menu-bar').addClass('fixed-menu');
        } else {
            $('.mobile-menu-bar').removeClass('fixed-menu');
        }
    }
});

function toggleTemplateSelect(event, id) {
//    $('#'+id).toggle();
    console.log("toggleTemplateSelect: element id "+id+".");
    var elm = document.getElementById(id);
    if (elm === null) {
        console.error("toggleTemplateSelect: Unable toggle template select. There is no element with id '"+id+"'.");
    } else {
        if (elm.style.display==="block") {
            elm.style.display = "none";
        } else {
            elm.style.display = "block";
        }
    }
    event.preventDefault();
    event.stopPropagation();
}

