import * as pdfjsLib from '../build/pdf.mjs';

pdfjsLib.GlobalWorkerOptions.workerSrc =
  '../build/pdf.worker.mjs';

const url = new URLSearchParams(window.location.search).get('file');

let pdfDoc = null;
let pageNum = 1;

const canvas = document.getElementById('pdf-canvas');
const ctx = canvas.getContext('2d');
const info = document.getElementById('info');

function renderPage(num) {
  pdfDoc.getPage(num).then(page => {
    const viewport = page.getViewport({ scale: 1.5 });

    canvas.width = viewport.width;
    canvas.height = viewport.height;

    page.render({ canvasContext: ctx, viewport });
    info.textContent = `${num} / ${pdfDoc.numPages}`;
  });
}

pdfjsLib.getDocument(url).promise.then(pdf => {
  pdfDoc = pdf;
  renderPage(pageNum);
});

document.getElementById('prev').onclick = () => {
  if (pageNum > 1) renderPage(--pageNum);
};

document.getElementById('next').onclick = () => {
  if (pageNum < pdfDoc.numPages) renderPage(++pageNum);
};
