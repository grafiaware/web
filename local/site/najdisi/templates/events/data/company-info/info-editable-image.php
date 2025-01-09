<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>
            <div class="field">
                <label>O nás</label>
                <textarea class="edit-userinput" type="text" name="introduction" placeholder="" maxlength="1000"><?= $introduction ?? '' ?></textarea>
            </div>
            <div class="field">
                <label>Video</label>
                <textarea id="youtubeUrl" type="text" name="videolink" placeholder="https://www.youtube.com/watch?v=..." maxlength="150"><?= $videoLink ?? '' ?></textarea>
                <p id="message"></p>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Proč k nám</label>
                    <textarea class="edit-userinput" type="text" name="positives" placeholder="" maxlength="1000"><?= $positives ?? '' ?></textarea>
                </div>
                <div class="field">
                    <label>Jak žijeme</label>
                    <textarea class="edit-userinput" type="text" name="social" placeholder="" maxlength="1000"><?= $social ?? '' ?></textarea>
                </div>
            </div>
            <div class="field">
                <p>Obrázky</p>
                <p>Vložte obrázek o maximální velikosti 2 MB</p>
                <div class="ui labeled button" tabindex="0">
                    <label id="upload-image-btn" class="ui button">
                        <i class="image icon"></i> Vybrat obrázek
                        <input id="hidden-file-input" type="file" accept=".jpg,.jpeg,.png,.gif" style="display: none;">
                    </label>
                    <span id="selected-file-name" class="ui basic left pointing label" style="margin-left: 10px;">
                        Žádný soubor nebyl vybrán
                    </span>
                </div>
                <div id="change-file-btn" class="ui icon button" style="display: none;" data-tooltip="Změnit obrázek"><i class="images icon"></i></div>
                <div id="remove-file-btn" class="ui icon button" style="display: none;" data-tooltip="Odstranit obrázek"><i class="trash icon"></i></div>
            </div>

