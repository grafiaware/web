<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;

use Pes\Text\Text;
use Pes\Text\Html;
use Moje\MojeHTML;

use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Model\Entity\JobToTag;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;

use Events\Model\Repository\LoginRepo;
use Events\Model\Entity\LoginInterface;

    $optionValues = [
    "toto je hodnota NULL" => "" ,
     1 =>  " retez1" ,
     2 =>  " retez1" 
    ]    ;    
    $jePole1 = is_array($optionValues);
 $aa = array_key_first($optionValues);
    $neni0_1 = (array_key_first($optionValues)!==0) ;
    //$useKeysAsValues1 = (!is_array($optionValues)) || (array_key_first($optionValues)!=0); 
    $useKeysAsValues1 = (!is_array($optionValues)) || (array_key_first($optionValues)!==0); 

//--------------------------------------
    
    $optionValues = [    
     1 =>  " retez1" ,
     2 =>  " retez1" ,
     "toto je hodnota NULL" => "" ,
    ]    ;
    $jePole1 = is_array($optionValues);
    $neni0_2 = (array_key_first($optionValues)!==0) ;
    //$useKeysAsValues2 = (!is_array($optionValues)) || (array_key_first($optionValues)!=0); 
    $useKeysAsValues1 = (!is_array($optionValues)) || (array_key_first($optionValues)!==0); 

?>   

    <div>
    <div class="ui styled fluid accordion">
    //----------------- pisecek -------------------
    <?php
         $context = 
        ["jmeno-mesta"=>4,
        "selectCompany"=>35,
        //"jmeno-mesta2"=>1,
        //"selectCompany2"=>"firma0"
        "jmeno-mesta2"=>"",
        "selectCompany2"=>""    
        ]; ?>
    
        <p> <?= Html::select("jmeno-mesta", "To je label Město:",
            [ 1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"],
            $context, []) ?></p>
                
        <p> <?= Html::select("selectCompany", "Company name:",
            [10=>"Firma10", 25=>"Firma1-město25", 35=>"Firma35", 70=>"jiná"],
            $context, []) ?></p>
               
        //--------------------------//
         <p> <?= Html::select("jmeno-mesta2", "To je label Město:",
            [ ''=>''  , 1=>"00", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"],
            $context, []) ?></p>
        
         <p> <?= Html::select("selectCompany2", "Company name:",
            [ ''=>'nic'  , "firma0" => "00", "Firma1" => "Firma2", "Firma3" =>"Firma1-město25", "Firma4"=>"Firma35", "Firma5" =>"jiná"],
            $context, []) ?></p>
        
        //--------------------------//

        <p> <?= Html::select("selectLogin", "Login name:",
            ["Uzivatel 0", "Uzivatel 1", "Uzivatel 2"],  //index od nuly
            ["selectLogin"=>"Uzivatel 2"], []) ?></p>

        <p> <?= Html::checkbox( [ 'žádné město' => [1=>"" ],
                                  'Plzeň-město' => [2=>"Plzeň-město"],
                                  'Plzeň-jih' => [3=>"Plzeň-jih"],
                                  'Klatovy' => [4=>"Klatovy"] ],
                                [2=>"Plzeň-město"] ) ?></p>


        <?= Html::checkbox(["Label1"=>['technická'=>'technická'],
                            "Label2"=>['manažerská/vedoucí'=>'manažerská/vedoucí']] ,
                           ['manažerská/vedoucí'=>'manažerská/vedoucí']  ) ?>
        <br/>
        <?= MojeHTML::checkbox( ["Label1"=>['technická'=>'technická'],
                                 "Label2"=>['manažerská/vedoucí'=>'manažerská/vedoucí']] ,
                                ['technická'=>'technická'] ) ?>
        
        <br/> //------------------------------------------------- <br/>
         
        <?= Html::radio(   'show',                  
                            ['labelAno' => true, 'labelNe' => false ]
                            /*$radiosetLbelsValues*/,                
                            ['show' => false ] /*$context*/
                                             ) ?>
        <br/>//------------------------------------------------- <br/>
        <br/>//------------------------------------------------- <br/>
        <?= Html::checkbox(["Ano"=>['ano'=>true],
                            "Ne"=>['ne'=>false]] ,
                           ['ano'=> true]  ) ?>
                
         <br/>//------------------------------------------------- <br/>
        <?= Html::checkbox(["Zobraz"=>['show'=>true] ]
                             ) ?>

        

    </div>
    </div>



  
