<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header>
  <div class="logo">
    <img src="<?php echo get_template_directory_uri(); ?>/logo.png" alt="Vossen Logo">
  </div>

  <nav>
    <a href="#">Vind een club</a>
    <a href="#">Groepslessen</a>
    <a href="#">Training en advies</a>
  </nav>
</header>
