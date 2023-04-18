<?php
/**
 * Avoid author rewrite rules to be included.
 *
 * @package hide-author-archive
 */

// Avoid direct access.
defined( 'ABSPATH' ) || die();

// Remove rewrite rules.
add_filter( 'author_rewrite_rules', '__return_empty_array' );

/**
 * Stop canonical redirect
 *
 * @param string $redirect_url  URL to which users will be redirect.
 * @param string $requested_url Original URL.
 *
 * @return string
 */
function hide_author_archive_canonical( $redirect_url, $requested_url ) {
	if ( ! is_author() ) {
		return $redirect_url;
	}
	$author_query = filter_input( INPUT_GET, 'author' );
	if ( ! empty( $author_query ) && preg_match( '|^[0-9]+$|', $author_query ) ) {
		$redirect_url = false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'hide_author_archive_canonical', 10, 2 );

/**
 * Remove author query vars.
 *
 * @param string[] $vars Query vars.
 * @return string[]
 */
function hide_author_archive_query_var( $vars ) {
	if ( is_admin() ) {
		return $vars;
	}
	$new_vars = [];
	foreach ( $vars as $var ) {
		if ( ! in_array( $var, [ 'author_name', 'author' ], true ) ) {
			$new_vars[] = $var;
		}
	}

	return $new_vars;
}
add_filter( 'query_vars', 'hide_author_archive_query_var' );
