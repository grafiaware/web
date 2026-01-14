/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */

    //modalni okno pro registraci
    function openRegisterModal() {
        $('.page.modal.register').modal({
            useCSS   : true,
        })
        .modal('show');
    }
    // funkce zobrazení modal okna registrace - pro QR kód
    function showRegisterOnQr() {
        const params = new URLSearchParams(window.location.search);

        if (params.get('modal') === 'register') {
            openRegisterModal();
        }
    }
    // vyčištění URL po zavření modálu - smaže query parametr a položku v historii
    function clearUrlOnRegisterModalHide() {
        const url = new URL(window.location);
        if (url.searchParams.has('modal')) {
            url.searchParams.delete('modal');
            history.replaceState({}, '', url);
        }
    }
    $('.btn-register').click(function(){
        openRegisterModal();
    });
    showRegisterOnQr();
    
