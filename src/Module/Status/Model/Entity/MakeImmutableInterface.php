<?php

namespace Status\Model\Entity;

/**
 * Entita podporující přepnutí do read-only / znovu mutable režimu (typicky po getClone()).
 */
interface MakeImmutableInterface {

  /**
   * Ponechá entitu pouze pro čtení; mutační metody a read metody s vedlejšími účinky vyhodí výjimku.
   */
  public function makeImmutable(): void;

  /**
   * Zruší read-only režim (např. mutable flash clone pro getMessages + replaceEntityInMemory).
   */
  public function makeMutable(): void;
}
