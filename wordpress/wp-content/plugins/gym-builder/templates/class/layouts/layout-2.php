
<?php
/**
 * Hook: gym_builder_class_loop_item.
 *
 *
 * @hooked class_thumbnail - 10
 */


do_action('gym_builder_class_loop_item_start',$id);

?>
<?php
/**
 * Hook: gym_builder_class_loop_item.
 *
 * @hooked class_loop_item_wrapper_start - 10
 * @hooked class_loop_item_title - 20
 * @hooked class_loop_item_description - 30
 * @hooked class_loop_item_meta - 40
 * @hooked class_loop_item_footer_wrapper_start- 50
 * @hooked class_loop_item_category- 60
 * @hooked class_loop_item_footer_button- 70
 * @hooked class_loop_item_footer_wrapper_end- 80
 * @hooked class_loop_item_wrapper_end- 100
 */

do_action('gym_builder_class_loop_item',$id);

?>