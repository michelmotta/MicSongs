<div class="micsongs-player">
  <audio class="player" preload="auto" tabindex="0" controls="" style="width: 100%;">
    <source src="">
    Your Fallback goes here
  </audio>
  <ol class="playlist">
    <?php
      $postsIds = explode(',', $musica);
      $args = array(
        'post_type' => 'micsongs',
        'post__in' => $postsIds
      );

      $posts = get_posts($args);
      $count = 0;
      foreach ($posts as $p) :
      $songurl = get_post_meta( $p->ID, 'song-url', true );
    ?>
      <li class="<?php if($count == 0) echo "active"; ?>">
        <a href="<?php echo $songurl; ?>">
          <?php echo $p->post_title; ?>
        </a>
        <a href="<?php echo $songurl;?>" class="download-song block" data-songid="<?php echo $p->ID ;?>" data-songname="<?php echo $p->post_title;?>" style="float: right;" download>Baixar</a>
      </li>
    <?php  
      $count++;
      endforeach;
    ?>
  </ol>
</div>
