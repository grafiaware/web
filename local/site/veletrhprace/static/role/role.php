<div>
    <form class="ui huge form" action="" method="POST" >               
        <div class="field">
            Role:
            <?php  if (isset ($role) ) {   ?>
                    <input  type="text" name="role" placeholder="" maxlength="50" 
                            pattern="[a-z]+"   title="Jen malá písmena - bez háčků a čárek" value="<?= $role ?>">
                    <button class='ui secondery button' type='submit' formaction='auth/v1/role/<?= $role ?>'>Ulozit</button>
                    <button class='ui primary button' type='submit' formaction='auth/v1/role/<?= $role ?>/remove'>Odstranit</button>
            <?php  } else {   ?>    
                    <input  type="text" name="role" placeholder="Zadejte jednoslovný název role složený z alfanumerických znaků" maxlength="50" 
                            pattern="[a-z]+"   title="Jen malá písmena - bez háčků a čárek" 
                            required>
                    <button class='ui secondery button' type='submit' formaction='auth/v1/role' >Uložit</button>             
            <?php  } ?>            
        </div>
    </form >
  </div>

   
