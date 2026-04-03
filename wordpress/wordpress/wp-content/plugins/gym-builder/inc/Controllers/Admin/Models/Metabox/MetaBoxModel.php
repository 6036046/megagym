<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Models\Metabox;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use GymBuilder\Inc\Controllers\Admin\Models\Metabox\RegisterPostMeta;

class MetaBoxModel{
    public $registerPostMeta;
    public function register(){
        RegisterPostMeta::getInstance();
    }
    
}