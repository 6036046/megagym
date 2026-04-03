<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $id int class id
 * @var $layout string class layout
 * @var $is_slider_layout bool slider layout
 */

use GymBuilder\Inc\Controllers\Helpers\Functions;

$args = [
	'id'     => $id,
	'layout' => $layout
];
?>
<div class="class-item <?php if ($is_slider_layout) echo esc_attr('swiper-slide'); ?>">
	<?php
	Functions::get_template( 'class/layouts/' . $layout, $args );
	?>
</div>