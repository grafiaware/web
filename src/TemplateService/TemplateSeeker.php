<?php
namespace TemplateService;

use Configuration\TemplateControlerConfigurationInterface;

use TemplateService\Exception\UnknownTemplateTypeException;

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
     * @param array $templatesType Pole složek, ve kterých bude hledat template
     * @param string $templateName Jméno hledané template
     * @return string|false Cesta k souboru s template nebo false
     */
    public function seekTemplate($templatesType, $templateName) {
        $templateExtension = $this->configuration->getDefaultExtension();
        $templatesFolders = $this->configuration->getFolders();
        if (!array_key_exists($templatesType, $templatesFolders)) {
            throw new UnknownTemplateTypeException("Hledaný typ template '$templatesType' neexistuje v konfiguraci složek template.");
        }
        foreach ($templatesFolders[$templatesType] as $templatesFolder) {
            $filename = $templatesFolder.$templateName.$templateExtension;
            if (is_readable($filename)) {
                return $filename;
            }
        }
        return false;
    }
}