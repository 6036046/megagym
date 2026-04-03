<?php
/**
 * De basisconfiguratie voor WordPress.
 *
 * Dit bestand bevat de volgende configuraties:
 * * Database-instellingen
 * * Geheime sleutels
 * * Database-tabelprefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 * @package WordPress
 */

// ** Database-instellingen - Deze gegevens krijg je van je webhoster ** //
/** De naam van de database voor WordPress */
define( 'DB_NAME', ' st1738846931' );

/** Database gebruikersnaam */
define( 'DB_USER', ' st1738846931' );

/** Database wachtwoord */
define( 'DB_PASSWORD', 'WkLv7naIMOukQQt' );

/** Database hostnaam */
define( 'DB_HOST', 'localhost' );

/** Database karakterset om te gebruiken bij het maken van tabellen. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Het database-collatietype. Verander dit niet als je twijfelt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authenticatie unieke sleutels en salts.
 *
 * Verander deze in unieke zinnen! Je kunt deze genereren via de 
 * WordPress.org secret-key service: https://api.wordpress.org/secret-key/1.1/salt/
 */
define( 'AUTH_KEY',         'a146f2763f306f1785a9e6a0a44846ad303d5382' );
define( 'SECURE_AUTH_KEY',  '1ffb53b4e8bb2a4036375787be37c3e2beddf9de' );
define( 'LOGGED_IN_KEY',    'e9b81eb3a03d21828469e8c0cd3ce4d79a3f0938' );
define( 'NONCE_KEY',        'c84034e23d93e585a7b451ef98ad7f4d17c5cc23' );
define( 'AUTH_SALT',        'e2905d2780358c017d41273d639cb6dd8dd2962d' );
define( 'SECURE_AUTH_SALT', '56d4209e3815d322047c79e27a7e491ec281c7ca' );
define( 'LOGGED_IN_SALT',   '2a25334c902fddd10705d907ba7c359cd9cd4186' );
define( 'NONCE_SALT',       '26c04e2d601f8c26c627f0adb5748306dc5a805b' );
/**#@-*/

/**
 * WordPress database tabel prefix.
 *
 * Je kunt meerdere installaties in één database hebben als je elke installatie
 * een unieke prefix geeft. Gebruik alleen cijfers, letters en underscores!
 */
$table_prefix = 'wp_';

/**
 * Voor ontwikkelaars: WordPress debug modus.
 *
 * Zet deze op true om notificaties te tonen tijdens het ontwikkelen.
 */
define( 'WP_DEBUG', false );

/* Voeg eventuele aangepaste waarden hieronder toe. */

// Detectie voor HTTPS achter een reverse proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
    $_SERVER['HTTPS'] = 'on';
}

/* Dat is alles, stop met bewerken! Veel plezier met publiceren. */

/** Absolute pad naar de WordPress-map. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** Stelt WordPress variabelen in en includeert bestanden. */
require_once ABSPATH . 'wp-settings.php';