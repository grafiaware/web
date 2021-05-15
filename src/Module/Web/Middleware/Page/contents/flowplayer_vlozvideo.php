<?php
function video_flv ($mat,$cislo_vlozvideo) {
// priklad ocekavaneho slotu    --%VLOZVIDEOFLV_Studio_Z_Pracovni_veletrh2%--
// 
//echo "<br>bude video"  .  mb_substr ( $mat, 16, mb_strlen($mat)-16-3)  . "*<br>";
    
$video_jm_souboru = mb_substr ( $mat, 16, mb_strlen($mat)-16-3)  ;
$script_pro_video="";
//$cislo_vlozvideo=$cislo_vlozvideo+1 ;

// style="display:block;width:512px;height:288px;" 
$script_pro_video =
'<center> <a  href="video/' .  $video_jm_souboru .'.flv" 	
        style="display:block;width:426px;height:240px;" 
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
?>
