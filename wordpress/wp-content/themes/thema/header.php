<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <header class="site-header">
        <div class="logo">
            <?php 
            if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                the_custom_logo();
            } else {
                echo '<h2><a href="' . esc_url( home_url( '/' ) ) . '" style="color:#D4AF37; text-decoration:none;">MEGA GYM</a></h2>';
            }
            ?>
        </div>
        
        <nav class="main-nav">
            <?php 
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array( 
                    'theme_location' => 'primary',
                    'container' => false 
                ) ); 
            } else {
                echo '<p style="color:white; font-size:12px;">Wijs een menu toe in WP</p>';
            }
            ?>
        </nav>

        <div class="header-actions">
            <button class="btn-primary" style="background-color: #D4AF37; color: #000;">LID WORDEN</button>
        </div>
    </header>