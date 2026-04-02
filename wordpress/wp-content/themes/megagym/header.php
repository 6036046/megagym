<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header>
  <div class="site-wrapper header-inner">
    <div class="logo">
      <?php megagym_logo_html(); ?>
    </div>
    <nav class="primary-navigation">
      <?php
      wp_nav_menu( array(
        'theme_location' => 'primary',
        'container' => false,
        'menu_class' => 'primary-nav',
        'fallback_cb' => function() {
          echo '<a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';
          echo '<a href="' . esc_url( home_url( '/#events' ) ) . '">Events</a>';
          echo '<a href="' . esc_url( home_url( '/#reviews' ) ) . '">Reviews</a>';
        }
      ) );
      ?>
    </nav>
  </div>
</header>
<div class="site-wrapper">
