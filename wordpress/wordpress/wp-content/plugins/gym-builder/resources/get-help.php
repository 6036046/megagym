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
	.gb-help-wrapper {
		width: 99%;
        display:flex;
        align-items: flex-start;
		margin-top: 50px;
        gap:20px;
        flex-wrap:wrap;
	}
    .gb-help-content-wrapper{
        flex:0 0 48%;
    }
	.gb-document-box {
		background-color: #fff;
		-webkit-box-shadow: 0 1px 18px 0 rgba(0,0,0,.08);
		box-shadow: 0 1px 18px 0 rgba(0,0,0,.08);
		border-radius: 4px;
		padding: 30px 20px;
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
	}
	.gb-document-box .gb-box-icon {
		height: 30px;
		width: 30px;
		background-color: #ecf1ff;
		border-radius: 50%;
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-align: center;
		-ms-flex-align: center;
		align-items: center;
		-webkit-box-pack: center;
		-ms-flex-pack: center;
		justify-content: center;
		-ms-flex-line-pack: center;
		align-content: center;
		margin-right: 10px;
	}
	.gb-document-box .gb-box-icon i {
		font-size: 20px;
		color: #005dd0;
	}
	.gb-document-box .gb-box-content {
		-webkit-box-flex: 1;
		-ms-flex: 1;
		flex: 1;
		display:flex;
		flex-wrap:wrap;
	}
	.gb-document-box .gb-box-content.gb-feature-list{
		display:block;
	}
	.gb-document-box .gb-box-content .gb-box-title {
		margin: 0 0 12px 0;
		font-size: 20px;
		color: #000;
		font-weight: 600;
	}
	.gb-document-box+.gb-document-box {
		margin-top: 30px;
	}
	body .gb-admin-btn {
		text-align: center;
		display: inline-block;
		font-size: 15px;
		font-weight: 400;
		color: #fff;
        background: #005dd0;
		text-decoration: none;
		padding: 9px 18px;
		border-radius: 4px;
		position: relative;
		z-index: 2;
		line-height: 1.4;
		-webkit-transition: all .3s ease-in-out;
		transition: all .3s ease-in-out;
		height: auto;
		margin-top: auto;
	}
	body .gb-admin-btn:hover {
		background-color: #0a4b78;
		text-decoration: none;
	}
	.gb-help-section .embed-wrapper {
		position: relative;
		display: block;
		width: calc(100% - 40px);
		padding: 0;
		overflow: hidden;
	}
	.gb-help-section .embed-wrapper::before {
		display: block;
		content: "";
		padding-top: 56.25%;
	}
	.gb-help-section iframe {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border: 0;
	}
	.gb-help-wrapper .gb-document-box .gb-box-title {
		margin-bottom: 41px;
	}
	.gb-help-wrapper .gb-document-box .gb-box-icon {
		margin-top: -6px;
	}
	.gb-help-wrapper .gb-help-section {
		flex:0 0 48%;
	}

	.gb-feature-list ul {
		column-count: 2;
		column-gap: 30px;
		margin-bottom: 0;
	}
	.gb-feature-list ul li {
		padding: 0 0 12px;
		margin-bottom: 0;
		width: 100%;
		font-size: 14px;
	}
	.gb-feature-list ul li:last-child {
		padding-bottom: 0;
	}
	.gb-feature-list ul li i {
		color: #4C6FFF;
	}
	.gb-pro-feature-content {
		display: flex;
		flex-wrap: wrap;
	}
	.gb-pro-feature-content .gb-document-box + .gb-document-box {
		margin-left: 20px;
	}

	.gb-pro-feature-content .gb-document-box {
		flex: 0 0 calc(50% - 51px);
		margin-top: 20px;
	}
	.gb-testimonials {
		display: flex;
		flex-wrap: wrap;
	}
	.gb-testimonials .gb-testimonial + .gb-testimonial  {
		margin-left: 30px;
	}
	.gb-testimonials .gb-testimonial  {
		flex: 0 0 calc(50% - 30px);
		display:flex;
		flex-wrap:wrap;
	}
	.gb-testimonial .client-info {
		display: flex;
		flex-wrap: wrap;
		font-size: 14px;
		align-items: center;
	}
	.gb-testimonial .client-info img {
		width: 60px;
		height: 60px;
		object-fit: cover;
		border-radius: 50%;
		margin-right: 10px;
		border: 1px solid #ddd;
		-webkit-box-shadow: 0 1px 3px rgb(0, 0, 0, 0.2);
		box-shadow: 0 1px 3px rgb(0, 0, 0, 0.2);
	}
	.gb-testimonial .client-info .gb-star {
		color: #FFDF00;
	}
	.gb-testimonial .client-info .client-name {
		display: block;
		color: #000;
		font-size: 16px;
		font-weight: 600;
		margin: 8px 0 0;
	}
	.gb-call-to-action a {
		color: inherit;
		display: flex;
		flex-wrap: wrap;
		width: 100%;
		height: 100%;
		flex: 1;
		align-items: center;
		font-size: 28px;
		font-weight: 700;
		text-decoration: none;
		margin-left: 130px;
		position: relative;
		outline: none;
		-webkit-box-shadow: none;
		box-shadow: none;
	}
	.gb-call-to-action a::before {
		content: "";
		position: absolute;
		left: -30px;
		top: 50%;
		height: 30%;
		width: 5px;
		background: #fff;
		transform: translateY(-50%);
	}
	.gb-call-to-action:hover a {
		text-decoration: underline;
	}
	.gb-testimonial p {
		text-align: justify;
		flex-basis:100%;
        margin-bottom: 25px;
	}
    .gb-document-box p{
        margin-bottom: 20px;
        font-size: 16px;
    }
    .testimonial-box{
        min-height:212px;
    }
	@media (max-width:1440px) {
		.gb-help-wrapper .gb-help-section{
			flex:0 0 96%;
		}
		.gb-help-content-wrapper{
			flex:0 0 99%;
        }
	}
	@media all and (max-width: 1400px) {
		.gb-help-wrapper {
			width: 80%;
		}

	}
	@media all and (max-width: 1025px) {
		.gb-pro-feature-content .gb-document-box {
			flex: 0 0 calc(50% - 55px)
		}
		.gb-pro-feature-content .gb-document-box + .gb-document-box + .gb-document-box {
			margin-left: 0;
		}
	}
	@media all and (max-width: 991px) {
		.gb-help-wrapper {
			width: calc(100% - 40px);
		}
		.gb-call-to-action a {
			justify-content: center;
			margin-left: auto;
			margin-right: auto;
			text-align: center;
		}
		.gb-call-to-action a::before {
			content: none;
		}
	}
	@media all and (max-width: 600px) {
		.gb-document-box .gb-box-content .gb-box-title {
			line-height: 28px;
		}
		.gb-help-section .embed-wrapper {
			width: 100%;
		}
		.gb-feature-list ul {
			column-count: 1;
		}
		.gb-feature-list ul li {
			width: 100%;
		}
		.gb-call-to-action a {
			padding-left: 25px;
			padding-right: 25px;
			font-size: 20px;
			line-height: 28px;
			width: 80%;
		}
		.gb-testimonials {
			display: block;
		}
		.gb-testimonials .gb-testimonial + .gb-testimonial {
			margin-left: 0;
			margin-top: 30px;
			padding-top: 30px;
			border-top: 1px solid #ddd;
		}
		.gb-pro-feature-content .gb-document-box {
			width: 100%;
			flex: auto;
		}
		.gb-pro-feature-content .gb-document-box + .gb-document-box {
			margin-left: 0;
		}

		.gb-help-wrapper .gb-document-box {
			display: block;
			position: relative;
		}

		.gb-help-wrapper .gb-document-box .gb-box-icon {
			position: absolute;
			left: 20px;
			top: 30px;
			margin-top: 0;
		}

		.gb-document-box .gb-box-content .gb-box-title {
			margin-left: 45px;
		}
	}
</style>

<div class="gb-help-wrapper" >
	<div class="gb-help-section gb-document-box">
		<div class="gb-box-icon"><i class="dashicons dashicons-media-document"></i></div>
		<div class="gb-box-content">
			<h3 class="gb-box-title">Thank you for installing Gym Builder</h3>
			<div class="embed-wrapper">
				<iframe src="<?php echo esc_url( $iframe ); ?>" title="Shop Builder" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
	</div>
	<div class="gb-help-content-wrapper">
        <div class="gb-document-box testimonial-box">
            <div class="gb-box-icon"><i class="dashicons dashicons-thumbs-up"></i></div>
            <div class="gb-box-content">
                <h3 class="gb-box-title">Happy clients of the Gym Builder</h3>
                <div class="gb-testimonials">
                    <div class="gb-testimonial">
                        <p>Well organized, clean code, easy to use. I am really happy to use it.</p>
                        <div class="client-info">
                            <img src="<?php echo esc_url(plugin_dir_url( dirname( __FILE__) ).'assets/admin/images/client-1.jpeg');?>">
                            <div>
                                <div class="gb-star">
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                </div>
                                <span class="client-name">coderalamin</span>
                            </div>
                        </div>
                    </div>
                    <div class="gb-testimonial">
                        <p>really one of the best plugins out there thank you so much for creating this.</p>
                        <div class="client-info">
                            <img src="<?php echo esc_url(plugin_dir_url( dirname( __FILE__) ).'assets/admin/images/client-2.jpeg');?>">
                            <div>
                                <div class="gb-star">
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                </div>
                                <span class="client-name">theanonx</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="gb-pro-feature-content">
            <div class="gb-document-box">
                <div class="gb-box-icon"><i class="dashicons dashicons-sos"></i></div>
                <div class="gb-box-content">
                    <h3 class="gb-box-title">Need Help?</h3>
                    <p>Stuck with something? Please create a
                        <a href="<?php echo esc_url( $contact ); ?>">ticket here</a>.</p>
                    <a href="<?php echo esc_url( $contact ); ?>" target="_blank" class="gb-admin-btn">Get Support</a>
                </div>
            </div>
            <div class="gb-document-box">
                <div class="gb-box-icon"><i class="dashicons dashicons-smiley"></i></div>
                <div class="gb-box-content">
                    <h3 class="gb-box-title">Happy Our Work?</h3>
                    <p>If you are happy with <strong>Gym Builder</strong> plugin, please add a rating. It would be glad to us.</p>
                    <a href="<?php echo esc_url( $review ); ?>" class="gb-admin-btn" target="_blank">Post Review</a>
                </div>
            </div>
        </div>
    </div>
</div>
