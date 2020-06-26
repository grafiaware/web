    <meta http-equiv="X-UA-Compatible" content="IE=Edge" >    <!--  vynucuje nejnovější dostupný mode - edge -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Grafia, s.r.o.">
    <meta name="robots" content="ALL,FOLLOW">
    <meta name="page-topic" content="Grafia, s.r.o.">
    <meta name="verify-v1" content="mUDu9R2bF8n/JiAQ++iWMhop6G0VyMgkEPAH/8B8gAY="> <!-- Google sitemaps -->

    <title>BOMBA!!!!</title>

    <link rel="icon" href="<?= Middleware\Web\AppContext::getAppPublicDirectory().'grafia/img/Grafia.ico'?>" type="image/x-icon">


    <link rel="stylesheet" type="text/css" href="<?= Middleware\Web\AppContext::getAppPublicDirectory().'semantic/dist/semantic.min.css'?>">
    <link rel="stylesheet" type="text/css" href="<?= Middleware\Web\AppContext::getAppPublicDirectory().'newlayout/css/zkouska_less.css'?>" >
    <link rel="stylesheet" type="text/css" href="<?= Middleware\Web\AppContext::getAppPublicDirectory().'grafia/css/styles.css'?>" />

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <?= $editableJsLinks ?? '' ?>
    <script src="<?= Middleware\Web\AppContext::getAppPublicDirectory().'semantic/dist/semantic.min.js'?>"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.3.1/css/ol.css" type="text/css">
    <style>
      .map {
        height: 400px;
        width: 600px;
      }
      #OpenLayers_Control_Attribution_7{ 
        position: absolute;
        background: #ffffffc7;
        right: 0;
        bottom: 0;
      }
      @media (max-width: 768px) {
          .map{
              width: 350px;
              height: 400px;
          }
          
      }
    </style>
    <script src="http://www.openlayers.org/api/OpenLayers.js"></script>
    