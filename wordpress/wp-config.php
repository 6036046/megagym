<?php
/**
* The base configuration for WordPress
*
* The wp-config.php creation script uses this file during the installation.
* You don't have to use the website, you can copy this file to "wp-config.php"
* and fill in the values.
*
* This file contains the following configurations:
*
* * Database settings
* * Secret keys
* * Database table prefix
* * ABSPATH
*
* @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
*
* @package WordPress
*/

 
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'st1738846931' );
 
/** Database username */
define( 'DB_USER', 'st1738846931' );
 
/** Database password */
define( 'DB_PASSWORD', 'WkLv7naIMOukQQt' );
 
/** Database hostname */
define( 'DB_HOST', 'localhost' );
 
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
 
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
 
/**#@+
* Authentication unique keys and salts.
*
* Change these to different unique phrases! You can generate these using
* the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
*
* You can change these at any point in time to invalidate all existing cookies.
* This will force all users to have to log in again.
*
* @since 2.6.0
*/
define( 'AUTH_KEY',         'dDPgsn45^cnW?gpd^-Z8gZw/Kg>xRBOFPvGL;XmL:*Vlg23{QM48}XMrPZ3U9~|Q' );
define( 'SECURE_AUTH_KEY',  'F)Fs+Ij}omYwO^R2INmWt5SYtD#Nff^S&rK=(J82#)1GQ.()Q2)RpKZ-cYR!5>:l' );
define( 'LOGGED_IN_KEY',    '{J) Dj(,zt%0(Lc|#qJ$59qm%7MXCn_636IDc`O;#t?yiqgUt(V9y|<X&Uf/nm-!' );
define( 'NONCE_KEY',        'e@4E[R03@5:8CAX8a3G]&Ew|M&p&jug|&/9,sA~jn8[S*B:0vkHp6ls[z>w<!O:?' );
define( 'AUTH_SALT',        '[F;a^d%DM_H)-&$fvpktK ~/r`gp,Qs48Yokj!7=Ax3_e?xPh*xuFuZ1yLS^J+QO' );
define( 'SECURE_AUTH_SALT', ']/~|l@Ug6`Tugn^^6e{mjq 2G#>,Y%?`z+XQA|-)<&o=_7Jm<J17N<c 1V#e5nA!' );
define( 'LOGGED_IN_SALT',   '<JKHoGOw&ezF%c_k]MUhY7yC-~@[reBl,RCGyQn-[?756G_#GD+:Zn1LU4-A=KWR' );
define( 'NONCE_SALT',       'F:{1Ymq~:zR;Wnqr-C2,kkD{xsENxzV$~JqI0e@WlQCL38f1wzgy5A}K8%]?)z%<' );
 
/**#@-*/
 
/**
* WordPress database table prefix.
*
* You can have multiple installations in one database if you give each
* a unique prefix. Only numbers, letters, and underscores please!
*
* At the installation time, database tables are created with the specified prefix.
* Changing this value after WordPress is installed will make your site think
* it has not been installed.
*
* @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
*/
$table_prefix = 'wp_';
 
/**
* For developers: WordPress debugging mode.
*
* Change this to true to enable the display of notices during development.
* It is strongly recommended that plugin and theme developers use WP_DEBUG
* in their development environments.
*
* For information on other constants that can be used for debugging,
* visit the documentation.
*
* @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
*/
define( 'WP_DEBUG', false );
 
/* Add any custom values between this line and the "stop editing" line. */
 
 
/* That's all, stop editing! Happy publishing. */
 
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
 
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';