<?php
namespace Events\Model\Entity;

use Events\Model\Entity\Institution;
use Events\Model\Entity\InstitutionType;
use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;

/**
 * Description of InstitutionTypeAggregateInstitution
 *
 * @author vlse2610
 */
class InstitutionTypeAggregateInstitution extends InstitutionType implements InstitutionTypeAggregateInstitutionInterface {
    
    /**
     * @var Institution []
     */
    private $institutions = [];
    
    
      /**
     *
     * @param type $id id institution
     * @return InstitutionInterface|null
     */
    public function getInstitution($id): ?InstitutionInterface{
        
        return array_key_exists($id, $this->institutions) ? $this->institutions[$id] : null;

    }

    
    
    /**
     *
     * @return InstitutionInterface[]
     */
    public function getInstitutionsArray(): array {

        return $this->institutions;        
    }
    
    

    /**
     *
     * @return InstitutionInterface[]
     */
//    public function getInstitutionsArraySorted($sortType): array{
//        $institutions = $this->institutions;
//        switch ($sortType) {
//            case self::BY_PRIORITY :
//                \usort($institutions, array($this, "compareByPriority"));
//                break;
//
//            default:
//                break;
//        }
//        return $institutions;
//    }

    
    /**
     * 
     * @param  $institutions[]
     * @return InstitutionTypeAggregateInstitutionInterface
     */
    public function exchangeInstitutionArray(array $institutions=[]): InstitutionTypeAggregateInstitutionInterface{
         
        $this->institutions = $institutions;
        return $this;
     }
   
}
