<?php
/**
 * @package GymBuilder
 * @var array $args
 * @var int   $type_name
 * @var int   $type_count
 * @var string $type_value
 * @version 1.0.0
 */

$active_class = $type_count === 1 ? 'active':'';
?>
<li><a href="#" class="item <?php echo esc_attr($active_class); ?>" data-tab="<?php echo esc_attr($type_name) ?>"><?php echo esc_html($type_value) ?></a></li>
