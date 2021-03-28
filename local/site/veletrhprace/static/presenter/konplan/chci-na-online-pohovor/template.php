<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

?>

    <div id="chci-na-online-pohovor">
        <?php
        $headline = 'Online pohovor';
        $perex =
            '
            ';
        $footer = '';

        $eventTypeName = "";  // viz Model\Arraymodel\EventType
        $institutionName = "Konplan";

        $event = (new EventList())->getEventList($eventTypeName, $institutionName);

        //include Configuration::componentControler()['templates']."timecolumn/template.php";
        include Configuration::componentControler()['templates']."timeline-boxes/template.php";
        ?>
    </div>