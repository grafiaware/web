<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>
            <div class="field">
                <label>O nás</label>
                <input class="edit-userinput" type="text" name="introduction" placeholder="" maxlength="1000" 
                                        value="<?= Text::esc_attr($introduction ?? '') ?>">
            </div>
            <div class="field">
                <label>Video</label>
                <input id="youtubeUrl" type="text" name="videolink" placeholder="https://www.youtube.com/watch?v=..." maxlength="150" 
                                        value="<?= Text::esc_attr($videoLink ?? '') ?>">
                <p id="message"></p>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Proč k nám</label>
                    <input class="edit-userinput" type="text" name="positives" placeholder="" maxlength="1000" 
                           value="<?= Text::esc_attr($positives ?? '') ?>">
                </div>
                <div class="field">
                    <label>Jak žijeme</label>
                    <input class="edit-userinput" type="text" name="social" placeholder="" maxlength="1000" 
                                            value="<?= Text::esc_attr($social ?? '') ?>">
                </div>
            </div>             

