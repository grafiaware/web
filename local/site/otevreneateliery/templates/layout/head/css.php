<?php
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
/** @var PhpTemplateFunctionsInterface $this */
?>
<link rel="stylesheet" type="text/css" href="<?= $linksSite ?>semantic-ui/semantic.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= $linksCommon ?>css/layout.css" />
    <link rel="stylesheet" type="text/css" href="<?= $linksCommon ?>css/templates.css" />
    <link rel="stylesheet" type="text/css" href="<?= $linksCommon.'css/old/styles.css'?>" />
    <link rel="stylesheet" type="text/css" href="<?= $linksCommon.'css/media.css'?>" />

    <?= $this->insertConditionally( !$isEditableMode, __DIR__.'/cssNoneditableMode.php', ['linksCommon'=>$linksCommon] ); ?>
    <?= $this->insertConditionally( $isEditableMode, __DIR__.'/cssEditableMode.php', ['linksCommon'=>$linksCommon] ); ?>

