<?php
/**
 * @package Gym Builder/templates/class/loop
 * @var int   $id
 * @version 1.0.0
 */
use GymBuilder\Inc\Controllers\Hooks\TemplateHooks;
?>
<div class="gym-builder-content">
	<?php
	    TemplateHooks::class_loop_item_title($id);
        TemplateHooks::class_loop_item_description($id);
        do_action('gym_builder_class_icon',$id);
	?>
</div>
<?php

/**
 * Hook: gym_builder_class_loop_item.
 *
 *
 * @hooked class_thumbnail - 10
 */

do_action('gym_builder_class_loop_item_start',$id);
