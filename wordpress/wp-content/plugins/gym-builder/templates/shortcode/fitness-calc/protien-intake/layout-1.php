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
<div class="gym-builder-protein-intake-calculator">
	<div class="fitness-content-wrapper">
		<div class="fitness-calculation-wrapper">
			<h2 class="calculator-title"><?php echo esc_html( $calculator_heading_text ); ?></h2>
			<?php if ($calculator_des){ ?>
				<p class="calculator-description"><?php echo wp_kses_post($calculator_des); ?></p>
			<?php } ?>
			<div class="calc-type-wrapper">
				<div class="metric-input-field">
					<input type="radio" id="gb-protein-intake-radio-metric"
					       class="form-control gb-protein-intake-radio" name="gb-protein-intake-radio"
						<?php echo esc_attr($metric_checked); ?>
						   value="metric">
					<label for="gb-protein-intake-radio-metric"> <?php esc_html_e( 'Metric', 'gym-builder' ) ?></label>
					<span class="gb-ftc-error">require</span>
				</div>
				<div class="imperial-input-field">
					<div class="imperial-input-field">
						<input type="radio" id="gb-protein-intake-radio-imperial"
						       class="form-control gb-protein-intake-radio" name="gb-protein-intake-radio"
							<?php echo esc_attr($imperial_checked); ?>
							   value="imperial" >
						<label for="gb-protein-intake-radio-imperial"><?php esc_html_e( 'Imperial', 'gym-builder' ); ?></label>
						<span class="gb-ftc-error">require</span>
					</div>
				</div>
			</div>
			<div class="columns-2">
                <div class="age-input-field">
                    <label for="gb-protein-intake-age"> <?php esc_html_e( 'Age', 'gym-builder' ); ?> </label>
                    <input type="text" id="gb-protein-intake-age" class="numeric-input" name="gb-protein-intake-age" />
                    <span id="gb-protein-intake-age-error" class="gb-ftc-error"><?php esc_html_e( 'Age should be in 14 to 80 range', 'gym-builder' ); ?></span>
                </div>
				<div class="gender-input-field">
					<label for="gb-protein-intake-sex">
                        <?php esc_html_e( 'Sex', 'gym-builder' ); ?>
                    </label>
					<select id="gb-protein-intake-sex"  name="gb-protein-intake-sex">
						<option value="male"><?php esc_html_e( 'Male', 'gym-builder' );?></option>
						<option value="female"><?php esc_html_e( 'Female', 'gym-builder' );?></option>
					</select>
				</div>
			</div>
			<div class="columns-2">
				<div class="height-input-field">
					<label class="gb-protein-intake-height" for="gb-protein-intake-height">
						<?php esc_html_e( 'Height', 'gym-builder' ); ?>
					</label>
					<input  id="gb-protein-intake-height"
					        pattern="[0-9]"
					        type="text"
					        name="gb-protein-intake-height"
                            class="numeric-input"
					        placeholder="<?php echo esc_attr($height_placeholder_text); ?>" />
					<span id="gb-protein-intake-height-error" class="gb-ftc-error">require</span>
				</div>
				<div class="weight-input-field">
					<label for="gb-protein-intake-weight">
						<?php esc_html_e( 'Weight', 'gym-builder' ); ?>
					</label>
					<input id="gb-protein-intake-weight"  pattern="[0-9]" type="text" class="numeric-input" name="gb-protein-intake-weight" placeholder="<?php echo esc_attr($weight_placeholder_text); ?>" />
					<span id="gb-protein-intake-weight-error" class="gb-ftc-error">require</span>
				</div>
			</div>
            <div class="columns-2">
                <div class="goal-input-field">
                    <label for="gb-protein-intake-goal"><?php esc_html_e( 'Goal', 'gym-builder' ); ?></label>
                    <select id="gb-protein-intake-goal"  name="gb-protein-intake-goal">
                        <option value="fat-loss"><?php esc_html_e( 'Fat loss', 'gym-builder' ); ?></option>
                        <option value="maintenance"><?php esc_html_e( 'Maintenance', 'gym-builder' ); ?></option>
                        <option value="muscle-gain"><?php esc_html_e( 'Muscle gain', 'gym-builder' ); ?></option>
                    </select>
                </div>
                <div class="activity-level-field">
                    <label for="gb-protein-intake-activity-level">
                        <?php esc_html_e( 'Activity level', 'gym-builder' ); ?>
                    </label>
                    <select id="gb-protein-intake-activity-level"  name="gb-protein-intake-activity-level">
                        <option value="sedentary"><?php esc_html_e( 'Sedentary', 'gym-builder' ); ?></option>
                        <option value="lightly-active"><?php esc_html_e( 'Lightly Active', 'gym-builder' ); ?></option>
                        <option value="moderately-active"><?php esc_html_e( 'Moderately Active', 'gym-builder' ); ?></option>
                        <option value="very-active"><?php esc_html_e( 'Very Active', 'gym-builder' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="gb-protein-intake-btn">
                <input type="submit" value="<?php echo esc_attr($calculator_btn_text); ?>" class="btn btn-default gb-ftc-btn">
            </div>
            <p class="gb-ftc-result-string"><?php esc_html_e('You should take ', 'gym-builder'); ?>  <span class=gb-protein-intake-calculated-result>......</span><?php  esc_html_e(' protein per day','gym-builder')?></p>
		</div>
	</div>
</div>
