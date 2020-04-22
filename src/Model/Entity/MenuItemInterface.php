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
interface MenuItemInterface extends EntityInterface {

    public function getUidFk();
    public function getLangCodeFk();
    public function getTypeFk();
    public function getId();
    public function getTitle();
    public function getActive();

    /**
     * @return \DateTime
     */
    public function getShowTime();

    /**
     * @return \DateTime
     */
    public function getHideTime();

    public function getActual();
    public function getComponentName();

    public function setUidFk($uidFk): MenuItemInterface;
    public function setLangCodeFk($lang): MenuItemInterface;
    public function setType($type): MenuItemInterface;
    public function setId($id): MenuItemInterface;
    public function setTitle($title): MenuItemInterface;
    public function setActive($active): MenuItemInterface;
    public function setShowTime(\DateTime $start): MenuItemInterface;
    public function setHideTime(\DateTime $stop): MenuItemInterface;
    public function setActual($actual): MenuItemInterface;
    public function setComponentName($componentName): MenuItemInterface;
}
