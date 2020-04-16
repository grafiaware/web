
<!--<div id="bodywrap">    

<div id="headwrap">    /** headwrap**AAA/   
    < ?php include "body/hlavicka.php"; ?>
</div>   headwrap /    

 ============================================================     
<div id="colswrap">   /** colswrap**/ 
    < ?php include "body/telo.php"; ?>

<div class="reseter">&nbsp;</div>
</div>     colswrap /

<div class="reseter">&nbsp;</div>
 ============================================================= 

<div id="footwrap">      /**footwrap**/   
    < ?php include "body/paticka.php"; ?>
</div> footwrap 

</div> bodywrap /-->
 <div class="ui container">
    <div class="column">
        <header>
            <?php include "body/hlavicka.php"; ?>
        </header>
        <main class="page-content">
            <?php include "body/telo.php"; ?>
        </main>
        <footer>
            <?php include "body/paticka.php"; ?>
        </footer>
    </div>
</div>