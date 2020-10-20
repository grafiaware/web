<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Deployer;


/**
 * Description of WebDeployer
 *
 * @author pes2704
 */
class WebDeployer extends DeployerAbstract {
    const SITE_PATH = 'Site';

    public function __construct() {
        ;
    }

    public function deploy($nick) {
        if (!fileExists(self::SITE_PATH-"/$nick")) {
            throw new SiteFolderNotExistsException("Neexistuje zadaná složka pro site deployemnt $nick.");
        }

        try {

        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
}
