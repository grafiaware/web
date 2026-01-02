<?php
namespace Component\ViewModel\RouteSegment;

/**
 *
 * @author pes2704
 */
interface SingleRouteSegmentInterface {
    
    const REMOVE_POSTFIX = "remove";
    
    /**
     * Metoda informuje zda je nastaven identifikátor kolekce (list).
     * 
     * @return bool
     */
    public function hasList(): bool;

    /**
     * Metoda vrací hodnotu identifikátoru kolekce (list).
     * 
     * @return string|null
     */
    public function getList(): ?string;
    
    /**
     * Metoda nastaví identifikátor potomka pro metody getSavePath() a getRemovePath().
     * 
     * @param string $id
     */
    public function setChildId(string $id=null);
    
    /**
     * Metoda informuje zda je nastaven identifikátor potomka pro metody getSavePath() a getRemovePath().
     * 
     * @return bool
     */
    public function hasChildId(): bool;
    
    /**
     * Metoda vrací hodnotu identifikátoru potomka pro metody getSavePath() a getRemovePath().
     * 
     * @return string|null
     */
    public function getChildId(): ?string;
    
    /**
     * Vrací url path pro uložení nového potomka - nového prvku kolekce potomků. 
     * Identifikuje kolekci (list). Path je zakončena identifikátorem kolekce.
     * Path je enkódována jako url.
     * 
     * @return string
     */
    public function getAddPath(): string;
    
    /**
     * Vrací url path pro uložení jednoho potomka - jednoho prvku kolekce potomků. 
     * Identifikuje kolekci a potomka. Path obsahuje identifikátor kolekce (list) a je zakončena identifikátorem potomka (item). 
     * Identifikátor potomka musí být nastaven metodou setChildId(string $id).
     * Path je enkódována jako url.
     * 
     * @return string
     */
    public function getSavePath(): string;
    
    /**
     * Vrací url path pro smazání jednoho potomka - jednoho prvku kolekce potomků. 
     * Identifikuje kolekci a potomka a na konec připojí příponou pro API akci remove, předpokládá použití html formuláře a metody POST i pro mazání.
     * Path obsahuje identifikátor kolekce (list) a je zakončena identifikátorem potomka (item) a příponou pro API akci remove. 
     * Identifikátor potomka musí být nastaven metodou setChildId(string $id).
     * Path je enkódována jako url.
     * 
     * @return string
     */
    public function getRemovePath(): string;
    
}
