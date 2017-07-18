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
    	//add_action('init', array($this, 'create_micssongs_taxonomies'), 0);
  
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
      	'supports' => array('title', 'editor','thumbnail')
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
    add_meta_box('micsongs_meta_box', __('Upload de música'), array($this,'micsongs_meta_box_callback'), 'micsongs', 'normal', 'default');
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
   
    <p>
      <label class="">Música</label><br>
      <input type="text" name="song" class="regular-text" value="<?php if(isset($micsongs_meta['song'])) echo $micsongs_meta['song'][0]; ?>"/>
    </p>
   
  <?php    
  }
}

new micsongs();