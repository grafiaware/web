<?php
namespace Component\ViewModel\RouteSegment;

use Component\ViewModel\RouteSegment\ListRouteSegmentInterface;
 
use UnexpectedValueException;
/**
 * Description of FamilyRouteSegment
 *
 * @author pes2704
 */
class SingleRouteSegment implements ListRouteSegmentInterface {
    
    private $prefix;
    private $listName;
    private $childId;


    public function __construct(string $prefix, string $listName) {
        if (! (strlen($listName))) {
            throw new UnexpectedValueException("nejsou nastaveny všechny povinné parametry. Parametry routy musí neprázdné.");
        }
        $this->prefix = $prefix;
        $this->listName = $listName;
    }
    
    public function hasList(): bool {
        return ($this->listName);
    }
    
    public function getList(): ?string {
        return $this->listName;
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
        if (!$this->hasList()) {
            throw new LogicException("List parameter have not been set.");
        }
        $prefix = $this->prefix;
        $listName = $this->getList();
        return $this->encodePath("$prefix/$listName");
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return string
     * @throws LogicException
     */
    public function getSavePath(): string {
        if (!$this->hasList()) {
            throw new LogicException("List parameter have not been set.");
        }
        if (!isset($this->childId)) {
            throw new LogicException("Child id have not been set.");
        }        
        $prefix = $this->prefix;
        $listName = $this->getList();
        $childId = $this->childId;
        return $this->encodePath("$prefix/$listName/$childId");        

    }
    
    /**
     * {@inheritDoc}
     * 
     * @return string
     * @throws LogicException
     */
    public function getRemovePath(): string {
        if (!$this->hasList()) {
            throw new LogicException("List parameter have not been set.");
        }
        if (!isset($this->childId)) {
            throw new LogicException("Child id have not been set.");
        }        
        $prefix = $this->prefix;
        $listName = $this->getList();
        $childId = $this->childId;
        $remove = ListRouteSegmentInterface::REMOVE_POSTFIX;
        return $this->encodePath("$prefix/$listName/$childId/$remove");        
    }
        
    private function encodePath($path) {
        return implode('/', array_map(function ($v) {return rawurlencode($v);}, explode('/', $path)));
    }
}
