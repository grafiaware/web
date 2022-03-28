<?php
namespace Model\Entity;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Interface entity, která se vyskytuje vevíce instancí odlišených od sebe identitou (klíčem).
 * 
 * @author pes2704
 */
interface EntityInterface extends EntitySingletonInterface {

    public function getKeyAttribute();
}
