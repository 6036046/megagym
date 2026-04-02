<?php get_header(); ?>
<main class="section">
  <div class="site-wrapper">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article class="card">
        <h1><?php the_title(); ?></h1>
        <div class="event-meta">
          <span><?php echo esc_html( get_the_date() ); ?></span>
          <span><?php echo esc_html( get_post_type() ); ?></span>
        </div>
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; endif; ?>
  </div>
</main>
<?php get_footer(); ?>
