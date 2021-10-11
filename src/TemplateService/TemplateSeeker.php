<?php
namespace TemplateService;

use Configuration\TemplateControlerConfigurationInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateSeeker
 *
 * @author pes2704
 */
class TemplateSeeker implements TemplateSeekerInterface {

    /**
     *
     * @var TemplateControlerConfigurationInterface
     */
    private $configuration;

    public function __construct(TemplateControlerConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Vyhledá soubor template se zadaným jménem template ve složkách zadaných jako pole. Prohledává složky v pořadí, ve kterém jsou zapsány v poli složek.
     * Vrací plnou cestu k souboru s template. Pokud soubor s template nenalezne, vrací false.
     *
     * @param array $templatesFolders Pole složek, ve kterých bude hledat template
     * @param string $templateName Jméno hledané template
     * @return string|false Cesta k souboru s template nebo false
     */
    public function seekTemplate($templatesFolders, $templateName) {
        $templateExtension = $this->configuration->getDefaultExtension();
        foreach ($templatesFolders as $templatesFolder) {
            $filename = $templatesFolder.$templateName.$templateExtension;
            if (is_readable($filename)) {
                return $filename;
            }
        }
        return false;    }
}
