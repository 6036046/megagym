<?php get_header(); ?>
<main>
  <section class="hero">
    <div class="site-wrapper">
      <h1>Word sterker, train slimmer en groei in jouw gym community.</h1>
      <p>Een compleet platform voor sporters die nieuws, evenementen, reviews en trainingen willen combineren in één actieve community.</p>
      <div class="hero-actions">
        <a class="button-primary" href="#events">Bekijk events</a>
        <a class="button-secondary" href="#reviews">Lees reviews</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="section-heading">
      <h2>Wat je hier vindt</h2>
      <p>Een gym website gebouwd voor leden, trainers en sportfans: overzichtelijke events, betrouwbare reviews en directe links naar partners.</p>
    </div>
    <div class="grid-3">
      <div class="card">
        <h3>Nieuws & community</h3>
        <p>Houd je community op de hoogte met belangrijke updates, nieuwe trainingsschema’s en lokale activiteiten.</p>
      </div>
      <div class="card">
        <h3>Events & workshops</h3>
        <p>Organiseer sportevenementen, workshops en groepslessen met duidelijke data en locaties voor alle leden.</p>
      </div>
      <div class="card">
        <h3>Reviews & partners</h3>
        <p>Laat leden producten en diensten beoordelen en koppel eenvoudig door naar externe partners.</p>
      </div>
    </div>
  </section>

  <section id="events" class="section">
    <div class="section-heading">
      <h2>Komende events</h2>
      <p>Bekijk de meest recente sportevenementen en schrijf je direct in via externe links.</p>
    </div>
    <?php echo do_shortcode( '[gct_events limit="3"]' ); ?>
  </section>

  <section id="reviews" class="section">
    <div class="section-heading">
      <h2>Review overzicht</h2>
      <p>De beste trainingsproducten, coaches en services binnen onze community, beoordeeld door echte leden.</p>
    </div>
    <?php echo do_shortcode( '[gct_reviews limit="3"]' ); ?>
  </section>

  <section class="section">
    <div class="section-heading">
      <h2>Begin vandaag</h2>
      <p>Word lid van de community en gebruik onze tools om makkelijk informatie te delen en events te organiseren.</p>
      <div class="hero-actions">
        <?php echo do_shortcode( '[gct_cta]' ); ?>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
