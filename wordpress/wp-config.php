<?php
define( 'WP_CACHE', true );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'shop' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'yV[/]ps]__z|W2lr~sB7k@W5N26(OfzkfW&Y +hrn4kCcAb+:?!~H. _;{!{h6q[' );
define( 'SECURE_AUTH_KEY',  'Wbff[UTS(qu-g+D+TnC6i~+8+VSA(oK*$Gg<ssR~k-i&E)=AK~{=TOqzarP^Bsq9' );
define( 'LOGGED_IN_KEY',    'Bm^=BIz*-&F[AFO}T~).LAl6U!^=Eun,7]2 CJ_I]9N@v /Whp; 7E|r]WqbGGUT' );
define( 'NONCE_KEY',        '>qIl:]P oixre]u bNwMom6O#M7:(20i%: O*6R3GR#_toL(zEq?>s1`{~gb(iT;' );
define( 'AUTH_SALT',        'lT`6J/wpoF^iT3 M$u{g!4~j Nl/81f+Paf`{< j?C)3Py:,TZ8d9.C6n}QTa1(5' );
define( 'SECURE_AUTH_SALT', 'Kb[fcnL|>;5ABVS+M056z:p/@g`g@V87BiuR%@JN04|UV,*(,0LC}e@;Ic &sQ9r' );
define( 'LOGGED_IN_SALT',   '*&gR.69KjwzLy3=n8Pmh@*06#.+l4(swLQ5}j ]]0ybToKkZbm*N521ltIOxyW%t' );
define( 'NONCE_SALT',       'SaIn7e+poh>OW]v],5.q/PJLE5Jj~f9)yg[yw64^QdvR]e~jzo1y(5^%0I@c^UJt' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
