<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoFkUniqueInterface extends DaoFkCommonInterface {

    public function getByFk($fkAttributesName, array $fkNameValueTouples);

}