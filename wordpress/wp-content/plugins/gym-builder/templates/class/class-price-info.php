<?php

use GymBuilder\Inc\Controllers\Helpers\Functions;
$class_price_package_name = '';
$class_price_package_id = get_post_meta( get_the_ID(), 'gym_builder_pricing_package_name', true );
$class_price_package_list_ids = get_post_meta( get_the_ID(), 'gym_builder_package_prices', true );
$class_per_day_duration = get_post_meta(get_the_ID(),'gym_builder_course_duration_time',true);
if ( $class_price_package_id && $class_price_package_id != 0 ) {
	$class_price_package_id_obj = get_term_by( 'id', $class_price_package_id, 'gb_pricing_plan_category' );
	$class_price_package_name   = $class_price_package_id_obj->name;
}
$i = 1;
?>
<?php if ( $class_price_package_name || $class_per_day_duration || $class_price_package_list_ids){ ?>
    <div class="class-price-info">
        <h3 class="single-heading"><?php esc_html_e( 'Class Pricing Info & Details', 'gym-builder' ); ?></h3>
        <div class="price-info-box">
            <?php
            if ( !empty($class_price_package_name) ) {
                ?>
                <div class="item">
                    <div class="label"><?php esc_html_e( 'Package Name:', 'gym-builder' ); ?></div>
                    <div class="content"><?php echo esc_html( $class_price_package_name ); ?></div>
                </div>
            <?php }
            if ($class_per_day_duration){
                ?>
                <div class="item">
                    <div class="label"><?php esc_html_e( 'Package Per Day Duration:', 'gym-builder' ); ?></div>
                    <div class="content"><?php echo esc_html( $class_per_day_duration ); ?></div>
                </div>
            <?php }
            if ( $class_price_package_list_ids ) {
                foreach ( $class_price_package_list_ids as $id ) {
                $title    = get_the_title( $id );
                $price    = Functions::get_price_with_label($id) ?? '0';
                $duration = get_post_meta( $id, 'gym_builder_package_price_duration', true );
                ?>
                <div class="item">
                    <div class="label"><?php esc_html_e( 'Pricing Plan ' . $i . ':', 'gym-builder' ); ?></div>
                    <div class="content"><?php
                        echo esc_html( $title . " - " . $price );
                        if ($duration){
                            echo esc_html(" /".$duration);
                        }
                        ?>
                    </div>
                </div>
                <?php
                $i = $i + 1;
            }
            }
            ?>
        </div>
    </div>
<?php } ?>
