<?php
/**
 * @package Gym Builder/templates/class/loop
 * @var array $args
 * @var int   $class_id
 * @version 1.0.0
 */

use WpDreamers\GBCBAP\Helper\Fns;

$button_text = get_post_meta( $class_id, 'gym_builder_class_button_text', true );
$button_link = get_post_meta( $class_id, 'gym_builder_class_button_url', true ) ?:'#';

if (function_exists('gbcbap') &&  Fns::is_wc_product( $class_id )){
    do_action('gym_builder_class_page_buy_button',$class_id);
}elseif($button_text){
    ?>
    <div class="class-button">
        <a class="gym-builder-btn" href="<?php echo esc_url( $button_link); ?>"><?php  echo esc_html($button_text);  ?>
        </a>
    </div>
<?php }
