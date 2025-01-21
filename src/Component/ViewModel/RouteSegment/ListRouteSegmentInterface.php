<?php
namespace Component\ViewModel\RouteSegment;

/**
 *
 * @author pes2704
 */
interface ListRouteSegmentInterface {
    
    const REMOVE_POSTFIX = "remove";
    
    public function hasList(): bool;

    public function getList(): ?string;
    
    /**
     * 
     * @param string $id
     */
    public function setChildId(string $id=null);
    
    public function hasChildId(): bool;
    
    public function getChildId(): ?string;
    
    /**
     * Vrací url path pro "rodinu" - kolekci potomků. Path je zakončena jménem kolekce potomků.
     * Path je enkódována jako url.
     * 
     * @return string
     */
    public function getAddPath(): string;
    
    /**
     * Vrací url path pro jednoho potomka - prvek kolekce potomků. Path je zakončena identifikátorem potomka. Identifikátor musí být nastaven metodou setChildId(string $id).
     * Path je enkódována jako url.
     * 
     * @return string
     */
    public function getSavePath(): string;
    
    /**
     * Vrací url path pro jednoho potomka - prvek kolekce potomků následovanou příponou pro API akci remove. Path je zakončena identifikátorem potomka 
     * a příponou pro API akci remove. Identifikátor potomka musí být nastaven metodou setChildId(string $id).
     * Path je enkódována jako url.
     * 
     * @return string
     */
    public function getRemovePath(): string;
    
}
