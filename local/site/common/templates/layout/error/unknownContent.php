<?php
use Pes\Text\Html;
use Pes\Text\Text;
    $error = "Omlouváme se, obsah není zveřejněn.".PHP_EOL."Entschuldigung, der Inhalt ist nicht veröffentlicht.".PHP_EOL."Вибачте, вміст не публікується.";

?>
                <div style ="display: block;">
                    <p>
                        <?= Text::nl2br($error ?? '') ?>
                    </p>
                    <p>
                        <?= Text::nl2br($message ?? '') ?>
                    </p>
                </div>

