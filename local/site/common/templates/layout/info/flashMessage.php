<?php
use Pes\Text\Text;
use Pes\Text\Html;

// class flashtoast je selektor pro volání semantic funkce toast (body.js)
$toastAtrributes = [
        'id'=>($message ?? '') ? "flash". time() : "",
        'class'=>["ui small toast", "flashtoast", $severity ?? 'info']  // severity: "warning", "info", "success"
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
