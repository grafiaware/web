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
     * Připraví přidání proměné obsahující renderer do proměnných šablony (do kontextu). Parametrem této metody je jméno rendereru.
     * Renderer je získán z renderer kontejneru a přidán do proměpřed renderováním šablony.
     * Podmínkou použití je, že renderer musí být možné získat z renderer kontejneru a tedy je také to, že komponent musí mít pro použití této metody nastaven renderer kontejner.
     *
     * @param string $variableName Jméno proměnné kontextu šablony.
     * @param string $rendererName Jméno rendereru pro renderer kontejner.
     */
    public function addChildRendererName($variableName, $rendererName);

    public function setItemId($menuItemId): AuthoredComponentInterface;

}
