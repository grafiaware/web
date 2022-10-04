<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;



use Red\Model\Repository\LanguageRepo;
use Red\Model\Entity\Language;
//        <?= $this->insert(ConfigurationCache::componentController()['templates']."zprava"."/template.php", $tiskovaZprava) 

?>



<?php
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();
if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $role = $loginAggregate->getCredentials()->getRole() ?? '';
}

//----------------------------------------------------------------
/** @var LanguageRepo $languageRepo */   
/** @var Language[] $languages */

$languageRepo = $container->get(LanguageRepo::class);
$languages = $languageRepo->findAll();

foreach ($languages as $language) {
    $lanCodeZde[] = $language->getLangCode();  
    
    $languagesJeden['code'] = $language->getLangCode();
    $languagesJeden['locale'] = $language->getLocale();
    
    $languagesArr[] = $languagesJeden;

}
foreach ($languages as $language) {
    $poleEntit[]=['mujObjekt'=>$language];
    
}  
//echo $this->repeat(__DIR__."/content/sablonaSPromennymiZAsocPole.php", $poleEntit);
//
//echo $this->insert(__DIR__."/content/sablonaSKontextem.php", $languages);

echo $this->insert(__DIR__."/content/logout.php");