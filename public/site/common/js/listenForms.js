/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


    
    function listenForms(loaderElement) {
        let forms = navigation.querySelectorAll("form");
        console.log(`cascade: Listen forms match `+forms.length+' forms.');
        for (const form of [...forms]) {
            form.addEventListener("submit", event => {
                ;
            }
        }
    }