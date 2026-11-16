<?php
/** D1: skener obsluhy – kamera, stanoviště, fronta skenů offline, záloha vyhledáním. */
/** @var array $lottery */
/** @var array $stations klíč => ['label', 'issue', 'refresh'] */
/** @var string $station */
/** @var string $jsQrUrl */
$id = (int) $lottery['id'];
?>
<div class="card">
    <h1>Skener – <?= htmlspecialchars($lottery['name']) ?></h1>
    <div class="row">
        <?php foreach ($stations as $key => $s): ?>
            <a class="btn <?= $key === $station ? '' : 'secondary' ?>" href="events/v1/lottery/<?= $id ?>/scanner?station=<?= urlencode($key) ?>">
                <?= htmlspecialchars($s['label']) ?></a>
        <?php endforeach; ?>
    </div>
    <p class="muted">Stanoviště: <b><?= htmlspecialchars($stations[$station]['label']) ?></b> –
        <?= !empty($stations[$station]['issue']) ? 'vydá los (platný los prodlouží)' : 'prodlouží platný los' ?>.</p>
</div>
<div class="card" style="text-align:center">
    <video id="video" playsinline muted style="width:100%; max-height:50vh; background:#000; border-radius:4px"></video>
    <canvas id="canvas" hidden></canvas>
    <p><button id="start" type="button" class="big">Zapnout kameru</button></p>
    <div id="result" class="winner" style="font-size:20px"></div>
    <p id="queue" class="muted"></p>
</div>
<div class="card">
    <h2>Kód nebo číslo lístku ručně</h2>
    <form id="manual" class="row"><input type="text" id="manualCode" placeholder="kód z QR nebo číslo lístku" autocomplete="off"><button type="submit">Odeslat</button></form>
</div>
<div class="card">
    <h2>Vyhledat návštěvníka (bez kódu)</h2>
    <form id="search" class="row"><input type="search" id="q" placeholder="e-mail, jméno nebo přihlašovací jméno"><button type="submit" class="secondary">Hledat</button></form>
    <div id="found"></div>
</div>
<div class="card">
    <h2>Poslední skeny</h2>
    <table id="log"></table>
</div>
<script src="<?= htmlspecialchars($jsQrUrl) ?>"></script>
<script>
(function () {
    'use strict';
    var LOTTERY = <?= $id ?>, STATION = <?= json_encode($station) ?>;
    var QKEY = 'vp-lottery-scan-queue-' + LOTTERY;
    var video = document.getElementById('video'), canvas = document.getElementById('canvas'), ctx = canvas.getContext('2d', {willReadFrequently: true});
    var resultEl = document.getElementById('result'), queueEl = document.getElementById('queue'), logEl = document.getElementById('log');
    var last = {code: '', at: 0}, detector = null, sending = false, audio = null;

    function loadQueue() { try { return JSON.parse(localStorage.getItem(QKEY) || '[]'); } catch (e) { return memQueue; } }
    var memQueue = [];
    function saveQueue(q) { memQueue = q; try { localStorage.setItem(QKEY, JSON.stringify(q)); } catch (e) {} }
    function showQueue() {
        var q = loadQueue();
        queueEl.textContent = q.length ? ('Neodeslané skeny: ' + q.length + ' – odešlou se, až bude spojení.') : (navigator.onLine ? 'Online.' : 'Offline – skeny se ukládají.');
    }
    function beep(ok) {
        try {
            audio = audio || new (window.AudioContext || window.webkitAudioContext)();
            var o = audio.createOscillator(), g = audio.createGain();
            o.frequency.value = ok ? 880 : 220; o.connect(g); g.connect(audio.destination); g.gain.value = 0.2;
            o.start(); o.stop(audio.currentTime + (ok ? 0.15 : 0.5));
        } catch (e) {}
        if (navigator.vibrate) { navigator.vibrate(ok ? 80 : [200, 100, 200]); }
    }
    function show(ok, name, message) {
        resultEl.style.color = ok ? '#1b7a2b' : '#b00020';
        resultEl.innerHTML = '';
        var n = document.createElement('div'); n.textContent = name || ''; n.style.fontSize = '26px';
        var m = document.createElement('div'); m.textContent = message; m.style.fontSize = '18px'; m.style.fontWeight = 'normal';
        resultEl.appendChild(n); resultEl.appendChild(m);
        var tr = document.createElement('tr'), t = new Date();
        [t.toLocaleTimeString('cs-CZ', {hour: '2-digit', minute: '2-digit'}), name || '', message].forEach(function (v) {
            var td = document.createElement('td'); td.textContent = v; tr.appendChild(td);
        });
        tr.className = ok ? 'ok' : 'bad';
        logEl.insertBefore(tr, logEl.firstChild);
        while (logEl.rows.length > 15) { logEl.deleteRow(-1); }
        beep(ok);
    }
    function enqueue(text) {
        var q = loadQueue();
        q.push({text: text, station: STATION, scannedAt: String(Date.now())});
        saveQueue(q); showQueue(); flush();
    }
    function flush() {
        if (sending) { return; }
        var q = loadQueue();
        if (!q.length) { showQueue(); return; }
        sending = true;
        var item = q[0], body = new URLSearchParams({code: item.text, station: item.station, scannedAt: item.scannedAt});
        fetch('events/v1/lottery/' + LOTTERY + '/scan', {method: 'POST', body: body, credentials: 'same-origin', headers: {'Accept': 'application/json'}})
            .then(function (r) { if (r.status === 401 || r.status === 403) { throw {auth: true}; } return r.json(); })
            .then(function (res) {
                saveQueue(loadQueue().slice(1));
                show(!!res.ok, res.name, res.message);
                sending = false; flush();
            })
            .catch(function (e) {
                sending = false;
                if (e && e.auth) { show(false, '', 'Nejste přihlášeni jako obsluha – přihlaste se a obnovte stránku.'); }
                else { show(false, '', 'Bez spojení – sken je uložen a odešle se později.'); }
                showQueue();
            });
    }
    function onCode(text) {
        var now = Date.now();
        if (text === last.code && now - last.at < 4000) { return; }
        last = {code: text, at: now};
        enqueue(text);
    }
    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            if (detector) {
                detector.detect(video).then(function (codes) { if (codes.length) { onCode(codes[0].rawValue); } }).catch(function () {});
            } else if (window.jsQR) {
                var w = video.videoWidth, h = video.videoHeight, scale = Math.min(1, 640 / Math.max(w, h));
                canvas.width = Math.round(w * scale); canvas.height = Math.round(h * scale);
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                var img = ctx.getImageData(0, 0, canvas.width, canvas.height);
                var code = jsQR(img.data, img.width, img.height, {inversionAttempts: 'dontInvert'});
                if (code && code.data) { onCode(code.data); }
            }
        }
        setTimeout(tick, 250);
    }
    document.getElementById('start').addEventListener('click', function () {
        var btn = this;
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { show(false, '', 'Kamera není dostupná (je potřeba HTTPS a povolení kamery). Použijte ruční zadání.'); return; }
        navigator.mediaDevices.getUserMedia({video: {facingMode: 'environment'}, audio: false}).then(function (stream) {
            video.srcObject = stream; video.play(); btn.hidden = true;
            if ('BarcodeDetector' in window) { try { detector = new BarcodeDetector({formats: ['qr_code']}); } catch (e) { detector = null; } }
            tick();
        }).catch(function () { show(false, '', 'Kameru se nepodařilo zapnout – povolte ji v prohlížeči.'); });
    });
    document.getElementById('manual').addEventListener('submit', function (e) {
        e.preventDefault(); var v = document.getElementById('manualCode'); if (v.value.trim()) { last = {code: '', at: 0}; onCode(v.value.replace(/\s+/g, '')); v.value = ''; }
    });
    document.getElementById('search').addEventListener('submit', function (e) {
        e.preventDefault();
        var q = document.getElementById('q').value.trim(), found = document.getElementById('found');
        found.textContent = 'Hledám…';
        fetch('events/v1/lottery/' + LOTTERY + '/visitor?q=' + encodeURIComponent(q), {credentials: 'same-origin'})
            .then(function (r) { return r.json(); })
            .then(function (list) {
                found.innerHTML = '';
                if (!list.length) { found.textContent = 'Nikdo nenalezen.'; return; }
                list.forEach(function (v) {
                    var f = document.createElement('form'); f.method = 'POST'; f.action = 'events/v1/lottery/' + LOTTERY + '/presence'; f.className = 'row';
                    var s = document.createElement('span'); s.textContent = v.name + ' (' + v.login + ')' + (v.joined ? '' : ' – nezapojen');
                    f.appendChild(s);
                    [['login', v.login], ['return', location.pathname.replace(/^.*?(events\/v1\/)/, '$1') + location.search], ['issue', <?= !empty($stations[$station]['issue']) ? "'1'" : "'0'" ?>]].forEach(function (p) {
                        var i = document.createElement('input'); i.type = 'hidden'; i.name = p[0]; i.value = p[1]; f.appendChild(i);
                    });
                    if (v.joined) { var b = document.createElement('button'); b.type = 'submit'; b.className = 'small'; b.textContent = <?= json_encode(!empty($stations[$station]['issue']) ? 'Vydat / prodloužit' : 'Prodloužit') ?>; f.appendChild(b); }
                    found.appendChild(f);
                });
            }).catch(function () { found.textContent = 'Hledání vyžaduje spojení.'; });
    });
    window.addEventListener('online', flush);
    setInterval(flush, 10000);
    showQueue(); flush();
})();
</script>
