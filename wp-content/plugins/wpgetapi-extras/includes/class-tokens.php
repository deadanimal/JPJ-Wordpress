<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The main class
 *
 * @since 1.0.0
 */
class WpGetApi_Extras_Tokens {

    // this is so we can use ajax from CF7
    public $user_id;

    /**
     * Main constructor
     *
     * @since 1.0.0
     *
     */
    public function __construct() {

        add_filter( 'wpgetapi_endpoint', array( $this, 'endpoint_tokens' ), 5, 2 );
        add_filter( 'wpgetapi_query_parameters', array( $this, 'query_string_tokens' ), 5, 2 );
        add_filter( 'wpgetapi_header_parameters', array( $this, 'header_tokens' ), 5, 2 );
        add_filter( 'wpgetapi_body_parameters', array( $this, 'body_tokens' ), 5, 2 );
        require_once ( ABSPATH . '/wp-load.php' );
    }


    public function replace_tokens( $text ) {

        $text = $this->replace_system_token( $text );
        $text = $this->replace_date_token( $text );
        $text = $this->replace_post_token( $text );
        $text = $this->replace_user_token( $text );

        return $text;

    }


    /*
     * User Tokens
     * Format: (type:id of user:user field|format for date_registered or an array key)
     * Examples: (user:current:first_name), (user:2:date_registered|y-m-d), (user:2:some_meta_field_array|array_key_name)
     * 
     */
    public function replace_user_token( $text ) {

        $type = '(user:';

        if ( strpos( $text, $type ) !== false ) {

            $user_id = $this->user_id ? $this->user_id : get_current_user_id();

            $filter = null;

            // remove the start & end
            $text = strstr( $text, $type ); // remove anything before type
            $text = str_replace( $type, '', $text );  // remove type
            $text = rtrim( $text, ')' ); // remove last bracket

            // extract the filter if any (date filter)
            if ( strpos( $text, '|' ) !== false ) 
                $filter = (string) substr( $text, strpos( $text, "|" ) + 1 ); 

            // remove the filter pipe
            if( $filter || $filter === '0' )
                $text = str_replace( '|' . $filter, '', $text );

                      
            // get sub keys
            $keys = explode( ':', $text );
            
            // send errors
            if( count( $keys ) == 1 )
                return 'Missing a token parameter.';

            if( count( $keys ) >= 3 )
                return 'Too many token parameters.';

            // get our id - it must only using 2 params
            if( count( $keys ) == 2 ) {
                $user_id = $keys[0] === 'current' ? $user_id : absint( $keys[0] );
                $text = $keys[1];
            }

            $user = get_userdata( $user_id );
            $user = isset( $user->data ) ? get_object_vars( $user->data ) : null;

            if( $user === null )
                return 'No user found';

            // date fields with date format filter
            if( $filter && in_array( $text, array( 'user_registered' ) ) )
                return date( $filter, strtotime( $user[ $text ] ) );

            // standard user fields
            if( isset( $user[ $text ] ) )
                return wp_kses_post( $user[ $text ] );

            // ignore password
            if( $text == 'user_pass' )
                return 'Password not allowed.';

            // try for a meta field
            $meta = get_user_meta( $user['ID'], $text, true );
            if( $meta && ( $filter || $filter === '0' ) ) {
                $text = maybe_unserialize( $meta );
                if( $text ) {
                    $text = $text[ $filter ];
                    return wp_kses_post( $text );
                }
            }

            if( $meta && ! $filter )
                return wp_kses_post( $meta );


        }

        return (string) $text;

    }

    /*
     * Post Tokens
     * Format: (type:id of post:post field|format for date)
     * Examples: (post:current:post_title), (post:current:ID), (post:453:post_date|Y-m-d), (post:453:_yoast_wpseo_metadesc)
     * 
     */
    public function replace_post_token( $text ) {

        $type = '(post:';

        if ( strpos( $text, $type ) !== false ) {

            // remove the start & end
            $text = strstr( $text, $type ); // remove anything before type
            $text = str_replace( $type, '', $text );  // remove type
            $text = rtrim( $text, ')' ); // remove last bracket

            // extract the filter if any (date filter)
            if ( strpos( $text, '|' ) !== false ) 
                $filter = substr( $text, strpos( $text, "|" ) + 1 ); 

            // remove the filter pipe
            if( $filter )
                $text = str_replace( '|' . $filter, '', $text );

            // get sub keys
            $keys = explode( ':', $text );
            
            // send errors
            if( count( $keys ) == 1 )
                return 'Missing a token parameter.';

            if( count( $keys ) >= 3 )
                return 'Too many token parameters.';

            // get our id - it must only using 2 params
            if( count( $keys ) == 2 ) {
                $post_id = $keys[0] === 'current' ? get_the_ID() : absint( $keys[0] );
                $text = $keys[1];
            }

            $post = get_post( $post_id, ARRAY_A );

            // date fields with date format filter
            if( $filter && in_array( $text, array( 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt' ) ) )
                return date( $filter, strtotime( $post[ $text ] ) );
            
            // permalink
            if( $text == 'permalink' )
                return get_the_permalink( $post['ID'] );

            // standard post fields
            if( isset( $post[ $text ] ) )
                return wp_kses_post( $post[ $text ] );

            // try for a meta field
            $meta = get_post_meta( $post['ID'], $text, true );
            if( $meta && ( $filter || $filter === '0' ) ) {
                $text = maybe_unserialize( $meta );
                if( $text ) {
                    $text = $text[ $filter ];
                    return wp_kses_post( $text );
                }
            }

            if( $meta && ! $filter )
                return wp_kses_post( $meta );

        }

        return (string) $text;

    }


    /*
     * Date Tokens
     * Format: (type|format for date)
     * Examples: (date), (time), (date|Y-m-d), (date:+1 month), (date:+1 month|y-md)
     * 
     */
    public static function replace_date_token( $text ) {

        // just the time
        if( $text == '(time)' )
            return date( get_option( 'time_format' ) );

        $type = '(date';
          
        if ( strpos( $text, $type ) !== false ) {

            if( $text == '(date)' )
                return date( get_option( 'date_format' ) );

            // remove the start & end
            $text = strstr( $text, $type ); // remove anything before type
            $text = str_replace( $type, '', $text );  // remove type
            $text = rtrim( $text, ')' ); // remove last bracket

            // extract the format if any (date format)
            if( ( $pos = strpos( $text, "|" ) ) !== FALSE ) {
                $format = substr( $text, $pos +1 );
                $text = str_replace( '|' . $format, '', $text ); // remove the format pipe
            }

            // extract the param if any (+1 month)
            if( ( $pos = strpos( $text, ":" ) ) !== FALSE )
                $text = substr( $text, $pos +1 );


            // if we have the date plus a param
            if ( $format && $text )
                return date( $filter, strtotime( $text ) );

            // if we just have the date and no param
            if ( $format && ! $text )
                return date( $filter );

            // if we just have the date and no param
            if ( ! $format && $text )
                return date( get_option( 'date_format' ), strtotime( $text ) );

        }

        return (string) $text;
        
    }

    /*
     * System Tokens
     * Format: (type:variable:query var)
     * Examples: (system:get:my_query_var)
     * 
     */
    public function replace_system_token( $text ) {

        $pieces = explode( '(system:', $text );

        if ( count( $pieces ) > 1 ) {
            foreach ( $pieces as $i => $value ) {

                $raw = explode( ')', $value );
                $params = reset( $raw );

                // get sub keys
                $keys = explode( ':', $params );

                if( ! isset( $keys[0] ) || ! isset( $keys[1] ) )
                    continue;

                $type = $keys[0];
                $var = sanitize_text_field( $keys[1] );

                switch ( $type ) {
                    case 'get':
                    case '_get':
                    case '_GET':
                    case 'GET':
                        $text = isset( $_GET[ $var ] ) ? $_GET[ $var ] : '';
                        break;
                    case 'post':
                    case '_post':
                    case '_POST':
                    case 'POST':
                        $text = isset( $_POST[ $var ] ) ? $_POST[ $var ] : '';
                        break;
                    case '_request':
                    case '_REQUEST':
                    case 'request':
                        $text = isset( $_REQUEST[ $var ] ) ? $_REQUEST[ $var ] : '';
                        break;
                    case 'cookie':
                    case '_cookie':
                    case '_COOKIE':
                    case 'COOKIE':
                        $text = isset( $_COOKIE[ $var ] ) ? $_COOKIE[ $var ] : '';
                        break;
                    case 'session':
                    case '_session':
                    case '_SESSION':
                    case 'SESSION':
                        $text = isset( $_SESSION[ $var ] ) ? $_SESSION[ $var ] : '';
                        break;
                    case 'server':
                    case '_server':
                    case '_SERVER':
                    case 'SERVER':
                        $text = isset( $_SERVER[ $var ] ) ? $_SERVER[ $var ] : '';
                        break;
                }

            }

        }

        return (string) $text;

    }


    /**
     * Maybe do tokens in endpoint
     */
    public function endpoint_tokens( $endpoint, $api ) {

        if( ! isset( $endpoint ) )
            return;

        $this->user_id = isset( $api->args['user_id'] ) ? $api->args['user_id'] : '';

        if ( strpos( $endpoint, '(' ) !== false && strpos( $endpoint, ')' ) !== false ) {
            $tokens = $this->get_the_tokens( $endpoint );
            if( ! empty( $tokens ) ) {
                foreach ( $tokens as $i => $token ) {
                    $token_value = $this->replace_tokens( $token );
                    $endpoint = str_replace( $token, $token_value, $endpoint );
                }
            }
        }

        return $endpoint;

    }


    /**
     * Maybe do tokens in query string
     */
    public function query_string_tokens( $params, $api ) {

        if( ! isset( $params ) || ! is_array( $params ) )
            return;

        $this->user_id = isset( $api->args['user_id'] ) ? $api->args['user_id'] : '';

        foreach( $params as $key => $value ) {

            if( ! is_array( $value ) ) {

                if ( strpos( $value, '(' ) !== false && strpos( $value, ')' ) !== false ) {

                    $tokens = $this->get_the_tokens( $value );

                    if( ! empty( $tokens ) ) {

                        foreach ( $tokens as $i => $token ) {

                            $token_value = $this->replace_tokens( $token );
                            $value = str_replace( $token, $token_value, $value );

                        }

                    }
                    
                }

            } else {

                $value = $this->body_tokens( $value, $api );

            }

            $params[ $key ] = $value;

        }

        return $params;

    }

    /**
     * Maybe do tokens in headers
     */
    public function header_tokens( $headers, $api ) {

        if( ! isset( $headers['headers'] ) || ! is_array( $headers['headers'] ) )
            return;

        $this->user_id = isset( $api->args['user_id'] ) ? $api->args['user_id'] : '';

        foreach( $headers['headers'] as $key => $value ) {

            if( ! is_array( $value ) ) {

                if ( strpos( $value, '(' ) !== false && strpos( $value, ')' ) !== false ) {

                    $tokens = $this->get_the_tokens( $value );

                    if( ! empty( $tokens ) ) {

                        foreach ( $tokens as $i => $token ) {

                            $token_value = $this->replace_tokens( $token );
                            $value = str_replace( $token, $token_value, $value );

                        }

                    }
                    
                }

            } else {

                $value = $this->body_tokens( $value, $api );

            }

            $headers['headers'][ $key ] = $value;

        }

        return $headers;

    }

    /**
     * Maybe do tokens in body
     */
    public function body_tokens( $params, $api ) {

        if( ! isset( $params ) || ! is_array( $params ) )
            return;
        
        $this->user_id = isset( $api->args['user_id'] ) ? $api->args['user_id'] : '';

        foreach( $params as $key => $value ) {

            if( ! is_array( $value ) ) {

                if ( strpos( $value, '(' ) !== false && strpos( $value, ')' ) !== false ) {

                    $tokens = $this->get_the_tokens( $value );

                    if( ! empty( $tokens ) ) {

                        foreach ( $tokens as $i => $token ) {

                            $token_value = $this->replace_tokens( $token );
                            $value = str_replace( $token, $token_value, $value );

                        }

                    }
                    
                }

            } else {

                $value = $this->body_tokens( $value, $api );

            }
            
            $params[ $key ] = $value;

        }

        return $params;

    }


    public function get_the_tokens( $str, $start = '(', $end = ')', $with_from_to = true ){
        $arr = [];
        $last_pos = 0;
        $last_pos = strpos($str, $start, $last_pos);
        while ($last_pos !== false) {
            $t = strpos($str, $end, $last_pos);
            $arr[] = ($with_from_to ? $start : '').substr($str, $last_pos + 1, $t - $last_pos - 1).($with_from_to ? $end : '');
            $last_pos = strpos($str, $start, $last_pos+1);
        }
        return $arr; 
    }

}

return new WpGetApi_Extras_Tokens();


