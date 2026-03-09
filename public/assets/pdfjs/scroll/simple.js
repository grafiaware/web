import * as pdfjsLib from '../build/pdf.mjs';

pdfjsLib.GlobalWorkerOptions.workerSrc =
  '../build/pdf.worker.mjs';
  
  
// verze 1
// Dynamický rerender při resize OLD
window.addEventListener('resize', () => {
  document.querySelectorAll('.page[data-rendered="1"]')
    .forEach(div => {
      div.dataset.rendered = '0';
      div.innerHTML = '';
    });
});

// verze 2
//let resizeTimeout;

//window.addEventListener('resize', () => {
//  clearTimeout(resizeTimeout);
//
//  resizeTimeout = setTimeout(() => {
//    document
//      .querySelectorAll('.page[data-rendered="1"]')
//      .forEach(div => renderPage(div));
//  }, 150);
//});


const url = new URLSearchParams(window.location.search).get('file');
const wrapper = document.getElementById('wrapper');

let pdfDoc = null;

/**
 * Spočítá scale na šířku wrapperu
 */
function getScale(page) {
  const viewport = page.getViewport({ scale: 1 });
  return wrapper.clientWidth / viewport.width;
}

// verze 1
/**
 * Renderuje stránku do EXISTUJÍCÍHO placeholderu
 */
async function renderPage(pageDiv) {
  if (pageDiv.dataset.rendered === '1') return;

  const pageNum = Number(pageDiv.dataset.page);
  const page = await pdfDoc.getPage(pageNum);

  const scale = getScale(page);
  const viewport = page.getViewport({ scale });

  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');

  canvas.width = viewport.width;
  canvas.height = viewport.height;

  pageDiv.style.minHeight = 'auto';
  pageDiv.appendChild(canvas);
  pageDiv.dataset.rendered = '1';

  page.render({ canvasContext: ctx, viewport });
}

// verze 2
//async function renderPage(pageDiv) {   // canvas se vytváří jen jednou
//  const pageNum = Number(pageDiv.dataset.page);
//  const page = await pdfDoc.getPage(pageNum);
//
//  const scale = getScale(page);
//  const viewport = page.getViewport({ scale });
//
//  let canvas = pageDiv.querySelector('canvas');
//  if (!canvas) {
//    canvas = document.createElement('canvas');
//    pageDiv.appendChild(canvas);
//  }
//
//  const ctx = canvas.getContext('2d');
//
//  canvas.width = viewport.width;
//  canvas.height = viewport.height;
//
//  pageDiv.dataset.rendered = '1';
//
//  page.render({ canvasContext: ctx, viewport });
//}

/**
 * IntersectionObserver – lazy load
 */
function setupObserver() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        renderPage(entry.target);
      }
    });
  }, {
    root: wrapper,
    rootMargin: '800px'
  });

  // vytvoříme PLACEHOLDERY (1:1 ke stránkám PDF)
  for (let i = 1; i <= pdfDoc.numPages; i++) {
    const pageDiv = document.createElement('div');
    pageDiv.className = 'page';
    pageDiv.dataset.page = i;
    pageDiv.style.minHeight = '100vh'; // rezervace místa
    wrapper.appendChild(pageDiv);
    observer.observe(pageDiv);
  }
}

/**
 * Init
 */
pdfjsLib.getDocument(url).promise.then(pdf => {
  pdfDoc = pdf;
  setupObserver();
});
