<?php

namespace GymBuilder\Inc\Controllers\Admin;

use GymBuilder\Inc\Traits\SingleTonTrait;
if ( ! defined( 'ABSPATH' ) ) exit;
class PluginSurvey{
    use SingleTonTrait;
    public string $textdomain = 'gym-builder';
    public function __construct() {
        add_action( 'admin_footer', [ $this, 'deactivation_popup' ], 99 );
    }
    public function deactivation_popup() {
        global $pagenow;
        if ( 'plugins.php' !== $pagenow ) {
            return;
        }

        $this->dialog_box_style();
        $this->deactivation_scripts();
        ?>
        <div id="deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?>" title="Quick Feedback">
            <!-- Modal content -->
            <div class="modal-content">
                <div id="feedback-form-body-<?php echo esc_attr( $this->textdomain ); ?>">
                    <div class="feedback-input-header">
                        <?php echo esc_html__( 'If you have a moment, please share why you are deactivating Gym Builder:', 'gym-builder' ); ?>
                    </div>

                    <div class="feedback-input-wrapper">
                        <input id="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-bug_issue_detected" class="feedback-input"
                               type="radio" name="reason_key" value="bug_issue_detected">
                        <label for="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-bug_issue_detected" class="feedback-label">Bug Or Issue detected.</label>
                    </div>

                    <div class="feedback-input-wrapper">
                        <input id="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-no_longer_needed" class="feedback-input" type="radio"
                               name="reason_key" value="no_longer_needed">
                        <label for="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-no_longer_needed" class="feedback-label">I no longer
                            need the plugin</label>
                    </div>
                    <div class="feedback-input-wrapper conditional">
                        <input id="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-found_a_better_plugin" class="feedback-input"
                               type="radio" name="reason_key" value="found_a_better_plugin">
                        <label for="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-found_a_better_plugin" class="feedback-label">I found a
                            better plugin</label>
                        <input class="feedback-feedback-text" type="text" name="reason_found_a_better_plugin"
                               placeholder="Please share the plugin name">
                    </div>
                    <div class="feedback-input-wrapper">
                        <input id="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-couldnt_get_the_plugin_to_work" class="feedback-input"
                               type="radio" name="reason_key" value="couldnt_get_the_plugin_to_work">
                        <label for="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-couldnt_get_the_plugin_to_work" class="feedback-label">I
                            couldn't get the plugin to work</label>
                    </div>

                    <div class="feedback-input-wrapper">
                        <input id="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-temporary_deactivation" class="feedback-input"
                               type="radio" name="reason_key" value="temporary_deactivation">
                        <label for="feedback-deactivate-<?php echo esc_attr( $this->textdomain ); ?>-temporary_deactivation" class="feedback-label">It's a
                            temporary deactivation</label>
                    </div>
                    <span style="color:red;font-size: 13px;"></span>
                </div>
                <p style="margin: 10px 0 15px 0;">
                    Please let us know about any issues you are facing with the plugin.
                    How can we improve the plugin?
                </p>
                <div class="feedback-text-wrapper-<?php echo esc_attr( $this->textdomain ); ?>">
					<textarea id="deactivation-feedback-<?php echo esc_attr( $this->textdomain ); ?>" rows="4" cols="40"
                              placeholder=" Write something here. How can we improve the plugin?"></textarea>
                    <span style="display: block;color:red;font-size: 13px;margin-top: 5px;"></span>
                </div>
            </div>
        </div>
        <?php
    }
    public function dialog_box_style() {
        ?>
        <style>
            /* Add Animation */
            @-webkit-keyframes animatetop {
                from {
                    top: -300px;
                    opacity: 0
                }
                to {
                    top: 0;
                    opacity: 1
                }
            }

            @keyframes animatetop {
                from {
                    top: -300px;
                    opacity: 0
                }
                to {
                    top: 0;
                    opacity: 1
                }
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> {
                display: none;
            }

            .ui-dialog-titlebar-close {
                display: none;
            }

            /* The Modal (background) */
            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal {
                display: none; 
                position: fixed; 
                z-index: 1; 
                padding-top: 100px;
                left: 0;
                top: 0;
                width: 100%; 
                height: 100%; 
                overflow: auto; 
            }

            /* Modal Content */
            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content {
                position: relative;
                margin: auto;
                padding: 0;
            }
            

            div#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> p {
                font-size: 15px;
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content > * {
                width: 100%;
                overflow: hidden;
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content textarea {
                border: 1px solid rgba(0, 0, 0, 0.3);
                padding: 15px;
                width: 100%;
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content textarea:focus {
                border-color: #2271b1;
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content input.feedback-feedback-text {
                border: 1px solid rgba(0, 0, 0, 0.3);
                min-width: 250px;
            }

            /* The Close Button */
            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> input[type="radio"] {
                margin: 0;
            }

            .ui-dialog-title {
                font-size: 18px;
                font-weight: 600;
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-body {
                padding: 2px 16px;
            }

            .ui-dialog-buttonset {
                background-color: #fefefe;
                padding: 0 17px 25px;
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }

            .ui-dialog-buttonset button {
                min-width: 110px;
                text-align: center;
                border: 1px solid rgba(0, 0, 0, 0.1);
                padding: 0 15px;
                border-radius: 5px;
                height: 40px;
                font-size: 15px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: 0.3s all;
                background: rgba(0, 0, 0, 0.02);
                margin: 0;
            }

            .ui-dialog-buttonset button:nth-child(2) {
                background: transparent;
            }

            .ui-dialog-buttonset button:hover {
                background: #2271b1;
                color: #fff;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?>"] {
                background-color: #fefefe;
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                z-index: 99;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?>"] .ui-dialog-title {
                text-transform: uppercase;
                font-weight: 700;
                font-size: 16px;
                padding-left: 15px;
                padding-right: 15px;
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> {
                padding: 30px !important;
            }

            #deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .feedback-input-header {
                font-weight: 600;
                font-size: 15px;
                line-height: 1.4;
                margin-bottom: 20px;
            }

            div#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?>,
            .ui-draggable .ui-dialog-titlebar {
                padding: 18px 15px;
                box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
                text-align: left;
            }

            div#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content .feedback-input-wrapper {
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 8px;
                /*line-height: 2;*/
                padding: 0 1px;
                font-size: 14px;
            }

            div#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content .feedback-input-wrapper.conditional {
                flex-wrap: wrap;
            }

            div#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content .feedback-input-wrapper.conditional .feedback-feedback-text {
                flex: 0 0 calc(100% - 24px);
                margin: 5px 0 10px 24px;
                min-height: 40px;
                border: 1px solid rgba(0, 0, 0, 0.3);
                padding: 0 15px;
            }

            div#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content .feedback-input-wrapper.conditional .feedback-feedback-text:focus {
                border-color: #2271b1;
            }

            .ui-widget-overlay.ui-front {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 999;
                background-color: rgba(0, 0, 0, 0.5);
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-dialog-buttonset {
                background-color: #fefefe;
                box-shadow: none;
                z-index: 99;
                padding-left: 30px;
                padding-right: 30px;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-dialog-buttonpane,
            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-widget-content {
                border: 0;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-resizable-handle {
                display: none !important;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-dialog-buttonset .ui-button {
                font-size: 14px;
                font-weight: 500;
                line-height: 1.2;
                padding: 8px 16px;
                outline: none;
                border: none;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-dialog-buttonset .ui-button:first-child {
                background: #005dd0;
                color: #fff;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-dialog-buttonset .ui-button:first-child:hover {
                background: #1f3edc;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-dialog-buttonset .ui-button:last-child {
                background: none;
            }

            .ui-dialog[aria-describedby="deactivation-dialog-gym-builder"] .ui-dialog-buttonset .ui-button:last-child:hover {
                background: #d80e0e;
                color: #fff;
            }
        </style>

        <?php
    }
    public function deactivation_scripts() {
        wp_enqueue_script( 'jquery-ui-dialog' );
        ?>
        <script>
            jQuery(document).ready(function ($) {

                // Open the deactivation dialog when the 'Deactivate' link is clicked
                $('.deactivate #deactivate-gym-builder').on('click', function (e) {
                    e.preventDefault();
                    var href = $('.deactivate #deactivate-gym-builder').attr('href');
                    $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content input[name="reason_found_a_better_plugin"]').hide();
                    var dialogbox = $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?>').dialog({
                        modal: true,
                        width: 550,
                        show: {
                            effect: "fadeIn",
                            duration: 400
                        },
                        hide: {
                            effect: "fadeOut",
                            duration: 100
                        },

                        buttons: {
                            Submit: function () {
                                submitFeedback();
                            },
                            Cancel: function () {
                                $(this).dialog('close');
                                window.location.href = href;
                            }
                        }
                    });


                    // Close the dialog when clicking outside of it
                    dialogbox.on('change', 'input[type="radio"]', function (event) {
                        var reasons = $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> input[type="radio"]:checked').val();
                        if( 'found_a_better_plugin' === reasons ){
                            $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content input[name="reason_found_a_better_plugin"]').show();
                        } else {
                            $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content input[name="reason_found_a_better_plugin"]').hide();
                        }
                    });

                    // Close the dialog when clicking outside of it
                    $(document).on('click', '.ui-widget-overlay.ui-front', function (event) {
                        if ($(event.target).closest(dialogbox.parent()).length === 0) {
                            dialogbox.dialog('close');
                        }
                    });

                    // Customize the button text
                    $('.ui-dialog-buttonpane button:contains("Submit")').text('Submit & Deactivate');
                    $('.ui-dialog-buttonpane button:contains("Cancel")').text('Skip & Deactivate');
                });

                // Submit the feedback
                function submitFeedback() {
                    var href = $('.deactivate #deactivate-gym-builder').attr('href');
                    var reasons = $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> input[type="radio"]:checked').val();
                    var feedback = $('#deactivation-feedback-<?php echo esc_attr( $this->textdomain ); ?>').val();
                    var better_plugin = $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?> .modal-content input[name="reason_found_a_better_plugin"]').val();
                    // Perform AJAX request to submit feedback
                    if (!reasons && !feedback && !better_plugin) {
                        // Define flag variables
                        $('#feedback-form-body-<?php echo esc_attr( $this->textdomain ); ?> span').text('Choose The Reason');
                        $('.feedback-text-wrapper-<?php echo esc_attr( $this->textdomain ); ?> span').text('Please provide me with some advice.');
                        return;
                    }

                    if (!reasons ) {
                        // Define flag variables
                        $('#feedback-form-body-<?php echo esc_attr( $this->textdomain ); ?> span').text('Choose The Reason');
                        $('.feedback-text-wrapper-<?php echo esc_attr( $this->textdomain ); ?> span').text('Please provide me with some advice.');
                        return;
                    }

                    if ( 'bug_issue_detected' === reasons && !feedback ) {
                        // Define flag variables
                        $('.feedback-text-wrapper-<?php echo esc_attr( $this->textdomain ); ?> span').text('Please provide more details regarding the issue so we can address it in future updates.');
                        return;
                    }

                    if ('temporary_deactivation' === reasons && !feedback) {
                        window.location.href = href;
                        return;
                    }

                    $.ajax({
                        url: 'https://wpdreamers.com/wp-json/GymBuilder/pluginSurvey/v1/Survey/appendToSheet',
                        method: 'GET',
                        dataType: 'json',
                        data: {
                            website: '<?php echo esc_url( home_url() ); ?>',
                            reasons: reasons ? reasons : '',
                            better_plugin: better_plugin,
                            feedback: feedback,
                            wpplugin: 'GymBuilder',
                            date: new Date().toISOString().split('T')[0]
                        },
                        success: function (response) {
                        },
                        error: function (xhr, status, error) {
                            // Handle the error response

                            console.error('Error', error);
                        },
                        complete: function (xhr, status) {
                            $('#deactivation-dialog-<?php echo esc_attr( $this->textdomain ); ?>').dialog('close');
                            window.location.href = href;
                        }

                    });
                }

            });

        </script>

        <?php
    }
}