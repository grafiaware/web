                <!--buttons-->
                <div>
                    <?=
                    $editable ?? false ? 
                        (isset($id) 
                        ?
                            "<button class='ui primary button' type='submit' formaction='$componentRouteSegment/$id' > Uložit změny </button>"
                        :
                            "<button class='ui primary button' type='submit' formaction='$componentRouteSegment' > Přidat </button>" 
                        )        
                    : "";
                    ?>
                    <?=
                    $remove ?? false ? "<button class='ui primary button' type='submit' formaction='$componentRouteSegment/$id/remove' > Odstranit </button>" : "";
                    ?>
                </div>   
