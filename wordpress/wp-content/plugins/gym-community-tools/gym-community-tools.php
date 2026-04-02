<?php
/**
 * Plugin Name: Gym Community Tools
 * Plugin URI: https://example.com/gym-community-tools
 * Description: Adds community post types, event and review shortcodes, and partner settings for the Gym community website.
 * Version: 1.0.0
 * Author: DevSkills
 * Author URI: https://example.com
 * Text Domain: gym-community-tools
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Gym_Community_Tools {
    public function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_post_statuses' ) );
        add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
        add_shortcode( 'gct_events', array( $this, 'shortcode_events' ) );
        add_shortcode( 'gct_reviews', array( $this, 'shortcode_reviews' ) );
        add_shortcode( 'gct_cta', array( $this, 'shortcode_cta' ) );
        add_action( 'admin_menu', array( $this, 'settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_notices', array( $this, 'admin_notice_plugin_active' ) );
    }

    public function register_post_statuses() {
        register_post_status( 'scheduled', array(
            'label' => _x( 'Scheduled', 'post' ),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>' ),
        ) );
    }

    public function register_post_types() {
        register_post_type( 'gct_event', array(
            'labels' => array(
                'name' => __( 'Events', 'gym-community-tools' ),
                'singular_name' => __( 'Event', 'gym-community-tools' ),
                'add_new_item' => __( 'Add New Event', 'gym-community-tools' ),
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
            'menu_icon' => 'dashicons-calendar-alt',
            'rewrite' => array( 'slug' => 'events' ),
        ) );

        register_post_type( 'gct_review', array(
            'labels' => array(
                'name' => __( 'Reviews', 'gym-community-tools' ),
                'singular_name' => __( 'Review', 'gym-community-tools' ),
                'add_new_item' => __( 'Add New Review', 'gym-community-tools' ),
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
            'menu_icon' => 'dashicons-thumbs-up',
            'rewrite' => array( 'slug' => 'reviews' ),
        ) );
    }

    public function register_meta_boxes() {
        add_meta_box( 'gct_event_details', __( 'Event Details', 'gym-community-tools' ), array( $this, 'render_event_meta_box' ), 'gct_event', 'normal', 'default' );
        add_meta_box( 'gct_review_details', __( 'Review Details', 'gym-community-tools' ), array( $this, 'render_review_meta_box' ), 'gct_review', 'normal', 'default' );
    }

    public function render_event_meta_box( $post ) {
        wp_nonce_field( 'gct_save_meta', 'gct_meta_nonce' );
        $event_date = get_post_meta( $post->ID, '_gct_event_date', true );
        $event_location = get_post_meta( $post->ID, '_gct_event_location', true );
        $event_link = get_post_meta( $post->ID, '_gct_event_link', true );
        ?>
        <p>
            <label for="gct_event_date"><?php esc_html_e( 'Datum', 'gym-community-tools' ); ?></label><br>
            <input type="date" id="gct_event_date" name="gct_event_date" value="<?php echo esc_attr( $event_date ); ?>" style="width:100%;max-width:320px;" />
        </p>
        <p>
            <label for="gct_event_location"><?php esc_html_e( 'Locatie', 'gym-community-tools' ); ?></label><br>
            <input type="text" id="gct_event_location" name="gct_event_location" value="<?php echo esc_attr( $event_location ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="gct_event_link"><?php esc_html_e( 'Aanmeldlink', 'gym-community-tools' ); ?></label><br>
            <input type="url" id="gct_event_link" name="gct_event_link" value="<?php echo esc_url( $event_link ); ?>" style="width:100%;" />
        </p>
        <?php
    }

    public function render_review_meta_box( $post ) {
        wp_nonce_field( 'gct_save_meta', 'gct_meta_nonce' );
        $review_score = get_post_meta( $post->ID, '_gct_review_score', true );
        $review_brand = get_post_meta( $post->ID, '_gct_review_brand', true );
        $review_link = get_post_meta( $post->ID, '_gct_review_link', true );
        ?>
        <p>
            <label for="gct_review_brand"><?php esc_html_e( 'Merk of service', 'gym-community-tools' ); ?></label><br>
            <input type="text" id="gct_review_brand" name="gct_review_brand" value="<?php echo esc_attr( $review_brand ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="gct_review_score"><?php esc_html_e( 'Score (1-5)', 'gym-community-tools' ); ?></label><br>
            <input type="number" id="gct_review_score" name="gct_review_score" min="1" max="5" step="1" value="<?php echo esc_attr( $review_score ); ?>" style="width:120px;" />
        </p>
        <p>
            <label for="gct_review_link"><?php esc_html_e( 'Kooplink of externe review', 'gym-community-tools' ); ?></label><br>
            <input type="url" id="gct_review_link" name="gct_review_link" value="<?php echo esc_url( $review_link ); ?>" style="width:100%;" />
        </p>
        <?php
    }

    public function save_meta_boxes( $post_id, $post ) {
        if ( ! isset( $_POST['gct_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gct_meta_nonce'] ) ), 'gct_save_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( 'gct_event' === $post->post_type ) {
            update_post_meta( $post_id, '_gct_event_date', sanitize_text_field( wp_unslash( $_POST['gct_event_date'] ?? '' ) ) );
            update_post_meta( $post_id, '_gct_event_location', sanitize_text_field( wp_unslash( $_POST['gct_event_location'] ?? '' ) ) );
            update_post_meta( $post_id, '_gct_event_link', esc_url_raw( wp_unslash( $_POST['gct_event_link'] ?? '' ) ) );
        }

        if ( 'gct_review' === $post->post_type ) {
            update_post_meta( $post_id, '_gct_review_brand', sanitize_text_field( wp_unslash( $_POST['gct_review_brand'] ?? '' ) ) );
            update_post_meta( $post_id, '_gct_review_score', intval( wp_unslash( $_POST['gct_review_score'] ?? 0 ) ) );
            update_post_meta( $post_id, '_gct_review_link', esc_url_raw( wp_unslash( $_POST['gct_review_link'] ?? '' ) ) );
        }
    }

    public function shortcode_events( $atts ) {
        $atts = shortcode_atts( array( 'limit' => 3 ), $atts, 'gct_events' );
        $query = new WP_Query( array(
            'post_type' => 'gct_event',
            'posts_per_page' => absint( $atts['limit'] ),
            'post_status' => 'publish',
            'orderby' => 'meta_value',
            'meta_key' => '_gct_event_date',
            'order' => 'ASC',
        ) );

        if ( ! $query->have_posts() ) {
            return '<p>Er zijn momenteel geen upcoming events.</p>';
        }

        ob_start();
        echo '<div class="events-list">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $date = get_post_meta( get_the_ID(), '_gct_event_date', true );
            $location = get_post_meta( get_the_ID(), '_gct_event_location', true );
            $link = get_post_meta( get_the_ID(), '_gct_event_link', true );
            ?>
            <article class="event-item">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="event-meta">
                    <?php if ( $date ) : ?><span><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></span><?php endif; ?>
                    <?php if ( $location ) : ?><span><?php echo esc_html( $location ); ?></span><?php endif; ?>
                </div>
                <p><?php echo esc_html( get_the_excerpt() ?: wp_trim_words( get_the_content(), 22 ) ); ?></p>
                <?php if ( $link ) : ?>
                    <a class="button-secondary" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Inschrijven', 'gym-community-tools' ); ?></a>
                <?php endif; ?>
            </article>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();

        return ob_get_clean();
    }

    public function shortcode_reviews( $atts ) {
        $atts = shortcode_atts( array( 'limit' => 3 ), $atts, 'gct_reviews' );
        $query = new WP_Query( array(
            'post_type' => 'gct_review',
            'posts_per_page' => absint( $atts['limit'] ),
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ) );

        if ( ! $query->have_posts() ) {
            return '<p>Er zijn nog geen reviews geplaatst.</p>';
        }

        ob_start();
        echo '<div class="reviews-list">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $brand = get_post_meta( get_the_ID(), '_gct_review_brand', true );
            $score = intval( get_post_meta( get_the_ID(), '_gct_review_score', true ) );
            $link = get_post_meta( get_the_ID(), '_gct_review_link', true );
            ?>
            <article class="review-item">
                <h3><?php the_title(); ?></h3>
                <div class="review-meta">
                    <?php if ( $brand ) : ?><span><?php echo esc_html( $brand ); ?></span><?php endif; ?>
                    <?php if ( $score ) : ?><span><?php echo esc_html( sprintf( '%s/5', $score ) ); ?></span><?php endif; ?>
                </div>
                <p><?php echo esc_html( get_the_excerpt() ?: wp_trim_words( get_the_content(), 22 ) ); ?></p>
                <?php if ( $link ) : ?>
                    <a class="button-secondary" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Meer informatie', 'gym-community-tools' ); ?></a>
                <?php endif; ?>
            </article>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();

        return ob_get_clean();
    }

    public function shortcode_cta() {
        $button_text = $this->get_setting( 'cta_button_text', __( 'Word lid', 'gym-community-tools' ) );
        $button_link = $this->get_setting( 'cta_button_link', 'https://example.com' );
        return '<a class="button-primary" href="' . esc_url( $button_link ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $button_text ) . '</a>';
    }

    public function settings_page() {
        add_options_page(
            __( 'Gym Community Settings', 'gym-community-tools' ),
            __( 'Gym Community', 'gym-community-tools' ),
            'manage_options',
            'gct-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'gct_settings_group', 'gct_settings', array( $this, 'sanitize_settings' ) );
        add_settings_section( 'gct_main_section', __( 'Algemene instellingen', 'gym-community-tools' ), null, 'gct-settings' );
        add_settings_field( 'cta_button_text', __( 'Knoptekst', 'gym-community-tools' ), array( $this, 'render_text_field' ), 'gct-settings', 'gct_main_section', array(
            'label_for' => 'cta_button_text',
            'option_name' => 'gct_settings',
            'description' => __( 'Tekst voor de call-to-action knop op de homepage.', 'gym-community-tools' ),
        ) );
        add_settings_field( 'cta_button_link', __( 'Koppeling', 'gym-community-tools' ), array( $this, 'render_text_field' ), 'gct-settings', 'gct_main_section', array(
            'label_for' => 'cta_button_link',
            'option_name' => 'gct_settings',
            'description' => __( 'Extern adres voor de call-to-action knop.', 'gym-community-tools' ),
        ) );
    }

    public function render_settings_page() {
        $settings = get_option( 'gct_settings', array() );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Gym Community Settings', 'gym-community-tools' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'gct_settings_group' ); ?>
                <?php do_settings_sections( 'gct-settings' ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_text_field( $args ) {
        $options = get_option( $args['option_name'], array() );
        $value = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '';
        printf(
            '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" /><p class="description">%4$s</p>',
            esc_attr( $args['label_for'] ),
            esc_attr( $args['option_name'] ),
            esc_attr( $value ),
            esc_html( $args['description'] )
        );
    }

    public function sanitize_settings( $input ) {
        return array(
            'cta_button_text' => sanitize_text_field( $input['cta_button_text'] ?? '' ),
            'cta_button_link' => esc_url_raw( $input['cta_button_link'] ?? '' ),
        );
    }

    private function get_setting( $key, $default = '' ) {
        $settings = get_option( 'gct_settings', array() );
        return isset( $settings[ $key ] ) && ! empty( $settings[ $key ] ) ? $settings[ $key ] : $default;
    }

    public function admin_notice_plugin_active() {
        if ( ! is_plugin_active( plugin_basename( __FILE__ ) ) ) {
            return;
        }
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Gym Community Tools is actief en klaar om custom events en reviews te tonen.', 'gym-community-tools' ) . '</p></div>';
    }
}

new Gym_Community_Tools();
