<?php


add_action( 'after_setup_theme', 'setup' );
function setup(){
    add_theme_support( 'post-thumbnails' );
}


function my_awesome_func( $data ) {
    $posts = get_posts();
    $output = [];
//    $posts['image'] = wp_get_attachment_url( get_post_thumbnail_id($posts[0]->ID) );

    foreach ($posts as $index => $post){
        $output[$index]['title'] = $post->post_title;
        $output[$index]['image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        $output[$index]['full_image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
    }
    return $output;
}



add_action( 'rest_api_init', function () {
    register_rest_route( 'bahman/v1', '/posts', array(
        'methods' => 'GET',
        'callback' => 'my_awesome_func',
    ) );
} );

