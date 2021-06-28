<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored;

/**
 *
 * @author pes2704
 */
interface AuthoredComponentInterface {

    /**
     * Nastaví cestu ke složce se složkami šablon používaných v komponentě.
     *
     * @param string $templatesPath
     */
    public function setTemplatesPath($templatesPath);

    /**
     * Přidá do proměnných šablony (do kontextu) proměnnou obsahující renderer. Parametrem této metody je jméno rendereru.
     * Renderer je získán z render kontejneru před renderováním šablony.
     * Podmínkou použití je, že renderer musí být možné získat z renderer kontejneru a tedy je také to, že komponent musí mít pro použití této metody nastaven renderer kontejner.
     *
     * @param string $variableName Jméno proměnné kontextu šablony.
     * @param string $rendererName Jméno rendereru pro renderer kontejner.
     */
    public function addSubRendererName($variableName, $rendererName);

    public function setItemId($menuItemId): AuthoredComponentInterface;

    /**
     * Cesta ke složce se složkami šablon používaných v komponentě.
     *
     * @param string $templateName
     * @return string
     */
    public function getTemplatePath($templateName): string;

}
