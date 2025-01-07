<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Events\Model\Entity;

/**
 *
 * @author pes2704
 */
interface CompanyInfoInterface {
    
    /**
     * 
     * @return string|null
     */
    public function getCompanyId(): ?string;

    /**
     * 
     * @return string|null
     */
    public function getIntroduction(): ?string;

    /**
     * 
     * @return string|null
     */
    public function getVideoLink(): ?string;

    /**
     * 
     * @return string|null
     */
    public function getPositives(): ?string;

    /**
     * 
     * @return string|null
     */
    public function getSocial(): ?string;

    /**
     * 
     * @param type $companyId
     * @return CompanyInfoInterface
     */
    public function setCompanyId($companyId): CompanyInfoInterface;

    /**
     * 
     * @param type $introduction
     * @return CompanyInfoInterface
     */
    public function setIntroduction($introduction): CompanyInfoInterface;

    /**
     * 
     * @param type $videoLink
     * @return CompanyInfoInterface
     */
    public function setVideoLink($videoLink): CompanyInfoInterface;

    /**
     * 
     * @param type $positives
     * @return CompanyInfoInterface
     */
    public function setPositives($positives): CompanyInfoInterface;

    /**
     * 
     * @param type $social
     * @return CompanyInfoInterface
     */
    public function setSocial($social): CompanyInfoInterface;
    
}
