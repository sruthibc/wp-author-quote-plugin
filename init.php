<?php
   /*
   Plugin Name: Author Quote Block
   description: Add useful quotes and their authors to top and bottom of posts
   Version: 1.0.0
   Author: Sruthi Krishna
   Author URI:
   License: GPL2
   */
  function wpdocs_authers_init() {
    $labels = array(
        'name'                  => _x( 'Authors', 'Post type general name', 'Quote' ),
        'singular_name'         => _x( 'Quote', 'Post type singular name', 'Quote' ),
        'menu_name'             => _x( 'Quote Authors', 'Admin Menu text', 'Quote' ),
        'name_admin_bar'        => _x( 'Quote', 'Add New on Toolbar', 'Quote' ),
        'add_new'               => __( 'Add a New Author', 'Quote' ),
        'add_new_item'          => __( 'Add a New Author', 'Quote' ),
        'new_item'              => __( 'New Quote', 'Quote' ),
        'edit_item'             => __( 'Edit Author', 'Quote' ),
        'view_item'             => __( 'View Quote', 'Quote' ),
        'all_items'             => __( 'All Quote Authors', 'Quote' ),
        'search_items'          => __( 'Search Quote', 'Quote' ),
        'parent_item_colon'     => __( 'Parent Quote:', 'Quote' ),
        'not_found'             => __( 'No Quote found.', 'Quote' ),
        'not_found_in_trash'    => __( 'No Quote found in Trash.', 'Quote' ),
        'featured_image'        => _x( 'Author Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'Quote' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'Quote' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'Quote' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'Quote' ),
        'archives'              => _x( 'Quote archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'Quote' ),
        'insert_into_item'      => _x( 'Insert into Quote', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'Quote' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this Quote', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'Quote' ),
        'filter_items_list'     => _x( 'Filter Quote list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'Quote' ),
        'items_list_navigation' => _x( 'Quote list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'Quote' ),
        'items_list'            => _x( 'Quote list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'Quote' ),
    );     
    $args = array(
        'labels'             => $labels,
        'description'        => 'Quote custom post type.',
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'quote' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'supports'           => array( 'title',  'thumbnail' ),
        'show_in_rest'       => true
    );
      
    register_post_type( 'quote', $args );
}
add_action( 'init', 'wpdocs_authers_init' );


function my_url_add_metabox() {
    add_meta_box(
         'my_url_section',           // The HTML id attribute for the metabox section
         'Author Link',     // The title of your metabox section
         'site_url_metabox_callback',  // The metabox callback function (below)
         'quote'                  // Your custom post type slug
     );
 }
 add_action( 'add_meta_boxes', 'my_url_add_metabox' );

 function site_url_metabox_callback( $post ) {

    // Create a nonce field.
     wp_nonce_field( 'my_url_metabox', 'my_url_metabox_nonce' );
 
     // Retrieve a previously saved value, if available.
     $url = get_post_meta( $post->ID, '_author_my_url', true );
     $_author_quote_1 = get_post_meta( $post->ID, '_author_quote_1', true );
     $_author_quote_2 = get_post_meta( $post->ID, '_author_quote_2', true );    


    // Create the metabox field mark-up.
    ?>

       <p><br><br>
          <label>Author URL </label><input type="text" name="_author_my_url" value="<?php echo esc_url( $url ); ?>" size="70" class="regular-text" style="width: 18em;"/> 
		 <p>
		 This will usually be in the format https://example.com/authors#their_name<br>
		 Note: The 2 Quotes should be added to the individual posts they will appear on.
		 <br><br><br>
       </p>	
    <?php 
}

function my_url_save_metabox( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['my_url_metabox_nonce'] ) ) {
       return;
    }
 
    $nonce = $_POST['my_url_metabox_nonce'];
 
    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'my_url_metabox' ) ) {
       return;
    }
 
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
       return;
    }
 
    // Check the user's permissions.
     if ( ! current_user_can( 'edit_post', $post_id ) ) {
       return;
    }
 
    // Check for and sanitize user input.
    if ( ! isset( $_POST['_author_my_url'] ) ) {
       return;
    }
 
    $url = esc_url_raw( $_POST['_author_my_url'] );
    $_author_quote_1 = esc_textarea( $_POST['_author_quote_1']) ;
    $_author_quote_2 = esc_textarea($_POST['_author_quote_2'] );
    

    // Update the meta fields in the database, or clean up after ourselves.
    if ( empty( $url ) ) {
       delete_post_meta( $post_id, '_author_my_url' );
    } else {
       update_post_meta( $post_id, '_author_my_url', $url );
    }

    update_post_meta( $post_id, '_author_quote_1', $_author_quote_1 );
    update_post_meta( $post_id, '_author_quote_2', $_author_quote_2 );       

}
add_action( 'save_post', 'my_url_save_metabox' );

add_action('wp','author_quote_count');
function author_quote_count()
{
   global $author_quote_count;
   $author_quote_count = 0;
}

 
 add_shortcode( 'author_quote', 'author_quote__add_custom_shortcode' );
function author_quote__add_custom_shortcode() {

     global $post,$author_quote_count;
     $author_quote_count ++;
     
     if($post->how_author_quote == true &&  !empty($post->select_author))
     {
      $author_post = get_post($post->select_author);
        
         ?>
            <style>
                .post-quote .post-bottom-text .fancy{display:block; text-align: center;}
                .post-quote .post-bottom-text p{text-align: center;}
                .post-quote .post-bottom-text p a{font-size:10px;padding-left: 0;text-transform: uppercase;letter-spacing:4px;}
            </style>
            <div class="post-quote">
                <div class="post-content">
                    <p><?php echo get_post_meta($post->ID, '_author_quote_'.$author_quote_count,true); ?></p>
                </div>  
                <div class="post-image">
                  <?php echo get_the_post_thumbnail($author_post->ID , 'thumbnail', array( 'class' => 'alignleft' ) ); ?>
                </div>
                <div class="post-bottom-text"> 
          <figure>
            <span class="fancy" > <?php echo $author_post->post_title  ?> </span>
            <?php 
                if(!empty($author_post->_author_my_url)){ ?>
                    <p><a href="<?php echo esc_url($author_post->_author_my_url) ?>"><?php echo $author_post->post_title  ?>  FOR MUSIC CRITIC</a></p>
            <?php }
            ?>
            
          </figure>				
                </div>
            </div>  
            

         <?php

     }  
     
}

add_filter( 'the_content', 'add_quotes_to_post' );

function add_quotes_to_post(){

    $post = get_post();
    $strContent = $post->post_content; 

    if ( is_single() && 'post' == get_post_type()){

        ob_start();  
        author_quote__add_custom_shortcode();
        $first_quote = ob_get_contents();
        ob_get_clean();

        ob_start();
        author_quote__add_custom_shortcode();
        $second_quote = ob_get_contents();
        ob_get_clean();

        $strContent = $first_quote. $strContent . $second_quote;
    }

    return $strContent;
}


?>