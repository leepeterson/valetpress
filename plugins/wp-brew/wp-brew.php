<?php
require_once( ABSPATH . "wp-includes/pluggable.php" );
/**
 * Plugin Name:     WP-Brew DevTool
 * Plugin URI:      https://www.rawlemurdy.com
 * Description:     Specific functions used by WP-Brew <b>NOT TO BE USED IN PRODUCTION</b>.
 * Author:          Rawle Murdy Associates
 * Author URI:      https://www.rawlemurdy.com
 * Version:         0.0.9
 *
 * @package         wpb
 * @since           20190929
 * @author          lpeterson
 */

/**
 * []
 *
 * @since 0.0.1
 *
 * @package wpb
 */
function wpb_header_function() {
	print(
		'<meta name="robots" content="noodp,noydir">'."\n".
		'<meta http-equiv="x-dns-prefetch-control" content="on">'."\n".
		'<link rel="dns-prefetch" href="//www.google-analytics.com">'."\n".
		'<link rel="dns-prefetch" href="//fonts.googleapis.com">'."\n".
		'<link rel="dns-prefetch" href="//ajax.googleapis.com">'."\n".
		'<link rel="dns-prefetch" href="' . get_site_url() . '">'."\n".
		'<link rel="canonical" href="' . get_site_url() . '">'."\n"
	);
}
add_action('wp_head','wpb_header_function');

/**
 * Enable gzip compression with brotli
 *
 * @since 0.0.1
 *
 * @package wpb
 */
function wpb_http_compression() {
	// Dont use on Admin HTML editor
	if (stripos($uri, '/js/tinymce') !== false)
		return false;

	// Check if ob_gzhandler already loaded
	if (ini_get('output_handler') == 'ob_gzhandler')
		return false;

	// Load HTTP Compression if correct extension is loaded
	if (extension_loaded('zlib'))
			if(!ob_start("ob_gzhandler")) ob_start();
}
add_action('init', 'wpb_http_compression');

/**
 * Remove JS/CSS versions
 *
 * @since 0.0.1
 *
 * @package wpb
 */
function wpb_remove_cssjs_ver( $src ) {
	if( strpos( $src, '?ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}
add_filter( 'style_loader_src', 'wpb_remove_cssjs_ver', 9999 );
add_filter( 'script_loader_src', 'wpb_remove_cssjs_ver', 9999 );

/**
 * Remove WP Version from header and feed
 *
 * @since 0.0.1
 *
 * @package wpb
 */
add_filter('the_generator', function() {
	return '';
});

/**
 * Reduce image resolution
 *
 * @since 0.0.1
 *
 * @package wpb
 */
add_filter('jpeg_quality', function( $arg ) {
	return 80;
});

/**
 * Sharpen resized jpeg images
 *
 * @since 0.0.1
 *
 * @package wpb
 * @see http://wpsnipp.com/index.php/functions-php/sharpen-resized-wordpress-uploaded-images-jpg/
 *
 */
function wpb_sharpen_resized_file( $resized_file ) {
	$image = wp_load_image( $resized_file );
	if ( !is_resource( $image ) )
		return new WP_Error( 'error_loading_image', $image, $file );
	$size = @getimagesize( $resized_file );
	if ( !$size )
		return new WP_Error('invalid_image', __('Could not read image size'), $file);
	list($orig_w, $orig_h, $orig_type) = $size;
	switch ( $orig_type ) {
		case IMAGETYPE_JPEG:
			$matrix = array(
				array(-1, -1, -1),
				array(-1, 16, -1),
				array(-1, -1, -1),
			);
			$divisor = array_sum(array_map('array_sum', $matrix));
			$offset   = 0;
			imageconvolution($image, $matrix, $divisor, $offset);
			imagejpeg($image, $resized_file, apply_filters( 'jpeg_quality', 80, 'edit_image' ));
			break;
		case IMAGETYPE_PNG:
			return $resized_file;
		case IMAGETYPE_GIF:
			return $resized_file;
	}
	return $resized_file;
}
add_filter('image_make_intermediate_size', 'wpb_sharpen_resized_file', 999);

/**
 * Automatically expire transients
 *
 * Provided courtesy of PressJitsu
 *
 * @since 0.0.1
 *
 * @package wpb
 */
class wpb_Transient_Cleaner {
		public static function load() {
			add_action( 'init', array( __CLASS__, 'schedule_events' ) );
		}

		/**
		 * Schedule cron events, runs during init.
		 */
		public static function schedule_events() {
			if ( ! wp_next_scheduled( 'pj_transient_cleaner' ) )
				wp_schedule_event( time(), 'daily', 'pj_transient_cleaner' );

			add_action( 'pj_transient_cleaner', array( __CLASS__, 'cleaner' ) );
		}

		/**
		 * Runs in a wp-cron intsance.
		 */
		public static function cleaner() {
			global $wpdb;

			$timestamp = time() - 24 * HOUR_IN_SECONDS; // expired x hours ago.
			$time_start = time();
			$time_limit = 30;
			$batch = 100;

			// @todo Look at site transients too.
			// Don't take longer than $time_limit seconds.
			while ( time() < $time_start + $time_limit ) {
				$option_names = $wpdb->get_col( "SELECT `option_name` FROM {$wpdb->options} WHERE `option_name` LIKE '\_transient\_timeout\_%'
					AND CAST(`option_value` AS UNSIGNED) < {$timestamp} LIMIT {$batch};" );

				if ( empty( $option_names ) )
					break;

				// Add transient keys to transient timeout keys.
				foreach ( $option_names as $key => $option_name )
					$option_names[] = '_transient_' . substr( $option_name, 19 );

				// Create a list to use with MySQL IN().
				$options_in = implode( ', ', array_map( function( $item ) use ( $wpdb ) {
					return $wpdb->prepare( '%s', $item );
				}, $option_names ) );

				// Delete transient and transient timeout fields.
				$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` IN ({$options_in});" );

				// Break if no more deletable options available.
				if ( count( $option_names ) < $batch * 2 )
					break;
			}
		}
}
wpb_Transient_Cleaner::load();

/**
 * Add Livereload.js to header function
 *
 * @since 0.0.1
 *
 * @package wpb
 */
function wpb_livereload_add() {
	echo '<script src="'.get_site_url().':35729/livereload.js" defer></script>';
}
add_action('wp_head', 'wpb_livereload_add');

/**
 * Automatically logs in a visitor when accessing the admin login area
 *
 * Whitelist: Add IP addresses to $ip_whitelist. If empty, all IPs are allowed.
 * Username:  Specify the login username in the "user" GET parameter
 *            (e.g. ?user=admin). If "user" is not set, the value of
 *            $default_user_login will be used instead. If "*" is set, the first
 *            administrator found in wp_users is used. If logged out, or already
 *            logged in, nothing special will occur.
 *
 * @since 0.0.1
 * @author Jackson Cooper <jackson@jacksonc.com>
 * @copyright Copyright (c) 2014, Jackson Cooper
 * @license MIT
 * @package wpb
 */
function automatic_user_login() {
	// Already logged in, not necessary
	if ( is_user_logged_in() ) {
		wp_redirect( admin_url() );
		return;
	}

	// IP whitelist. If this is empty, whitelisting will be disabled.
	$ip_whitelist = [ '127.0.0.1', '::1' ];

	// Default user to login as.
	// If this is "*", the first administrator user will be used.
	// If the "user" GET parameter is set, this will be used.
	$default_user_login = '*';

	//
	$ip_blocked = ( !empty( $ip_whitelist ) and !in_array( $_SERVER['REMOTE_ADDR'], $ip_whitelist ) );

	//
	$user_logged_out = ( isset( $_GET['loggedout'] ) and $_GET['loggedout'] === 'true' );

	// IP not whitelisted or User just logged out
	if ( $ip_blocked or $user_logged_out )
		return;

	// Fetch the user to login as, if it exists
	$user_login = ( isset( $_GET['user'] ) ) ? $_GET['user'] : $default_user_login;

	if ( $user_login === '*' ) {
		$user = current( get_users( [ 'role' => 'administrator' ] ) );

		if ( $user === false )
			wp_die( __( 'ERROR: No admin users exist.' ) );
	}
	else {
		$user = get_user_by( 'login', $user_login );
		if ( $user === false ) {
			$admin_users = get_users( [ 'role' => 'administrator' ] );

			$admin_users_atr = implode( ', ', array_map( function( $admin_user ) {
				return $admin_user->data->user_login;
			}, $admin_users ) );

			wp_die( __( 'ERROR: User '.$user_login.' does not exist. Other administrators: '.$admin_users_atr ) );
		}
	}

	// Login as $user and re-load / re-direct to the admin page
	$user_id = $user->ID;
	wp_set_current_user( $user_id, $user->user_login );
	wp_set_auth_cookie( $user_id, true );
	do_action( 'wp_login', $user->user_login );
	wp_redirect( admin_url() );
}
add_action( 'login_init', 'automatic_user_login' );
add_action( 'after_setup_theme', 'automatic_user_login' );
