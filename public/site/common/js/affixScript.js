/*
 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement
 */

function loadError(oError) {
  throw new URIError("The script " + oError.target.src + " didn't load correctly.");
}
function onLoad() {
    alert("The script \"myScript2.js\" has been correctly loaded.");
}
function affixScriptToHead(url, onloadFunction) {
  var newScript = document.createElement("script");
  newScript.onerror = loadError;
  if (onloadFunction) { newScript.onload = onloadFunction; }
  document.head.appendChild(newScript);  // do head
  //document.currentScript.parentNode.insertBefore(newScript, document.currentScript);  //před aktuální sktipt (třeba do body
  newScript.src = url;
}

// affixScriptToHead("myScript1.js");
// affixScriptToHead("myScript2.js", function () { alert("The script \"myScript2.js\" has been correctly loaded."); });
// affixScriptToHead("myScript1.js", ??);