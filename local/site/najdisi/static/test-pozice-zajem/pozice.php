<?php
use Events\Model\Entity\JobInterface;

use Pes\Text\Html;

?>
                   
            <div class="ui grid stackable">
                
                <?php
                if($isVisitor AND $isVisitorDataPost) {
                    include 'pozice/titleVisitorPost.php';
                }
                if($isRepresentativeOfCompany) {
                    include 'pozice/titleRepresentative.php';
                }
                ?>
                    
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
                                                        'data-red-apiuri'=>"events/v1/data/job/{$job->getId()}/jobrequest/$loginName"
                                                    ]
                                                );
                                            ?>
                                        </div>
                                    </div>
                               
                                    <?php
                                } elseif ($isRepresentativeOfCompany) {
                                    include 'pozice/contentRepresentative.php';
                                } elseif (!isset($loginAggregate)) {
                                    include 'pozice/contentNotLoggedIn.php';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

