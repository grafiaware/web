
                                    <div class="sixteen wide column center aligned">
                                        <div class="ui large basic button blue profil-visible">
                                            <i class="star icon"></i>
                                            <span>Mám zájem o tuto pozici</span>
                                            <i class="star icon"></i>
                                        </div>
                                        <div class="profil hidden">
                                            <div class="active title">
                                                <i class="exclamation icon"></i>Přihlaste se jako návštěvník. <i class="user icon"></i> Přihlášení návštěvníci mohou posílat žádosti přímo zaměstnavateli. Pokud ještě nejste zaregistrování, nejprve se registrujte. <i class="address card icon"></i>
                                            </div>
                                            <?php
                                            if (isset($block)) {
                                                ?>
                                                <a href="<?= "web/v1/page/block/".$block->getName()."#chci-navazat-kontakt" ?>">
                                                    <div class="ui large basic button grey profil-visible">
                                                        Chci jít na stánek pro kontaktní údaje
                                                    </div>
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

