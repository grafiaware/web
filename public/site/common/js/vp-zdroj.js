/**
 * F1 (#47, docs/vp/zadani/F1.md): zdroj návštěvy pro měření registrací.
 *
 * - UTM parametry z adresy stránky (utm_source, utm_medium, utm_campaign) pošle serveru, ten je uloží do session;
 * - po souhlasu nebo změně souhlasu v cookie liště (CookieConsent, události cc:onFirstConsent / cc:onChange) požádá
 *   server, aby srovnal trvalou cookie vp_zdroj: se souhlasem „analytika“ ji zapíše ze session, bez souhlasu smaže.
 *
 * Vkládá jen NajdiSi se zapnutým přepínačem vp.odkazy (templates/layout/head/scripts.php).
 */
(function () {
    'use strict';
    function call(query) {
        try {
            fetch(new URL('events/v1/source' + (query || ''), document.baseURI).toString(),
                {credentials: 'same-origin', headers: {'Accept': 'application/json'}}).catch(function () {});
        } catch (e) { /* bez fetch nic */ }
    }
    var params = new URLSearchParams(window.location.search);
    var utm = new URLSearchParams();
    ['utm_source', 'utm_medium', 'utm_campaign'].forEach(function (k) {
        var v = params.get(k);
        if (v) { utm.set(k, v.slice(0, 100)); }
    });
    if (utm.toString() !== '') {
        call('?' + utm.toString());
    }
    window.addEventListener('cc:onFirstConsent', function () { call(''); });
    window.addEventListener('cc:onChange', function () { call(''); });
})();
