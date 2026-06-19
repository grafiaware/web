<?php
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 * Description of EntityAbstract
 *
 * @author pes2704
 */
abstract class PersistableEntityAbstract implements PersistableEntityInterface {

    private $persisted = false;

    private $locked = false;
    /**
     *
     * @return \Model\Entity\PersistableEntityInterface
     */
    #[\Override]
    public function setPersisted(): PersistableEntityInterface {
        $this->persisted = TRUE;
        return $this;
    }

    #[\Override]
    public function setUnpersisted(): PersistableEntityInterface {
        $this->persisted = FALSE;
        return $this;
    }

    #[\Override]
    public function isPersisted(): bool {
        return $this->persisted;
    }

    #[\Override]
    public function lock(): PersistableEntityInterface {
        $this->locked = true;
        return $this;
    }

    #[\Override]
    public function unlock(): PersistableEntityInterface {
        $this->locked = false;
        return $this;
    }

    #[\Override]
    public function isLocked(): bool {
        return $this->locked;
    }
    
    /**
     * Po klonovÃ¡nÃ­ nastavÃ­ klonu unpersisted a unlock. TÃ­m vznikne "novÃ½" nepersistovanÃ½ objekt. !! Metoda neumÃ­ smazat identitu (id) nevÃ­, kterÃ¡ vlastnost (vlastnosti) definuje identitu.
     * Pokud je entita naÄtenÃ¡ z databÃ¡ze je nutnÃ© nastavit identitu na null.
     */
    #[\Override]
    public function __clone() {   //Netbeans generuje __clone(): void - je to bug, na tom PHP tiÅ¡e skonÄÃ­!
        $this->setUnpersisted();
        $this->unlock();
    }
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 * Description of EntityAbstract
 *
 * @author pes2704
 */
abstract class PersistableEntityAbstract implements PersistableEntityInterface {

    private $persisted = false;

    private $locked = false;
    /**
     *
     * @return \Model\Entity\PersistableEntityInterface
     */
    #[\Override]
    public function setPersisted(): PersistableEntityInterface {
        $this->persisted = TRUE;
        return $this;
    }

    #[\Override]
    public function setUnpersisted(): PersistableEntityInterface {
        $this->persisted = FALSE;
        return $this;
    }

    #[\Override]
    public function isPersisted(): bool {
        return $this->persisted;
    }

    #[\Override]
    public function lock(): PersistableEntityInterface {
        $this->locked = true;
        return $this;
    }

    #[\Override]
    public function unlock(): PersistableEntityInterface {
        $this->locked = false;
        return $this;
    }

    #[\Override]
    public function isLocked(): bool {
        return $this->locked;
    }
    
    /**
     * Po klonování nastaví klonu unpersisted a unlock. Tím vznikne "nový" nepersistovaný objekt. !! Metoda neumí smazat identitu (id) neví, která vlastnost (vlastnosti) definuje identitu.
     * Pokud je entita načtená z databáze je nutné nastavit identitu na null.
     */
    #[\Override]
    public function __clone() {   //Netbeans generuje __clone(): void - je to bug, na tom PHP tiše skončí!
        $this->setUnpersisted();
        $this->unlock();
    }
}
