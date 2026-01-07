<?php
use Pes\Text\Text;
use Pes\Text\Html;

/* 
 * 
 */
$title = "Archivuj ";
$uri = 'events/v1/maintenance/archivecompanies/2025/2026';
echo Html::tag("form", ["method"=>"POST", "action"=>$uri],
                    Html::input("uri", $title, ["uri"=>$uri], ["type"=>"submit", "size"=>"8", "maxlength"=>"10", 'style'=>'margin-left: 1em;'], [])
                );