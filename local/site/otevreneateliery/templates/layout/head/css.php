<?php
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
/** @var PhpTemplateFunctionsInterface $this */
?>
<link rel="stylesheet" type="text/css" href="<?= $linksSite ?>semantic-ui/semantic.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= $linksCommon ?>css/layout<?=$version ?>.css" />
    <link rel="stylesheet" type="text/css" href="<?= $linksCommon ?>css/templates<?=$version ?>.css" />
    <link rel="stylesheet" type="text/css" href="<?= $linksCommon.'css/media.css'?>" />
    <!-- head content -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.0.1/dist/cookieconsent.css">
    <?= $this->insertIf( !$isEditableMode, __DIR__.'/cssNoneditableMode.php', ['linksCommon'=>$linksCommon] ); ?>
    <?= $this->insertIf( $isEditableMode, __DIR__.'/cssEditableMode.php', ['linksCommon'=>$linksCommon] ); ?>

