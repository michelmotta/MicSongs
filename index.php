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
  	* This is a construct function. This function is loaded when the class is initialized. This function is responsible to load all hook wordpress functions
  	* @param none
  	* @return void
  	*/
  	public function __construct() 
  	{

		  add_action('init', array($this, 'micsongs_post_type'));
      add_action('init', array($this, 'create_micsongs_taxonomies'), 0);
      add_action('save_post', array($this, 'micsongs_meta_save'));
      add_filter('manage_micsongs_posts_columns', array($this, 'micsongs_set_custom_edit_columns'));
      add_action('manage_micsongs_posts_custom_column', array($this, 'micsongs_custom_column'), 10, 2 );

      add_action('admin_menu', array($this,'micsongs_options_page'));

		  add_action('admin_enqueue_scripts', array($this, 'micsongs_wp_enqueue_scripts'));
  
  	}


  	/**
  	* This function creates a MicSongs wordpress custom post type inside of wordpress administration. 
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
    * This function creates custom meta box to micsongs custom post type
    * @param none
    * @return void
    */
    function micsongs_meta_box()
    {        
      add_meta_box('micsongs_meta_box', __('Informações da música'), array($this,'micsongs_meta_box_callback'), 'micsongs', 'normal', 'default');
    }


    /**
    * This is a callback function for micslider_meta_box. This callback function generates html content to show inside the meta box
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
  * This function is initialize always when the custom post type micslider is saved. This function is responsible to validade and persist data.
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
  * This function creates a micslider_cat wordpress custom taxonomy to organize slider by custom post type 
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
  * This function creates a custom columns display to wordpress admin view
  * @param $columns
  * @return $columns
  */
  function micsongs_set_custom_edit_columns($columns) 
  {
    $columns = array(
      'cb' => '<input type="checkbox" />',
      'title' => __('Title'),
      'micsongs_icon' => __(''),
      'micsongs_size' => __('Tempo da Música'),
      'micsongs_categories' => __('Categorias'),
      'date' => __('Date')
    );
    
    return $columns;
  }


  /**
  * This function generates content to the custom colums display
  * @param none
  * @return void
  */
  function micsongs_custom_column( $column, $post_id ) 
  {
    switch ( $column ) {
      case 'micsongs_icon' :
          echo '<div class="dashicons-before dashicons-format-audio"></div>';
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
  * Create a micslider_cat wordpress custom taxonomy 
  * @param none
  * @return void
  */
  function micsongs_options_page()
  {
    add_submenu_page( 'edit.php?post_type=micsongs', 'Shortcode', 'Shortcode', 'manage_options', 'micsongs-options', array($this, 'micsongs_options_page_callback'));
  }

  /**
  * This is a callback function for micslider_options_page. This callback function generates html content to show inside the options page
  * @param none
  * @return void
  */
  function micsongs_options_page_callback() 
  { 
    $terms = get_terms( array(
      'taxonomy' => 'micsongs_cat',
      'hide_empty' => false,
    ));
    ?>
      <div class="micsongs-wrap wrap">

        <h1>MicSongs Shortcode Generator</h1>
        <div class="linha">
          <div class="coluna-30">
            <h2>Músicas Separadas</h2>
            <h2>Shortcode MS</h2>
            <p id="shortcode1">[micslider categoria="" quantidade="1"]</p>
          </div>
          <div class="coluna-30">
            <h2>Músicas em Playlist</h2>
            <h2>Shortcode MP</h2>
            <p id="shortcode2">[micslider categoria="" quantidade="1"]</p>
          </div> 
          <div class="coluna-30">
            <h2>Músicas por Categorias</h2>
            <select id="cat" onchange="myFunction()">
              <option value=''>Todas as categorias</option>
              <?php
                foreach ($terms as $term) {
                  echo '<option value="' . $term->name . '">' . $term->name . '</option>';
                }
              ?>
            </select>
            <h2>Shortcode MC</h2>
            <p id="shortcode3">[micslider categoria="" quantidade="1"]</p>
          </div>
          <div style="clear: both;"></div>
        </div>
      </div>
      <script>
        function myFunction() {

          var cat = document.getElementById("cat").value;
          var qtd = document.getElementById("qtd").value;

          document.getElementById("shortcode1").innerHTML = "[micslider categoria=&quot;" + cat + "&quot; quantidade=&quot;" + qtd + "&quot;]'";


        }
      </script>
    <?php
  }

  /**
  * This function add Jquery script to open wordpress media uploader
  * @param none
  * @return void
  */
  function micsongs_wp_enqueue_scripts() 
  {
		wp_enqueue_media();

    wp_register_style('micsongs_style', plugins_url('MicSongs/css/micsongs_style.css'));
    wp_enqueue_style('micsongs_style');

    wp_register_style('micsongs_select2css', plugins_url('MicSongs/css/select2.min.css'));
    wp_enqueue_style('micsongs_select2css');

    wp_register_script('micsongs_media', plugins_url('MicSongs/js/micsongs_media.js'), array('jquery'), '3.3.7', true );
    wp_enqueue_script('micsongs_media');

    wp_register_script('micsongs_select2js', plugins_url('MicSongs/js/select2.min.js'), array('jquery'), '3.3.7', true );
    wp_enqueue_script('micsongs_select2js');
    
  }	
  
}

new micsongs();