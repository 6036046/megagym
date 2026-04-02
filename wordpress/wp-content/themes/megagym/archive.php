<?php get_header(); ?>
<main class="section">
  <div class="site-wrapper">
    <div class="section-heading">
      <h2><?php post_type_archive_title(); ?></h2>
      <p>Bekijk alle items in deze categorie.</p>
    </div>
    <?php if ( have_posts() ) : ?>
      <div class="events-list">
        <?php while ( have_posts() ) : the_post(); ?>
          <article class="event-item">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="event-meta">
              <span><?php echo esc_html( get_post_type() ); ?></span>
              <span><?php echo esc_html( get_the_date() ); ?></span>
            </div>
            <p><?php the_excerpt(); ?></p>
          </article>
        <?php endwhile; ?>
      </div>
    <?php else : ?>
      <p>Er zijn geen items gevonden.</p>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
