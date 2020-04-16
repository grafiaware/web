<?php
function  vloz_video ($mat, $je_mobil , $cislo_vlozvideo) {
// priklad ocekavaneho slotu    --%VLOZVIDEOFLV_Studio_Z_Pracovni_veletrh2_bezpripony%--
// pro nemobilni verzi soubor jmeno.flv + jmeno.jpg
// pro mobilni verzi  soubor jmeno_mobil.mp4 + jmeno_mobil.jpg
// .flv soubor lze vlozit vicekrat za podminky ruznych jmen souboru .flv, u .mp4 toto neni podminkou
// poznamka 20160223  - u explo a mozilly nejde zobrazit !nase! .mp4 ve video tagu

//echo "<br>bude video"  .  mb_substr ( $mat, 16, mb_strlen($mat)-16-3)  . "*<br>";
    
$video_jm_souboru = mb_substr ( $mat, 16, mb_strlen($mat)-16-3)  ;
$script_pro_video ="";
if ($je_mobil===1)  {
    if (file_exists('video/' . $video_jm_souboru . "_mobil.mp4")) {
         $script_pro_video =  video_mp4 ($video_jm_souboru . "_mobil" );
    } elseif (file_exists('video/' . $video_jm_souboru . "_mobil.flv")) {
         $script_pro_video = video_flv($video_jm_souboru  . "_mobil", $cislo_vlozvideo);
    } elseif (file_exists('video/' .$video_jm_souboru . ".mp4")) {
         $script_pro_video =  video_mp4 ($video_jm_souboru );
    } elseif (file_exists('video/' .$video_jm_souboru . ".flv")) {
         $script_pro_video = video_flv($video_jm_souboru  , $cislo_vlozvideo);
    } else {
         $script_pro_video = '...&spades;...';
    }
}else { //neni mobil
    if (file_exists( 'video/' .$video_jm_souboru . ".mp4")) {
         $script_pro_video =  video_mp4 ($video_jm_souboru );
    } elseif (file_exists('video/' .$video_jm_souboru . ".flv")) {
         $script_pro_video = video_flv($video_jm_souboru  , $cislo_vlozvideo);
    } elseif (file_exists('video/' .$video_jm_souboru . "_mobil.mp4")) {
         $script_pro_video =  video_mp4 ($video_jm_souboru . "_mobil" );
    } elseif (file_exists('video/' .$video_jm_souboru . "_mobil.flv")) {
         $script_pro_video = video_flv($video_jm_souboru  . "_mobil", $cislo_vlozvideo);
    } else {
         $script_pro_video = '...&spades;...';
    }
    
}

return  $script_pro_video ;
}


function video_flv ($video_jm_souboru, $cislo_vlozvideo) {
$script_pro_video="";

// style="display:block;width:512px;height:288px;" 
$script_pro_video =
'<center> <a  href="video/' .  $video_jm_souboru .'.flv" 	
        style="display:block;width:384px;height:216px;" 
	id="player'. $cislo_vlozvideo .'">
</a> </center>
<script language="JavaScript">
	flowplayer("player' .$cislo_vlozvideo.  '", "flowplayer/flowplayer-3.2.7.swf", {
    clip:  {
        autoPlay: false,
        autoBuffering: false
    },
    canvas:  {
	backgroundImage: \'url(video/' .  $video_jm_souboru .  '.jpg)\'
    }
});
</script>' ;

return  $script_pro_video ;
}




function video_mp4 ($video_jm_souboru ) {
$script_pro_video="";

// style="display:block;width:512px;height:288px;" 

$script_pro_video = 
        '<video width="384" height="216" controls  poster="./video/' .  $video_jm_souboru .   '.jpg">
        <source src="./video/' . $video_jm_souboru .   '.mp4" type="video/mp4">
         Váš prohlížeč nepodporuje element video.
        </video>';

return  $script_pro_video ;
}

// toto bylo na mobilni verzi
// $video_htm = "<a href='./video/Studio Z_Veletrh prace mini003.mp4' target='_blank'>
//                                                 <img src='video/Studio Z_Veletrh prace mini.jpg'></a>";
//                                           $zaznam[$db_obsah]=str_replace($mat,  $video_htm, $zaznam[$db_obsah]);

// priklad
//<video width="320" height="240" controls="controls" poster="obrazek.png">
//  <source src="url_adresa.ogg" type="video/ogg">
//  <source src="url_adresa.mp4" type="video/mp4">
//  <source src="url_adresa.webm" type="video/webm">
//  Váš prohlížeč nepodporuje element video.
//</video>


?>
