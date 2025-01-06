<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\CompanyInterface;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;

use Exception;
use TypeError;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyViewModel extends ViewModelItemAbstract {

    private $status;  
    
    private $companyRepo;
    
    /**
     * 
     * @var CompanyInterface
     */
    private $company;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
    }
    
    use RepresentativeTrait;
        
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof CompanyInterface) {
            $this->company = $entity;
        } else {
            $cls = CompanyInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }

    public function isItemEditable(): bool {
        $this->loadCompany();
        return $this->isAdministratorEditor();
    }
    
    private function isCompanyRepresentative($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }

    private function loadCompany() {
        if (!isset($this->company)) {
            if ($this->hasItemId()) {
                $this->company = $this->companyRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadCompany();
        $componentRouteSegment = 'events/v1/company';   //TODO: getRouteSegment() do abstractu - obdobně jako ve ViewModelFamilyAbstract

        $id = $this->company->getId();
        if (isset($id)) {
            $item = [
                //route
                'actionSave' => $componentRouteSegment."/$id",
                'actionRemove' => $componentRouteSegment."/$id/remove",
                // data
                'fields' => ['name' => $this->company->getName()],
                ];
        } elseif ($this->isItemEditable()) {
            $item = [
                //route
                'actionAdd' => $componentRouteSegment,
                // text
                'addHeadline' => 'Přidej firmu',                
                // data
                'fields' => [],
                ];
        } else {
            $item = [];
        }
        
        $this->appendData($item);
        return parent::getIterator();
    }
}
