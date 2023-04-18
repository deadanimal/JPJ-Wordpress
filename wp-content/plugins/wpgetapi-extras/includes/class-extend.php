<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The main class
 *
 * @since 1.0.0
 */
class WpGetApi_Extras_Extend {

    //public $encryption = '';

    /**
     * Main constructor
     *
     * @since 1.0.0
     *
     */
    public function __construct() {

        // base 64
        add_action( 'wpgetapi_header_parameters', array( $this, 'maybe_base64_encode' ), 10, 2 );

        // caching
        add_filter( 'wpgetapi_before_get_request', array( $this, 'maybe_cache' ), 10, 2 );

        // add new results format
        add_filter( 'wpgetapi_results_format_options', array( $this, 'results_format_options' ), 10, 2 );

        // get nested data
        add_filter( 'wpgetapi_json_response_body', array( $this, 'nested_data' ), 10, 2 );

        // maybe output as xml
        add_filter( 'wpgetapi_raw_data', array( $this, 'output_xml' ), 10, 2 );

        // maybe do variables on endpoint
        add_filter( 'wpgetapi_final_url', array( $this, 'variables_on_endpoint' ), 10, 2 );

        // maybe add custom query variables - useful within shortcode
        add_filter( 'wpgetapi_final_url', array( $this, 'custom_query_variables' ), 11, 2 );

        // maybe add custom header variables
        add_filter( 'wpgetapi_header_parameters', array( $this, 'custom_header_variables' ), 10, 2 );

        // maybe add custom body variables
        add_filter( 'wpgetapi_body_parameters', array( $this, 'custom_body_variables' ), 10, 2 );

        // format the output of the shortcode
        add_filter( 'wpgetapi_raw_data', array( $this, 'shortcode_format_output' ), 11, 2 );

        // hook into the contact form 7 action upon processing the form
        add_action( 'wpcf7_before_send_mail', array( $this, 'cf7_after_submission' ), 1, 3 );

        add_action( 'wpcf7_init', array( $this, 'cf7_add_form_tag' ) );

    }



    /**
     * CF7 custom form-tag.
     * [wpgetapi hubspot|create_contact|append "properties,createdate"]
     * @since  2.0.1
     */
    public function cf7_add_form_tag() {
        wpcf7_add_form_tag( 
            'wpgetapi', 
            array( $this, 'cf7_form_tag_handler' ),
        );
    }

    public function cf7_form_tag_handler( $tag ) {

        $format_error = 'Your wpgetapi form tag is not formatted correctly.';

        if( ! isset( $tag->options[0] ) || empty( $tag->options[0] ) )
            return $format_error;

        // extract the api_id, endpoint_id, message type
        $values = explode( "|", $tag->options[0], 3 );
        if ( ! isset( $values[0] ) || ! isset( $values[1] ) ) 
            return $format_error;

        if ( ! isset( $values[2] ) ) 
            $values[2] = 'none';

        // include our current logged in user
        $values[3] = get_current_user_id();

        $atts = array(
            'type' => 'hidden',
            'name' => 'wpgetapi',
            'value' => json_encode( $values ),
        );

        $inputs = '';
        $inputs .= sprintf( '<input %s />', wpcf7_format_atts( $atts ) );

        // if we have keys set
        if( isset( $tag->values[0] ) && ! empty( $tag->values[0] ) ) {

            // extract the keys
            $keys = explode( ",", $tag->values[0], 10 );

            $atts_keys = array(
                'type' => 'hidden',
                'name' => 'wpgetapi_keys',
                'value' => json_encode( $keys ),
            );

            $inputs .= sprintf( '<input %s />', wpcf7_format_atts( $atts_keys ) );

        }

        return $inputs;

    }


    /**
     * CF7 submission.
     * 
     * @since  2.0.1
     */
    public function cf7_after_submission( $contact_form, &$abort, $submission ) {

        $wpgetapi = $submission->get_posted_data( 'wpgetapi' );
        $keys = $submission->get_posted_data( 'wpgetapi_keys' );
        
        // if no wpgetapi field, bail
        if( ! $wpgetapi ) 
            return $contact_form;

        $wpgetapi = json_decode( $wpgetapi, true );

        // if we have keys, add them (or it)
        if( $keys && ! empty( $keys ) ) {
            $keys = json_decode( $keys, true );
        } else {
            $keys = '';
        }
        
        $user_id = isset( $wpgetapi[3] ) ? absint( $wpgetapi[3] ) : '';

        // call our API
        $data = wpgetapi_endpoint( $wpgetapi[0], $wpgetapi[1], 
            array( 
                'debug' => false, 
                'user_id' => $user_id, 
            ),
            $keys
        );    

        // get the form properties
        $properties = $contact_form->get_properties();

        // set the success message
        if( $wpgetapi[2] ) {

            $mail_sent_ok = $properties['messages']['mail_sent_ok'];

            switch ( $wpgetapi[2] ) {
                case 'none':
                    $properties['messages']['mail_sent_ok'] = $mail_sent_ok;
                    break;
                case 'replace':
                    $properties['messages']['mail_sent_ok'] = $data;
                    break;
                case 'prepend':
                    $properties['messages']['mail_sent_ok'] = $data . ' ' . $mail_sent_ok;
                    break;
                case 'append':
                    $properties['messages']['mail_sent_ok'] = $mail_sent_ok . ' ' . $data;
                    break;
            }
        }

        $contact_form->set_properties($properties);

    }


    /**
     * Maybe_base64_encode for login.
     * @since  1.4.3
     */
    public function maybe_base64_encode( $headers, $api ) {

        // if we have headers
        if( isset( $headers['headers'] ) && ! empty( $headers['headers'] ) ) {

            foreach ( $headers['headers'] as $name => $value ) {

                // if we have value with 'base64' keyword
                if ( strpos( $value, 'base64_encode' ) !== false ) {

                    // extract the value to encode
                    preg_match('#\((.*?)\)#', $value, $match );
                    $to_encode = isset( $match[1] ) ? $match[1] : null;
                    if( ! $to_encode )
                        return $headers;

                    // get anything before the keyword such as Basic etc
                    list($before, $after) = explode( 'base64_encode', $value );
                    if( ! $before )
                        return $headers;

                    // encode it
                    $headers['headers'][ $name ] = $before . base64_encode( $to_encode );

                }

            }

        }

        return $headers;

    }


    /**
     * Maybe do variables on endpoint
     */
    public function variables_on_endpoint( $url, $api ) {

        // if we have endpoint variables set, proceed
        if( isset( $api->args['endpoint_variables'] ) && ! empty( $api->args['endpoint_variables'] ) ) {

            foreach ( $api->args['endpoint_variables'] as $index => $var ) {

                if ( strpos( $api->endpoint, '{' . $index . '}' ) !== false ) {

                    $url = str_replace( '{' . $index . '}', $var, $url );

                }

            }

        }

        return $url;
    }


    /**
     * Maybe do variables on endpoint
     */
    public function custom_query_variables( $url, $api ) {

        // if we have endpoint variables set, proceed
        if( isset( $api->args['query_variables'] ) && ! empty( $api->args['query_variables'] ) ) {

            $vars = explode(',', $api->args['query_variables'] );
            $finalArray = array();

            if( $vars && is_array( $vars ) ) {
                
                foreach ($vars as $var) {
                    $couple = explode('=', $var);
                    $finalArray[$couple[0]] = $couple[1];
                }

            } else {

                $couple = explode( '=', $api->args['query_variables'] );
                $finalArray[$couple[0]] = $couple[1];

            }


            $url = add_query_arg( $finalArray, $url );

        }

        return $url;
    }

    /**
     * Maybe do variables in headers
     */
    public function custom_header_variables( $headers, $api ) {

        // if we have endpoint variables set, proceed
        if( isset( $api->args['header_variables'] ) && ! empty( $api->args['header_variables'] ) ) {

            $vars = $api->args['header_variables'];

            if( $vars && is_array( $vars ) ) {

                foreach ( $vars as $key => $value ) {
                    $headers['headers'][ $key ] = $value;
                }
                
            }

        }

        return $headers;

    }

    /**
     * Maybe do variables in body
     */
    public function custom_body_variables( $body, $api ) {
        
        // if we have endpoint variables set, proceed
        if( isset( $api->args['body_variables'] ) && ! empty( $api->args['body_variables'] ) ) {

            $vars = $api->args['body_variables'];

            if( $vars && is_array( $vars ) ) {

                foreach ( $vars as $key => $value ) {
                    $body[ $key ] = $value;
                }
                
            }

        }

        return $body;

    }


    /**
     * Add new options to result format
     */
    public function results_format_options( $options ) {
        $new = array(
            'xml_string' => __( 'XML (as string)', 'wpgetapi' ),
            'xml_array' => __( 'XML (as array data)', 'wpgetapi' ),
        );
        $options = array_merge( $options, $new );
        return $options;
    }


    /**
     * Output in XML
     */
    public function output_xml( $data, $api ) {

        // skip doing XML for debug
        if( $api->debug )
            return $data;

        // returning in XML string format
        if( $api->results_format == 'xml_string' ) {
            return wp_kses_post( $data );
        }

        // returning in XML string array
        if( $api->results_format == 'xml_array' ) {
            $data = simplexml_load_string( $data );
            // converts all data to arrays, removing objects
            $data = json_decode( json_encode( $data ), true );
            $data = apply_filters( 'wpgetapi_json_response_body', $data, $api->keys );
            return $data;
        }

        return $data;

    }

    /**
     * Format the output of the shortcode
     */
    public function shortcode_format_output( $data, $api ) {

        if( isset( $api->args['format'] ) && ! empty( $api->args['format'] ) ) {

            // number formatting
            if ( strpos( $api->args['format'], 'number_format(' ) !== false ) {

                // extract the decimal value
                preg_match('#\((.*?)\)#', $api->args['format'], $match );
                $decimals = isset( $match[1] ) ? $match[1] : '0';
                
                $data = number_format_i18n( $data, $decimals );

            }

            // html formatting
            if ( strpos( $api->args['format'], 'html' ) !== false ) {
                
                $label = $api->args['html_labels'];

                // so we don't allow any weird tags to come through
                $html_tag = 'div';
                switch ( $api->args['html_tag'] ) {
                    case 'div': $html_tag = 'div'; break;
                    case 'li': $html_tag = 'li'; break;
                    case 'span': $html_tag = 'span'; break;
                    default: $html_tag = 'div'; break;
                } 

                $wrap_tag = $html_tag == 'li' ? 'ul' : 'div';

                $data = '<' . $wrap_tag . ' class="wpgetapi_html wpgetapi_outer_wrap">' . $this->format_html( $data, '', $html_tag, $label ) . '</' . $wrap_tag . '>';

            }

        }    

        return $data;

    }

    /**
     * Format keys to readable words.
     * @since  1.0.0
     */
    public function _format_key_to_label( $string ) {
        $string = sanitize_text_field( $string );
        $string = str_replace('_', '', ucwords($string, '_'));
        $string = str_replace('-', '', ucwords($string, '-'));
        $words = preg_replace('/(?<!\ )[A-Z]/', ' $0', $string);
        return ucwords( $words );
    }

    /**
     * Format our HTML
     */
    public function format_html( $data, $output, $tag, $label ){   

        static $output = '';

        if( ! is_array( $data ) ) {

            $output .= '<' . $tag . ' class="">' . $data . '</' . $tag . '>';  

        } else {

            // iterate over each element's key and value so you can check either    
            foreach( $data as $key => $value ) { 

                $class = sanitize_file_name( 'wpgetapi_' . $key );
                $label_out = $label == 'true' ? '<span>' . $this->_format_key_to_label( $key ) . '</span> ' : '';

                // if element is an array, then run it through function    
                if( is_array( $value ) ) {  

                    $item_class = is_numeric( $key ) ? 'wpgetapi_item ' : '';
                    $output .= '<' . $tag . ' class="' . $item_class . $class . '">' . $label_out; 
                    $this->format_html( $value, $output, $tag, $label ); 
                    $output .= '</' . $tag . '>'; 

                } else { 

                    $output .= '<' . $tag . ' class="' . $class . '">' . $label_out . '' . $value . '</' . $tag . '>';   

                } 

            }

        }
            
        return $output;   

    } 

    /**
     * Removed in version 1.4.8
     * Just keeping this here as we may use again in future
     */
    // public function array_insert( $array, $position, $insert ) {
    //     if ($position > 0) {
    //         if ($position == 1) {
    //             array_unshift($array, array());
    //         } else {
    //             $position = $position - 1;
    //             array_splice($array, $position, 0, array(
    //                 ''
    //             ));
    //         }
    //         $array[$position] = $insert;
    //     }

    //     return $array;
    // }


    /**
     * Setup and do caching if set
     */
    public function maybe_cache( $response, $api ) {

        if( isset( $api->cache_time ) && $api->cache_time > 0 ) {

            // if we have query variables set and need to dynamically cache these
            if( isset( $api->args['query_variables'] ) && ! empty( $api->args['query_variables'] ) ) {
                $query_vars = '_' . wp_hash( $api->args['query_variables'] );
            } else {
                $query_vars = '';
            }

            $transient_name = 'wpgetapi_' . $api->api_id . '_' . $api->endpoint_id . $query_vars;

            // Do we have this information in our transients already?
            $transient = get_transient( $transient_name );

            // Yep!  Just return it and we're done.
            if( ! empty( $transient ) ) {
                
                // The function will return here every time after the first time it is run, until the transient expires.
                return $transient;

            } 

            $args = isset( $api->final_headers ) ? $api->final_headers : $api->final_request_args;
            $response = wp_remote_get( $api->final_url, $args );

            // Don't bother caching stuff we don't need
            if( 
                is_array( $response ) && 
                ! is_wp_error( $response ) && 
                isset( $response['response'] ) && 
                isset( $response['response']['code'] )
            ) { 

                // set tmp array as we are only going to store specific data
                $tmp = array();

                $tmp['headers'] = $response['headers'];
                $tmp['body'] = $response['body'];
                $tmp['response'] = $response['response'];
                $tmp['cookies'] = $response['cookies'];

                // Save the API response
                $transient = set_transient( $transient_name, $tmp, apply_filters( 'wpgetapi_cache_time', $api->cache_time ) );

            }
 
        }

        return $response;

    }


    /**
     * Get the nested data
     */
    public function nested_data( $data = array(), $keys = array() ) {

        // if we have keys
        if( $keys && is_array( $keys ) ) {
            
            $count = count( $keys );
            $keys = wpgetapi_sanitize_text_or_array( $keys );

            // using a switch statement until find better solution
            switch ( $count ) {
                case 1:
                    $data = $data[ $keys[0] ];
                    break;
                case 2:
                    $data = $data[ $keys[0] ][ $keys[1] ];
                    break;
                case 3:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ];
                    break;
                case 4:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ][ $keys[3] ];
                    break;
                case 5:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ][ $keys[3] ][ $keys[4] ];
                    break;
                case 6:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ][ $keys[3] ][ $keys[4] ][ $keys[5] ];
                    break;
                case 7:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ][ $keys[3] ][ $keys[4] ][ $keys[5] ][ $keys[6] ];
                    break;
                case 8:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ][ $keys[3] ][ $keys[4] ][ $keys[5] ][ $keys[6] ][ $keys[7] ];
                    break;
                case 9:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ][ $keys[3] ][ $keys[4] ][ $keys[5] ][ $keys[6] ][ $keys[7] ][ $keys[8] ];
                    break;
                case 10:
                    $data = $data[ $keys[0] ][ $keys[1] ][ $keys[2] ][ $keys[3] ][ $keys[4] ][ $keys[5] ][ $keys[6] ][ $keys[7] ][ $keys[8] ][ $keys[9] ];
                    break;
            }

        }

        return $data;

    }


}

return new WpGetApi_Extras_Extend();