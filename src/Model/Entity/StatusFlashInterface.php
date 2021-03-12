<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface StatusFlashInterface extends EntitySingletonInterface {

    /**
     * Vrací message
     * @return string
     */
    public function getMessage();

    /**
     * Vrací command se životností do příštího requestu (standartní "flash" životnost).
     */
    public function getCommand();

    /**
     * Vrací command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     */
    public function getPostCommand();

    /**
     * Nastaví message
     * @param string $flash
     * @return $this
     */
    public function setMessage(string $flash): StatusFlashInterface;

    /**
     * Připojí message ke na konec existujícího řetezce flash message oddělenou znakem (znaky) konce řádku PHP_EOL
     * @param string $flash
     * @return $this
     */
    public function appendMessage(string $flash): StatusFlashInterface;

    /**
     * Nastaví command se životností do příštího requestu (standartní "flash" životnost).
     *
     * @param type $command
     * @return \Model\Entity\StatusFlashInterface
     */
    public function setCommand($command): StatusFlashInterface;

    /**
     * Nastaví command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     *
     * @param type $command
     * @return \Model\Entity\StatusFlashInterface
     */
    public function setPostCommand($command): StatusFlashInterface;

    /**
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function revolveAfterProcess(ServerRequestInterface $request): void;
}
