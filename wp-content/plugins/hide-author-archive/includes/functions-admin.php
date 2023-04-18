<?php
/**
 * Admin setting screen.
 *
 * @package hide-author-archive
 */

/**
 * Register admin fields.
 */
function hide_author_archive_fields() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}
	// Register settings section.
	add_settings_section( 'hide_author_archive', __( 'Hide Author Archive', 'hide-author-archive' ), function() {
		printf( '<p class="description">%s</p>', esc_html__( 'Author archive pages is hidden by default. You can set other page in which user\'s ID may revealed.', 'hide-author-archive' ) );
	}, 'reading' );
	// Register setting fields.
	add_settings_field( 'hide_author_archive_int_rest', __( 'REST API', 'hide-author-archive' ), function() {
		printf(
			'<label><input type="checkbox" value="1" name="%1$s" id="%1$s" %2$s /> %3$s </label>',
			'hide_author_archive_int_rest',
			checked( hide_author_archive_in_rest(), 1, false ),
			esc_html__( 'Hide authors list in REST API', 'hide-author-archive' )
		);
	}, 'reading', 'hide_author_archive' );
	register_setting( 'reading', 'hide_author_archive_int_rest' );
}
add_action( 'admin_init', 'hide_author_archive_fields' );

/**
 * Detect if filter author in rest api.
 *
 * @return string
 */
function hide_author_archive_in_rest() {
	return (string) get_option( 'hide_author_archive_int_rest', '1' );
}
