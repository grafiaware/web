<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\TypeSelect;

use Red\Component\ViewModel\Content\MenuItemViewModel;

use Red\Model\Entity\MenuItemApiInterface;
use Red\Service\ItemCreator\Enum\ApiModuleEnum;
use Red\Service\ItemCreator\Enum\ApiGeneratorEnum;
use Red\Middleware\Redactor\Controler\ItemEditControler;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class ItemTypeSelectViewModel extends MenuItemViewModel implements ItemTypeSelectViewModelInterface {
    
    /**
     * @return MenuItemApiInterface array of
     */
    public function getTypeGenerators() {
        // label => menu_item_type
        return [
            'red paper - stránka redakčního systému typu paper'=> ApiModuleEnum::RED_MODULE.ItemEditControler::SEPARATOR.ApiGeneratorEnum::PAPER_GENERATOR, 
            'red article - stránka redakčního systému typu article'=> ApiModuleEnum::RED_MODULE.ItemEditControler::SEPARATOR.ApiGeneratorEnum::ARTICLE_GENERATOR, 
            'red multipage - stránka redakčního systému typu multipage'=> ApiModuleEnum::RED_MODULE.ItemEditControler::SEPARATOR.ApiGeneratorEnum::MULTIPAGE_GENERATOR, 
            'red static - stránka vytvářená html šablonou s možností použít data redakčního systému'=> ApiModuleEnum::RED_MODULE.ItemEditControler::SEPARATOR.ApiGeneratorEnum::STATIC_GENERATOR,
            'events static - stránka vytvářená html šablonou s možností použít data modulu Events'=> ApiModuleEnum::EVENTS_MODULE.ItemEditControler::SEPARATOR.ApiGeneratorEnum::STATIC_GENERATOR,
            'auth static - stránka vytvářená html šablonou s možností použít data modulu Auth'=> ApiModuleEnum::AUTH_MODULE.ItemEditControler::SEPARATOR.ApiGeneratorEnum::STATIC_GENERATOR
            ];
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'menuItem' => $this->statusViewModel->getPresentedMenuItem(),
                    'typeGenerators' => $this->getTypeGenerators()
                ]
                );
        return parent::getIterator();
    }
}
