<?php
namespace Red\Component\View\Generated;

use Component\View\ComponentCompositeAbstract;

use Red\Component\ViewModel\Generated\LanguageSelectViewModel;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class LanguageSelectComponent extends ComponentCompositeAbstract {

    /**
     * @var LanguageSelectViewModel
     */
    protected $contextData;
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
