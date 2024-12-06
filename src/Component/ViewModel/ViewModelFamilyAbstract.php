<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelFamilyInterface;
use Component\ViewModel\ViewModelAbstract;

use LogicException;

/**
 * Description of ViewModelChildListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelFamilyAbstract extends ViewModelAbstract implements ViewModelFamilyInterface {
    
    private $parentName;
    private $parentId;
    private $childName;
    
    public function setFamily(string $parentName, string $parentId, string $childName) {
        if (! ($parentName && $parentId && $childName)) {
            throw new UnexpectedValueException("nejsou nastaveny všechny povinné parametry.");
        }
        $this->parentName = $parentName;
        $this->parentId = $parentId;
        $this->childName = $childName;
    }
    
    public function hasFamily(): bool {
        return ($this->parentName && $this->parentId && $this->childName);
    }
    
    public function getParentName(): string {
        return $this->parentName;
    }
    
    public function getParentId(): string {
        return $this->parentId;
    }
    
    public function getItemName(): string {
        return $this->childName;
    }
    
    public function getFamilyRouteSegment(): string {
        if (!$this->hasFamily()) {
            throw new LogicException("All family parameters have not been set.");
        }
        $parentName = $this->getParentName();
        $parentId = $this->getParentId();
        $itemName = $this->getItemName();
        return "$parentName/$parentId/$itemName";
    }
        
}
