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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'eARqNslo}Q~WC_Byb]^f_30=wz&?`4tgZRZ<2bCW$nKC*n;5NIHgSWfw;@X|(_&j');
define('SECURE_AUTH_KEY',  'OfO!,()r%|[kH O%G`WC;|/|Q5u~{}#v7Q*-y^df$=AfpB&@-J~9H~t%oAS&b4l!');
define('LOGGED_IN_KEY',    '$g95%#$Zy#nJ&rJ;$;^OaRGfV{rE4=[b}K|5}E=2kIR~!UH~wY]tvSbS/DRMJf,c');
define('NONCE_KEY',        'Ku4azq(4+LghfxN$}G(v{POCBn0+ D&1~17}+YZ,#>_Jc3p[BUipR9^uj|@?]9Vi');
define('AUTH_SALT',        '}sRko%hU:x1h6A32K&V}3qyh0WWJznJ J#U#8e#ig[*$2j96&Ui.tXR!l_8)A7zP');
define('SECURE_AUTH_SALT', '5:c$:EKSM#K:AL4C|Ly)chnSfp;icrXwcNE17L#^Gd?G@Z&!6!}6Y8NA!]A-<7g2');
define('LOGGED_IN_SALT',   'I5)(Av}6y-c3(SpVg0e5?k:7z&MdI6<S LkRf9I1`{~g!u9R9D%.F_N[`QoKBBM(');
define('NONCE_SALT',       '8Nw6Z2=pz &>ReRj1!d!7wz{Z2WYwgV2KJ%0*jwCm| ~H2jm~!~XPsg[NAA,T]#!');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
