<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\CompanyInfoInterface;

/**
 * Description of CompanyAddress
 *
 * @author vlse2610
 */
class CompanyInfo extends PersistableEntityAbstract implements CompanyInfoInterface {

    private $companyId;   //NOT NULL
    
    private $introduction;      //default NULL
    private $videoLink;      //default NULL
    private $positives;      //default NULL
    private $social;      //default NULL

    public function getCompanyId(): ?string {
        return $this->companyId;
    }

    public function getIntroduction(): ?string {
        return $this->introduction;
    }

    public function getVideoLink(): ?string {
        return $this->videoLink;
    }

    public function getPositives(): ?string {
        return $this->positives;
    }

    public function getSocial(): ?string {
        return $this->social;
    }

    public function setCompanyId($companyId): CompanyInfoInterface {
        $this->companyId = $companyId;
        return $this;
    }

    public function setIntroduction($introduction): CompanyInfoInterface {
        $this->introduction = $introduction;
        return $this;
    }

    public function setVideoLink($videoLink): CompanyInfoInterface {
        $this->videoLink = $videoLink;
        return $this;
    }

    public function setPositives($positives): CompanyInfoInterface {
        $this->positives = $positives;
        return $this;
    }

    public function setSocial($social): CompanyInfoInterface {
        $this->social = $social;
        return $this;
    }



}
