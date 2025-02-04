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
                                'data-red-apiuri'=>$uriCascadeCompanyFamilyJob,
                            ]
                        );
                ?>
                <div class="row">
                    <div class="sixteen wide column">
                        <div  class="navazat-kontakt">
                            <div class="ui grid">
                                <?php
                                if ($isVisitor) {
                                    include 'pozice/contentVisitor.php';
                                } elseif ($isRepresentativeOfCompany) {
                                    include 'pozice/contentRepresentative.php';
                                } else {
                                    include 'pozice/contentOthers.php';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

