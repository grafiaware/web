<?php
use Pes\Text\Text;
use Pes\Text\Html;

// flashMessages je číselné pole - je třeba použít proměnnou $repeatItem

$toastAtrributes = [
        'id'=>($message ?? '') ? "domtoast". time() : "",
        'class'=>["ui small toast", $severity ?? 'info']  // "warning", "info", "success"
    ];


?>
<!--['red','orange','yellow','olive','green','teal','blue','violet','purple','pink','brown','grey','black'],-->
            <div <?= Html::attributes($toastAtrributes) ?>>
                <div class="content">
                    <p><i class="circle times icon"></i></p>
                    <p>
                        <?= Text::nl2br($message ?? '') ?>
                    </p>
                </div>
            </div>
