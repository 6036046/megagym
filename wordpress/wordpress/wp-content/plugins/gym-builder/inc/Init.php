<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc;

final class Init {
    /**
     * Store all classes inside an array
     * @return array full list of services
     */
    public static function get_services() {
        return [
           Base\Enqueue::class,
           Base\SettingsLink::class,
           Base\InitialControllers::class,
           Controllers\Admin\Models\Metabox\MetaBoxModel::class,
           Controllers\Admin\Settings\Pages\Dashboard::class,
        ];
    }

    /**
     * Loop through the classes,initialize theme,
     * and call the register() method if it exits
     */
    public static function register_services() {

        foreach ( self::get_services() as $class ) {
            $service = self::instantiate( $class );
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }

        }

    }

    /**
     * Initialize the class
     * @param class $class class from serivices array
     * @param class instance new instance of the class
     */
    private static function instantiate( $class ) {
        return new $class;
    }

}
