<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

$iframe  = 'https://www.youtube.com/embed/videoseries?list=PLhe8H70KgCii7HCo7ZdLVYUniF_18gGni';

$contact = 'https://wpdreamers.com/contact-us/';

$review  = 'https://wordpress.org/support/plugin/gym-builder/reviews/?filter=5#new-post';


?>
<style>
    .gb-extensions-page-wrapper{
        margin-left: auto;
        margin-right: auto;
        max-width: 100%;
        padding: 15px;
    }
    @media(min-width: 1440px){
        .gb-extensions-page-wrapper{
            max-width:1400px;
        }
    }
    .gb-extensions-content-wrap{
        display:grid;
        gap: 24px;
    }
    .extensions-content{
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 40px rgba(0,0,0,.05);
        padding: 30px;
    }
    .extensions-wrapper{
        display: grid;
        gap: 30px;
    }
    .gb-extensions-container{
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
    }
    @media(max-width: 767px){
        .gb-extensions-container{
            display:block;
        }
    }
    .single-extension-item{
        flex: 0 0 calc(33.33% - 20px);
        transition: transform .2s,-webkit-transform .2s;
        width: calc(33.33% - 20px);
    }
    @media(max-width: 767px){
        .single-extension-item{
            width: 100%;
            margin-bottom: 30px;
        }
    }
    .single-extension-item .item-wrap{
        overflow:hidden;
        border-radius: 15px;
        box-shadow: 0 0 50px rgba(0,0,0,.05);
        border: 1px solid #f1f1f1;
        min-height: 540px;
        display:flex;
        flex-direction: column;
    }
    .single-extension-item .item-wrap .content{
        padding:0 30px 30px 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .single-extension-item .extension-name{
        font-size:20px;
        line-height: 1.4;
        margin-bottom: 15px;
    }
    .single-extension-item .description p{
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    .single-extension-item .img-wrapper img{
        max-width: 100%;
        min-height: 235px;
        object-fit: cover;
    }
    .gb-extensions-page-wrapper .extensions-page-header{
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 40px rgba(0,0,0,.05);
        padding: 30px;
        margin-bottom: 30px;
    }
    .gb-extensions-page-wrapper .button-wrapper{
        margin-top: auto;
    }
    .gb-extensions-page-wrapper .button-wrapper a{
        background: rgba(67,96,239,.15);
        color: #005dd0;
        font-size: 12px;
        font-weight: 600;
        height: 37px;
        min-width: 120px;
        text-transform: uppercase;
        display:inline-flex;
        align-items:center;
        justify-content: center;
        text-decoration: none;
        border-radius: 4px;
    }
    .gb-extensions-page-wrapper .button-wrapper a:active,
    .gb-extensions-page-wrapper .button-wrapper a:focus{
        box-shadow: none;
        outline:0;
    }
    .gb-extensions-page-wrapper .extensions-heading{
        color: #242424;
        font-size: 22px;
        font-weight: 600;
        line-height: 1.2;
        margin: 0;
    }
</style>
<div class="wrap gb-extensions-wrap">
    <div class="gb-extensions-page-wrapper">
        <div class="gb-extensions-content-wrap">
            <div class="extensions-content">
                <div class="extensions-wrapper">
                    <h2 class="extensions-heading">
		                <?php echo esc_html('Premium Add-Ons and Themes to Supercharge Your Gym Builder'); ?>
                    </h2>
                    <div class="gb-extensions-container">
                        <div class="single-extension-item">
                            <div class="item-wrap">
                                <div class="img-wrapper">
                                    <img src="<?php echo esc_url(plugin_dir_url( dirname( __FILE__) ).'assets/admin/images/yoga-theme.jpg');?>"/>
                                </div>
                                <div class="content">
                                    <div class="content-top">
                                        <h3 class="extension-name"><?php echo esc_html('Yoga & Fitness Studio WordPress Theme'); ?></h3>
                                        <div class="description">
                                            <p><?php echo esc_html('Build your dream yoga website with our easy-to-use Yoga WordPress Theme. Featuring live customizer options, 3 modern homepages, one-click demo import, custom Elementor widgets, and Fluent Forms integration—it’s everything you need for a stunning fitness site!'); ?></p>
                                        </div>
                                    </div>
                                    <div class="button-wrapper">
                                        <a href="<?php echo esc_url('https://wpdreamers.com/themes/yoga-wordpress-themes/'); ?>" target="_blank"><?php echo esc_html('Buy Now'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="single-extension-item">
                            <div class="item-wrap">
                                <div class="img-wrapper">
                                    <img src="<?php echo esc_url(plugin_dir_url( dirname( __FILE__) ).'assets/admin/images/attendance-system.jpg');?>"/>
                                </div>
                                <div class="content">
                                    <div class="content-top">
                                        <h3 class="extension-name"><?php echo esc_html('Attendance System'); ?></h3>
                                        <div class="description">
                                            <p><?php echo esc_html('Effortlessly track gym member attendance with precise check-ins, check-outs, and advanced search functionality. Generate and print detailed reports, manage total attendance counts, and reset them when needed—all seamlessly integrated into Gym Builder.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="button-wrapper">
                                        <a href="<?php echo esc_url('https://gymbuilder.wpdreamers.com/addons/member-attendance-system/'); ?>" target="_blank"><?php echo esc_html('Buy Now'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="single-extension-item">
                            <div class="item-wrap">
                                <div class="img-wrapper">
                                    <img src="<?php echo esc_url(plugin_dir_url( dirname( __FILE__) ).'assets/admin/images/registration-form.jpg');?>"/>
                                </div>
                                <div class="content">
                                    <div class="content-top">
                                        <h3 class="extension-name"><?php echo esc_html('Member Registration Popup'); ?></h3>
                                        <div class="description">
                                            <p><?php echo esc_html('The Registration Popup feature enhances user registration by providing multiple flexible options for displaying and designing registration forms. This feature is designed to optimize user experience, improve engagement, and ensure security during the registration process.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="button-wrapper">
                                        <a href="<?php echo esc_url('https://gymbuilder.wpdreamers.com/addons/registration-page-popup/'); ?>" target="_blank"><?php echo esc_html('Buy Now'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="single-extension-item">
                            <div class="item-wrap">
                                <div class="img-wrapper">
                                    <img src="<?php echo esc_url(plugin_dir_url( dirname( __FILE__) ).'assets/admin/images/zoom-integration.jpg');?>"/>
                                </div>
                                <div class="content">
                                    <div class="content-top">
                                        <h3 class="extension-name"><?php echo esc_html('Zoom Integration'); ?></h3>
                                        <div class="description">
                                            <p><?php echo esc_html('The  Zoom Integration effortlessly for online classes with customizable badges, secure participant verification, and automated email notifications for meeting details.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="button-wrapper">
                                        <a href="<?php echo esc_url('https://gymbuilder.wpdreamers.com/addons/zoom-integration/'); ?>" target="_blank"><?php echo esc_html('Buy Now'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="single-extension-item">
                            <div class="item-wrap">
                                <div class="img-wrapper">
                                    <img src="<?php echo esc_url(plugin_dir_url( dirname( __FILE__) ).'assets/admin/images/class-booking.jpg');?>"/>
                                </div>
                                <div class="content">
                                    <div class="content-top">
                                        <h3 class="extension-name"><?php echo esc_html('Class Booking With Woocommerce Payment & Offline Payment'); ?></h3>
                                        <div class="description">
                                            <p><?php echo esc_html('Make class bookings simple with the Class Booking with WooCommerce Payment and Offline Payment add-on. Easily manage free and paid bookings, accept payments online or offline, and send automatic reminders to members about upcoming classes. Perfect for gyms and fitness centers, this tool makes bookings and payments hassle-free for both owners and members.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="button-wrapper">
                                        <a href="<?php echo esc_url('https://gymbuilder.wpdreamers.com/addons/class-booking-with-woo-payment-offline-payment/'); ?>" target="_blank"><?php echo esc_html('Buy Now'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

