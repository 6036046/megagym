<?php

namespace GymBuilder\Inc\Controllers\Admin\Notice;

use GymBuilder\Inc\Abstracts\Discount;
use GymBuilder\Inc\Traits\SingleTonTrait;


if ( ! defined( 'ABSPATH' ) ) {
    exit( 'This script cannot be accessed directly.' );
}
class PersonalSessionNotice extends Discount {
    use SingleTonTrait;
    public function the_options(): array {
        return [
            'option_name'    => 'personal-session-notice',
            'global_check'   => isset( $GLOBALS['personal_session_notice'] ),
            'plugin_name'    => 'Gym Builder',
            'notice_for'     => 'Unlock Personal Session Pro Features Today!',
            'download_link'  => 'https://wpdreamers.com/',
            'start_date'     => '10 December 2023',
            'end_date'       => '30 January 2029',
            'notice_message' => 'Take your gym management to the next level with our Personal Session Pro Addon. Available now for only $19. Limited-time deal — grab it before it’s gone!',
        ];
    }
}