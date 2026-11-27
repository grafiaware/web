# Externí JS knihovny

Knihovny třetích stran (ne kód projektu). Kód vytvořený v projektu patří do `public/site/common`.
Při přidání knihovny doplň řádek: soubor, verze, licence, zdroj, kde se používá.

| Soubor | Verze | Licence | Zdroj | Použití |
|---|---|---|---|---|
| `jsQR-1.4.0.min.js` | 1.4.0 | Apache-2.0 | https://github.com/cozmo/jsQR | D1 – skener QR kódů (`Pages/scanner.php`) |
| `jspdf-4.2.1.umd.min.js` | 4.2.1 | MIT (`jspdf-4.2.1.LICENSE`) | https://github.com/parallax/jsPDF, npm `dist/jspdf.umd.min.js` | D1c – PDF papírových lístků (`public/site/common/js/lottery-paper-pdf.js`) |
| `qrcode-generator-2.0.4.js` | 2.0.4 | MIT (v hlavičce souboru) | https://github.com/kazuhikoarase/qrcode-generator, npm `dist/qrcode.js` | D1c – QR kódy na lístcích (vektorově v jsPDF) |
| `svg2pdf-2.9.0.umd.min.js` | 2.9.0 | MIT (`svg2pdf-2.9.0.LICENSE`) | https://github.com/yWorks/svg2pdf.js, npm `dist/svg2pdf.umd.min.js` | F3 – SVG QR kódů z QRdirectoru vektorově v PDF podkladu plakátu (`public/site/common/js/promo-poster-pdf.js`) |
