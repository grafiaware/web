<?php
namespace Red\Component\ViewModel\Content;

use Component\ViewModel\ViewModelAbstract;
use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Entity\MenuItemInterface;
use Component\ViewModel\StatusViewModelInterface;

use LogicException;
use Exception;
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

    /**
     * {@inheritDoc}
     * 
     * Identifikátor je generován v konstruktoru pomocí uniquid().
     * @return type
     */
    public function getComponentUid() {
        return $this->uid;
    }

    /**
     * {@inheritDoc}
     * 
     * @return StatusViewModelInterface
     */
    public function getStatusViewModel(): StatusViewModelInterface {
        return $this->statusViewModel;
    }
    
    /**
     * {@inheritDoc}
     * 
     *
     * @param type $menuItemId
     * @throws LogicException
     */
    public function setMenuItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return string
     * @throws LogicException
     */
    public function getMenuItemId() {
        if (!isset($this->menuItemId)) {
            throw new LogicException("Modelu ". static::class ." nebyla nastavena hodnota menu item id. Hodnotu je nutné nastavit voláním metody setItemId().");
        }
        return $this->menuItemId;
    }
    
    /**
     * {@inheritDoc}
     * 
     * Metoda čte entitu MenuItem z databáze (pomocí repository) podle id zadaného setMenuItemId($menuItemId). 
     * Pokud id nebylo zadáno, metoda vyhodí výjimku.
     * 
     * @return MenuItemInterface
     * @throws LogicException
     */
    public function getMenuItem(): ?MenuItemInterface {
        try {
            $id = $this->getMenuItemId();
        } catch (Exception $exc) {
            throw new LogicException($exc->getMessage());
        }

        return $this->menuItemRepo->getById($id);
    }
}
