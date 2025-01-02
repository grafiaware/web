<?php
namespace Events\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Component\ViewModel\Manage\RepresentationActionViewModelInterface;
use Events\Model\Entity\RepresentativeInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class RepresentationActionViewModel extends ViewModelAbstract implements RepresentationActionViewModelInterface {

    private $status;
    
    private $representativeRepo;
    
    private $companyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            RepresentativeRepoInterface $representativeRepo,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->representativeRepo = $representativeRepo;
        $this->companyRepo = $companyRepo;

    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }
    
    public function getIterator() {
        $representativeActions = $this->status->getRepresentativeActions();
        if (isset($representativeActions)) {
            $represesentative =  $representativeActions->getRepresentative();
            $companyId = isset($represesentative) ? $represesentative->getCompanyId() : null;
            $editData = $representativeActions->getDataEditable();
            $companyName = isset($companyId) ? $this->companyRepo->get($represesentative->getCompanyId())->getName() : '';
        }
        $placeholderKey = '';
        
        //TODO: nastavit automatocky hodnotu a selected pro select, pokud user není multirepresentative                     if (!$this->isMultiRepresentative()) {

        
        $array = [
            'loginName' => $this->status->getUserLoginName(),
            'isAdministrator' => $this->isAdministrator(),
            'idCompanyArray' => $this->createIdCompanyArray($this->status->getUserLoginName(), $placeholderKey, 'Vyberte zastupovanou firmu'),
            'selectedCompanyId' => $companyId,
            'placeholderValue' => $placeholderKey,
            'editData' => $editData,
            'companyName' => $companyName,
        ];
        return new ArrayIterator($array);        
    }
    
    public function isMultiRepresentative(): bool {
        return (count($this->getRepresentativesByLoginName($this->status->getUserLoginName())))>1 ? true : false;
    }
    
    /**
     * Generuje pole company id=>name, první položku vytvoří práznou (''=>'') pro select element required
     * @param type $loginName
     * @return type
     */
    private function createIdCompanyArray($loginName, $placeholderKey, $placeholderText) {
        $a = [];
        $a[$placeholderKey] = $placeholderText;  // klíč = hodnota první položky $idCompanyArray pro zajištění funkčnosti required select
        $representatives = $this->getRepresentativesByLoginName($loginName);
        foreach ($representatives as $representative) {
            /** @var RepresentativeInterface $representative */
            $a[$representative->getCompanyId()] = $this->companyRepo->get($representative->getCompanyId())->getName();
        }
        return $a;
    }
    
    private function getRepresentativesByLoginName($loginName): array {
        return $this->representativeRepo->findByLoginName($loginName);
    }
}
