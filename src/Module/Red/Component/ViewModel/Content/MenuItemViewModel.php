<?php
namespace Red\Component\ViewModel\Content;

use Component\ViewModel\ViewModelAbstract;
use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Entity\MenuItemInterface;
use Component\ViewModel\StatusViewModelInterface;

use LogicException;

/**
 * Description of MenuItemViewModel
 *
 * @author pes2704
 */
abstract class MenuItemViewModel extends ViewModelAbstract implements MenuItemViewModelInterface {

    /**
     * @var MenuItemRepoInterface
     */
    protected $menuItemRepo;

    protected $menuItemId;
    
    /**
     * @var StatusViewModelInterface
     */
    protected $statusViewModel;

    private $uid;

    public function __construct(
            StatusViewModelInterface $status,
            MenuItemRepoInterface $menuItemRepo
            ) {
        $this->statusViewModel = $status;
        $this->menuItemRepo = $menuItemRepo;
        $this->uid = uniqid();
        // Gets a prefixed unique identifier based on the current time in microseconds.
        // With an empty <code>prefix</code>, the returned string will be 13 characters long.
    }

    public function getComponentUid() {
        return $this->uid;
    }

    public function getStatusViewModel(): StatusViewModelInterface {
        return $this->statusViewModel;
    }
    
    /**
     * Nastaví id položky MenuItem, podle kterého bude načítáná příslušná entita s obsahem (např. Paper, Article, Multipage) a ItemAction
     * Obvykle je metoda volána z metody Front kontroleru.
     *
     * @param type $menuItemId
     * @throws LogicException
     */
    public function setMenuItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }
    
    public function getMenuItemId() {
        if (!isset($this->menuItemId)) {
            throw new LogicException("Modelu ". static::class ." nebyla nastavena hodnota menu item id. Hodnotu je nutné nastavit voláním metody setItemId().");
        }
        return $this->menuItemId;
    }
    
    public function getMenuItem(): MenuItemInterface {
        return $this->menuItemRepo->getById($this->getMenuItemId());
    }
}
