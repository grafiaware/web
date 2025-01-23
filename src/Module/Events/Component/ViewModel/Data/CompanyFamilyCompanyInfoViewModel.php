<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyInfoRepoInterface;
use Events\Model\Entity\CompanyInfoInterface;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;
use TypeError;
use Exception;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyCompanyInfoViewModel extends ViewModelFamilyItemAbstract {
    
    private $status;
    private $companyRepo;
    private $companyInfoRepo;
    /**
     * 
     * @var CompanyInfoInterface
     */
    private $companyInfo;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyInfoRepoInterface $companyInfoRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyInfoRepo = $companyInfoRepo;
    }

    use RepresentativeTrait;
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof CompanyInfoInterface) {
            $this->companyInfo = $entity;
        } else {
            $cls = CompanyInfoInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
    
    private function loadCompanyInfo() {
        if (!isset($this->companyInfo)) {
            if ($this->getFamilyRouteSegment()->hasChildId()) {
                $this->companyInfo = $this->companyInfoRepo->get($this->getFamilyRouteSegment()->getChildId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadCompanyInfo();
        $this->getFamilyRouteSegment()->setChildId($this->companyInfo->getCompanyId());  //pk = fk
        $componentRouteSegment = $this->getFamilyRouteSegment();
        if ($componentRouteSegment->hasChildId()) {        
            $array = [
                //route
                    'actionSave' => $componentRouteSegment->getSavePath(),
                    'actionRemove' => $componentRouteSegment->getRemovePath(),
                // data
                'fields' => [
                    'companyNetworksUri' => "events/v1/data/company/{$this->companyInfo->getCompanyId()}/network",
                    'introduction' => $this->companyInfo->getIntroduction(),
                    'videoLink' => $this->companyInfo->getVideoLink(),
                    'positives' => $this->companyInfo->getPositives(),
                    'social' => $this->companyInfo->getSocial()
                ],                
            ];
        } else {
            /** @var CompanyInterface $company */ 
            if ($this->companyRepo->get($this->getFamilyRouteSegment()->getParentId())) {  // validace id rodiče
                if ($this->isItemEditable()) {
                    $array = [
                        // text
                        'addHeadline' => 'Přidej informace zaměstnavatele',                      
                        //route
                        'actionAdd' => $componentRouteSegment->getAddPath(),
                        // data
                        'fields' => [
                            'companyNetworksUri' => false,
                            ]
                        ];        
                } else {
                    $array = [];
                }
            }
        }
        $this->appendData($array);
        return parent::getIterator();        
    }
}
