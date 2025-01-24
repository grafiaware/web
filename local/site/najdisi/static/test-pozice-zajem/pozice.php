<?php
use Events\Model\Entity\JobInterface;

use Pes\Text\Html;

?>
    <div class="ui grid">               
        <div class="sixteen wide column">
            <p class="podnadpis">
                <?= $nazevPozice ?? 'DUMMY $nazevPozice' ?>, <?= $mistoVykonu ?? 'DUMMY $mistoVykonu' ?>
                <?= "DUMMY $this->repeat(__DIR__.'/pozice/tag.php', $jobTags, 'tag')" ?>
                <?php
                if($isVisitor AND $isVisitorDataPost) {
                    include 'titleVisitorPost.php';
                }
                if($isRepresentativeOfCompany) {
                    include 'titleRepresentative.php';
                }
                ?>
            </p>
        </div>
        <div class="sixteen wide column">
            <div class="ui grid stackable">
                <?php
                    /** @var JobInterface $job */
                    echo Html::tag('div', 
                            [
                                'class'=>'cascade',
                                'data-red-apiuri'=>"events/v1/data/company/$companyId/job/{$job->getId()}",
                            ]
                        );
                ?>
                <div class="row">
                    <div class="sixteen wide column">
                        <div  class="navazat-kontakt">
                            <div class="ui grid">
                                <?php
//----------                               
                                if ($isVisitor) {
                                    ?>
                                    <div class="sixteen wide column center aligned">
                                    <?php
                                    if($isVisitorDataPost) {
                                        ?>
                                        <div class="ui large button green profil-visible">
                                            <i class="play icon"></i>
                                            <span>Chci si prohlédnout údaje, které jsem odeslal/a  &nbsp;</span>
                                            <i class="play flipped icon"></i>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="ui large button blue profil-visible">
                                            <i class="play icon"></i>
                                            <span>Mám zájem o tuto pozici &nbsp;</span>
                                            <i class="play flipped icon"></i>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                    <div class="sixteen wide column">
                                        <div class="profil hidden">
                                            <?php
                                                echo Html::tag('div', 
                                                    [
                                                        'class'=>'cascade',
                                                        'data-red-apiuri'=>"events/v1/data/visitorprofile/$visitorLoginName"
                                                    ]
                                                );
                                            ?>
                                        </div>
                                    </div>
                               
                                    <?php
    //----------                                     
                                } elseif ($isRepresentativeOfCompany) {
                                    if($isVisitorDataPost) {
                                        ?>
                                        <div class="sixteen wide column center aligned">

                                            <div class="ui large button green profil-visible">
                                                <i class="play icon"></i>
                                                <span>Chci si prohlédnout údaje, které zájemci odeslali  &nbsp;</span>
                                                <i class="play flipped icon"></i>
                                            </div>
                                        </div>
                                        <div class="sixteen wide column">
                                            <div class="profil hidden">
                                                <p>TADY MÁ BÝT VISITOR JOB REQUEST - ZATÍM MÍSTO TOHO UKAZUJI VISITOR PROFILE</P>
                                            <?php
                                                echo Html::tag('div', 
                                                    [
                                                        'class'=>'cascade',
                                                        'data-red-apiuri'=>"events/v1/data/visitorprofile/$visitorLoginName"
                                                    ]
                                                );
                                            ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
//----------                                   
                                } elseif (!isset($loginAggregate)) {
                                    ?>
                                    <div class="sixteen wide column center aligned">
                                        <div class="ui large button blue profil-visible">
                                            <i class="play icon"></i>
                                            <span>Mám zájem o tuto pozici</span>
                                            <i class="play flipped icon"></i>
                                        </div>
                                        <div class="profil hidden">
                                            <div class="active title">
                                                <i class="exclamation icon"></i>Přihlaste se jako návštěvník. <i class="user icon"></i> Přihlášení návštěvníci mohou posílat přímo zaměstnavateli. Pokud ještě nejste zaregistrování, nejprve se registrujte. <i class="address card icon"></i>
                                            </div>
                                            <?php
                                            if (isset($block)) {
                                                ?>
                                                <a href="<?= "web/v1/page/block/".$block->getName()."#chci-navazat-kontakt" ?>">
                                                    <div class="ui large button grey profil-visible">
                                                        Chci jít na stánek pro kontaktní údaje
                                                    </div>
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

