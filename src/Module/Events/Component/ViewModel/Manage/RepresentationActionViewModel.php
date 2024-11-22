<?php
namespace Events\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Component\ViewModel\Manage\RepresentationActionViewModelInterface;
use Events\Model\Entity\RepresentativeInterface;

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

    public function getIterator() {
        $representativeActions = $this->status->getRepresentativeActions();
        if (isset($representativeActions)) {
            $repsesentative =  $representativeActions->getRepresentative();
            $editData = $representativeActions->getDataEditable();
        }
        $placeholderKey = '';
        $array = [
            'loginName' => $this->status->getUserLoginName(),
            'idCompanyArray' => $this->createIdCompanyArray($this->status->getUserLoginName(), $placeholderKey, 'Vyberte zastupovanou firmu'),
            'selectedCompanyId' => isset($repsesentative) ? $repsesentative->getCompanyId() : null,
            'placeholderValue' => $placeholderKey,
            'editData' => $editData
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
