<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface StatusPresentationInterface {

    /**
     * @return Language
     */
    public function getLanguage(): ?Language;

    /**
     *
     * @param \Model\Entity\Language $language
     * @return \Model\Entity\StatusPresentationInterface
     */
    public function setLanguage(Language $language): StatusPresentationInterface;

    /**
     * @return string
     */
    public function getRequestedLangCode();

    /**
     *
     * @param string $requestedLangCode
     */
    public function setRequestedLangCode($requestedLangCode);

    /**
     * @return string
     */
    public function getItemUid();

    /**
     *
     * @param atring $itemUid
     * @return \Model\Entity\StatusPresentationInterface
     */
    public function setItemUid($itemUid): StatusPresentationInterface;

    /**
     *
     * @return \Model\Entity\UserActions|null
     */
    public function getUserActions(): ?UserActions;

    /**
     *
     * @param \Model\Entity\UserActions $userStatus
     */
    public function setUserActions(UserActions $userStatus);
}
