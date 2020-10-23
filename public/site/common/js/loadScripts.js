/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


loadScripts(sources) {
    sources.forEach(src => {
        var script = document.createElement('script');
        script.src = src;
        script.async = false; //<-- the important part
        document.body.appendChild( script ); //<-- make sure to append to body instead of head
    });
}

loadScripts(['/scr/script1.js','src/script2.js'])