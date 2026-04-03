<?php
/****
* Plugin Name:Gym Builder
* Plugin URI: https://gymbuilder.wpdreamers.com/
* Author: WPDreamers
* Author URI: https://wpdreamers.com/
* Description: The Best Gym Building Plugin for WordPress to Create Gym,Fitness,Body Building,Yoga Website
* Version: 2.3.2
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
* Text Domain:gym-builder
******/



if(! defined('ABSPATH')){
    die;
}

if(file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

if(class_exists('GymBuilder\\Inc\\Init')){
    GymBuilder\Inc\Init::register_services();
}
