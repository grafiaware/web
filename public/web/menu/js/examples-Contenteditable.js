/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*    Netbeans connector:
 *
 */
//    document.documentElement.addEventListener('keydown', function(e) {
//        var keyCode = e.keyCode;
//        var shift = e.shiftKey;
//        var ctrl = e.ctrlKey;
//        var meta = e.metaKey;
//        if (keyCode === 83  && shift && (ctrl || meta))  /* 'S' */

document.addEventListener('keydown', function (event) {
  var esc = event.which == 27,
      nl = event.which == 13,
      el = event.target,
      input = el.nodeName != 'INPUT' && el.nodeName != 'TEXTAREA',
      data = {};

  if (input) {
    if (esc) {
      // restore state
      document.execCommand('undo');
      el.blur();
    } else if (nl) {
      // save
      data[el.getAttribute('data-name')] = el.innerHTML;

      // we could send an ajax request to update the field
      /*
      $.ajax({
        url: window.location.toString(),
        data: data,
        type: 'post'
      });
      */
      log(JSON.stringify(data));

      el.blur();
      event.preventDefault();
    }
  }
}, true);

function log(s) {
  document.getElementById('debug').innerHTML = 'value changed to: ' + s;
}

//verze 2

$('p[contenteditable=true]').live('blur',function(){

	$.ajax({
		type:'POST',
		url:'edit_item.php',
		data:{
			content: $(this).text(),
			id:      $(this).parent().attr('id')
		},
		success:function(msg){
			if(!msg){
				//console.error('update failure');
				alert('update failure');
			}
		}
	});

});

