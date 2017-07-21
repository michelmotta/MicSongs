<?php 
if($musica){
  $songTitle = get_the_title( $musica );
  $songurl = get_post_meta($musica, 'song-url', true); 
  $songDescription = get_post_meta($musica, 'song-description', true); 
?>
  <p id="song-name-mss"><?php echo $songTitle;?></p>
  <p id="song-description-mss"><?php echo $songDescription;?></p>
  <audio preload="auto" tabindex="0" controls="" style="width: 100%;">
    <source src="<?php echo $songurl;?>" type="audio/mpeg">
    Your browser does not support the audio tag.
  </audio> 
  <p id="song-download-mss" ><a href="<?php echo $songurl;?>" class="download-song" data-songid="<?php echo $musica;?>" data-songname="<?php echo $songTitle;?>" style="float: right;" download>Baixar</a></p>
<?php
}