<div class="micsongs-player">
  <audio class="player" preload="auto" tabindex="0" controls="" style="width: 100%;">
    <source src="">
    Your Fallback goes here
  </audio>
  <ol class="playlist">
  <?php
    $categoriesIds = explode(',', $categoria);

    $terms = get_terms('micsongs_cat', array('include' => $categoriesIds));
    if ($terms){
      $counter = 0;
      foreach ( $terms as $term ) {
        $counter++;
        echo  "<center><p id='category'>" . $term->name . '</p></center>';
        $args = array(
          'post_type' => 'micsongs',
          'tax_query' => array(
            array(
              'taxonomy' => 'micsongs_cat',
              'field' => 'id',
              'terms' => $term->term_id,
              'include_children' => true,
              'operator' => 'IN'
            )
          ),
        );

        $posts = get_posts($args);
        $count = 0;
        foreach ($posts as $p) :
        $songurl = get_post_meta( $p->ID, 'song-url', true );
        $songDescription = get_post_meta($p->ID, 'song-description', true); 
        $allowDownload = get_post_meta($p->ID, 'song-allow-download', true);
      ?>
        <li class="<?php if($count == 0) echo "active"; ?>">
          <a href="<?php echo $songurl; ?>">
            <?php echo $p->post_title; ?>
          </a>
          <a href="#" class="block" style="float: right;" data-toggle="collapse" data-target="#msc<?php echo $counter . $p->ID; ?>"><i class="fa fa-info" aria-hidden="true"></i> Ver Mais</a>
          <?php
            if($allowDownload == "yes"){
          ?>    
            <a href="<?php echo $songurl;?>" class="download-song block" data-songid="<?php echo $p->ID ;?>" data-songname="<?php echo $p->post_title;?>" style="float: right;" download><i class="fa fa-download" aria-hidden="true"></i> Baixar</a>
          <?php
            }
          ?>
          <div id="msc<?php echo $counter . $p->ID; ?>" class="collapse songDescription">
            <?php echo $songDescription; ?>
          </div> 
        </li>
      <?php  
        $count++;
        endforeach;
      ?>
  <?php
      }
    }
  ?>
  </ol>
</div>