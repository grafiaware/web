<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;

use Red\Model\Entity\LanguageInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class LanguageHydrator implements HydratorInterface {

    /**
     *
     * @param LanguageInterface $language
     * @param type $rowData
     */
    public function hydrate(EntityInterface $language, ArrayAccess $rowData) {
        /** @var LanguageInterface $language */
        $language
            ->setLangCode($rowData->offsetGet('lang_code'))
            ->setLocale($rowData->offsetGet('locale'))
            ->setName($rowData->offsetGet('name'))
            ->setCollation($rowData->offsetGet('collation'))
            ->setState($rowData->offsetGet('state'));
    }

    /**
     *
     * @param LanguageInterface $language
     * @param type $rowData
     */
    public function extract(EntityInterface $language, ArrayAccess $rowData) {
        /** @var LanguageInterface $language */
        $rowData->offsetSet('lang_code',  $language->getLangCode());
        $rowData->offsetSet('locale',  $language->getLocale());
        $rowData->offsetSet('name',  $language->getName());
        $rowData->offsetSet('collation',  $language->getCollation());
        $rowData->offsetSet('state',  $language->getState());
    }

}
