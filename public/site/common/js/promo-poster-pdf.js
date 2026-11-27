/**
 * F3 (#47, docs/vp/zadani/F1.md): podklad plakátu PDF pro grafika – generuje prohlížeč (jsPDF).
 *
 * Bez typografie a designu: objekty pod sebou, každý s malým šedým popiskem („QR – billboard“, „Název firmy“…).
 * QR webu vektorově z matice (qrcode-generator), QR z QRdirectoru vektorově ze SVG (svg2pdf.js), texty písmem
 * Liberation Sans (plné UTF-8). Grafik PDF otevře v Illustratoru / InDesignu a objekty přesune a upraví ručně.
 *
 * Data: GET events/v1/promo/<id>/poster → {code, name, formats, objects: [{kind: 'qr-web'|'qr-svg'|'text', label, url?, svg?, text?}]}
 * Použití: PromoPosterPdf.build(data, {regular, bold}, 'A4').then(function (doc) { doc.save(PromoPosterPdf.fileName(data, 'A4')); })
 */
(function () {
    'use strict';

    var FORMATS = {
        A4: {w: 210, h: 297, margin: 15, qr: 60, text: 14, label: 8},
        A3: {w: 297, h: 420, margin: 20, qr: 90, text: 20, label: 10}
    };
    var PT = 0.3528;            // 1 pt v mm
    var LH = 1.25;              // výška řádku vůči velikosti písma
    var GAP = 8;                // mezera mezi objekty (mm)

    var fontCache = {};

    function base64(buffer) {
        var bytes = new Uint8Array(buffer), chunk = 0x8000, s = '';
        for (var i = 0; i < bytes.length; i += chunk) {
            s += String.fromCharCode.apply(null, bytes.subarray(i, i + chunk));
        }
        return btoa(s);
    }

    function loadFont(url) {
        if (!fontCache[url]) {
            fontCache[url] = fetch(url, {credentials: 'same-origin'}).then(function (r) {
                if (!r.ok) {
                    throw new Error('Nepodařilo se načíst písmo (' + r.status + ').');
                }
                return r.arrayBuffer();
            }).then(base64);
        }
        return fontCache[url];
    }

    /** QR z adresy jako vektorové obdélníky (stejně jako lístky D1c). */
    function qrVector(doc, text, x, y, size) {
        var q = qrcode(0, 'M');
        q.addData(text);
        q.make();
        var n = q.getModuleCount(), m = size / n;
        doc.setFillColor(0);
        for (var r = 0; r < n; r++) {
            var c = 0;
            while (c < n) {
                if (q.isDark(r, c)) {
                    var start = c;
                    while (c < n && q.isDark(r, c)) {
                        c++;
                    }
                    doc.rect(x + start * m, y + r * m, (c - start) * m + 0.01, m + 0.01, 'F');
                } else {
                    c++;
                }
            }
        }
    }

    /** Rozměry SVG (poměr stran z viewBox). */
    function svgElement(svgText) {
        var el = new DOMParser().parseFromString(svgText, 'image/svg+xml').documentElement;
        if (!el || el.nodeName.toLowerCase() !== 'svg') {
            throw new Error('SVG QR kódu nejde přečíst.');
        }
        var vb = (el.getAttribute('viewBox') || '0 0 1 1').split(/[\s,]+/).map(Number);
        return {el: el, ratio: vb[3] > 0 ? vb[2] / vb[3] : 1};
    }

    function label(doc, F, text, x, y) {
        doc.setFont('LS', 'normal');
        doc.setFontSize(F.label);
        doc.setTextColor(120);
        doc.text(text, x, y, {baseline: 'top'});
        doc.setTextColor(0);
        return y + F.label * PT * LH + 1.5;
    }

    function build(data, fonts, format) {
        var F = FORMATS[format] || FORMATS.A4;
        var doc = new jspdf.jsPDF({unit: 'mm', format: format === 'A3' ? 'a3' : 'a4', orientation: 'portrait', compress: true});
        doc.setProperties({title: 'Podklad plakátu – ' + data.name, subject: 'Veletrh práce a vzdělávání – odkaz ' + data.code,
            creator: 'Veletrh práce a vzdělávání (podklad pro grafika)'});
        doc.addFileToVFS('LS-Regular.ttf', fonts.regular);
        doc.addFont('LS-Regular.ttf', 'LS', 'normal');
        doc.addFileToVFS('LS-Bold.ttf', fonts.bold);
        doc.addFont('LS-Bold.ttf', 'LS', 'bold');

        var x = F.margin, w = F.w - 2 * F.margin, bottom = F.h - F.margin, y = F.margin;
        var chain = Promise.resolve();
        var ensure = function (height) {
            if (y + height > bottom && y > F.margin) {
                doc.addPage(format === 'A3' ? 'a3' : 'a4', 'portrait');
                y = F.margin;
            }
        };

        (data.objects || []).forEach(function (o) {
            chain = chain.then(function () {
                var labelH = F.label * PT * LH + 1.5;
                if (o.kind === 'qr-web') {
                    ensure(labelH + F.qr);
                    var yy = label(doc, F, o.label, x, y);
                    qrVector(doc, o.url, x, yy, F.qr);
                    y = yy + F.qr + GAP;
                    return;
                }
                if (o.kind === 'qr-svg') {
                    var s = svgElement(o.svg);
                    var sw = F.qr, sh = F.qr / s.ratio;
                    ensure(labelH + sh);
                    var ys = label(doc, F, o.label, x, y);
                    var top = ys;
                    y = ys + sh + GAP;
                    return doc.svg(s.el, {x: x, y: top, width: sw, height: sh});
                }
                // text: zalomení do šířky stránky, tučně jen název firmy
                doc.setFont('LS', o.label === 'Název firmy' ? 'bold' : 'normal');
                doc.setFontSize(F.text);
                var lines = doc.splitTextToSize(String(o.text), w);
                var lh = F.text * PT * LH;
                ensure(labelH + lines.length * lh);
                var yt = label(doc, F, o.label, x, y);
                doc.setFont('LS', o.label === 'Název firmy' ? 'bold' : 'normal');
                doc.setFontSize(F.text);
                doc.text(lines, x, yt, {baseline: 'top', lineHeightFactor: LH});
                y = yt + lines.length * lh + GAP;
            });
        });
        return chain.then(function () { return doc; });
    }

    function fileName(data, format) {
        return 'plakat-podklad-' + data.code + '-' + (format === 'A3' ? 'a3' : 'a4') + '.pdf';
    }

    /**
     * Tlačítka [data-poster-url][data-poster-format] uvnitř kontejneru: načte data, písma a stáhne PDF.
     * Kontejner nese data-font-regular, data-font-bold; stav ukazuje prvek [data-poster-status].
     */
    function bind(container) {
        var status = container.querySelector('[data-poster-status]');
        var say = function (t, bad) { if (status) { status.textContent = t; status.className = bad ? 'bad' : 'muted'; } };
        container.addEventListener('click', function (e) {
            var b = e.target.closest('[data-poster-url]');
            if (!b) {
                return;
            }
            e.preventDefault();
            var format = b.getAttribute('data-poster-format') || 'A4';
            b.disabled = true;
            say('Připravuji podklad plakátu…');
            Promise.all([
                fetch(b.getAttribute('data-poster-url'), {credentials: 'same-origin', headers: {Accept: 'application/json'}})
                    .then(function (r) { return r.json().then(function (j) { if (!r.ok) { throw new Error(j.error || ('Chyba ' + r.status)); } return j; }); }),
                loadFont(container.getAttribute('data-font-regular')),
                loadFont(container.getAttribute('data-font-bold'))
            ]).then(function (res) {
                return build(res[0], {regular: res[1], bold: res[2]}, format).then(function (doc) {
                    doc.save(fileName(res[0], format));
                    say('Hotovo: ' + fileName(res[0], format) + ' – objekty (QR, texty) jdou v grafickém programu samostatně přesouvat.');
                });
            }).catch(function (err) {
                say('Podklad se nepodařilo vytvořit: ' + err.message, true);
            }).then(function () { b.disabled = false; });
        });
    }

    window.PromoPosterPdf = {build: build, loadFont: loadFont, fileName: fileName, bind: bind, formats: FORMATS};
})();
