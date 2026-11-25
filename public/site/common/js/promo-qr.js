/**
 * F3 (#47, docs/vp/zadani/F1.md): QR kódy odkazů v administraci – přečtení QR ze SVG (QRdirector) v prohlížeči
 * před nahráním a náhledy na stránce ověření QR. Potřebuje jsQR (čtení) a qrcode-generator (náhledy QR webu).
 *
 * Čtení: SVG se vykreslí jako obrázek (<img>, skripty v SVG se v něm nespustí) na plátno a jsQR přečte adresu.
 */
(function () {
    'use strict';

    /** Přečte QR ze SVG textu; vrací Promise s adresou, nebo null. */
    function decodeSvg(svgText) {
        return new Promise(function (resolve) {
            var url = URL.createObjectURL(new Blob([svgText], {type: 'image/svg+xml'}));
            var img = new Image();
            img.onload = function () {
                var tries = [600, 1000, 300], result = null;
                for (var i = 0; i < tries.length && !result; i++) {
                    var size = tries[i], pad = Math.round(size * 0.1);
                    var c = document.createElement('canvas');
                    c.width = c.height = size + 2 * pad;
                    var g = c.getContext('2d');
                    g.fillStyle = '#fff';               // bílý okraj (tichá zóna) – SVG bývá bez pozadí
                    g.fillRect(0, 0, c.width, c.height);
                    var ratio = (img.naturalWidth || 1) / (img.naturalHeight || 1);
                    var w = ratio >= 1 ? size : size * ratio, h = ratio >= 1 ? size / ratio : size;
                    g.drawImage(img, pad + (size - w) / 2, pad + (size - h) / 2, w, h);
                    var d = g.getImageData(0, 0, c.width, c.height);
                    var code = window.jsQR ? jsQR(d.data, d.width, d.height, {inversionAttempts: 'attemptBoth'}) : null;
                    result = code && code.data ? code.data : null;
                }
                URL.revokeObjectURL(url);
                resolve(result);
            };
            img.onerror = function () { URL.revokeObjectURL(url); resolve(null); };
            img.src = url;
        });
    }

    /**
     * Formulář nahrání SVG: input[type=file][data-svg-file] → skryté pole svg_text a decoded_url, náhled a přečtená adresa.
     * Kontejner nese data-link-url (veřejná adresa odkazu bez kanálu) pro kontrolu, kam QR vede.
     */
    function bindUpload(form) {
        var file = form.querySelector('[data-svg-file]');
        var text = form.querySelector('[name=svg_text]');
        var decoded = form.querySelector('[name=decoded_url]');
        var preview = form.querySelector('[data-svg-preview]');
        var info = form.querySelector('[data-svg-info]');
        var submit = form.querySelector('button[type=submit]');
        var linkUrl = form.getAttribute('data-link-url') || '';
        submit.disabled = true;
        file.addEventListener('change', function () {
            text.value = '';
            decoded.value = '';
            submit.disabled = true;
            preview.innerHTML = '';
            var f = file.files && file.files[0];
            if (!f) {
                info.textContent = '';
                return;
            }
            if (f.size > 1048576) {
                info.textContent = 'Soubor je větší než 1 MB – nevypadá jako QR kód.';
                info.className = 'bad';
                return;
            }
            var reader = new FileReader();
            reader.onload = function () {
                var svg = String(reader.result || '');
                if (svg.indexOf('<svg') < 0) {
                    info.textContent = 'Soubor není SVG.';
                    info.className = 'bad';
                    return;
                }
                text.value = svg;
                var img = new Image();
                img.alt = 'náhled QR';
                img.style.cssText = 'width:140px;height:140px;object-fit:contain;background:#fff;border:1px solid #ddd';
                img.src = URL.createObjectURL(new Blob([svg], {type: 'image/svg+xml'}));
                preview.appendChild(img);
                info.textContent = 'Čtu QR kód…';
                info.className = 'muted';
                decodeSvg(svg).then(function (url) {
                    decoded.value = url || '';
                    submit.disabled = false;
                    if (!url) {
                        info.textContent = 'QR kód se nepodařilo přečíst. Nahrát jde i tak – ověřte ho pak skenem telefonem.';
                        info.className = 'bad';
                    } else if (linkUrl && url.indexOf(linkUrl) === 0) {
                        info.textContent = 'QR vede přímo na tento odkaz: ' + url;
                        info.className = 'ok';
                    } else {
                        info.textContent = 'QR vede na: ' + url + ' – u QRdirectoru je to jeho adresa; v QRdirectoru musí být cíl '
                            + linkUrl + '/<kanál>. Ověřte skenem telefonem.';
                        info.className = 'muted';
                    }
                });
            };
            reader.readAsText(f);
        });
    }

    /** Náhledy QR webu z adresy: prvky [data-qr-text] dostanou SVG z qrcode-generator. */
    function renderQr(root) {
        (root || document).querySelectorAll('[data-qr-text]').forEach(function (el) {
            var q = qrcode(0, 'M');
            q.addData(el.getAttribute('data-qr-text'));
            q.make();
            el.innerHTML = q.createSvgTag({cellSize: 3, margin: 2, scalable: true});
        });
    }

    window.PromoQr = {decodeSvg: decodeSvg, bindUpload: bindUpload, renderQr: renderQr};
})();
