<?php
function megagym_setup() {
    // Ondersteuning voor logo toevoegen
    add_theme_support( 'custom-logo', array(
        'height'      => 70,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ) );
    
    // Menu registreren
    register_nav_menus( array(
        'primary' => __( 'Hoofdmenu', 'megagym' ),
    ) );
}
add_action( 'after_setup_theme', 'megagym_setup' );

// Stylesheet inladen
function megagym_scripts() {
    wp_enqueue_style( 'megagym-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'megagym_scripts' );
?>