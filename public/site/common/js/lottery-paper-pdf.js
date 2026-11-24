/*
 * D1c (#108): PDF papírových anketních lístků losování – skládá se v prohlížeči správce (jsPDF), server posílá jen JSON
 * (GET events/v1/lottery/:id/paper/data?from=&to=&per=). Stránka: Pages/paper.php, formulář #lottery-paper-pdf.
 *
 * A4 na výšku, 4 lístky (2×2 A6, ústřižek dole) nebo 2 lístky (A5 na šířku, ústřižek vpravo).
 * Lístek: anketní díl (název, jméno, příjmení, e-mail, otázky ankety, souhlas s pravidly, malé číslo a kód)
 * + ústřižek za čárkovanou čarou (QR adresy skenu L<kód> vektorově, velké číslo, pokyny).
 * Písmo: TTF načtené přímo (Liberation Sans), plné UTF-8.
 * Tisk (#153): s volbou „Přizpůsobit“ (ne 100 %), list se řeže podle řezových značek (čárky na okrajích, křížek mezi lístky),
 * okraje listu se neořezávají. Obsah je od řezu GAP mm; u okraje listu o STRIP mm blíž k okraji PDF, protože tiskárna
 * nechá na okraji nepotištěný pruh (počítá se 5 mm) a ten patří k okraji lístku.
 * Knihovny: public/assets/lib/jspdf-4.2.1.umd.min.js, qrcode-generator-2.0.4.js; písmo public/assets/fonts/liberation-sans.
 */
(function () {
    'use strict';

    var PT = 0.3528;            // 1 pt v mm
    var LH = 1.2;               // výška řádku vůči velikosti písma
    var SCALES = [1, 0.93, 0.86, 0.8];   // nejmenší písmo ankety 80 % (4 na A4: 6,4 pt)
    var CHUNK = 25;             // lístků mezi aktualizacemi průběhu
    var GAP = 6;                // obsah lístku ↔ řez (mm)
    var STRIP = 5;              // nepotištěný pruh na okraji listu při tisku „Přizpůsobit“ (mm)
    var EDGE = GAP - STRIP;     // obsah lístku ↔ okraj PDF stránky (mm)
    var MARK = 6;               // délka řezové značky na okraji stránky (mm)
    var CROSS = 3;              // rameno křížku mezi lístky (mm)

    var A4 = {w: 210, h: 297};
    var LAYOUTS = {
        4: {
            slipW: 105, slipH: 148.5, cols: 2, font: 8, title: 10.5, field: 6.5,
            survey: {stubGap: 6.5},                 // pod anketou: malé číslo a kód, pak čára ústřižku
            small: {aboveStub: 3},                  // účaří malého řádku nad čarou ústřižku
            stub: {side: 'bottom', size: 46, qr: 34, number: 34, text: 7.5}
        },
        2: {
            slipW: 210, slipH: 148.5, cols: 1, font: 9.5, title: 13, field: 8,
            survey: {stubGap: 7, bottom: 6},        // vpravo od ankety čára ústřižku; dole malé číslo a kód
            small: {fromBottom: 0},                 // účaří malého řádku na spodní hranici obsahu
            stub: {side: 'right', size: 70, qr: 40, number: 40, text: 8.5}
        }
    };

    function base64(buffer) {
        var bytes = new Uint8Array(buffer), s = '';
        for (var i = 0; i < bytes.length; i += 0x8000) {
            s += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000));
        }
        return btoa(s);
    }

    var fontCache = {};
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

    function mm(pt) {
        return pt * PT;
    }

    /**
     * Anketní díl. draw=false jen změří; vrací y pod posledním prvkem.
     */
    function survey(doc, data, area, L, s, draw) {
        var x = area.x, w = area.w, y = area.y;
        var fs = L.font * s, line = mm(fs) * LH;
        doc.setTextColor(0);
        doc.setDrawColor(0);
        doc.setLineWidth(0.2);

        doc.setFont('LS', 'bold');
        doc.setFontSize(L.title * s);
        var title = doc.splitTextToSize(data.lottery.name, w);
        if (draw) {
            doc.text(title, x, y, {baseline: 'top', lineHeightFactor: LH});
        }
        y += title.length * mm(L.title * s) * LH + 2 * s;

        doc.setFontSize(fs);
        var labelW = Math.max(doc.getTextWidth('Jméno'), doc.getTextWidth('Příjmení'), doc.getTextWidth('E-mail')) + 2;
        ['Jméno', 'Příjmení', 'E-mail'].forEach(function (label) {
            if (draw) {
                doc.text(label, x, y + L.field * s - mm(fs) - 0.6, {baseline: 'top'});
                doc.line(x + labelW, y + L.field * s - 0.6, x + w, y + L.field * s - 0.6);
            }
            y += L.field * s;
        });
        y += 3 * s;

        data.questions.forEach(function (q) {
            doc.setFont('LS', 'bold');
            doc.setFontSize(fs);
            var head = q.text + (q.required ? ' *' : '') + (q.type === 'multi' ? ' (lze více možností)' : '');
            var lines = doc.splitTextToSize(head, w);
            if (draw) {
                doc.text(lines, x, y, {baseline: 'top', lineHeightFactor: LH});
            }
            y += lines.length * line + 0.6 * s;
            doc.setFont('LS', 'normal');
            if (q.type === 'text' || !q.options.length) {
                y += L.field * s;
                if (draw) {
                    doc.line(x, y - 0.6, x + w, y - 0.6);
                }
                y += 1.4 * s;
                return;
            }
            var box = 2.3 * s, ox = x;
            q.options.forEach(function (o) {
                var tw = doc.getTextWidth(o);
                if (ox > x && ox + box + 1 + tw > x + w) {
                    ox = x;
                    y += line + 0.4 * s;
                }
                if (draw) {
                    var by = y + (line - box) / 2 - 0.2;
                    if (q.type === 'multi') {
                        doc.rect(ox, by, box, box);
                    } else {
                        doc.circle(ox + box / 2, by + box / 2, box / 2);
                    }
                    doc.text(o, ox + box + 1, y, {baseline: 'top'});
                }
                ox += box + 1 + tw + 3 * s;
            });
            y += line + 2 * s;
        });

        var consent = 'Souhlasím s pravidly losování' + (data.lottery.rulesText ? ' (' + data.lottery.rulesText + ')' : '') + '.';
        var box = 2.3 * s;
        var cl = doc.splitTextToSize(consent, w - box - 1);
        if (draw) {
            doc.rect(x, y + (line - box) / 2 - 0.2, box, box);
            doc.text(cl, x + box + 1, y, {baseline: 'top', lineHeightFactor: LH});
        }
        y += cl.length * line;
        return y;
    }

    /** Odsazení obsahu lístku na pozici k (0 = vlevo nahoře): GAP u řezu, EDGE u okraje stránky. */
    function insets(L, k) {
        var rows = Math.round(A4.h / L.slipH), col = k % L.cols, row = Math.floor(k / L.cols);
        return {
            l: col === 0 ? EDGE : GAP, r: col === L.cols - 1 ? EDGE : GAP,
            t: row === 0 ? EDGE : GAP, b: row === rows - 1 ? EDGE : GAP
        };
    }

    /** Měřítko písma ankety, aby se vešla; null = nevejde se ani při nejmenším. Měří se nejmenší plocha (řez ze všech stran). */
    function fitScale(doc, data, L) {
        var area = surveyArea(L, 0, 0, {l: GAP, r: GAP, t: GAP, b: GAP});
        for (var i = 0; i < SCALES.length; i++) {
            if (survey(doc, data, area, L, SCALES[i], false) <= area.bottom) {
                return SCALES[i];
            }
        }
        return null;
    }

    /**
     * Čára ústřižku (y u 'bottom', x u 'right'). stub.size platí pro lístek s řezem na straně ústřižku;
     * u okraje stránky se ústřižek i s čárou posune o STRIP k okraji, takže na papíře vyjde stejně.
     */
    function stubLine(L, sx, sy, ins) {
        var inner = L.stub.size - GAP;
        return L.stub.side === 'bottom' ? sy + L.slipH - ins.b - inner : sx + L.slipW - ins.r - inner;
    }

    function surveyArea(L, sx, sy, ins) {
        var inner = L.stub.size - GAP;
        var right = L.stub.side === 'right' ? ins.r + inner + L.survey.stubGap : ins.r;
        var bottom = L.stub.side === 'bottom' ? ins.b + inner + L.survey.stubGap : ins.b + L.survey.bottom;
        return {
            x: sx + ins.l, y: sy + ins.t, w: L.slipW - ins.l - right,
            bottom: sy + L.slipH - bottom
        };
    }

    /** Řezové značky stránky: čárky na okrajích v ose řezů, křížek v průsečíku řezů. */
    function cutMarks(doc, L) {
        var rows = Math.round(A4.h / L.slipH);
        doc.setDrawColor(0);
        doc.setLineWidth(0.25);
        doc.setLineDashPattern([], 0);
        for (var c = 1; c < L.cols; c++) {                  // svislé řezy: čárka nahoře a dole
            var x = c * L.slipW;
            doc.line(x, 0, x, MARK);
            doc.line(x, A4.h - MARK, x, A4.h);
        }
        for (var r = 1; r < rows; r++) {                    // vodorovné řezy: čárka vlevo a vpravo
            var y = r * L.slipH;
            doc.line(0, y, MARK, y);
            doc.line(A4.w - MARK, y, A4.w, y);
            for (var c2 = 1; c2 < L.cols; c2++) {           // křížek mezi lístky
                var cx = c2 * L.slipW;
                doc.line(cx - CROSS, y, cx + CROSS, y);
                doc.line(cx, y - CROSS, cx, y + CROSS);
            }
        }
    }

    function qr(doc, text, x, y, size) {
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
                    doc.rect(x + start * m, y + r * m, (c - start) * m + 0.01, m + 0.01, 'F');   // přesah proti vlasovým mezerám
                } else {
                    c++;
                }
            }
        }
    }

    function slip(doc, data, ticket, L, scale, sx, sy, ins) {
        var area = surveyArea(L, sx, sy, ins);
        survey(doc, data, area, L, scale, true);

        // malé číslo a kód (zůstane na anketním dílu)
        doc.setFont('LS', 'normal');
        doc.setFontSize(7);
        var smallY = L.stub.side === 'bottom' ? stubLine(L, sx, sy, ins) - L.small.aboveStub
            : sy + L.slipH - ins.b - L.small.fromBottom;
        doc.text('Lístek č. ' + ticket.label, area.x, smallY);
        doc.text(ticket.code, area.x + area.w, smallY, {align: 'right'});

        // ústřižek
        var st = L.stub, url = data.scanBase + ticket.code;
        doc.setDrawColor(0);
        doc.setLineWidth(0.3);
        doc.setLineDashPattern([2, 1.2], 0);
        var lines1 = 'Při výhře vás vyvoláme číslem.';
        var lines2 = 'Ústřižek si nechte – je dokladem výhry.';
        var lines3 = 'Registrujte se na ' + data.registerHost + ' – příště vám stačí QR kód v mobilu.';
        if (st.side === 'bottom') {
            var top = stubLine(L, sx, sy, ins);
            doc.line(sx, top, sx + L.slipW, top);
            doc.setLineDashPattern([], 0);
            var qy = sy + L.slipH - ins.b - st.qr;             // QR dole na hranici obsahu
            qr(doc, url, sx + ins.l, qy, st.qr);
            var tx = sx + ins.l + st.qr + 5, tw = sx + L.slipW - ins.r - tx;
            doc.setFont('LS', 'bold');
            doc.setFontSize(st.number);
            doc.text(ticket.label, tx, qy - 1, {baseline: 'top'});
            var y = qy + mm(st.number) * 0.95 + 2;
            y = stubText(doc, [[lines1, 'bold'], [lines2, 'normal'], [lines3, 'normal']], tx, y, tw, st.text, 'left');
        } else {
            var left = stubLine(L, sx, sy, ins), cx = (left + sx + L.slipW - ins.r) / 2;   // na střed mezi čárou a pravou hranicí obsahu
            doc.line(left, sy, left, sy + L.slipH);
            doc.setLineDashPattern([], 0);
            var qy2 = sy + ins.t + 8;
            qr(doc, url, cx - st.qr / 2, qy2, st.qr);
            doc.setFont('LS', 'bold');
            doc.setFontSize(st.number);
            doc.text(ticket.label, cx, qy2 + st.qr + 5, {baseline: 'top', align: 'center'});
            stubText(doc, [[lines1, 'bold'], [lines2, 'normal'], [lines3, 'normal']], cx, qy2 + st.qr + 7 + mm(st.number),
                st.size - 10, st.text, 'center');
        }
    }

    function stubText(doc, parts, x, y, w, size, align) {
        doc.setFontSize(size);
        parts.forEach(function (p, i) {
            doc.setFont('LS', p[1]);
            var lines = doc.splitTextToSize(p[0], w);
            doc.text(lines, x, y, {baseline: 'top', align: align, lineHeightFactor: 1.15});
            y += lines.length * mm(size) * 1.15 + (i === 1 ? 1.5 : 0.4);
        });
        return y;
    }

    /** Český tvar podle počtu: plural(2, 'strana', 'strany', 'stran'). */
    function plural(n, one, few, many) {
        return n + ' ' + (n === 1 ? one : (n >= 2 && n <= 4 ? few : many));
    }

    function pause() {
        return new Promise(function (resolve) {
            setTimeout(resolve, 0);
        });
    }

    /**
     * Vytvoří dokument jsPDF. onProgress(hotovo, celkem). Vrací {doc, scale}; chyba = Error s českou zprávou.
     */
    async function build(data, fonts, onProgress) {
        var L = LAYOUTS[data.perPage] || LAYOUTS[4];
        var doc = new jspdf.jsPDF({unit: 'mm', format: 'a4', orientation: 'portrait', compress: true});
        doc.setProperties({title: 'Lístky – ' + data.lottery.name, creator: 'Losování (D1c)'});
        doc.addFileToVFS('LS-Regular.ttf', fonts.regular);
        doc.addFont('LS-Regular.ttf', 'LS', 'normal');
        doc.addFileToVFS('LS-Bold.ttf', fonts.bold);
        doc.addFont('LS-Bold.ttf', 'LS', 'bold');

        var scale = fitScale(doc, data, L);
        if (scale === null) {
            throw new Error(data.perPage === 4
                ? 'Anketa se na lístek (4 na A4) nevejde. Zvolte 2 lístky na A4 nebo zkraťte otázky.'
                : 'Anketa se na lístek nevejde ani při 2 lístcích na A4. Zkraťte otázky nebo možnosti.');
        }
        var per = data.perPage === 2 ? 2 : 4, n = data.tickets.length;
        for (var i = 0; i < n; i++) {
            var k = i % per;
            if (i > 0 && k === 0) {
                doc.addPage('a4', 'portrait');
            }
            if (k === 0) {
                cutMarks(doc, L);
            }
            slip(doc, data, data.tickets[i], L, scale, (k % L.cols) * L.slipW, Math.floor(k / L.cols) * L.slipH, insets(L, k));
            if ((i + 1) % CHUNK === 0) {
                onProgress(i + 1, n);
                await pause();
            }
        }
        onProgress(n, n);
        return {doc: doc, scale: scale};
    }

    function fileName(data) {
        var t = data.tickets;
        return 'losovani-' + data.lottery.id + '-listky-' + t[0].label + '-' + t[t.length - 1].label + '.pdf';
    }

    function init(form) {
        var button = form.querySelector('[data-pdf-start]');
        var progress = form.querySelector('progress');
        var status = form.querySelector('[data-pdf-status]');
        var busy = false;

        function say(text, bad) {
            status.textContent = text;
            status.className = bad ? 'bad' : 'muted';
        }

        button.addEventListener('click', async function () {
            if (busy) {
                return;
            }
            if (!window.jspdf || !window.qrcode) {
                say('Knihovny pro PDF se nenačetly. Obnovte stránku.', true);
                return;
            }
            busy = true;
            button.disabled = true;
            progress.hidden = false;
            progress.value = 0;
            say('Načítám data lístků…');
            try {
                var params = new URLSearchParams(new FormData(form));
                var resp = await fetch(form.dataset.url + '?' + params.toString(), {credentials: 'same-origin', headers: {Accept: 'application/json'}});
                var data = await resp.json().catch(function () {
                    return {error: 'Server nevrátil data lístků (' + resp.status + ').'};
                });
                if (!resp.ok || data.error) {
                    throw new Error(data.error || 'Chyba serveru (' + resp.status + ').');
                }
                if (!data.tickets.length) {
                    throw new Error('V zadaném rozsahu nejsou žádné lístky.');
                }
                var fonts = {regular: await loadFont(form.dataset.fontRegular), bold: await loadFont(form.dataset.fontBold)};
                var started = performance.now();
                var result = await build(data, fonts, function (done, total) {
                    progress.value = done / total;
                    say('Lístek ' + done + ' z ' + total + '…');
                });
                say('Ukládám PDF…');
                await pause();
                result.doc.save(fileName(data));
                var pages = result.doc.getNumberOfPages();
                say('Hotovo: ' + plural(data.tickets.length, 'lístek', 'lístky', 'lístků') + ', ' + plural(pages, 'strana', 'strany', 'stran') + ' A4 za '
                    + (Math.round((performance.now() - started) / 100) / 10).toLocaleString('cs-CZ') + ' s'
                    + (result.scale < 1 ? ' (písmo ankety zmenšeno na ' + Math.round(result.scale * 100) + ' %, aby se vešla)' : '')
                    + '. Tiskněte s volbou „Přizpůsobit“ (ne 100 %) a řežte podle značek na okrajích a křížků.');
            } catch (e) {
                say(e.message || String(e), true);
            } finally {
                busy = false;
                button.disabled = false;
                progress.hidden = true;
            }
        });
    }

    window.LotteryPaperPdf = {build: build, loadFont: loadFont, fileName: fileName, layouts: LAYOUTS, insets: insets};

    var form = document.getElementById('lottery-paper-pdf');
    if (form) {
        init(form);
    }
})();
