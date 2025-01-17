<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;
use Component\ViewModel\FamilyInterface;
use \Component\ViewModel\FamilyTrait;

use Events\Model\Repository\NetworkRepoInterface;
use Events\Model\Repository\CompanytoNetworkRepoInterface;
use Events\Model\Entity\CompanytoNetworkInterface;
use Events\Model\Entity\NetworkInterface;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;

use Exception;
use TypeError;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class NetworkViewModel extends ViewModelItemAbstract implements FamilyInterface{

    private $status;  
    
    private $networkRepo;
    
    /**
     * @var NetworkInterface
     */
    private $network;
    
    /**
     * @var CompanytoNetworkInterface
     */
    private $companyToNetwork;
    
    /**
     * @var CompanytoNetworkRepoInterface
     */
    private $companyToNetworkRepo;


    public function __construct(
            StatusViewModelInterface $status,
            CompanytoNetworkRepoInterface $companyToNetworkRepo,
            NetworkRepoInterface $networkRepo
            ) {
        $this->status = $status;
        $this->companyToNetworkRepo = $companyToNetworkRepo;
        $this->networkRepo = $networkRepo;
    }
    
    use RepresentativeTrait;
    use FamilyTrait;
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof NetworkInterface) {
            $this->network = $entity;
        } else {
            $cls = NetworkInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadNetwork();
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }

    private function loadNetwork() {
        if (!isset($this->network)) {
            if ($this->hasItemId()) {
                $this->network = $this->networkRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
        $this->companyToNetwork = $this->companyToNetworkRepo->get($this->getFamilyRouteSegment()->getParentId(), $this->network->getId());
    }
    
    public function getIterator() {
        $this->loadNetwork();
        $componentRouteSegment = 'events/v1/network';   //TODO: getRouteSegment() do abstractu - obdobně jako ve ViewModelFamilyAbstract

        $id = $this->network->getId();
        if (isset($id)) {
            $item = [
                //route
                'actionSave' => $componentRouteSegment."/$id",
                'actionRemove' => $componentRouteSegment."/$id/remove",
                'id' => $id,
                // data
                'fields' => [
                    'networkId' => $id,
                    'icon' => $this->network->getIcon(), 
                    'embedCodeTemplate' => $this->network->getEmbedCodeTemplate(), 
                    'link'=> isset($this->companyToNetwork) ? $this->companyToNetwork->getLink() : ''
                    ],
                ];
        } elseif ($this->isItemEditable()) {
            $item = [
                //route
                'actionAdd' => $componentRouteSegment,
                // text
                'addHeadline' => 'Přidej network',                
                // data
                'fields' => [],
                ];
        }        
        
        $this->appendData($item);
        return parent::getIterator();
    }
}
