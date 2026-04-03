<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $trainer_id integer trainer id
 */

/**
 * Hook: gym_builder_trainer_loop_item.
 *
 * @hooked trainer_thumbnail - 10
 */

do_action('gym_builder_trainer_loop_item_start',$trainer_id);

?>
<?php
/**
 * Hook: gym_builder_trainer_loop_item.
 *
 * @hooked class_thumbnail - 10
 */

do_action('gym_builder_trainer_loop_item',$trainer_id);

?>
