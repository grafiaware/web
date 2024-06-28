<?php
namespace Red\Service\ItemAction;

use Red\Model\Entity\ItemActionInterface;
use Auth\Model\Entity\LoginInterface;
use DateInterval;

/**
 *
 * @author pes2704
 */
interface ItemActionServiceInterface {
    
    /**
     * Vrací editora itemu podle zadaného id (pokud existuje).
     * 
     * @param type $itemId MenuItem id
     * @return string|null
     */
    public function getActiveEditor($itemId): ?string;
    
    /**
     * Smaže z repository item action pro zadaný item id a login.
     * 
     * @param type $itemId MenuItem id
     * @param type $loginName Login jméno uživatele
     */
    public function remove($itemId, $loginName);
    
    /**
     * Smaže z repository všechny ItemActions zadaného uživatele. Určeno pro mazání akcí při odhlašování uživatele.
     * 
     * @param type $loginName Login jméno uživatele
     */
    public function removeUserItemActions($loginName);
    
    /**
     * Vyčistí item actions a pokud po vyčištění item se zadaným item id needituje jiný uživatel než zadaný uživatel
     * přidá item action ze zadaným item id a login name.
     * 
     * Smaže z repository všechny ItemActions jiných uživatelů, které jsou starší než zadaný interval. 
     * NEMAŽE záznamy zadaného uživatele i když jsou starší, přihlášený uživatel tak může editovat v jedné session a stále, déle než je požadovaný timeout interval.
     * Metoda čistí item actions při zpracování každého requestu, který chce přidat item action. 
     * 
     * @param DateInterval $interval 4asový interval po jehož uplynutí jsou položky editované jiným uživatelem uvolněny pro další editaci.
     * @param string $itemId id položky menu
     * @param string $loginName Jméno uživatele, pro kterého se item actions nemají mazat
     * @return ItemActionInterface
     */
    public function refreshItemActionsAndCreateNew(DateInterval $interval, $itemId, $loginName): ItemActionInterface ;
    
    /**
     * Vyčistí item actions a pokud po vyčištění item se zadaným item id needituje jiný uživatel než zadaný uživatel
     * přidá item action ze zadaným item id a login name.
     * 
     * Smaže z repository všechny ItemActions jiných uživatelů, které jsou starší než zadaný interval. 
     * NEMAŽE záznamy zadaného uživatele i když jsou starší, přihlášený uživatel tak může editovat v jedné session a stále, déle než je požadovaný timeout interval.
     * Metoda čistí item actions při zpracování každého requestu, který chce přidat item action. 
     * 
     *  
     * @param DateInterval $interval 4asový interval po jehož uplynutí jsou položky editované jiným uživatelem uvolněny pro další editaci.
     * @param string $itemId id položky menu
     * @param string $loginName Jméno uživatele, pro kterého se item actions nemají mazat
     * @return string|null Jméno uživatele, který položku edituje
     */
    public function refreshItemActionsAndGetLockedBy(DateInterval $interval, $itemId, $loginName): ?string;    
}
