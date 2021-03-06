<?php

namespace Component\View\Authored\Menu;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Menu\MenuViewModelInterface;

/**
 * Description of MenuItemComponentSwitch
 *
 * @author pes2704
 */
class MenuItemSelectorComponent extends AuthoredComponentAbstract {

    /**
     * @var MenuViewModelInterface
     */
    protected $viewModel;

    /**
     *
     * @param MenuViewModelInterface $viewModel
     */
    public function __construct(MenuViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function getString($data = null) {
        $menuItem = $this->viewModel->getPresentedMenuNode()->getMenuItem();
        $editable = $this->viewModel->isEditableArticle;
        $menuItemType = $menuItem->getTypeFk();
            switch ($menuItemType) {
                case 'segment':
                    if ($editable) {
                        $component = $this->container->get('article.headlined.editable');
                    } else {
                        $component = $this->container->get('article.headlined');
                    }
                    break;
                case 'empty':
                    if ($editable) {
                        $content = $this->container->get(ItemTypeSelectComponent::class);
                    } else {
                        $component = $this->container->get('article.headlined');
                    }
                    break;
                case 'paper':
                    if ($editable) {
                        $component = $this->container->get('article.headlined.editable');
                    } else {
                        $component = $this->container->get('article.headlined');
                    }
                    break;
                case 'redirect':
                    $component = "No content for redirect type.";
                    break;
                case 'root':
                        $component = $this->container->get('article.headlined');
                    break;
                case 'trash':
                        $component = $this->container->get('article.headlined');
                    break;

                default:
                        $component = $this->container->get('article.headlined');
                    break;
            }
        return $component;



        $component->getString($data);
    }
}
