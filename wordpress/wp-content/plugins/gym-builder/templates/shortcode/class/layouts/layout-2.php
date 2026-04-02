<?php
/**
 * @package GymBuilder
 * @var bool $show_routine_nav
 * @var string $shortcode_time_format
 * @var array $args
 * @var array   $schedule
 * @var array $weeknames
 * @var array $class_query_info
 * @version 1.0.0
 */
use GymBuilder\Inc\Controllers\Models\GymBuilderClass;

?>
<div class="gym-builder-table-routine">
    <?php if ($show_routine_nav){ ?>
        <div class="gym-builder-routine-nav">
            <ul class="gb-nav gb-nav-tabs">
                <li class="active"><a href="#" data-id="all"><?php _e( 'All', 'gym-builder' );?></a></li>
                <?php foreach ( $class_query_info as $key=>$value ): ?>
                    <li><a data-id="<?php echo esc_attr( $key );?>" href="#"><?php echo esc_html( $value );?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php } ?>
    <table>
        <tr>
            <th></th>
			<?php foreach ( $weeknames as $weekname ): ?>
                <th class="column-title"><span><?php echo esc_html( $weekname );?></span></th>
			<?php endforeach; ?>
        </tr>
		<?php foreach ( $schedule as $schedule_time => $schedule_value ): ?>
            <tr>
                <th class="column-title"><?php echo $schedule_time;?></th>
				<?php
				foreach ( $weeknames as $weekname => $weekvalue ) {
					$has_cell = false;
					foreach ( $schedule_value as $schedule_week => $routine ) {
						if ( $weekname == $schedule_week ) {
							echo '<td>';
							GymBuilderClass::print_routine( $routine,$weekname,$shortcode_time_format );
							echo '</td>';
							$has_cell = true;
						}
					}
					if ( !$has_cell ) {
						echo '<td></td>';
					}
				}
				?>
            </tr>
		<?php endforeach; ?>
    </table>
</div>





