<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Menu\Model\HierarchyHooks;

/**
 * Description of MenuStyles
 *
 * @author pes2704
 */
class MenuListStyles {
    
    private $style = [
      0 => ['ul' => 'ul-class-for-depth0', 'li' => 'li-class-for-depth0'],
      1 => ['ul' => 'ul-class-for-depth1', 'li' => 'li-class-for-depth1'],
      2 => ['ul' => 'ul-class-for-depth2', 'li' => 'li-class-for-depth2'],
      3 => ['ul' => 'ul-class-for-depth3', 'li' => 'li-class-for-depth3'],
      4 => ['ul' => 'ul-class-for-depth4', 'li' => 'li-class-for-depth4'],
      5 => ['ul' => 'ul-class-for-depth5', 'li' => 'li-class-for-depth5'],
      6 => ['ul' => 'ul-class-for-depth6', 'li' => 'li-class-for-depth6'],
      7 => ['ul' => 'ul-class-for-depth7', 'li' => 'li-class-for-depth7'],
      8 => ['ul' => 'ul-class-for-depth8', 'li' => 'li-class-for-depth8']
    ];
    
    private $default = ['ul' => 'ul-class-default', 'li' => 'li-class-default'];
    
    /**
     * 
     * @param integer $depth
     * @param string $tagName Jméno tagu, pro který je styl hledán. Jméno tagu musí být platná hodnota MenuStylesTagEnum
     * @return string
     * @throws UnexpectedValueException  Neznámé jméno tagu, pro který je styl hledán.
     */
    public function getStyle($depth, $tagName) {
        if (array_key_exists($depth, $this->style)) {
            return $this->style[$depth][$tagName];
        } else {
            return $this->default[$tagName];
        }
    }
    
}
