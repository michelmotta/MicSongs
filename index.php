<?php
/**
 * Plugin Name: MicSongs
 * Plugin URI: https://github.com/michelmotta/MicSongs
 * Description: It's pretty wordpress plugin developed to upload and display mp3 songs using shortcodes. 
 * Version: 0.1
 * Author: Michel Motta da Silva
 * Author URI: https://github.com/michelmotta
 * License: GPL2
 */ 


class micsongs
{

	/**
	* @param none
	* @return void
	*/
	public function __construct() 
	{

    register_activation_hook( __FILE__, array($this, 'micsongs_db_install'));

	  add_action('init', array($this, 'micsongs_post_type'));
    add_action('init', array($this, 'create_micsongs_taxonomies'), 0);

    add_action('save_post', array($this, 'micsongs_meta_save'));

    add_filter('manage_micsongs_posts_columns', array($this, 'micsongs_set_custom_edit_columns'));

    add_action('manage_micsongs_posts_custom_column', array($this, 'micsongs_custom_column'), 10, 2 );

    add_shortcode('mss', array($this,'micsongsMss_shortcode'));
    add_shortcode('msp', array($this,'micsongsMsp_shortcode'));
    //add_shortcode('msc', array($this,'micsongsMsc_shortcode'));

    add_action('admin_menu', array($this,'micsongs_options_page'));

	  add_action('admin_enqueue_scripts', array($this, 'micsongs_wp_enqueue_scripts_admin'));

    add_action('wp_enqueue_scripts', array($this, 'micsongs_wp_enqueue_scripts_frontend'));

    add_action('wp_ajax_micsongs_downloads', array($this,'micsongs_downloads'));

    add_action('wp_ajax_nopriv_micsongs_downloads', array($this,'micsongs_downloads'));

	}


  /**
  * @param none
  * @return void
  */
  function micsongs_db_install() {

    global $wpdb;

    $table = $wpdb->prefix . 'micsongs_downloads';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      song_id int(100) NOT NULL,
      song_name varchar(150) NOT NULL,
      song_downloads int(100) DEFAULT '0',
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
  }


	/**
	* @param none
	* @return void
	*/
	function micsongs_post_type() 
	{ 
  	$labels = array(
    	'name' => _x('Músicas', 'post type general name'),
    	'singular_name' => _x('Música', 'post type singular name'),
    	'add_new' => _x('Adicionar Novo', 'Nova música'),
    	'add_new_item' => __('Nova Música'),
    	'edit_item' => __('Editar Música'),
    	'new_item' => __('Nova Música'),
    	'view_item' => __('Ver Música'),
    	'search_items' => __('Procurar Músicas'),
    	'not_found' =>  __('Nenhum registro encontrado'),
    	'not_found_in_trash' => __('Nenhum registro encontrado na lixeira'),
    	'parent_item_colon' => '',
    	'menu_name' => 'Músicas'
 	  );

  	$args = array(
    	'labels' => $labels,
    	'public' => false,
    	'public_queryable' => true,
    	'show_ui' => true,           
    	'query_var' => true,
    	'rewrite' => true,
    	'capability_type' => 'post',
    	'has_archive' => true,
    	'menu_icon' => 'dashicons-format-audio',
    	'hierarchical' => false,
    	'menu_position' => null,
    	'register_meta_box_cb' => array($this, 'micsongs_meta_box'),       
    	'supports' => array('title', 'thumbnail')
  	);

  	register_post_type('micsongs' , $args );
  	flush_rewrite_rules();
	}


	/**
  * @param none
  * @return void
  */
  function micsongs_meta_box()
  {        
    add_meta_box('micsongs_meta_box', __('Informações da música'), array($this,'micsongs_meta_box_callback'), 'micsongs', 'normal', 'default');
  }


  /**
  * @param $post
  * @return void
  */
  function micsongs_meta_box_callback($post)
  {
    wp_nonce_field(basename( __FILE__ ), 'micsongs_nonce');
    $micsongs_meta = get_post_meta($post->ID);
  ?>
   
    <div class="linha">
     	<div class="coluna-50">
     		<label class="">Música</label><br>
        <input type="text" id="song-url" name="song-url" class="song-input" value="<?php if(isset($micsongs_meta['song-url'])) echo $micsongs_meta['song-url'][0]; ?>" readonly="readonly"/><br><br>

        <center><input id="micsong_upload_button" class="button button-primary button-large" type="button" value="Upload música" /></center>

     	</div>
     	<div class="coluna-50">
     		<label class="">Autor</label><br>
        <input type="text" name="song-author" class="song-input" value="<?php if(isset($micsongs_meta['song-author'])) echo $micsongs_meta['song-author'][0]; ?>"/><br>

        <label class="">Descrição</label><br>
        <textarea type="text" name="song-description" class="song-input"/><?php if(isset($micsongs_meta['song-description'])) echo $micsongs_meta['song-description'][0]; ?></textarea>
     	</div>
      <div style="clear: both;"></div>
    </div>
    
  <?php    
  }


  /**
  * @param $post_id
  * @return void
  */
  function micsongs_meta_save($post_id)
  {
   
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = (isset($_POST['micsongs_nonce']) && wp_verify_nonce($_POST['micsongs_nonce'], basename(__FILE__))) ? 'true' : 'false';

    if($is_autosave || $is_revision || !$is_valid_nonce) 
      return;

    if(isset($_POST['song-url'])) 
      update_post_meta($post_id, 'song-url', sanitize_text_field($_POST['song-url']));
  
    if(isset($_POST['song-author'])) 
      update_post_meta($post_id, 'song-author', sanitize_text_field($_POST['song-author']));

    if(isset($_POST['song-description'])) 
      update_post_meta($post_id, 'song-description', sanitize_text_field($_POST['song-description']));
   
  }


  /**
  * @param none
  * @return void
  */
  function create_micsongs_taxonomies() 
  {
    $labels = array(
      'name'  => _x('Categoria', 'taxonomy general name'),
      'singular_name' => _x('Categoria', 'taxonomy singular name'),
      'search_items'  => __('Procurar categorias'),
      'all_items' => __('Todos as categorias'),
      'parent_item' => __('Categorias semelhantes'),
      'parent_item_colon' => __('Categoria semelhante:'),
      'edit_item' => __('Editar categoria'),
      'update_item' => __('Atualizar categoria'),
      'add_new_item'  => __('Adicionar nova categoria'),
      'new_item_name' => __('Nova categoria'),
      'menu_name' => __('Categorias')
    );
    $args = array(
      'hierarchical'  => true,
      'labels'  => $labels,
      'show_ui' => true,
      'show_admin_column' => true,
      'query_var' => true,
      'rewrite' => array( 'slug' => 'micsongs_cat' ),
    );
    register_taxonomy('micsongs_cat', array('micsongs'), $args );
  }


  /**
  * @param $columns
  * @return $columns
  */
  function micsongs_set_custom_edit_columns($columns) 
  {
    $columns = array(
      'cb' => '<input type="checkbox" />',
      'title' => __('Title'),
      'micsongs_icon' => __(''),
      'micsongs_downloads' => __('Downloads'),
      'micsongs_size' => __('Tempo da Música'),
      'micsongs_categories' => __('Categorias'),
      'date' => __('Date')
    );
    
    return $columns;
  }


  /**
  * @param none
  * @return void
  */
  function micsongs_custom_column( $column, $post_id ) 
  {
    switch ( $column ) {
      case 'micsongs_icon' :
        echo "<div class='dashicons-before dashicons-format-audio icon-space'></div>";
      break;

      case 'micsongs_downloads' :
        global $wpdb;
        $table = $wpdb->prefix . 'micsongs_downloads';

        $row = $wpdb->get_row("SELECT * FROM $table WHERE song_id=$post_id", ARRAY_A);
    
        if($row){
          echo  $row['song_downloads'];
        }else{
          echo "0";
        }  
      break;

      case 'micsongs_size' :

        $micsongs_meta = get_post_meta($post_id);
        $urlFile = $micsongs_meta['song-url'][0];

        $attachment_id = attachment_url_to_postid( $urlFile );

        $data = wp_get_attachment_metadata($attachment_id);

        echo $data['length_formatted'];

      break;

      case 'micsongs_categories' :
        $terms = get_the_terms($post_id, 'micsongs_cat');
        if ($terms)
          foreach ($terms as $term) {
            echo $term->name . ',';
          }
        else
          echo "<small>Sem Categoria</small >";
      break;
    }
  }


  /**
  * @param none
  * @return void
  */
  function micsongs_options_page()
  {
    add_submenu_page( 'edit.php?post_type=micsongs', 'Shortcode', 'Shortcode', 'manage_options', 'micsongs-options', array($this, 'micsongs_options_page_callback'));
  }


  /**
  * @param none
  * @return void
  */
  function micsongs_options_page_callback() 
  { 
    $args = array(
      'post_type' => 'micsongs'
    );
    $wp_query = new WP_Query( $args );

    $termsMc = get_terms( array(
      'taxonomy' => 'micsongs_cat',
      'hide_empty' => false,
    ));
    ?>
      <div class="micsongs-wrap wrap">

        <h1>MicSongs Shortcode Generator</h1>
        <div class="linha">
          <div class="coluna-30">
            <h2>Músicas Separadas</h2>
            <select id="ms" onchange="micsongsMs()" class="select2">
            <?php if ($wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
              <option value="<?php the_ID();?>"><?php the_title();?></option>
            <?php endwhile; wp_reset_query();  else: ?>
              <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
            <?php endif; ?>
            </select>
            <h2>Shortcode MS</h2>
            <p id="shortcode1"></p>
          </div>
          <div class="coluna-30">
            <h2>Músicas em Playlist</h2>
            <select id="mp" onchange="micsongsMp()" class="select2-tag">
            <?php if ($wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
              <option value="<?php the_ID();?>"><?php the_title();?></option>
            <?php endwhile; wp_reset_query();  else: ?>
              <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
            <?php endif; ?>
            </select>
            <h2>Shortcode MP</h2>
            <p id="shortcode2"></p>
          </div> 
          <div class="coluna-30">
            <h2>Músicas por Categorias (Playlist)</h2>
            <select id="mc" onchange="micsongsMc()" class="select2">
              <option value=''>Todas as categorias</option>
              <?php
                foreach ($termsMc as $termMc) {
                  echo '<option value="' . $termMc->term_id . '">' . $termMc->name . '</option>';
                }
              ?>
            </select>
            <h2>Shortcode MC</h2>
            <p id="shortcode3"></p>
          </div>
          <div style="clear: both;"></div>
        </div>
      </div>
    <?php
  }


  /**
  * @param $atts
  * @param $content = null
  * @return $content
  */
  function micsongsMss_shortcode($atts, $content = null) 
  {
    extract(shortcode_atts(array(
      "musica" => '',
    ), $atts));

    ob_start();
    
    include 'includes/shortcode_mss.php';

    $content = ob_get_contents();
    ob_end_clean();
    
    return $content;
  }


  /**
  * @param $atts
  * @param $content = null
  * @return $content
  */
  function micsongsMsp_shortcode($atts, $content = null) 
  {
    extract(shortcode_atts(array(
      "musica" => '',
    ), $atts));

    ob_start();
    
    include 'includes/shortcode_msp.php';

    $content = ob_get_contents();
    ob_end_clean();
    
    return $content;
  }
  

  /**
  * @param none
  * @return void
  */
  function micsongs_downloads() 
  {
    global $wpdb;

    $table = $wpdb->prefix . 'micsongs_downloads';

    $songId = $_POST['songId'];
    $songName = $_POST['songName'];

    $row = $wpdb->get_row("SELECT * FROM $table WHERE song_id=$songId", ARRAY_A);
    
    if($row){
      $row['song_downloads'] = $row['song_downloads'] + 1;
      $wpdb->update( 
        $table, 
        array( 
          'song_downloads' => $row['song_downloads'],
          'time' => current_time('mysql')
        ), 
        array('song_id' => $songId)
      );
    }else{
      $wpdb->insert($table, array(
        'song_id' => $songId, 
        'song_name' => $songName,
        'song_downloads' => 1,
        'time' => current_time('mysql')
      ));
    }
    exit;
  }


  /**
  * @param none
  * @return void
  */
  function micsongs_wp_enqueue_scripts_admin() 
  {
		wp_enqueue_media();
    wp_enqueue_style('micsongs_admin_style', plugins_url('MicSongs/css/micsongs_admin_style.css'));
    wp_enqueue_style('micsongs_select2css', plugins_url('MicSongs/css/select2.min.css'));
    wp_enqueue_script('main_admin', plugins_url('MicSongs/js/main_admin.js'), array('jquery'), null, true);
    wp_enqueue_script('micsongs_select2js', plugins_url('MicSongs/js/select2.min.js'), array('jquery'), null, true);
    
  }	
  

  /**
  * @param none
  * @return void
  */
  function micsongs_wp_enqueue_scripts_frontend() 
  {
    wp_enqueue_script('main_frontendjs', plugins_url('MicSongs/js/main_frontend.js'), array('jquery'), null, true);
    wp_localize_script('main_frontendjs', 'WPaAjax',array('ajaxurl' => admin_url( 'admin-ajax.php' )));

    wp_enqueue_style('micsongs_frontend_style', plugins_url('MicSongs/css/micsongs_frontend_style.css'));
    
  } 
  
}

new micsongs();