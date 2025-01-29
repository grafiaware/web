<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>
        <div class="ui grid">
            <div class="sixteen wide column">
                <p>
                    <i class="icons">
                        <i class="building icon"></i>
                        <i class="corner user icon"></i>
                    </i>
                    <?= $name ?? '' ?></p>
            </div>    
        </div>