<?php
function vossen_theme_styles() {
    wp_enqueue_style('vossen-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'vossen_theme_styles');
