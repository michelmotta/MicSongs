<?php 
if($musica){
  $songTitle = get_the_title( $musica );
  $songurl = get_post_meta($musica, 'song-url', true); 
  $songDescription = get_post_meta($musica, 'song-description', true); 
?>
  <p id="song-name-mss"><?php echo $songTitle;?></p>
  <p id="song-description-mss"><?php echo $songDescription;?></p>
  <audio controls>
    <source src="<?php echo $songurl;?>" type="audio/mpeg">
    Your browser does not support the audio tag.
  </audio> 
  <p id="song-download-mss"><a href="<?php echo $songurl;?>" download>Baixe essa música</a></p>
<?php
}