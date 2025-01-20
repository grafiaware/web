<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\Network;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\HydratorInterface;


/**
 * Description of JobTagHydrator
 *
 * @author vlse2610
 */
class NetworkHydrator extends TypeHydratorAbstract implements HydratorInterface {
    
   /**
    * 
    * @param JobTag $network
    * @param ArrayAccess $rowData
    */
    public function hydrate(EntityInterface $network, ArrayAccess $rowData) {
        /** @var  Network $network */
        $network->setId($this->getPhpValue($rowData, 'id'));                
        $network->setIcon($this->getPhpValue($rowData, 'icon' ));
        $network->setTitle($this->getPhpValue($rowData, 'title' ));
        $network->setEmbedCodeTemplate($this->getPhpValue($rowData, 'embed_code_template' ));
    } 

    /**
     * 
     * @param JobTag $network
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $network, ArrayAccess $rowData) {
        /** @var  Network $network */
        $this->setSqlValue( $rowData, 'icon', $network->getIcon() ); 
        $this->setSqlValue( $rowData, 'title', $network->getTitle() ); 
        $this->setSqlValue( $rowData, 'embed_code_template', $network->getEmbedCodeTemplate() ); 
    }
}
