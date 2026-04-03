<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $layout string trainer layout
 */

global $post;
$trainer_id=get_the_ID();
use GymBuilder\Inc\Controllers\Helpers\Functions;

$args = [
    'trainer_id'     => $trainer_id,
    'layout' => $layout
];
?>
<div class="trainer-item">
    <?php
    Functions::get_template( 'trainer/layouts/' . $layout, $args );
    ?>
</div>