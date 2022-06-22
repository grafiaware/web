<?php
namespace Service\TemplateService;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pes2704
 */
interface TemplateSeekerInterface {

    /**
     * Vyhledá soubor template se zadaným jménem template ve složkách zadaných jako pole. Prohledává složky v pořadí, ve kterém jsou zapsány v poli složek.
     * Vrací plnou cestu k souboru s template. Pokud soubor s template nenalezne, vrací false.
     *
     * @param string $templatesType Typ šablony
     * @param string $templateName Jméno hledané template
     * @return string|false Cesta k souboru s template nebo false
     */
    public function seekTemplate($templatesType, $templateName);
}
