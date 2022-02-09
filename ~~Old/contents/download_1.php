<?php
if (isset($_GET['list']) AND $_GET['list']='download') {
    if (isset($_GET['file'])) {
        $souborProDownload = $_GET['file'];
        if ($souborProDownload) {
            if (is_readable('./files/'.$souborProDownload)) {
                require_once 'Elementy/VynucenyDownload.php';
                Elementy_VynucenyDownload::download('./files/'.$souborProDownload);
            }
        }
    }
}

?>
