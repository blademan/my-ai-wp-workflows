<?php
/**
 * Plugin Name:       Admin Skin
 * Description:       Clean, professional wp-admin restyle with light/dark mode, custom login screen and optional white-labelling for client users.
 * Version:           1.0.0
 * Requires at least: 6.9
 * Requires PHP:      8.2
 * Author:            Design in DC
 * License:           GPL-2.0-or-later
 * Text Domain:       dic-admin-skin
 *
 * @package DicAdminSkin
 */

defined( 'ABSPATH' ) || exit;

define( 'DIC_ADMIN_SKIN_VERSION', '1.0.0' );
define( 'DIC_ADMIN_SKIN_FILE', __FILE__ );
define( 'DIC_ADMIN_SKIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DIC_ADMIN_SKIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DIC_ADMIN_SKIN_OPTION', 'dic_admin_skin_options' );
define( 'DIC_ADMIN_SKIN_MODE_META', 'dic_admin_skin_mode' );

require_once DIC_ADMIN_SKIN_DIR . 'includes/settings.php';
require_once DIC_ADMIN_SKIN_DIR . 'includes/skin.php';
require_once DIC_ADMIN_SKIN_DIR . 'includes/branding.php';

/**
 * Default option values.
 *
 * @return array<string, mixed>
 */
function dic_admin_skin_defaults(): array {
	return array(
		'brand_color'     => '#2563eb',
		'logo_id'         => 0,
		'login_bg'        => '#f3f4f6',
		'footer_text'     => '',
		'sidebar'         => 'dark',
		'clean_dashboard' => 1,
		'whitelabel'      => 0,
		'agency_domain'   => '',
	);
}

/**
 * Saved options merged over defaults.
 *
 * @return array<string, mixed>
 */
function dic_admin_skin_options(): array {
	$saved = get_option( DIC_ADMIN_SKIN_OPTION, array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), dic_admin_skin_defaults() );
}

/**
 * Agency users keep the full, unbranded admin. Everyone else is a "client".
 * With no agency domain configured, everybody counts as agency (white-label does nothing).
 */
function dic_admin_skin_is_agency_user(): bool {
	$opts   = dic_admin_skin_options();
	$domain = (string) $opts['agency_domain'];
	if ( '' === $domain ) {
		return true;
	}
	$user   = wp_get_current_user();
	$result = $user && $user->exists() && str_ends_with( strtolower( $user->user_email ), '@' . $domain );
	return (bool) apply_filters( 'dic_admin_skin_is_agency_user', $result, $user );
}
