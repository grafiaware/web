<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Menu;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModel;
use Red\Service\ItemApi\ItemApiServiceInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Middleware\Redactor\Controler\HierarchyControler;  //konstanty
use Red\Model\Entity\HierarchyAggregateInterface;
use Component\View\ComponentInterface;

/**
 * Description of DriverViewModel
 *
 * @author pes2704
 */
class DriverViewModel extends ViewModelAbstract implements DriverViewModelInterface {

    /**
     * @var StatusViewModel
     */
    private $statusViewModel;
    private $itemApiService;
    private $menuItem;
    private $presented;
    private $editable = false;
    private $itemType;

    public function __construct(StatusViewModel $status, ItemApiServiceInterface $itemApiService) {
        $this->statusViewModel = $status;
        $this->itemApiService = $itemApiService;
    }
    
    public function withMenuItem(MenuItemInterface $menuItem): DriverViewModelInterface {
        $this->menuItem = $menuItem;
        return $this;
    }
    
    public function setPresented(bool $presented) {
        $this->presented = $presented;
    }
    
    public function setEditable(bool $editable) {
        $this->editable = $editable;
    }
    
    public function setItemType($itemType) {
        $this->itemType = $itemType;
    }
    
    public function getItemType() {
        return $this->itemType;
    }
    
    public function getUid() {
        return $this->menuItem->getUidFk();
    }
    
    public function getId() {
        return $this->menuItem->getId();
    }
    
    public function getPageApi() {
        return $this->itemApiService->getPageApiUri($this->menuItem);
    }
    
    public function getRedContentApi() {
        return $this->itemApiService->getContentApiUri($this->menuItem);
    }
    
    /**
     * 
     * @return type
     */
    public function getRedDriverApi() {
        return $this->isPresented() ? $this->itemApiService->getDriverApiUri($this->menuItem) : $this->itemApiService->getPresentedDriverApiUri($this->menuItem);
    }
    
    public function getTitle() {
        return $this->menuItem->getTitle();
    }
    
    public function isActive(): bool {
        return $this->menuItem->getActive();
    }
    
    public function isPresented(): bool {
        return $this->presented ?? false;        
    }
    
    public function presentEditableMenu(): bool {
        return $this->statusViewModel->presentEditableMenu();
    }
    
    public function isPasteMode(): bool {
        $cut = $this->statusViewModel->getFlashPostCommand(HierarchyControler::POST_COMMAND_CUT);
        $copy = $this->statusViewModel->getFlashPostCommand(HierarchyControler::POST_COMMAND_COPY);
        return ($cut OR $copy);        
    }
}
