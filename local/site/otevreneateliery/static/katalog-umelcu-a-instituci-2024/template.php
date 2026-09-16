<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

/** @var PhpTemplateRendererInterface $this */

include __DIR__ . '/datum_a_cas_konani.php';
include ConfigurationCache::componentControler()['templates'] . 'katalog/template.php';
