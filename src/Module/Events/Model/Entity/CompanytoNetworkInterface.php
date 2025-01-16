<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface CompanytoNetworkInterface extends PersistableEntityInterface {
    
    
   /**
    * 
    * @return string
    */
    public function getCompanyId() ;

    
    /**
     * 
     * @return string
     */
    public function getNetworkId() ;
    
    /**
     * @return string
     */
    public function getLink();        

    /**
     * @return string
     */
    public function getPublished();
    
    /**
     * 
     * @param type $jobId
     * @return NetworkInterfaceCompanytoNetworkInterface $this
     */
    public function setCompanyId(  $jobId) : CompanytoNetworkInterface ;                    
       
     /**
     * 
     * @param int $jobTagId
     * @return NetworkInterfaceCompanytoNetworkInterface $this
     */
    public function setNetworkId($jobTagId) : CompanytoNetworkInterface;
    
    /**
     * 
     * @param type $link
     * @return CompanytoNetworkInterface
     */
    public function setLink($link): CompanytoNetworkInterface;
    
    /**
     * 
     * @param type $published
     * @return CompanytoNetworkInterface
     */
    public function setPublished($published): CompanytoNetworkInterface;    
    
}
