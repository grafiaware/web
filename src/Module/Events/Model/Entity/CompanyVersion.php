<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\CompanyVersionInterface;

/**
 * Description of CompanyAddress
 *
 * @author vlse2610
 */
class CompanyVersion extends PersistableEntityAbstract implements CompanyVersionInterface {

    private $version;   //NOT NULL, primary key

    public function getVersion(): string {
        return $this->version;
    }
    
    public function setVersion($version): CompanyVersionInterface {
        $this->version = $version;
        return $this;
    }
}
