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
<div class="gym-builder-water-intake-calculator">
	<div class="fitness-content-wrapper">
		<div class="fitness-calculation-wrapper">
			<h2 class="calculator-title"><?php echo esc_html( $calculator_heading_text ); ?></h2>
			<?php if ($calculator_des){ ?>
				<p class="calculator-description"><?php echo wp_kses_post($calculator_des); ?></p>
			<?php } ?>
			<div class="calc-type-wrapper">
				<div class="metric-input-field">
					<input type="radio" id="gb-water-intake-radio-metric"
					       class="form-control gb-water-intake-radio" name="gb-water-intake-radio"
						<?php echo esc_attr($metric_checked); ?>
						   value="metric">
					<label for="gb-water-intake-radio-metric"> <?php esc_html_e( 'Metric', 'gym-builder' ) ?></label>
					<span class="gb-ftc-error">require</span>
				</div>
				<div class="imperial-input-field">
					<div class="imperial-input-field">
						<input type="radio" id="gb-water-intake-radio-imperial"
						       class="form-control gb-water-intake-radio" name="gb-water-intake-radio"
							<?php echo esc_attr($imperial_checked); ?>
							   value="imperial" >
						<label for="gb-water-intake-radio-imperial"><?php esc_html_e( 'Imperial', 'gym-builder' ); ?></label>
						<span class="gb-ftc-error">require</span>
					</div>
				</div>
			</div>
			<div class="columns-2">
                <div class="age-input-field">
                    <label for="gb-water-intake-age"> <?php esc_html_e( 'Age', 'gym-builder' ); ?> </label>
                    <select id="gb-water-intake-age" class="left form-control" name="gb-water-intake-age">
                        <option value="14-18">14-18</option>
                        <option value="19-30">19-30</option>
                        <option value="31-50">31-50</option>
                        <option value="50+">50+</option>
					</select>
                </div>
				<div class="gender-input-field">
					<label for="gb-water-intake-sex">
                        <?php esc_html_e( 'Sex', 'gym-builder' ); ?>
                    </label>
					<select id="gb-water-intake-sex"  name="gb-water-intake-sex">
						<option value="male"><?php esc_html_e( 'Male', 'gym-builder' );?></option>
						<option value="female"><?php esc_html_e( 'Female', 'gym-builder' );?></option>
					</select>
				</div>
			</div>
			<div class="columns-2">
				<div class="height-input-field">
					<label class="gb-water-intake-height" for="gb-water-intake-height">
						<?php esc_html_e( 'Height', 'gym-builder' ); ?>
					</label>
					<input  id="gb-water-intake-height"
					        pattern="[0-9]"
					        type="text"
                            class="numeric-input"
					        name="gb-water-intake-height"
					        placeholder="<?php echo esc_attr($height_placeholder_text); ?>" />
					<span id="gb-water-intake-height-error" class="gb-ftc-error">require</span>
				</div>
				<div class="weight-input-field">
					<label for="gb-water-intake-weight">
						<?php esc_html_e( 'Weight', 'gym-builder' ); ?>
					</label>
					<input id="gb-water-intake-weight" class="numeric-input" pattern="[0-9]" type="text" name="gb-water-intake-weight" placeholder="<?php echo esc_attr($weight_placeholder_text); ?>" />
					<span id="gb-water-intake-weight-error" class="gb-ftc-error">require</span>
				</div>
			</div>
            <div class="columns-2">
                <div class="season-input-field">
                    <label for="gb-water-intake-season"><?php esc_html_e( 'Season', 'gym-builder' ); ?></label>
                    <select id="gb-water-intake-season"  name="gb-water-intake-season">
                        <option value="winter"><?php esc_html_e( 'Winter', 'gym-builder' ); ?></option>
                        <option value="normal"><?php esc_html_e( 'Normal', 'gym-builder' ); ?></option>
                        <option value="summer"><?php esc_html_e( 'Summer', 'gym-builder' ); ?></option>
                    </select>
                </div>
                <div class="activity-level-field">
                    <label for="gb-water-intake-activity-level">
                        <?php esc_html_e( 'Activity level', 'gym-builder' ); ?>
                    </label>
                    <select id="gb-water-intake-activity-level"  name="gb-water-intake-activity-level">
                        <option value="lightly-active"><?php esc_html_e( 'Lightly Active', 'gym-builder' ); ?></option>
                        <option value="moderately-active"><?php esc_html_e( 'Moderately Active', 'gym-builder' ); ?></option>
                        <option value="very-active"><?php esc_html_e( 'Very Active', 'gym-builder' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="gb-water-intake-btn">
                <input type="submit" value="<?php echo esc_attr($calculator_btn_text); ?>" class="btn btn-default gb-ftc-btn">
            </div>
            <p class="gb-ftc-result-string"><?php esc_html_e('You should take ', 'gym-builder'); ?>  <span class=gb-water-intake-calculated-result>......</span><?php  esc_html_e(' water per day','gym-builder')?></p>
		</div>
	</div>
</div>
