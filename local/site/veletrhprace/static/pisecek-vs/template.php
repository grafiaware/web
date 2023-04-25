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

?>   

    <div>
    <div class="ui styled fluid accordion">
    //----------------- pisecek -------------------
        <p> <?= Html::select("jmeno-mesta", "To je label Město:",
            [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"],
            ["jmeno-mesta"=>4], []) ?></p>

        <p> <?= Html::select("selectCompany", "Company name:",
            [10=>"Firma10", 25=>"Firma1-město25", 35=>"Firma35", 70=>"jiná"],
            ["selectCompany"=>35], []) ?></p>

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

    </div>
    </div>



  
