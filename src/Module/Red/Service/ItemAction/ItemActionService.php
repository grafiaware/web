<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 * 
 */

namespace Red\Service\ItemAction;

use Red\Model\Entity\ItemAction;

use Red\Model\Repository\ItemActionRepoInterface;
use Red\Model\Entity\ItemActionInterface;
use Auth\Model\Entity\LoginInterface;
use DateTime;
use DateInterval;

use Red\Service\ItemAction\Exception\UnableToAddItemActionForItemException;
use Red\Service\ItemAction\Exception\ItemActionNotExistsException;

/**
 * Description of ItemAction
 *
 * @author pes2704
 */
class ItemActionService implements ItemActionServiceInterface {
    
    private $itemActionRepo;

    public function __construct(ItemActionRepoInterface $itemActionRepo) {
        $this->itemActionRepo = $itemActionRepo;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param type $itemId MenuItem id
     * @param type $loginName Login jméno uživatele
     * @return ItemActionInterface Vytvořená entita
     */
    public function getActiveEditor($itemId): ?string {
        $itemAction = $this->itemActionRepo->getByItemId($itemId);
        return isset($itemAction) ? $itemAction->getEditorLoginName() : null;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param type $itemId MenuItem id
     * @param type $loginName Login jméno uživatele
     * @throws ItemActionNotExistsException
     */
    public function remove($itemId, $loginName) {
        $dbItemAction = $this->itemActionRepo->get($itemId, $loginName);  // načtení z db
        if (!isset($dbItemAction)) {
            throw new ItemActionNotExistsException("Unable to remove item action. Item action for item id '$itemId' and actual user not exists.");
        }
        $this->itemActionRepo->remove($dbItemAction);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param type $loginName Login jméno uživatele
     */
    public function removeUserItemActions($loginName) {
        foreach ($this->itemActionRepo->findByLoginName($loginName) as $itemAction) {
            $this->itemActionRepo->remove($itemAction);
        }
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param DateInterval $interval Interval, po který budou item actions zachovány
     * @param type $itemId MenuItem id
     * @param type $loginName Login jméno uživatele
     * @throws UnableToAddItemActionForItemException
     */
    public function refreshItemActionsAndCreateNew(DateInterval $interval, $itemId, $loginName): ItemActionInterface {
//        If you use a “simple” INSERT, you’ll get a PRIMARY KEY constraint violation error, as you would expect. The entire INSERT statement - which could involve multiple rows - will fail.
//        If you use INSERT IGNORE, the records with duplicate keys in the INSERT statement will be silently “thrown away” and the records already in the DB will be untouched. Records in the INSERT statement without duplicate keys will be INSERTed. I’ve mostly seen INSERT IGNORE behavior used for application-driven “crash recovery” operations.
//        If you use INSERT ... ON DUPLICATE KEY UPDATE, records with duplicate PRIMARY KEYs will trigger de facto UPDATEs on existing records in the database that they are “conflicting with”. The details on which columns get UPDATEd by this operation are specified by directives you specify in the INSERT .. ON DUPLICATE KEY syntax.
        $unableToAdd = false;
        $timeoutDatetime = (new DateTime())->sub($interval);
        foreach ($this->itemActionRepo->findWithAnotherLoginName($loginName) as $itemAction) {
            if ($itemAction->getCreated()<$timeoutDatetime) {
                $this->itemActionRepo->remove($itemAction); // cizí starý - smaž        
            } elseif($itemAction->getItemId()==$itemId) {
                $editedBy = $itemAction->getEditorLoginName();
                $unableToAdd = true;    // cizí a fresh - edituje někdo jiný
            }
        }
        if ($unableToAdd) {
            throw new UnableToAddItemActionForItemException("Unable to add item action for itemid '$itemId' and actual user. Item action with item id '$itemId' already exists for user '{$editedBy}'.");
        }
        $itemAction = new ItemAction();
        $itemAction->setItemId($itemId);
        $itemAction->setEditorLoginName($loginName);
        $this->itemActionRepo->add($itemAction);
        return $itemAction;
    }

}
