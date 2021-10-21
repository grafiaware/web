
            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
                <?php
                if($readonly === '') {
                ?>
                <p class="text maly primarni-barva okraje-vertical">
                    <i class="info red icon"></i>
                    Údaje jsou předvyplněné z profilu návštěvníka, ale můžete je před odesláním upravit
                </p>
                <?php
                }
                ?>
            </div>
            <div class="active content">
                <form class="ui huge form" action="" method="POST" enctype="multipart/form-data">
                    <input type='hidden' name="short-name" value="<?= $shortName ?>">
                    <input type='hidden' name="position-name" value="<?= $positionName ?>">
                    <input type='hidden' name="visitor-login-name" value="<?= $visitorLoginName ?? '' ?>">
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před jménem</label>
                            <input <?= $readonly ?> type="text" name="prefix" placeholder="" maxlength="45" value="<?= $prefix ?>">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input <?= $readonly ?> type="text" name="name" placeholder="Jméno" maxlength="90" value="<?= $firstName ?>">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input <?= $readonly ?> type="text" name="surname" placeholder="Příjmení" maxlength="90" value="<?= $surname ?>">
                        </div>
                        <div class="three wide field">
                            <label>Titul za jménem</label>
                            <input <?= $readonly ?> type="text" name="postfix" placeholder="" maxlength="45" value="<?= $postfix ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input <?= $readonlyEmail ?> type="email" name="email" placeholder="mail@example.cz" maxlength="90" value="<?= $email ?>">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input <?= $readonly ?> type="tel" name="phone" placeholder="+420 777 8888 555" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45" value="<?= $phone ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea <?= $disabled ?> name="cv-education-text" class="working-data"><?= $cvEducationText ?></textarea>
                        </div>
                        <div class="field">
                            <label>Pracovní zkušenosti, dovednosti</label>
                            <textarea <?= $disabled ?> name="cv-skills-text" class="working-data"><?= $cvSkillsText ?></textarea>
                        </div>
                    </div>

                    <label><b>Nahrané soubory</b></label>
                    <div class="field margin">
                        <p>
                            <i class="file alternate icon"></i><b>Životopis: <?= $cvDocumentFilename ?></b>
                            <?php
                            if(isset($cvDocumentFilename)){
                            ?>
                                <!--<span class="text maly okraje-horizontal"><a><i class="eye outline icon"></i>Zobrazit soubor</a>-->
                                    <?php
                                    if($readonly === '') {
                                    ?>
                                    <!--<span class="text maly okraje-horizontal"><a><i class="trash icon"></i>Smazat</a></span>-->
                                    <?php
                                    }
                                    ?>
                             <?php
                            }
                            ?>
                        </p>
                        <p><i class="file alternate icon"></i><b>Motivační dopis: <?= $letterDocumentFilename ?></b>
                            <?php
                            if(isset($letterDocumentFilename)){
                            ?>
                                <!--<span class="text maly okraje-horizontal"><a><i class="eye outline icon"></i>Zobrazit soubor</a>-->
                                <?php
                                if($readonly === '') {
                                ?>
                                <!--<span class="text maly okraje-horizontal"><a><i class="trash icon"></i>Smazat</a></span>-->
                                <?php
                                }
                                ?>
                             <?php
                            }
                            ?>
                        </p>
                    </div>
                    <?php
                    if($readonly === '') {
                    ?>
                    <div class="field">
                        <label><?= $cvDocumentFilename ? 'Příloha - můžete nahrát jiný životopis' : 'Příloha - životopis'; ?></label>
                        <input type="file" name="<?= $uploadedCvFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="1">
                    </div>
                    <div class="field margin">
                        <label><?= $letterDocumentFilename ? 'Příloha - můžete nahrát jiný motivační dopis' : 'Příloha - motivační dopis'; ?></label>
                        <input type="file" name="<?= $uploadedLetterFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="1">
                    </div>
                    <div class="two fields">
                        <div class="field margin"></div>
                        <!--odesílá k uložení do databáze-->
                        <div class="field">
                            <button class="ui primary button" type="submit" formaction="event/v1/visitorpost">Odeslat údaje zaměstnavateli</button>
                        </div>
                    </div>
                    <?php
                    } elseif($isPresenter) {
                        ?>
                        <div class="two fields">
                            <div class="field margin"></div>

                            <div class="field">
                                <button class="ui primary button" type="submit" formaction="event/v1/sendvisitorpost">Odeslat mailem na <?= $presenterEmail ?></button>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </form>
            </div>
