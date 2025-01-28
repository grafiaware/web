<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\VisitorJobRequestRepoInterface;
use Events\Model\Repository\VisitorProfileRepoInterface;
use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Entity\VisitorProfileInterface;
use Model\Entity\EntityInterface;


use Access\Enum\RoleEnum;

use UnexpectedValueException;
use LogicException;
use TypeError;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class JobFamilyJobRequestViewModel extends ViewModelFamilyItemAbstract {
    
    private $status;
    private $jobRepo;
    private $jobRequestRepo;
    private $visitorProfileRepo;


    /**
     * @var VisitorJobRequestInterface
     */
    private $jobRequest;
    private $visitorProfile;

    public function __construct(
            StatusViewModelInterface $status,
            JobRepoInterface $jobRepo,
            VisitorJobRequestRepoInterface $jobRequestRepo,
            VisitorProfileRepoInterface $visitorProfileRepo
            ) {
        $this->status = $status;
        $this->jobRepo = $jobRepo;
        $this->jobRequestRepo = $jobRequestRepo;
        $this->visitorProfileRepo = $visitorProfileRepo;
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR);
    }
    
    private function isRepresentative() {
        return ($this->status->getUserRole() == RoleEnum::REPRESENTATIVE);
    }
    
    private function isVisitor() {
        return ($this->status->getUserRole() == RoleEnum::VISITOR);
    }
    
    private function isJobRequestCreator($jobRequestLoginName) {
        return ($this->status->getUserRole() == RoleEnum::VISITOR AND $this->status->getUserLoginName() == $jobRequestLoginName );
    }

    /**
     * Přihlášen user s rolí visitor, přihlašovací jméno je shodné s loginName požadovaného jobRequestu jobRequest dosud nebyl uložen.
     * Tedy pro nepřihlášeného jako visitor
     * @return bool
     */
    public function isItemEditable(): bool {
        $this->loadJobRequest(); 
        $this->loadVisitorProfile();
        return isset($this->visitorProfile) && !isset($this->jobRequest) && $this->isJobRequestCreator($this->getFamilyRouteSegment()->getChildId());
    }
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof VisitorJobRequestInterface) {
            $this->jobRequest = $entity;
            $this->getFamilyRouteSegment()->setChildId($this->jobRequest->getLoginLoginName());            
        } else {
            $cls = VisitorJobRequestInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    //TODO: SV test a exception do rodičovské metody
    private function loadJobRequest() {
        if (!isset($this->jobRequest)) {
            if ($this->getFamilyRouteSegment()->hasChildId()) {
                if (null===$this->jobRepo->get($this->getFamilyRouteSegment()->getParentId())) {
                    throw new UnexpectedValueException("The child entity has no requested parent.");
                }
                $this->jobRequest = $this->jobRequestRepo->get($this->getFamilyRouteSegment()->getChildId(), $this->getFamilyRouteSegment()->getParentId());     // get(loginname, jobid)
// job request je speciální - když není request, do formuláře se předvyplní data z profilu => nevadí, že není request a přitom někdo volá komponent s childId
//                if (!isset($this->jobRequest)) {
//                        throw new UnexpectedValueException("There is no requested child entity.");// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
//                }                
            } else {
                throw new UnexpectedValueException("There is no required child entity ID in the route..");// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    private function loadVisitorProfile() {
        $loginLoginName = $this->status->getUserLoginName();
        if (isset($loginLoginName)) {
            $this->visitorProfile = $this->visitorProfileRepo->get($loginLoginName);
        }
    }


    public function getIterator() {
        $this->loadJobRequest();
        $visitorEmail = $this->status->getUserEmail();

        if ($this->getFamilyRouteSegment()->hasChildId()) {   // požaduji job request
            if($this->getFamilyRouteSegment()->getChildId()) {    // job request již byl uložen
                    if($this->isRepresentative()) {  // jen podle role, nezjišťuji jestli reprezentuje company - nemám company repo a v kontoleru se automaticky odesílá právě přuhlášenému reprezentantovi
                        $buttonAction = $this->getFamilyRouteSegment()->getSavePath()."/send";  // routa s id job requestu doplněná o /send
                        $buttonTitle = 'Odeslat uložený zájem o pozici na svůj e-mail';
                    }
                    $item = [
                    //route
                    'actionSpecific' => $buttonAction ?? null,
                    'titleSpecific' => $buttonTitle ?? null,
                    // data
                    'fields' => [
                            'prefix' =>   $this->jobRequest->getPrefix(),
                            'name' =>     $this->jobRequest->getName(),
                            'surname' =>  $this->jobRequest->getSurname(),
                            'postfix' =>  $this->jobRequest->getPostfix(),
                            'phone' =>    $this->jobRequest->getPhone(),                    
                            'email' => $this->jobRequest->getEmail(),
    //                        'visitorEmail' => $visitorEmail,
                            'cvEducationText' =>  $this->jobRequest->getCvEducationText(),
                            'cvSkillsText' =>     $this->jobRequest->getCvSkillsText(),
                        ],
                    ];
            } elseif ($this->isItemEditable()) {   // job request ještě nebyl uložen a uživatel může editovat
                $this->loadVisitorProfile();    // job request se předvyplní z profilu 
                if (isset($this->visitorProfile)) {   // profil se čte pro přihlášeného visitora
                    $item = [
                        //route
                        'actionSpecific' => $this->getFamilyRouteSegment()->getAddPath(),  // actionSpecific je ve výchozím stavu enabled - uživatel nemusí v datech nic měnit, jen uloží
                        'titleSpecific' => 'Odeslat zájem o pozici',
                        // text
                        'addHeadline' => 'Nový zájem o pozici',                
                        'infoText' => 'Zájem o pozici je předvyplněn údaji z vašeho profilu návštěvníka. Před odesláním můžete údaje upravit.',
                        // data
                        'fields' => [
                            'prefix' => $this->visitorProfile->getPrefix(),
                            'name' =>     $this->visitorProfile->getName(),
                            'surname' =>  $this->visitorProfile->getSurname(),
                            'postfix' =>  $this->visitorProfile->getPostfix(),
                            'phone' =>    $this->visitorProfile->getPhone(),                    
                            'email' => $visitorEmail,   // email z registrace
                            'cvEducationText' =>  $this->visitorProfile->getCvEducationText(),
                            'cvSkillsText' =>     $this->visitorProfile->getCvSkillsText(),                        
                        ],
                        ];
                } else {
                    $item = [
                        // text
                            'infoText' => 'Odeslat zájem o pozici může jen přihlášený návštěvník s vytvořeným profilem. Vyplňte svůj profil návštěvníka.',
                        ];                     
//                    throw new LogicException("There is no profile for editable job request.");   // sem by skript neměl dostat, pokud není profile, nemá být item editable
                }
            }
        }      
        
        $this->appendData($item ?? []);
        return parent::getIterator();          
    }
}
