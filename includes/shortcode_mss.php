<?php 
if($musica){
  $songTitle = get_the_title( $musica );
  $songurl = get_post_meta($musica, 'song-url', true); 
  $songDescription = get_post_meta($musica, 'song-description', true); 
  $allowDownload = get_post_meta($musica, 'song-allow-download', true);
?>
<div class="micsongs-player">
  <audio preload="auto" tabindex="0" controls="" style="width: 100%;">
    <source src="<?php echo $songurl;?>" type="audio/mpeg">
    Your browser does not support the audio tag.
  </audio> 
  <p id="song-download-mss"><?php echo $songTitle;?>
    <a href="#" class="block" style="float: right;" data-toggle="collapse" data-target="#mss<?php echo $musica; ?>"><i class="fa fa-info" aria-hidden="true"></i> Ver Mais</a>
    <?php
      if($allowDownload == "yes"){
    ?>    
      <a href="<?php echo $songurl;?>" class="download-song block" data-songid="<?php echo $musica;?>" data-songname="<?php echo $songTitle;?>" style="float: right;" download>
        <i class="fa fa-download" aria-hidden="true"></i> Baixar
      </a>
    <?php
      }
    ?>
  </p>
  <div id="mss<?php echo $musica; ?>" class="collapse songDescription">
    <?php echo $songDescription; ?>
  </div> 
</div>  
<?php
}