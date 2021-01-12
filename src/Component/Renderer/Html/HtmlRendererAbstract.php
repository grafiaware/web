<?php
namespace Component\Renderer\Html;

use Pes\View\Renderer\RendererInterface;

use Pes\View\Renderer\ClassMap\ClassMapInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MenuRenderer
 *
 * @author pes2704
 */
abstract class HtmlRendererAbstract implements RendererInterface {
    /**
     *
     * @var ClassMapInterface
     */
    protected $classMap;

    /**
     *
     * @param ClassMapInterface $menuClassMap
     */
    public function __construct(ClassMapInterface $menuClassMap=NULL) {
        $this->classMap = $menuClassMap;
    }
}
