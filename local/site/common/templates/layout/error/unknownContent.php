<?php
use Pes\Text\Html;
use Pes\Text\Text;

    $message = 'Unknown content.'.$message ? PHP_EOL.$message : '';

?>
                <div style ="display: none;">
                    <p>
                        <?= Text::nl2br($message ?? '') ?>
                    </p>
                </div>

