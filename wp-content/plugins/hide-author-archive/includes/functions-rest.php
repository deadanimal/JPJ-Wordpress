<?php
/**
 * Handle user list on REST API.
 *
 * @package hide-author-archive
 */

/**
 * Hide author archive in REST API
 *
 * @param array           $args    Arguments for REST request.
 * @param WP_REST_Request $request Request object.
 * @return array
 */
function hide_author_archive_rest_query_filter( $args, $request ) {
	if ( ! hide_author_archive_in_rest() ) {
		return $args;
	}
	$capabilities = (array) apply_filters( 'hide_author_archive_rest_query_capability', [ 'list_users', 'edit_others_posts' ], $args, $request );
	foreach ( $capabilities as $capability ) {
		if ( current_user_can( $capability ) ) {
			return $args;
		}
	}
	$args['include'] = [ 0 ]; // False user's ID.
	return $args;
}
add_filter( 'rest_user_query', 'hide_author_archive_rest_query_filter', 10, 2 );

/**
 * Forbidden user request.
 *
 * @param WP_REST_Response|WP_Error|WP_HTTP_Response|mixed $response Response object.
 * @param array                                            $handler  Request handler.
 * @param WP_REST_Request                                  $request  Request object.
 * @return mixed
 */
function hide_author_archive_get_user( $response, $handler, $request ) {
	// Is GET request?
	if ( WP_REST_Server::READABLE !== $request->get_method() ) {
		return $response;
	}
	// Is user get endpoint?
	if ( ! preg_match( '#/wp/v2/users/(\d+)$#', $request->get_route(), $matches ) ) {
		return $response;
	}
	// Is option set?
	if ( ! hide_author_archive_in_rest() ) {
		return $response;
	}
	// In spite of user has a post or not,
	// Only editor can show user.
	$id = (int) $matches[1];
	if ( get_current_user_id() === $id || current_user_can( 'list_users' ) || current_user_can( 'edit_user', $id ) ) {
		return $response;
	}
	return new WP_Error(
		'rest_user_cannot_view',
		__( 'Sorry, you are not allowed to list users.' ),
		[
			'status' => rest_authorization_required_code(),
		]
	);
}
add_filter( 'rest_request_before_callbacks', 'hide_author_archive_get_user', 10, 3 );
