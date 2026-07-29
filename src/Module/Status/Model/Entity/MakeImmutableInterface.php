<?php

namespace Status\Model\Entity;

/**
 * Entita podporující přepnutí do read-only režimu (typicky po getClone()).
 */
interface MakeImmutableInterface {

  /**
   * Ponechá entitu pouze pro čtení; mutační metody a read metody s vedlejšími účinky vyhodí výjimku.
   */
  public function makeImmutable(): void;
}
