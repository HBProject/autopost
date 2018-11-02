<?php


add_action( 'after_setup_theme', 'setup' );
function setup(){
    add_theme_support( 'post-thumbnails' );
}




function my_awesome_func( $data ) {
    $posts = get_posts();
    $output = [];
    foreach ($posts as $index => $post){
        $output[$index]['title'] = $post->post_title;
        $output[$index]['image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        $output[$index]['full_image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
    }
    return $output;
}

// Meta Boxes


add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add()
{
    add_meta_box( 'my-meta-box-id', 'My First Meta Box', 'cd_meta_box_cb', 'post', 'normal', 'high' );
}


function cd_meta_box_cb()
{
    // $post is already set, and contains an object: the WordPress post
    global $post;
    $values = get_post_custom( $post->ID );
    $text = isset( $values['my_meta_box_text'] ) ? $values['my_meta_box_text'] : '';

    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>
    <p>
        <label for="my_meta_box_text">Text Label</label>
        <input type="text" name="my_meta_box_text" id="my_meta_box_text" value="<?php echo $text; ?>" />
    </p>
    <?php
}


add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'my_meta_box_nonce')) return;

    // if our current user can't edit this post, bail
    if (!current_user_can('edit_post')) return;

    // now we can actually save the data
    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );

    // Make sure your data is set before trying to save it
    if (isset($_POST['my_meta_box_text']))
        update_post_meta($post_id, 'my_meta_box_text', wp_kses($_POST['my_meta_box_text'], $allowed));
}

// End Meta Boxes



add_action( 'rest_api_init', function () {
    register_rest_route('bahman/v1', '/posts/', array(
        'methods' => 'GET',
        'callback' => 'my_awesome_func',
    ));
} );




function my_awesome_func2( $data ) {
    $post = get_post($data['post_id']);
    $output = [];
    $output['title'] = $post->post_title;
    $output['image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
    $output['full_image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
    return $output;
}



add_action( 'rest_api_init', function () {
    register_rest_route('bahman/v1', '/post/(?P<post_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'my_awesome_func2',
    ));
} );



// bahman/v1/post/