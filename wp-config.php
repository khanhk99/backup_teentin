<?php

/**

 * The base configuration for WordPress

 *

 * The wp-config.php creation script uses this file during the

 * installation. You don't have to use the web site, you can

 * copy this file to "wp-config.php" and fill in the values.

 *

 * This file contains the following configurations:

 *

 * * MySQL settings

 * * Secret keys

 * * Database table prefix

 * * ABSPATH

 *

 * @link https://wordpress.org/support/article/editing-wp-config-php/

 *

 * @package WordPress

 */


// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', "dan63356_teentin" );


/** MySQL database username */

define( 'DB_USER', "dan63356_teentin" );


/** MySQL database password */

define( 'DB_PASSWORD', "Khanhk99@tn.ptit" );


/** MySQL hostname */

define( 'DB_HOST', "localhost:3306" );


/** Database Charset to use in creating database tables. */

define( 'DB_CHARSET', 'utf8mb4' );


/** The Database Collate type. Don't change this if in doubt. */

define( 'DB_COLLATE', '' );


/**#@+

 * Authentication Unique Keys and Salts.

 *

 * Change these to different unique phrases!

 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}

 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.

 *

 * @since 2.6.0

 */

define( 'AUTH_KEY',         'vbUx4W8?SO@;@sEm/Us!#&ex0mEI60s5lW$P9LQhfAoM.#-QacxAV$5Q{m8R=+hl' );

define( 'SECURE_AUTH_KEY',  'FQbEHcvT0KO~5nt&!x-nQ0xB3J@8T)s8n#USUd{t*>V~IPPt(f44*/Hr+gr=$S=7' );

define( 'LOGGED_IN_KEY',    'VBc,7vYMu/Xm?gs<<4Hm`m+=^jC`#lr9^URPs^2GxMv[Dv@FS<Kz[0*p.Fa>11Th' );

define( 'NONCE_KEY',        '(/T.uXSh><y89+oC?C&|ckZ_gqtY5YvhO<4|d`z]K17}A#SL7^Pn9kwfq- :j9s+' );

define( 'AUTH_SALT',        'LWKI$Z<:&R}m?1YB;U<-_00KAI<2,(LT;Y1Yl,%=8__8 d*J+yWB1bwoo9pA6n9c' );

define( 'SECURE_AUTH_SALT', ':7_^}5/@~jY6)/|dy :zX.%p<q*w*Aj6dogml7,/GUs*Q%T^>I@Yk7;I-?+^~:^H' );

define( 'LOGGED_IN_SALT',   'zYzI57:{}Qg&M!-FeyVxM!Oh5pL)+Za._t7m)!cU$@r.(0Pdv{*Ji2[;zP*$cq8$' );

define( 'NONCE_SALT',       'i&brbLtG]+jC7ehj?TezgC.ZIQ&P=(I4,4bvF|R/xT%BAnI&-$_pV[c9+[53V/E,' );


/**#@-*/


/**

 * WordPress Database Table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'khanhk9_';


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


/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}


/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';


@ini_set('max_input_vars',4000);