<?php
namespace Component\ViewModel\RouteSegment;

use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;
 
use UnexpectedValueException;
/**
 * Description of FamilyRouteSegment
 *
 * @author pes2704
 */
class FamilyRouteSegment implements FamilyRouteSegmentInterface {
    
    private $prefix;
    private $parentName;
    private $parentId;
    private $childName;
    private $childId;


    public function __construct(string $prefix, string $parentName, string $parentId, string $childName) {
        if (! (strlen($parentName) && strlen($parentId) && strlen($childName))) {
            throw new UnexpectedValueException("nejsou nastaveny všechny povinné parametry. Parametry routy musí neprázdné.");
        }
        $this->prefix = $prefix;
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
    
    /**
     * 
     * @param string $id
     */
    public function setChildId(string $id=null) {
        $this->childId = $id;
    }
    
    public function hasChildId(): bool {
        return isset($this->childId);
    }
    
    public function getChildId(): ?string {
        return $this->childId;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return string
     * @throws LogicException
     */
    public function getAddPath(): string {
        if (!$this->hasFamily()) {
            throw new LogicException("All family parameters have not been set.");
        }
        $prefix = $this->prefix;
        $parentName = $this->getParentName();
        $parentId = $this->getParentId();
        $itemName = $this->getItemName();
        return $this->encodePath("$prefix/$parentName/$parentId/$itemName");
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return string
     * @throws LogicException
     */
    public function getSavePath(): string {
        if (!$this->hasFamily()) {
            throw new LogicException("All family parameters have not been set.");
        }
        if (!isset($this->childId)) {
            throw new LogicException("Child id have not been set.");
        }        
        $prefix = $this->prefix;
        $parentName = $this->getParentName();
        $parentId = $this->getParentId();
        $itemName = $this->getItemName();
        $childId = $this->childId;
        return $this->encodePath("$prefix/$parentName/$parentId/$itemName/$childId");        

    }
    
    /**
     * {@inheritDoc}
     * 
     * @return string
     * @throws LogicException
     */
    public function getRemovePath(): string {
        if (!$this->hasFamily()) {
            throw new LogicException("All family parameters have not been set.");
        }
        if (!isset($this->childId)) {
            throw new LogicException("Child id have not been set.");
        }        
        $prefix = $this->prefix;
        $parentName = $this->getParentName();
        $parentId = $this->getParentId();
        $itemName = $this->getItemName();
        $childId = $this->childId;
        $remove = FamilyRouteSegmentInterface::REMOVE_POSTFIX;
        return $this->encodePath("$prefix/$parentName/$parentId/$itemName/$childId/$remove");        
    }
        
    private function encodePath($path) {
        return implode('/', array_map(function ($v) {return rawurlencode($v);}, explode('/', $path)));
    }
}
