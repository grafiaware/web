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
//foreach ($languages as $language) {
//    $pole[]=['mujObjekt'=>$language];
//    
//}  

$this->insert("sablonaSKontextem", $languages);

?>
        
<div>sdfsdf
    <headline>headline
        <p><?= 'DDD ';            ?>
        <?php  echo 'DDD ';    ?> </p>
        
       <p> loginName:  <?= $loginName ?>  </p>
       <p> role:  <?= $role ?> </p>
    </headline>
    
    
    
    
    <content> content
         <?php ;
          echo "ZDE: ";
          echo $lanCodeZde[0] . ' ';
          echo $lanCodeZde[1] . ' ';
          echo $lanCodeZde[2] . ' ';      
        ?>
        <?= $this->repeat(__DIR__.'/content/one-language.php', $languagesArr );  ?>
        
    </content>
</div>


