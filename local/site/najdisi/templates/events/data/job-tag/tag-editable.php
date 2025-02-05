    <div class="two fields">   
        <div class="field">
            <input type="text" name="tag" placeholder="text tagu" maxlength="28" required value="<?= $tag ?? '' ?>">
        </div>
        <div class="field">                
            <input type="text" name="color" placeholder="zvolte barvu" maxlength="12" required value="<?= $color ?? 'grey' ?>">              
            <div class="ui pointing basic label">red orange yellow olive green teal blue violet purple pink brown grey</div>
        </div>
    </div>
