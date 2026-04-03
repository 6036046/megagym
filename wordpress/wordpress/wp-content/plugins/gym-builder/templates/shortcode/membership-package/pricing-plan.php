<?php
/**
 * @package GymBuilder
 * @var array $args
 * @var array $package_features
 * @var int   $package_price
 * @var int   $package_duration
 * @var int   $content_count
 * @var string $button_text
 * @var string  $button_url
 * @version 1.0.0
 */
use GymBuilder\Inc\Controllers\Helpers\Helper;
use WpDreamers\GBCBAP\Helper\Fns;

?>
<div class="package-item-content" data-tab="<?php echo esc_attr(Helper::get_membership_package_type_slug()) ?>">
    <h2 class="package-name"><?php the_title(); ?></h2>
    <div class="pricing-wrap">
        <?php
            echo esc_html($package_price);
            if ($package_duration){
                echo '<span class="price-duration">';
                echo esc_html("/".$package_duration);
                echo '</span>';
            }
        ?>
    </div>
    <?php
    if($package_features){
	    echo '<ul class="feature-item">';
	    foreach($package_features as $feature){
		    ?>
            <li>
                <span class="icon <?php echo esc_attr($feature['feature_icon']);?>">
                    <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.6906 0.612053C11.7269 2.31156 10.0655 3.92937 8.54456 6.04013C7.87378 6.97111 7.12784 8.06688 6.64023 9.10143C6.36187 9.65001 5.86007 10.5072 5.68901 11.3314C4.75337 10.4609 3.74838 9.47289 2.72011 8.69901C1.98719 8.14764 -0.123804 9.2718 0.735491 9.91837C2.27557 11.0767 3.55636 12.5195 5.05431 13.7296C5.68086 14.2351 7.06942 13.1372 7.39573 12.6766C8.46682 11.1591 8.61322 9.30415 9.39384 7.64887C10.5857 5.1173 12.6995 3.03772 14.7865 1.23185C16.1693 -0.0577877 14.7412 -0.295653 13.6926 0.612053" fill="#005dd0"/>
                    </svg>
                </span>
			    <?php echo esc_html($feature['feature_item']); ?>
            </li>
	    <?php }
	    echo '</ul>';
    }
    ?>
    <?php
    if (function_exists('gbcbap') &&  Fns::is_woo_product( get_the_ID() )){
	    do_action('gym_builder_pricing_plan_buy_button',get_the_ID());
    } elseif ($button_text){ ?>
        <div class="button-wrap">
            <a href="<?php echo esc_url($button_url); ?>" class="gym-builder-btn"><?php echo esc_html($button_text); ?></a>
        </div>
    <?php } ?>
</div>
