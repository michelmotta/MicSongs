<?php 
if($musica){
  $songTitle = get_the_title( $musica );
  $songurl = get_post_meta($musica, 'song-url', true); 
  $songDescription = get_post_meta($musica, 'song-description', true); 
?>
  <h3><?php echo $songTitle;?></h3>
  <p><?php echo $songDescription;?></p>
  <audio controls>
    <source src="<?php echo $songurl;?>" type="audio/mpeg">
    Your browser does not support the audio tag.
  </audio> 
<?php
}