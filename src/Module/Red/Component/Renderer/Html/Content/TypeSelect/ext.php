<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>

<script>
(function () {
    const radios = document.querySelectorAll('input[name="choice"]');
    const blocks = document.querySelectorAll('div[data-for]');

    // inicializace – vše skrýt
    blocks.forEach(block => {
        block.hidden = true;
        block.querySelector('input').required = false;
    });

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            blocks.forEach(block => {
                const input = block.querySelector('input');

                block.hidden = true;
                input.required = false;
            });

            const activeBlock = document.querySelector(
                `div[data-for="${radio.value}"]`
            );

            if (activeBlock) {
                const input = activeBlock.querySelector('input');

                activeBlock.hidden = false;
                input.required = true;
                input.focus();
            }
        });
    });
})();
</script>

<form id="myForm">
    <fieldset>
        <legend>Vyber možnost</legend>

        <label>
            <input type="radio" name="choice" value="a">
            Možnost A
        </label>

        <div data-for="a">
            <label>
                Upřesnění A:
                <input type="text" name="detail_a">
            </label>
        </div>

        <label>
            <input type="radio" name="choice" value="b">
            Možnost B
        </label>

        <div data-for="b">
            <label>
                Upřesnění B:
                <input type="text" name="detail_b">
            </label>
        </div>

        <label>
            <input type="radio" name="choice" value="c">
            Možnost C
        </label>

        <div data-for="c">
            <label>
                Upřesnění C:
                <input type="text" name="detail_c">
            </label>
        </div>
    </fieldset>

    <button type="submit">Odeslat</button>
</form>


