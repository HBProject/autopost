<?php get_header(); ?>


    <section class="features" id="features">
        <div class="container text-right">
            <h2><?= $post->post_title ?></h2>
            <br>
            <p class="text-justify"><?= $post->post_content ?></p>
        </div>
    </section>


<?php get_footer(); ?>