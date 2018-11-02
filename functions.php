<?php


add_action( 'after_setup_theme', 'setup' );
function setup(){
    add_theme_support( 'post-thumbnails' );
}


add_action('wp_enqueue_scripts', 'my_register_script_method');

function my_register_script_method () {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_media();
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

    $medias = get_post_meta($post->ID, 'medias', true);


    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>
    <style>
        .media-container{
            margin-bottom: 20px;
        }
        .media-container img{
            width: 100px;
            height: 100px;
            background-color: #ddd;
            border-radius: 5px;
            margin: 10px;
            display: block;
        }
        .media-item {
            display: inline-block;
            text-align: center;
        }
        .media-item button{
            display: block;
        }
    </style>
    <script>
        $(function () {

            var media_uploader = null;

            var medias_size = $('.media-container img').length;
            var next = medias_size;




            function open_media_uploader_gallery()
            {
                media_uploader = wp.media({
                    frame:    "post",
                    state:    "gallery-edit",
                    multiple: true
                });

                media_uploader.on("update", function(){

                    var length = media_uploader.state().attributes.library.length;
                    var images = media_uploader.state().attributes.library.models;

                    for(var iii = 0; iii < length; iii++)
                    {
                        var image_id = images[iii].attributes.id;
                        var image_url = images[iii].changed.url;
                        var image_caption = images[iii].changed.caption;
                        var image_title = images[iii].changed.title;
                        var image_description = images[iii].changed.description;

                        //this object contains URL for medium, small, large and full sizes URL.
                        var sizes = images[iii].changed.sizes;



                        $('.media-container').append('<div class="media-item"><img src="'+ image_url +'" ><input name="media'+ next +'" type="hidden" value="'+ image_id +'"><button type="button" id="delete-media" class="button button-link-delete">&times;</button></div>');
                        next++;



                    }


                    $('.media-container').append('<input name="media_count" type="hidden" value="'+(length + medias_size)+'">');

                });

                media_uploader.open();
            }


            $('#add-media').click(function () {
                open_media_uploader_gallery();
            });




        });
    </script>
    <p>

        <div class="media-container">
            <?php if ( !empty($medias) ): ?>
                    <?php foreach ( $medias as $index => $media ):  ?>
                <div class="media-item">
                        <img src="<?= wp_get_attachment_url( $media ) ?>">
                        <input id="item<?= $index ?>" name="media_<?= $index ?>" type="hidden" value="<?= $media ?>">
                        <button type="button" data-delete="item<?= $index ?>" id="delete-media" class="button button-link-delete">&times;</button>
                </div>
                    <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" id="add-media" class="button button-primary ">اضافه کردن عکس</button>


    </p>
    <?php
}


add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{

//    var_dump($_POST);
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


    $medias = [];
    for ($index = 0; $index < $_POST['media_count'] ; $index++) {
        $medias[$index] = $_POST["media$index"];
    }
    update_post_meta($post_id, 'medias', $medias);


}


// End Meta Boxes



//add_action( 'rest_api_init', function () {
//    register_rest_route('bahman/v1', '/posts/', array(
//        'methods' => 'GET',
//        'callback' => 'my_awesome_func',
//    ));
//} );
//



//function my_awesome_func2( $data ) {
//    $post = get_post($data['post_id']);
//    $output = [];
//    $output['title'] = $post->post_title;
//    $output['image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
//    $output['full_image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
//    return $output;
//}
//
//
//
//add_action( 'rest_api_init', function () {
//    register_rest_route('bahman/v1', '/post/(?P<post_id>\d+)', array(
//        'methods' => 'GET',
//        'callback' => 'my_awesome_func2',
//    ));
//} );
//
