<?php
use Events\Model\Entity\JobInterface;

use Pes\Text\Html;

?>
                   
            <div class="ui grid stackable">
                
                <?php
                if($isVisitor AND $isVisitorDataPost) {
                    include 'position/titleVisitorPost.php';
                }
                if($isRepresentativeOfCompany) {
                    include 'position/titleRepresentative.php';
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
                                    include 'position/contentVisitor.php';
                                } elseif ($isRepresentativeOfCompany) {
                                    include 'position/contentRepresentative.php';
                                } else {
                                    include 'position/contentOthers.php';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

