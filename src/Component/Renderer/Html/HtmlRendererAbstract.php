<?php
namespace Component\Renderer\Html;

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
abstract class HtmlRendererAbstract implements HtmlRendererInterface {

    /**
     * @var ClassMapInterface
     */
    protected $classMap;

    protected $viewModel;

    /**
     *
     * @param ClassMapInterface $menuClassMap
     */
    public function __construct(ClassMapInterface $menuClassMap=NULL) {
        $this->classMap = $menuClassMap;
    }

    public function getClassMap(): ClassMapInterface {
        return $this->classMap;
    }
}
