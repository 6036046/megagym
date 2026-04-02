<?php

/**
 * @package GymBuilder
 * @var array $args
 * @var string $calculator_shortcode_types
 * @var string $calculator_heading_text
 * @var string $calculator_des
 * @var string $calculator_unit
 * @var string $calculator_btn_text
 * @version 1.0.0
 */
$metric_checked = $imperial_checked='';
if ( 'imperial' === $calculator_unit ) {
	$imperial_checked = ' checked';
    $height_placeholder_text = __('Feet','gym-builder');
    $weight_placeholder_text = __('Pound','gym-builder');
} else {
	$metric_checked = ' checked';
	$height_placeholder_text = __('Centimeter','gym-builder');
	$weight_placeholder_text = __('Kilogram','gym-builder');
}
?>
<div class="gym-builder-body-fat-calculator">
    <div class="fitness-content-wrapper columns-2">
        <div class="fitness-calculation-wrapper">
            <h2 class="calculator-title"><?php echo esc_html( $calculator_heading_text ); ?></h2>
	        <?php if ($calculator_des){ ?>
                <p class="calculator-description"><?php echo wp_kses_post($calculator_des); ?></p>
	        <?php } ?>
            <div class="columns-2">
                <div class="metric-input-field">
                    <input type="radio" id="gb-body-fat-intake-radio-metric"
                           class="form-control gb-body-fat-radio" name="gb-body-fat-intake-radio"
		                <?php echo esc_attr($metric_checked); ?>
                           value="metric">
                    <label for="gb-body-fat-intake-radio-metric"> <?php esc_html_e( 'Metric', 'gym-builder' ) ?></label>
                    <span class="gb-ftc-error">require</span>
                </div>
                <div class="imperial-input-field">
                    <div class="standard-input-field">
                        <input type="radio" id="gb-body-fat-intake-radio-imperial"
                               class="form-control gb-body-fat-radio" name="gb-body-fat-intake-radio"
		                    <?php echo esc_attr($imperial_checked); ?>
                               value="imperial" >
                        <label for="gb-body-fat-intake-radio-imperial"><?php esc_html_e( 'Imperial', 'gym-builder' ); ?></label>
                        <span class="gb-ftc-error">require</span>
                    </div>
                </div>
            </div>
            <div class="columns-2">
                <div class="male-input-field">
                    <input value="male"   type="radio"  class="left  form-control" id="gb-body-fat-male" name="gb-body-fat-gender" checked />
                    <label for="gb-body-fat-male">
					    <?php esc_html_e( 'Male', 'gym-builder' ); ?>
                        <span id="gb-body-fat-male-error" class="gb-ftc-error">require</span>
                    </label>
                </div>
                <div class="female-input-field">
                    <input value="female"  type="radio" class="left form-control" id="gb-body-fat-female" name="gb-body-fat-gender"  />
                    <label for="gb-body-fat-female">
					    <?php esc_html_e( 'Female', 'gym-builder' ); ?>
                        <span id="gb-body-fat-female-error" class="gb-ftc-error">require</span>
                    </label>
                </div>
            </div>
            <div class="columns-2">
                <div class="height-input-field">
                    <label class="gb-body-fat-bmi-intake-height" for="gb-body-fat-intake-height">
					    <?php esc_html_e( 'Height', 'gym-builder' ); ?>
                    </label>
                    <input  id="gb-body-fat-intake-height"
                            pattern="[0-9]"
                            type="text"
                            class="left numeric-input form-control"
                            name="gb-body-fat-intake-height"
                            placeholder="<?php echo esc_attr($height_placeholder_text); ?>" />
                    <span id="gb-body-fat-intake-height-error" class="gb-ftc-error">require</span>
                </div>
                <div class="weight-input-field">
                    <label for="gb-body-fat-intake-weight">
					    <?php esc_html_e( 'Weight', 'gym-builder' ); ?>
                    </label>
                    <input id="gb-body-fat-intake-weight"  pattern="[0-9]" type="text" class="left numeric-input form-control" name="gb-body-fat-intake-weight" placeholder="<?php echo esc_attr($weight_placeholder_text); ?>" />
                    <span id="gb-body-fat-intake-weight-error" class="gb-ftc-error">require</span>
                </div>
            </div>
            <div class="columns-1">
                <div class="age-input-field">
                    <label class="gb-body-fat-age" for="gb-body-fat-age"><?php esc_attr_e('Age', 'gym-builder'); ?></label>
                    <input  id="gb-body-fat-age"  type="text"  class="left numeric-input form-control" name="gb-body-fat-age" />
                    <span id="gb-body-fat-age-error" class="gb-ftc-error">require</span>
                </div>
                <div class="gb-body-fat-btn">
                    <input type="submit" value="<?php echo esc_attr($calculator_btn_text); ?>" class="btn btn-default gb-ftc-btn">
                </div>
            </div>
            <p class="gb-ftc-result-string"><?php esc_html_e('Your body fat is ', 'gym-builder'); ?>  <span class=gb-body-fat-calculated-result>......</span></p>
        </div>
        <div id="gb_body_fat_info_chart">
            <div id="body_fat_result_table" class="gb_hide_result">
                <div class="body-fat-chart-title">
                    <h3><?php esc_html_e('Body Fat Chart','gym-builder'); ?></h3>
                </div>
                <table class="body-fat-chart">
                    <tr>
                        <th><?php esc_html_e( 'Description', 'gym-builder' );?></th>
                        <th><?php esc_html_e( 'Women', 'gym-builder' );?></th>
                        <th><?php esc_html_e( 'Men', 'gym-builder' );?></th>
                    </tr>
                        <tr>
                            <td><?php esc_html_e('Recommended','gym-builder'); ?></td>
                            <td>20-25%</td>
                            <td>8-14%</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('Average','gym-builder'); ?></td>
                            <td>22-25%</td>
                            <td>15-19%</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('Obese','gym-builder'); ?></td>
                            <td>30+%</td>
                            <td>25+%</td>
                        </tr>
                </table>
            </div>
        </div>
    </div>
</div>
