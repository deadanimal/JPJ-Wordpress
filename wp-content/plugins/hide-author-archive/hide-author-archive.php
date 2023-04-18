<?php
/**
 * Plugin Name: Hide Author Archive
 * Plugin URI:  https://github.com/kuno1/hide-author-archive
 * Description: Hide author archive pages.
 * Version:     1.1.6
 * Author:      Kunoichi INC.
 * Author URI:  https://kunoichiwp.com
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 * Text Domain: hide-author-archive
 * Domain Path: languages
 *
 * @package hide-author-archive
 */

// This file actually do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

// Load autoloader if exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * Load i18n.
 */
function hide_author_archive_i18n() {
	load_plugin_textdomain( 'hide-author-archive', false, basename( __DIR__ ) . '/languages' );
}
add_action( 'plugins_loaded', 'hide_author_archive_i18n' );

// Load includes.
require __DIR__ . '/includes/functions-rewrite.php';
require __DIR__ . '/includes/functions-rest.php';
require __DIR__ . '/includes/functions-admin.php';

/**
 * Flush rewrite rules.
 */
function hide_author_archive_activation_hook() {
	flush_rewrite_rules( false );
}
register_activation_hook( __FILE__, 'hide_author_archive_activation_hook' );
register_deactivation_hook( __FILE__, 'hide_author_archive_activation_hook' );
