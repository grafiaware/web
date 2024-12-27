<?php
namespace Component\ViewModel\RouteSegment;

 use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;
/**
 * Description of FamilyRouteSegment
 *
 * @author pes2704
 */
class FamilyRouteSegment implements FamilyRouteSegmentInterface {
    private $parentName;
    private $parentId;
    private $childName;
    
    public function __construct(string $parentName, string $parentId, string $childName) {
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
    
    public function getPath(): string {
        if (!$this->hasFamily()) {
            throw new LogicException("All family parameters have not been set.");
        }
        $parentName = $this->getParentName();
        $parentId = $this->getParentId();
        $itemName = $this->getItemName();
        return "$parentName/$parentId/$itemName";
    }
}
