<?php
use Pes\Text\Html;

                                    if($visitorJobRequestCount) {
                                        ?>
                                        <div class="sixteen wide column center aligned">

                                            <div class="ui large button green profil-visible">
                                                <i class="eye icon"></i>
                                                <span>Chci si prohlédnout údaje, které zájemci odeslali  &nbsp;</span>
                                                <i class="eye icon"></i>
                                            </div>
                                        </div>
                                        <div class="sixteen wide column">
                                            <div class="profil hidden">
                                            <?php
                                                echo Html::tag('div', 
                                                    [
                                                        'class'=>'cascade',
                                                        'data-red-apiuri'=>$uriCascadeJobFamilyJobrequestList     // nutné volat seznam job requestů (aktuální login name je representant ne visitor)
                                                    ]
                                                );
                                            ?>
                                            </div>
                                        </div>
                                        <?php
                                    }