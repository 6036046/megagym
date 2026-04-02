<?php

/**
 * @package GymBuilder
 * @var array $args
 * @var string $calculator_shortcode_types
 * @var string $calculator_unit
 * @var string $calculator_heading_text
 * @var string $calculator_des
 * @var string $calculator_btn_text
 * @version 1.0.0
 */

$uniqid = (int) rand();

$metric_checked = $imperial_checked = $metric_style = $imperial_style = '';
if (  'imperial' === $calculator_unit ) {
	$imperial_checked = ' checked';
	$metric_style = ' style="display:none;"';
} else {
	$metric_checked = ' checked';
	$imperial_style = ' style="display:none;"';
}

$metric_radio_html = '<input class="form-check-input" id="gym-builder-bmi-metric-' . $uniqid . '" type="radio" name="gym-builder-bmi-unit" value="metric"' . $metric_checked . '><label for="gym-builder-bmi-metric-' . $uniqid . '">' . __( 'Metric Units', 'gym-builder' ) . '</label>';
$imperial_radio_html = '<input class="form-check-input" id="gym-builder-bmi-imperial-' . $uniqid . '" type="radio" name="gym-builder-bmi-unit" value="imperial"' . $imperial_checked . '><label for="gym-builder-bmi-imperial-' . $uniqid . '">' . __( 'Imperial Units', 'gym-builder' ) . '</label>';

if ( 'imperial' === $calculator_unit ) {
	$radio_html = $imperial_radio_html . $metric_radio_html;
} else {
	$radio_html = $metric_radio_html . $imperial_radio_html;
}
$bmi_chart = array(
	array( __( 'Below 18.5', 'gym-builder' ),   __( 'Underweight', 'gym-builder' ) ),
	array( __( '18.5 - 24.9', 'gym-builder' ),  __( 'Normal', 'gym-builder' ) ),
	array( __( '25 - 29.9', 'gym-builder' ),  __( 'Overweight', 'gym-builder' ) ),
	array( __( '30 and Above', 'gym-builder' ), __( 'Obese', 'gym-builder' ) ),
);
$bmi_chart_encoded = json_encode( $bmi_chart );

?>
<div class="gym-builder-bmi-calculator columns-2">
    <div class="fitness-calculation">
        <h2 class="calculator-title"><?php echo esc_html( $calculator_heading_text ); ?></h2>
		<?php if ($calculator_des){ ?>
            <p class="calculator-description"><?php echo wp_kses_post($calculator_des); ?></p>
		<?php } ?>
        <form class="gym-builder-bmi-form">
            <div class="gym-builder-bmi-radio">
				<?php echo $radio_html; ?>
            </div>
            <div class="gym-builder-bmi-fields">
                <input type="text" class="gym-builder-bmi-fields-metric form-control numeric-input" name="gym-builder-bmi-weight" placeholder="<?php _e( 'Weight / kg', 'gym-builder' ); ?>"<?php echo $metric_style; ?>>
                <input type="text" class="gym-builder-bmi-fields-metric form-control numeric-input" name="gym-builder-bmi-height" placeholder="<?php _e( 'Height / cm', 'gym-builder' ); ?>"<?php echo $metric_style; ?>>
                <input type="text" class="gym-builder-bmi-fields-imperial form-control numeric-input" name="gym-builder-bmi-pound" placeholder="<?php _e( 'Weight / lbs', 'gym-builder' ); ?>"<?php echo $imperial_style; ?>>
                <input type="text" class="gym-builder-bmi-fields-imperial form-control numeric-input" name="gym-builder-bmi-feet" placeholder="<?php _e( 'Height / feet', 'gym-builder' ); ?>"<?php echo $imperial_style; ?>>
                <input type="text" class="gym-builder-bmi-fields-imperial form-control numeric-input" name="gym-builder-bmi-inch" placeholder="<?php _e( 'Height / inch', 'gym-builder' ); ?>"<?php echo $imperial_style; ?>>
            </div>
            <input type="submit" class= "gym-builder-bmi-submit" value="<?php echo esc_html( $calculator_btn_text ); ?>">
            <div class="gym-builder-bmi-result" style="display:none;" data-chart="<?php echo esc_attr( $bmi_chart_encoded ); ?>"><?php _e( 'Your BMI is:', 'gym-builder' );?> <span class="gym-builder-bmi-val"></span><?php _e( ', and weight status is:', 'gym-builder' );?> <span class="gym-builder-bmi-status"></span></div>
            <div class="gym-builder-bmi-error" data-emptymsg="<?php _e( 'Error: One or more fields are empty', 'gym-builder' );?>" data-numbermsg="<?php _e( 'Error: All field values must be a number', 'gym-builder' );?>"></div>
        </form>
    </div>
    <div class="fitness-calculator-chart">
        <div class="bmi-chart-title">
            <h3><?php esc_html_e('Body Mass Index','gym-builder'); ?></h3>
        </div>
        <table class="bmi-chart">
            <tr>
                <th><?php esc_html_e( 'BMI', 'gym-builder' );?></th>
                <th><?php esc_html_e( 'Weight Status', 'gym-builder' );?></th>
            </tr>
			<?php foreach ( $bmi_chart as $chart ): ?>
                <tr>
                    <td><?php echo $chart[0]; ?></td>
                    <td><?php echo $chart[1]; ?></td>
                </tr>
			<?php endforeach; ?>
        </table>
        
    </div>
</div>
