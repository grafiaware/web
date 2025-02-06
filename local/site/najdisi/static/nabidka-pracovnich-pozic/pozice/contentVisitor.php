<?php
use Pes\Text\Html;

                                    ?>
                                    <div class="sixteen wide column center aligned">
                                    <?php
                                    if($isVisitorDataPost) {
                                        ?>
                                        <div class="ui large basic button green profil-visible">
                                            <i class="mail bulk icon"></i>
                                            <span>Chci si prohlédnout údaje, které jsem odeslal/a  &nbsp;</span>
                                            <i class="mail bulk flipped icon"></i> 
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="ui large basic button blue profil-visible">
                                            <i class="star icon"></i>
                                            <span>Mám zájem o tuto pozici &nbsp;</span>
                                            <i class="star icon"></i>
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
                                                        'data-red-apiuri'=>$uriCascadeJobFamilyJobrequest
                                                    ]
                                                );
                                            ?>
                                        </div>
                                    </div>