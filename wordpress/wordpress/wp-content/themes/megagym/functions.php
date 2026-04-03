<?php
function megagym_styles() {
    wp_enqueue_style('megagym-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'megagym_styles');
