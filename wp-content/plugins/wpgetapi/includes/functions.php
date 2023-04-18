<?php

/*
 * Pretty print when debugging is enabled in options
 */
if (!function_exists('wpgetapi_pp')) {
    function wpgetapi_pp( $array ) {
        echo '<pre style="white-space:pre-wrap;">';
            print_r( $array );
        echo '</pre>' . "\n";
    }
}


/**
 * Dropdown options for results_format
 * It is set up this way so we can easily add the filter to be able to add extra format options
 * 
 */
function wpgetapi_results_format_options( $field ) {
    $options = apply_filters( 'wpgetapi_results_format_options', array(
        'json_string' => __( 'JSON string', 'wpgetapi' ),
        'json_decoded' => __( 'PHP array data', 'wpgetapi' ),
    ) );
    return $options;
}


/**
 * Recursive sanitation for text or array
 * 
 * @param $array_or_string (array|string)
 * @since  1.0.0
 * @return mixed
 */
function wpgetapi_sanitize_text_or_array($array_or_string) {
    if( is_string($array_or_string) ){
        $array_or_string = sanitize_text_field($array_or_string);
    }elseif( is_array($array_or_string) ){
        foreach ( $array_or_string as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = wpgetapi_sanitize_text_or_array($value);
            }
            else {
                $value = sanitize_text_field( $value );
            }
        }
    }

    return $array_or_string;
}


/**
 * wpgetapi_output_endpoint_test_area
 * 
 */
function wpgetapi_output_top_of_endpoint( $field_args, $field ) {
    
    ob_start();

    $test_disabled = true;

    if( $field->value )
        $test_disabled = false;

    ?>

    <pre class="functions">
        Template Tag: </span><span>wpgetapi_endpoint( '<?php esc_html_e( $field->args['api_id'] ); ?>', '<span class='endpoint_id'></span>', array('debug' => false) );</span><br>
        Shortcode: <span>[wpgetapi_endpoint api_id='<?php esc_html_e( $field->args['api_id'] ); ?>' endpoint_id='<span class='endpoint_id'></span>' debug='false']</span>
    </pre>

    <div class="wpgetapi-test-area" data-endpoint="<?php esc_attr_e( $field->value ); ?>" data-api="<?php esc_attr_e( $field->args['api_id'] ); ?>">
        <a class="test-button button-primary" href="#" <?php echo $test_disabled ? 'disabled' : ''; ?>><?php _e( 'Test Endpoint', 'wpgetapi' ); ?></a>
        <span class="handle" style="display:none">Hide/Show Results</span>
        <div class="wpgetapi-result"></div>
    </div>

    <?php
    
    $content = ob_get_contents();
    ob_end_clean();
    return $content;  

    return $field_args;

}
