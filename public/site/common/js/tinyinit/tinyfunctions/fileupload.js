/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


/**
 * Funkce vytvoří po kliknutí na tlačítko pro vyvolání file dialogu (hint "URL") v dialogu file picker (Vložit/upravit obrázek) nový elemenz input, 
 * nastaví mu atributy (type=file, accept=...............) a vyvolá input.click() - tím otevře file dialog.
 * Po vybrání souboru v dialogu (input.onchange) načte obsah souboru do blobCache (proměnná tiny) a vyplní údaje zpět do dialogu file picker. Pro 
 * pojmenování obrázku použije funkci image_unique_name(), která vrací unikátní jméno. To zajišťuje, že opakovaně uploadovaný obrázek ze stejného 
 * souboru má pokaždé nové jméno a nedojde k zobrazování nového obrázku na místech dříve uploadovaných a vložených obrázků se stejným jménem pro případ, že je 
 * ve stránce vloženo více obrázků se stejným jménem.
 *
 *
 * @param {type} callback - a callback to call, once you have hold of the file; it expects new value for the field as the first argument and optionally meta information for other fields in the dialog as the second one
 * @param {type} value - current value of the affected field
 * @param {type} meta - object containing values of other fields in the specified dialog (notice that meta.filetype contains the type of the field)
 * @returns {undefined}
 */
export const filePickerCallback = (callback, value, meta) => {
    // vytvoření input elementu (file input)
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', filePickerAccepted(meta));

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];  // https://developer.mozilla.org/en-US/docs/Web/API/File_API/Using_files_from_web_applications
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            // vytváří blobInfo a ukládám do blobCache - tiny by to udělal sám, ale zde vytvářím 
            // - blobInfo s unikátním jménem souboru (s tím se odesílá na server)
            // - původní jméno souboru (ze souborového systému - načteno dialogem) uschovám pro nastavení atributů html elementu - nastaví tiny sám pomocí callback
            const originalName = file.name.split('.').pop();  // split('.').pop - jméno souboru bez přípony
            const uniqueName = image_unique_name(originalName);
            const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            const base64 = reader.result.split(',')[1];  // reader.result konvertuje image na base64 string // Ignorujeme první prvek (před čárkou), extrahujeme druhý //const [, druhy] = str.split(','); 
            const blobInfo = blobCache.create(uniqueName, file, base64);
            blobCache.add(blobInfo);
            
            /* call the callback and populate the Title field with the file name */
            // For the link dialog    
            if (meta.filetype === 'file') {
//                callback(blobInfo.blobUri(), { title: originalName, text: 'Download: '+originalName , url: 'File: '+originalName});
                // první parametr callback je text, který bude vložen  do inputu a zobrazen při zobrazení html
                // pro tento případ - type=='file' druhý parametr je předán a lze jej získat v pluginu jako api.getData()
                callback(file.name, { fileName: file.name, originalName: originalName, blobInfo: blobInfo, id: id});
            }
            // For the image dialog
            if (meta.filetype === 'image') {
                callback(blobInfo.blobUri(), { title: originalName, originalName: originalName });
            }
            // For the media dialog
            if (meta.filetype === 'media') {
                callback(blobInfo.blobUri(), { title: originalName, originalName: originalName });
            }
        });
        reader.readAsDataURL(file);
    });

    input.click();
  };
  
  /**
   * Vrací seznam akceptovaných MIME typů souborů pro upload.
   * Vrací seznam podle typu souboru z input file dialogu - rozpoznává 'file', 'image', 'media'.
   * 
   * @param {type} meta
   * @returns {String}
   */
  function filePickerAccepted(meta) {
    // For the link dialog    
    if (meta.filetype === 'file') {
//        return 'application/pdf';
        return '.doc, .docx, .dot, .odt, .pages, .xls, .xlsx, .ods, .txt, .pdf';
    }
    // For the image dialog
    if (meta.filetype === 'image') {
        // IANA MIME types https://www.iana.org/assignments/media-types/media-types.xhtml#image
        // most commonly used web images types https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img
        // tinymce default value: 'jpeg,jpg,jpe,jfi,jif,jfif,png,gif,bmp,webp'
        return '.apng, .avif, .jpeg, .jpg, .jpe, .jfi, .jif, .jfif, .png, .gif, .bmp, .svg+xml, .webp';
        //input.setAttribute('accept', 'image/*');
    }
    // For the media dialog
    if (meta.filetype === 'media') {
        return 'video/*';
    }
    return '';
  };
  
  /**
   * Připojí ke jménu '@blobid' + timestamp v milisekundách
   * 
   * @param {type} originalName
   * @returns {String}
   */
    function image_unique_name(originalName) {
//        return originalName + '@blobid' + (new Date()).getTime();  // timestamp v milisekundách (čas od 1.1.1970)
        
        return originalName + '@uuid' + crypto.randomUUID();// crypto.randomUUID() -> 36 znaků
    };
    
/////////////////////////////////////////

// https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_handler

export const redImageUploadHandler = (blobInfo, progress) => new Promise((resolve, reject) => {

    const blob = blobInfo.blob();
    console.log('blob=', blob);
//        debugger;
    
    const xhr = new XMLHttpRequest();
    xhr.withCredentials = false;  // should pass along credentials (such as cookies, authorization headers, or TLS client certificates) for cross-domain uploads
    xhr.open('POST', 'red/v1/upload/image');

    xhr.upload.onprogress = function (e) {
        progress(e.loaded / e.total * 100);
    };

    xhr.onload = () => {
        if (xhr.status < 200 || xhr.status >= 300) {
            reject({ message: 'HTTP Error: ' + xhr.status + '. ' + xhr.statusText, remove: true });  // remove:true - smaže img element z html
            console.error('imageUploadHandler: failed upload - status: ' + xhr.status + ', message: ' + xhr.statusText);
            return;
        }
        try {
            const json = JSON.parse(xhr.responseText);
        } catch (e) {
            reject('Image upload failed due to invalid returned JSON.');
            return console.error('imageUploadHandler: ' + e);
        }
        if (!json || typeof json.location !== 'string') {
            reject('Invalid JSON: ' + xhr.responseText);
            console.error('imageUploadHandler: failed upload - message: ' + xhr.responseText);
            return;
        }
        resolve(json.location);
    };
    
    xhr.onerror = () => {
        reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        console.error('imageUploadHandler: failed upload - ' + xhr.status);
    };

    // získání menu item id - z editovatelného elementu (element ke kterému je připojen tiny) - editor získávám hledání obrázku v html pro zadané blobInfo
    // bezpečnější řešení - k uploadu dochází pro automatic_uploads: true (defaulně) vždy, když tiny najde neuploadovaný obrázek
    // V případě, že z nějakého důvodu dříve upload selhal, obrázky jsou v html uloženy jako <img src="data:image/jpeg;base64,...">, po loadu stránky
    // tiny takové nahradí za <img src="blob:http://apacheroot/..."> a všechny se pokusí automaticka odeslat - v tu chvíli editor není aktivní a nelze editovatelný element 
    // podle aktivního editoru
    // najdi IMG
    const result = findImage(blobInfo);
    // z IMG zjisti editor
    const editor = result.editor;
    const editedElementId = editor.id;
    // z editoru zjisti odpovídající DOM element
    const editedElement =  editor.getElement();
    const editedMenuItemId =  editedElement.getAttribute('data-red-menuitemid');


    //    const editedElementId =  tinymce.activeEditor.id;
    //    const editedElement =  tinymce.activeEditor.getElement();
    //    const editedMenuItemId =  editedElement.getAttribute('data-red-menuitemid');
    
    if(null === editedMenuItemId) {
        const msg = 'error image_upload_handler - element id ' + editedElementId + 'has no attribute data-red-menuitemid.';
        console.warn('imageUploadHandler: ' + msg);
    } else {
        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());  // blobinfo se vytváří v file_picker_callback_function
        formData.append('edited_item_id', editedMenuItemId);
        xhr.send(formData);
    }
});

/**
 * Hledá img podle blobInfo ve všech editorech zaregistrovaných tinymce.init(..), vrací objekt obsahující editor a img element nebo null.
 * @param {type} blobInfo
 * @returns {editor, img}
 */
function findImage(blobInfo) {
    const blobUri = blobInfo.blobUri();

    for (const editor of tinymce.get()) {   // tinymce.get() v Tiny verze 6 vrací pole editorů zaregistrovaných tinymce.init(..)
        const body = editor.getBody();
        if (!body) {
            continue;
        }
        const img = body.querySelector(
            `img[src="${CSS.escape(blobUri)}"]`
        );
        if (img) {
            return {
                editor,
                img
            };
        }
    }

    return null;
    
//optimalizace - můžeš si při inicializaci editorů vytvořit mapu:
//
//const editorMap = new Map();
//
//for (const editor of tinymce.get()) {
//    editorMap.set(editor.id, editor);
//}    
}

function createUniqueName() {
            const originalName = file.name.split('.').pop();  // split('.').pop - jméno souboru bez přípony
            const id = image_unique_name(originalName);
            const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            const base64 = reader.result.split(',')[1];  // reader.result konvertuje image na base64 string
            const blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);    
}