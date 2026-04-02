<?php
function megagym_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'megagym' ),
    ) );
}
add_action( 'after_setup_theme', 'megagym_setup' );

function megagym_scripts() {
    wp_enqueue_style( 'megagym-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
    wp_enqueue_script( 'megagym-script', get_stylesheet_directory_uri() . '/script.js', array(), filemtime( get_stylesheet_directory() . '/script.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'megagym_scripts' );

function megagym_logo_html() {
    if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
        the_custom_logo();
        return;
    }
    echo '<a class="logo-link" href="' . esc_url( home_url( '/' ) ) . '"><img src="' . esc_url( get_stylesheet_directory_uri() . '/logo.png' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"></a>';
}
