<?php
namespace Red\Service\ItemCreator\Enum;

use Pes\Type\Enum;

/**
 * Description of ItemGeneratorEnum
 *
 * @author pes2704
 */
class ItemApiGeneratorEnum extends Enum {
    const SELECT_GENERATOR = 'select';
    const ARTICLE_GENERATOR = 'article';
    const PAPER_GENERATOR = 'paper';
    const MULTIPAGE_GENERATOR = 'multipage';
    const STATIC_GENERATOR = 'static';
}
