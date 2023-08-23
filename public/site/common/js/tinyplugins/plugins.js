/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */

/*
  The following is an example of how to use the new plugin and the new
  toolbar button.
*/
//tinymce.init({
//  selector: 'textarea#custom-plugin',
//  plugins: 'example help',
//  toolbar: 'example | help'
//});

export const attachmentPlugin = (editor, url) => {
  const openDialog = () => editor.windowManager.open({
    title: 'Attachment plugin',
    body: {
      type: 'panel',
      // items:
      // pokud je filetype jeden z typů uvedených v parametru tiny init file_picker_types - volá se událost "change" on input v filePickerCallback
      // parametr meta v filePickerCallback(callback, value, meta) pak obsahuje {fieldname: "hodnoty name v items", filefiletype: "hodnota filetype v items"}
      items: [
        {
            type: 'htmlpanel',
            html: '<p>Vyplňte text k zobrazení, jinak bude použit název souboru.</p>'
        },
        {
          type: 'input',
          name: 'textToDisplay',
          label: 'Text k zobrazení'
        },
        {
          type: 'urlinput',
          filetype: 'file',  
          name: 'fileInput',
          label: 'Vyberte soubor'
        }
      ]
    },
    buttons: [
        // 'submit' or 'cancel' or 'custom' or 'menu'
      {
        type: 'cancel',
        text: 'Close'
      },
      {
        type: 'submit',
        text: 'Upload',
        buttonType: 'primary',
        icon: 'upload'  // https://www.tiny.cloud/docs/advanced/editor-icon-identifiers/
      }
    ],
    onSubmit: (dialogApi) => {   // https://www.tiny.cloud/docs/ui-components/dialog/#dialoginstanceapi
        const data = dialogApi.getData();
        // meta: { originalName: originalName, blobInfo: blobInfo)
        /* Insert content when the window form is submitted */
        // url.meta pbsahuje objekt předaný jako druhý parametr callback() v filesupload - příklad: data.fileInput.meta.fileName
        let fileName = data.fileInput.meta.fileName;
        let textToDisplay = data.textToDisplay;
        let blobInfoFromFileupload = data.fileInput.meta.blobInfo;
        const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
        const file = blobCache.get(data.fileInput.meta.id);
        attachmentUploadHandler(blobInfoFromFileupload)
        .then(
            function(value) {
                let location = value;
                let html = htmlTemplate(location, fileName, textToDisplay);
                editor.insertContent(html);  
            },
            function(error) {
                console.error(error);
            }
        );
      dialogApi.close();
    }
  });
  /* Add a button that opens a window */
  editor.ui.registry.addButton('attachment', {
    text: 'Add attachment',
    onAction: () => {
      /* Open window */
      openDialog();
    }
  });
  /* Adds a menu item, which can then be included in any menu via the menu/menubar configuration */
//  editor.ui.registry.addMenuItem('attachment', {
//    text: 'Attachment plugin',
//    onAction: () => {
//      /* Open window */
//      openDialog();
//    }
//  });
  /* Return the metadata for the Help plugin */
//  return {
//    getMetadata: () => ({
//      name: 'Attachment plugin',
//      url: 'http://exampleplugindocsurl.com'
//    })
//  };
};

const htmlTemplate = (location, fileName, textToDisplay='') => {
    return '<a href="' + location + '" download="' + fileName + '">' + (textToDisplay ? textToDisplay : fileName) + '</a>';
}

const tinyNotification = {
  error: (text, timeout=0) => {
    tinyNotification.createNotification(text, 'error', timeout);
  },
  warning: (text, timeout=0) => {
    tinyNotification.createNotification(text, 'warning', timeout);
  },
  success: (text, timeout=0) => {
    tinyNotification.createNotification(text, 'success', timeout);
  },
  info: (text, timeout=0) => {
    tinyNotification.createNotification(text, 'info', timeout);
  },
  createNotification: (text, type='info', timeout=0) => {
      const timeoutReal = timeout>0 ? timeout : 0;
      const closeButtonReal = timeout>0 ? false : true;
      tinymce.activeEditor.notificationManager.open({
        text: text,
        type: type,
        timeout: timeoutReal,
        closeButton: closeButtonReal
      })
  }
};

const attachmentUploadHandler = (blobInfo) => new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.withCredentials = false;  // should pass along credentials (such as cookies, authorization headers, or TLS client certificates) for cross-domain uploads
    xhr.open('POST', 'red/v1/upload/attachment');

//    xhr.upload.onprogress = function (e) {
//        progress(e.loaded / e.total * 100);
//    };

    xhr.onload = () => {
        if (xhr.status < 200 || xhr.status >= 300) {
            let message = 'Upload failed due to a HTTP error - HTTP status: ' + xhr.status + ', message: ' + xhr.statusText;
            tinyNotification.error(message);
            console.error('assetUploadHandler: ' + message);
            return;
        }
        const json = JSON.parse(xhr.responseText);
        if (!json || typeof json.location !== 'string') {
            let message = 'Upload failed - message: ' + xhr.responseText;
            tinyNotification.error(message);
            console.error('assetUploadHandler: ' + message);
            return;
        }
        resolve(json.location);
    };
    xhr.onerror = () => {
        tinyNotification.error('Upload failed due to a XHR Transport error. Code: ' + xhr.status);
        console.error('assetUploadHandler: failed upload - ' + xhr.status);
    };

    const formData = new FormData();
    // blobinfo se vytváří v file_picker_callback, blobInfo.blob() vrací typ File (podtyp Blob) - byl tam zapsán v file_picker_callback
    // pokud druhý parametr formData.append() je File, pak default hodnota vlastnosti filename objektu File je jméno souboru
    formData.append('file', blobInfo.blob()); 
    const editedElementId =  tinymce.activeEditor.id;
    const editedElement =  tinymce.activeEditor.getElement();
    const editedMenuItemId =  editedElement.getAttribute('data-red-menuitemid');
    if(null === editedMenuItemId) {
        const msg = 'error image_upload_handler - element id ' + editedElementId + 'has no attribute data-red-menuitemid.';
        console.warn('assetUploadHandler: ' + msg);
    } else {
        formData.append('edited_item_id', editedMenuItemId);
    }
    xhr.send(formData);
});
