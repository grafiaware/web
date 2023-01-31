<?php
namespace Web\Component\View\Manage;

use Web\Component\View\ComponentCompositeAbstract;
use Web\Component\Renderer\Html\NoPermittedContentRenderer;
use Web\Component\Renderer\Html\Manage\SelectTemplateRenderer;

use Access\Enum\AccessPresentationEnum;

use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class SelectTemplateComponent extends ComponentCompositeAbstract implements InheritDataViewInterface {

    /**
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritData(iterable $data): ViewInterface {
        return $this->setData($data);
    }
}
