<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$vystavovatele = [
        [
            'radekVystavovatelu' => [
                [
                    'nazevVystavovatele' => 'Firma s. r. o.',
                    'stanekVystavovateleOdkaz' => '',
                    'vyhodyProZamestnance' => [
                        [
                            'dataContent' => 'Pět týdnů dovolené',
                            'ikona' => 'calendar alternate',
                        ],
                        [
                            'dataContent' => 'Flexibilní pracovní doba',
                            'ikona' => 'clock',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => 'images/logo_grafia.png',
                        'alt' => 'Firma XY',
                        'width' => '',
                        'height' => '100',
                    ]
                ],
                [
                    'nazevVystavovatele' => '',
                    'stanekVystavovateleOdkaz' => '',
                    'vyhodyProZamestnance' => [
                        [
                            'dataContent' => '',
                            'ikona' => '',
                        ],
                        [
                            'dataContent' => '',
                            'ikona' => '',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => '',
                        'alt' => '',
                        'width' => '',
                        'height' => '',
                    ]
                ],
                
                [
                    'nazevVystavovatele' => '',
                    'stanekVystavovateleOdkaz' => '',
                    'vyhodyProZamestnance' => [
                        [
                            'dataContent' => '',
                            'ikona' => '',
                        ],
                        [
                            'dataContent' => '',
                            'ikona' => '',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => '',
                        'alt' => '',
                        'width' => '',
                        'height' => '',
                    ]
                ],
                
            ]
        ]
    ]
?>
    
    <div class="online-stanky">
        <div class="ui stackable centered grid">
            <?= $this->repeat(__DIR__.'/hala/rozvrzeni-vystavovatelu.php', $vystavovatele) ?>
        </div>
    </div>

