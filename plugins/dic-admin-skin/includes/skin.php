<?php
/**
 * Admin skin: styles, per-user light/dark mode, admin bar toggle.
 *
 * @package DicAdminSkin
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_head', 'dic_admin_skin_print_mode_script', 1 );
add_action( 'admin_enqueue_scripts', 'dic_admin_skin_enqueue' );
add_filter( 'admin_body_class', 'dic_admin_skin_body_class' );
add_action( 'admin_bar_menu', 'dic_admin_skin_mode_node', 100 );
add_action( 'wp_ajax_dic_admin_skin_mode', 'dic_admin_skin_save_mode' );

/**
 * @return string One of auto, light, dark.
 */
function dic_admin_skin_user_mode(): string {
	$mode = get_user_meta( get_current_user_id(), DIC_ADMIN_SKIN_MODE_META, true );
	return in_array( $mode, array( 'light', 'dark' ), true ) ? $mode : 'auto';
}

/** The block editor keeps its own light styles, so dark mode is skipped there. */
function dic_admin_skin_is_block_editor(): bool {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	return $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor();
}

/** Runs first in <head> so the right theme is set before any CSS paints (no flash). */
function dic_admin_skin_print_mode_script(): void {
	$mode = dic_admin_skin_is_block_editor() ? 'light' : dic_admin_skin_user_mode();
	?>
	<script>
	(function(){try{var m=<?php echo wp_json_encode( $mode ); ?>,d=document.documentElement;
	var t=m==='auto'?(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'):m;
	d.setAttribute('data-dic-mode',m);d.setAttribute('data-dic-theme',t);}catch(e){}})();
	</script>
	<?php
}

function dic_admin_skin_enqueue(): void {
	$opts = dic_admin_skin_options();

	wp_enqueue_style( 'dic-admin-skin', DIC_ADMIN_SKIN_URL . 'assets/admin-skin.css', array(), dic_admin_skin_asset_ver( 'assets/admin-skin.css' ) );
	wp_add_inline_style(
		'dic-admin-skin',
		':root{--dic-brand:' . sanitize_hex_color( $opts['brand_color'] ) . ';}'
	);

	wp_enqueue_script( 'dic-admin-skin', DIC_ADMIN_SKIN_URL . 'assets/admin-skin.js', array(), dic_admin_skin_asset_ver( 'assets/admin-skin.js' ), true );
	wp_add_inline_script(
		'dic-admin-skin',
		'window.dicAdminSkin = ' . wp_json_encode(
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'dic_admin_skin_mode' ),
				'labels'  => array(
					'auto'  => __( 'Theme: Auto', 'dic-admin-skin' ),
					'light' => __( 'Theme: Light', 'dic-admin-skin' ),
					'dark'  => __( 'Theme: Dark', 'dic-admin-skin' ),
				),
			)
		) . ';',
		'before'
	);
}

/**
 * @param string $classes Space-separated admin body classes.
 */
function dic_admin_skin_body_class( string $classes ): string {
	$opts = dic_admin_skin_options();
	return $classes . ' dic-skin dic-sidebar-' . ( 'light' === $opts['sidebar'] ? 'light' : 'dark' );
}

/**
 * @param WP_Admin_Bar $bar Admin bar instance.
 */
function dic_admin_skin_mode_node( WP_Admin_Bar $bar ): void {
	if ( ! is_admin() ) {
		return;
	}
	$mode   = dic_admin_skin_user_mode();
	$labels = array(
		'auto'  => __( 'Theme: Auto', 'dic-admin-skin' ),
		'light' => __( 'Theme: Light', 'dic-admin-skin' ),
		'dark'  => __( 'Theme: Dark', 'dic-admin-skin' ),
	);
	$bar->add_node(
		array(
			'id'     => 'dic-mode-toggle',
			'parent' => 'top-secondary',
			'title'  => '<span class="ab-icon" aria-hidden="true"></span><span class="ab-label dic-mode-label">' . esc_html( $labels[ $mode ] ) . '</span>',
			'href'   => '#',
			'meta'   => array( 'title' => __( 'Switch between auto, light and dark', 'dic-admin-skin' ) ),
		)
	);
}

/** AJAX: store the per-user mode. Nonce + login check + whitelist. */
function dic_admin_skin_save_mode(): void {
	check_ajax_referer( 'dic_admin_skin_mode', 'nonce' );

	if ( ! current_user_can( 'read' ) ) {
		wp_send_json_error( null, 403 );
	}

	$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';
	if ( ! in_array( $mode, array( 'auto', 'light', 'dark' ), true ) ) {
		wp_send_json_error( null, 400 );
	}

	update_user_meta( get_current_user_id(), DIC_ADMIN_SKIN_MODE_META, $mode );
	wp_send_json_success( array( 'mode' => $mode ) );
}
