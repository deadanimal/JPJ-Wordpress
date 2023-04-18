<?php
/**
 * Settings Required
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WpGetApi_Admin_Options' ) ) :

/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class WpGetApi_Admin_Options {
	
	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	public $option_metabox = array();

	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	public $metabox_id = '';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $menu_title = '';

	/**
	 * Options Tab Pages
	 * @var array
	 */
	public $options_pages = array();

	/**
	 * Is pro plugin installed
	 * @var array
	 */
	public $pro_plugin = false;

	/**
	 * Holds an instance of the object
	 *
	 * @var Myprefix_Admin
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	private function __construct() {
		// Set our title
		$this->menu_title = __( 'WPGetAPI', 'wpgetapi' );
		$this->title = __( 'WPGetAPI', 'wpgetapi' );
		
	}

	/**
	 * Returns the running object
	 *
	 * @return Myprefix_Admin
	 **/
	public static function get_instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}
	
	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_pages' ) );

		add_action( 'cmb2_admin_init', array( $this, 'init_custom_fields' ) );

		add_action( "cmb2_save_options-page_fields", array( $this, 'redirect' ), 1, 2 );

		add_action( 'admin_footer', array( $this, 'load_testing_javascript' ) ); 

		add_action( 'wp_ajax_wpgetapi_test_endpoint', array( $this, 'test_the_endpoint' ) );

	}


	/**
	 * Setup our custom fields
	 * @since  0.1.0
	 */
	public function init_custom_fields() {
		require_once WPGETAPIDIR . 'includes/class-fields.php';
		WpGetApi_Parameter_Field::init_parameter();
	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		$this->pro_plugin = is_plugin_active( 'wpgetapi-extras/wpgetapi-extras.php' ) ? true : false;
		$option_tabs = self::option_fields();
		foreach ($option_tabs as $index => $option_tab) {
			register_setting( $option_tab['id'], $option_tab['id'] );
		}
	}


	/**
	 * Get our saved API's from setup
	 * @since  0.1.0
	 */
	public function get_apis() {
		$setup = get_option( 'wpgetapi_setup' );
		if( empty( $setup ) || ! isset( $setup['apis'] ) || empty( $setup['apis'] ) )
			return;
		return $setup['apis'];
	}


	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	public function option_fields() {

		//Only need to initiate the array once per page-load
		if ( ! empty( $this->option_metabox ) ) {
			return $this->option_metabox;
		}

		// setup tab
		$option_metabox[] = array(
			'title'      => __( 'Setup', 'wpgetapi' ), 
			'menu_title' => __( 'Setup', 'wpgetapi' ),
			'id'         => 'wpgetapi_setup',
			'desc'       => __( 'Add the details of the API(s) you are using. Endpoints will be setup in the next step after hitting save.', 'wpgetapi' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( 'wpgetapi_setup' ), ),
			'show_names' => true,
			'fields'     => $this->setup_fields(),
		);

		// get our saved API's and create as tabs
		if( $this->get_apis() ) {

			foreach ( $this->get_apis() as $index => $api ) {
				
				$name 		= isset( $api['name'] ) && $api['name'] != '' ? sanitize_text_field( $api['name'] ) : 'API ' . absint( $index );
				$api_id 	= isset( $api['id'] ) && $api['id'] != '' ? sanitize_text_field( $api['id'] ) : $name;
				$metabox_id = strtolower( str_replace( '-', '_', sanitize_file_name( 'wpgetapi-' . $api_id ) ) );
				$base_url 	= isset( $api['base_url'] ) && $api['base_url'] != '' ? esc_url_raw( $api['base_url'], array( 'http', 'https' ) ) : '';
				$type 		= isset( $api['auth_type'] ) && $api['auth_type'] != '' ? sanitize_text_field( $api['auth_type'] ) : '';

				// tab
				$option_metabox[] = array(
					'title'      => $name,
					'menu_title' => $name,
					'id'         => $metabox_id,
					'desc'       => '',
					'show_on'    => array( 'key' => 'options-page', 'value' => array( $metabox_id ), ),
					'show_names' => true,
					'fields'     => $this->endpoint_fields( $type, $api_id, $base_url ),
				);

			}

		}

		$option_metabox = apply_filters( 'wpgetapi_admin_pages', $option_metabox );

		return $option_metabox;

	}

	public function setup_fields() {

		$fields[] = array(
		    'id'          => 'apis',
		    'type'        => 'group',
		    'name'        => '',
		    'description' => '',
		    'options'     => array(
		        'group_title'   => __( 'API {#}', 'wpgetapi' ), 
		        'add_button'    => __( 'Add New API', 'wpgetapi' ),
		        'remove_button' => __( 'Remove API', 'wpgetapi' ),
		        'sortable'      => true, // beta
		    ),
		    'fields'     => array(
				array(
				    'name' => __( 'API Name', 'wpgetapi' ),
				    'id'   => 'name',
				    'type' => 'text',
				    'attributes' => array( 
				    	'required' => true,
				    	'placeholder' => 'Google Maps',
				    ),
				    'desc' => __( 'The name of the API you are connecting to.', 'wpgetapi' ),
				),
				array(
				    'name' => __( 'Unique ID', 'wpgetapi' ),
				    'id'   => 'id',
				    'type' => 'text',
				    'attributes' => array( 
				    	'required' => true,
				    	'placeholder' => 'google_maps',
				    ),
				    'desc' => __( 'A unique ID for your API. Lowercase letters and underscores only.', 'wpgetapi' ),
				),
				array(
				    'name' => __( 'Base URL', 'wpgetapi' ),
				    'id'   => 'base_url',
				    'type' => 'text',
				    'attributes' => array( 
				    	'required' => true,
				    	'placeholder' => 'https://maps.googleapis.com/maps/api',
				    ),
				    'desc' => __( 'The base URL of the API you are connecting to.', 'wpgetapi' ),
				),
				
		    )
		);

		return $fields;

	}


	/**
	 * Endpoint settings.
	 * @return sale info array
	 *
	 */
	public function endpoint_fields( $type = '', $api_id = '', $base_url = '' ) {

		$fields = array();

		$cache_disabled = $this->pro_plugin ? '' : 'disabled';
		$cache_url = $this->pro_plugin ? '<a target="_blank" href="https://wpgetapi.com/docs/caching-api-calls/">Help <span class="dashicons dashicons-external"></span></a>' : '<a target="_blank" href="https://wpgetapi.com/downloads/pro-plugin/?utm_campaign=Cache Pro Plugin&utm_medium=Admin&utm_source=User">Pro Plugin<span class="dashicons dashicons-external"></span></a>';

		$endpoint_fields = apply_filters( 'wpgetapi_fields_endpoints', array(
			array(
			    'name' => __( 'Unique ID', 'wpgetapi' ),
			    'id'   => 'id',
			    'type' => 'text',
			    'classes' => 'field-id',
			    'attributes' => array( 
			    	'required' => true,
			    	'placeholder' => 'new_endpoint',
			    ),
			    'desc' => __( 'A unique ID for this endpoint. Lowercase letters and underscores only.', 'wpgetapi' ),
			    'before_row' => "wpgetapi_output_top_of_endpoint",
			    'api_id' => $api_id,

			),
			array(
			    'name' => __( 'Endpoint', 'wpgetapi' ),
			    'id'   => 'endpoint',
			    'type' => 'text',
			    'classes' => 'field-endpoint',
			    'attributes' => array( 
			    	'required' => true,
			    	'placeholder' => '/newendpoint',
			    ),
			    'desc' => __( 'The endpoint that will be appended to the base URL.', 'wpgetapi' ),
			),
			array(
			    'name' => __( 'Method', 'wpgetapi' ),
			    'id'   => 'method',
			    'type' => 'select',
			    'classes' => 'field-method',
			    'attributes' => array( 
			    	'required' => true,
			    ),
			    'options' => array( 
			    	'GET' => 'GET',
			    	'POST' => 'POST',
			    	'PUT' => 'PUT',
			    	'DELETE' => 'DELETE',
			    ),
			    'desc' => __( 'The request method for this endpoint.', 'wpgetapi' ),
			),

			array(
			    'name' => __( 'Results Format', 'wpgetapi' ),
			    'id'   => 'results_format',
			    'type' => 'select',
			    'classes' => 'field-results-format',
			    'attributes' => array( 
			    	'required' => true,
			    ),
			    'options_cb' => 'wpgetapi_results_format_options',
			    'desc' => sprintf( 
			    	__( 'The format of the results.<br>If using the shortcode, this must be set to JSON string. %1s', 'wpgetapi' ),
			    	'<a target="_blank" href="https://wpgetapi.com/docs/format-api-data-as-html/?utm_campaign=Shortcode JSON&utm_medium=Admin&utm_source=User">More options <span class="dashicons dashicons-external"></span></a>'
			    )
			),

			array(
			    'name' => __( 'Cache Time', 'wpgetapi' ),
	            'id'   => 'cache_time',
	            'type' => 'text',
	            'classes' => 'field-cache-time',
	            'attributes' => array( 
	                'placeholder' => '',
	                $cache_disabled => $cache_disabled,
	            ),
	            'desc' => sprintf( 
	            	__( 'The time in seconds to cache this request for. %1s', 'wpgetapi' ),
			    	$cache_url
			    )
			),

			array(
			    'name' => __( 'Query String', 'wpgetapi' ),
			    'id'   => 'query_parameters',
			    'type' => 'parameter',
			    'classes' => 'field-query-parameters',
			    'repeatable' => true,
			    'desc' => sprintf( 
			    	__( 'Parameters as name/value pairs that will be appended to the URL. %1s', 'wpgetapi' ),
			    	'<a target="_blank" href="https://wpgetapi.com/docs/adding-query-string-parameters/?utm_campaign=Query String&utm_medium=Admin&utm_source=User">Help <span class="dashicons dashicons-external"></span></a>'
			    )
			),
			array(
			    'name' => __( 'Headers', 'wpgetapi' ),
			    'id'   => 'header_parameters',
			    'type' => 'parameter',
			    'classes' => 'field-header-parameters',
			    'repeatable' => true,
			    'desc' => sprintf( 
			    	__( 'Parameters as name/value pairs, sent in the headers. %1s', 'wpgetapi' ),
			    	'<a target="_blank" href="https://wpgetapi.com/docs/sending-headers-in-request/?utm_campaign=Headers&utm_medium=Admin&utm_source=User">Help <span class="dashicons dashicons-external"></span></a>'
			    )
			),
			array(
			    'name' => __( 'Body POST Fields', 'wpgetapi' ),
			    'id'   => 'body_parameters',
			    'type' => 'parameter',
			    'classes' => 'field-body-parameters',
			    'repeatable' => true,
			    'desc' => sprintf( 
			    	__( 'Parameters as name/value pairs, sent in the body as POST fields. %1s', 'wpgetapi' ),
			    	'<a target="_blank" href="https://wpgetapi.com/docs/sending-post-fields-in-request/?utm_campaign=Body&utm_medium=Admin&utm_source=User">Help <span class="dashicons dashicons-external"></span></a>'
			    )
			),
			array(
			    'name' => __( 'Encode Body', 'wpgetapi' ),
			    'id'   => 'body_json_encode',
			    'type' => 'select',
			    'classes' => 'field-body-json-encode',
			    'options'     => array(
			    	'false'    => __( 'No encoding', 'wpgetapi' ),
			        'true'   => __( 'JSON encode', 'wpgetapi' ), 
			        'url'   => __( 'URL encode', 'wpgetapi' ), 
			    ),
			    'desc' => __( 'Encoding of the above Body parameters (if they are set). ', 'wpgetapi' ),
			),
	    ) );


		$fields[] = array(
		    'id'          => 'endpoints',
		    'type'        => 'group',
		    'name'        => '', 
		    'description' => '',		    
		    'options'     => array(
		        'group_title'   => __( 'Endpoint {#}', 'wpgetapi' ), 
		        'add_button'    => __( 'Add Endpoint', 'wpgetapi' ),
		        'remove_button' => __( 'Remove Endpoint', 'wpgetapi' ),
		        'sortable'      => true, // beta
		    ),
		    'before_group' => '<pre class="url">Base URL: <span>' . $base_url . '</span></pre>',
		    'fields' => $endpoint_fields,
		);
		
		return apply_filters( 'wpgetapi_fields', $fields );
		
	}


	public function add_options_pages() {

		$option_tabs = self::option_fields();

		foreach ($option_tabs as $index => $option_tab) {
			if ( $index == 0) {

				$this->options_pages[] = add_menu_page( $this->title, $this->menu_title, 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ), 'dashicons-editor-code'
				); //Link admin menu to first tab

				add_submenu_page( $option_tabs[0]['id'], $this->menu_title, $option_tab['menu_title'], 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) ); //Duplicate menu link for first submenu page
			} else {
				$this->options_pages[] = add_submenu_page( $option_tabs[0]['id'], $this->menu_title, $option_tab['menu_title'], 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) );
			}
		}

		foreach ( $this->options_pages as $page ) {
			// Include CMB CSS in the head to avoid FOUC
			add_action( "admin_print_styles-{$page}", array( 'CMB2_Hookup', 'enqueue_cmb_css' ) );
		}

	}

	/**
	 * Admin page markup. Mmply handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {

		$option_tabs = self::option_fields(); //get all option tabs
		$tab_forms = array();
		?>

		<div class="wrap wpgetapi">

			<div class="main_content_cell">

				<h1 class="wp-heading-inline"><?php esc_html_e( $this->title, 'wpgetapi' ) ?></h1>
				<!-- Options Page Nav Tabs -->
				<h2 class="nav-tab-wrapper">
					<?php foreach ($option_tabs as $option_tab) :
						
						$tab_slug = $option_tab['id'];
						$nav_class = 'nav-tab';
						if ( $tab_slug == $_GET['page'] ) {
							$nav_class .= ' nav-tab-active'; //add active class to current tab
							$tab_forms[] = $option_tab; //add current tab to forms to be rendered
						}

						?>
						
						<a class="<?php esc_attr_e( $nav_class ); ?>" href="<?php esc_url( menu_page_url( $tab_slug ) ); ?>"><?php esc_attr_e( $option_tab['menu_title'], 'wpgetapi' ); ?></a>

					<?php endforeach; ?>
				</h2>
				<!-- End of Nav Tabs -->

				<?php 
				//render all tab forms (normaly just 1 form) 
				foreach ($tab_forms as $tab_form) : ?>
				
					<div id="<?php esc_attr_e($tab_form['id']); ?>" class="cmb-form group">
						<div class="metabox-holder">
							<div class="pmpbox pad">
								<h3 class="title"><?php esc_html_e($tab_form['title'], 'wpgetapi'); ?></h3>
								<div class="desc"><?php echo wp_kses_post( $tab_form['desc'] ); ?></div>
								<?php cmb2_metabox_form( $tab_form, $tab_form['id'], $tab_form ); ?>
							</div>
						</div>
					</div>

				<?php endforeach; ?>

			</div>

			<div class="sidebar_cell">
				
				<?php
				$sidebar = self::sidebar_display();
				echo apply_filters( 'wpgetapi_admin_sidebar_display', $sidebar );
				?>

			</div>

		</div>

		<?php

	}


	public function test_the_endpoint() {
		
		$api_id = sanitize_text_field( $_POST['api_id'] );
		$endpoint_id = sanitize_text_field( $_POST['endpoint_id'] );
		$data = wpgetapi_endpoint( $api_id, $endpoint_id, array( 'test_endpoint' => true ) );

		$output = array( 
			'data' => $data, 
			'endpoint_id' => $endpoint_id 
		);
	    echo json_encode( $output );

		wp_die(); // this is required to terminate immediately and return a proper response

	}


	public function load_testing_javascript() { ?>

		<script type="text/javascript" >

		jQuery(document).ready(function($) {

			$('body').on( 'click', '.wpgetapi-test-area .test-button', function(){//delegated

		        var $this = $(this);
		        var area = $this.parents( '.wpgetapi-test-area' );
		        var api_id = $( area ).data( 'api' );
		        var endpoint_id = $( area ).data( 'endpoint' );

		        // disable button
		        $this.attr( 'disabled', true );

				var data = {
					'action': 'wpgetapi_test_endpoint',
					'api_id': api_id,
					'endpoint_id': endpoint_id
				};

				// send and get response
				jQuery.post(ajaxurl, data, function( response ) {
					
					var output = JSON.parse( response );

					jQuery( '.wpgetapi-test-area[data-endpoint="' + output.endpoint_id + '"] .handle' ).show();

					jQuery( '.wpgetapi-test-area[data-endpoint="' + output.endpoint_id + '"] .wpgetapi-result' ).html( 
						'<div class="side-notice"><p>These results contain the raw output of your API call as well as extra debugging information for testing purposes. <br>' +
						'Using the shortcode or the template tag on the front end will display the Data Output section as shown below. The data can be formatted any way you like, depending on your requirements.</p></div>' +
						output.data
					);

					// enable button
		        	$( '.wpgetapi-test-area[data-endpoint="' + output.endpoint_id + '"] .test-button' ).attr( 'disabled', false );

				});

		    });

		    $( '.wpgetapi-test-area .handle' ).on('click', function() {
			    $('.wpgetapi-result').toggle();
			});

		});

		</script> <?php

	}


	/**
	 * Sidebar markup.
	 * @since  1.4.1
	 */
	public function sidebar_display() {

		ob_start();

		// do our setup page
        if( isset( $_GET['page'] ) && $_GET['page'] === 'wpgetapi_setup' )
            return $this->sidebar_setup_page();

		?>

			<div class="box">
                <h3><?php esc_html_e( 'Endpoint Instructions', 'wpgetapi' ); ?></h3>
                <ol>
                	<li><?php esc_html_e( 'Fill in the Unique ID with lowercase letters and underscores only. something_like_this', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'Add the Endpoint which can be found in the docs of your API.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'Set the Method. Usually GET for getting data from the API and POST when sending data to the API.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'Set the desired Results Format.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'The rest of the fields are optional and will depend on the API you using.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'Hit the Save button.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'After saving, copy the Template Tag or the Shortcode and paste into appropriate place (page, post, theme file).', 'wpgetapi' ); ?></li>
                </ol>
            </div>
             
            <hr> 

           	<div class="box">    
           		<strong><?php esc_html_e( 'Getting Help', 'wpgetapi' ); ?></strong>     
                <ul>
                	<li><?php 
					printf(
						esc_html__( 'Visit our website to %1$s.', 'cmb2' ),
						'<a target="_blank" href="https://wpgetapi.com/docs/?utm_campaign=Docs&utm_medium=Admin&utm_source=User">view the docs</a>'
					);
					?></li>
                </ul>  
            </div>

		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}


    /**
     * Sidebar for our setup page.
     * @since 1.4.4
     */
    public function sidebar_setup_page() {

        ob_start();
        ?>

            <div class="box">
                <h3><?php esc_html_e( 'Setup Instructions', 'wpgetapi' ); ?></h3>
                <ol>
                	<li><?php esc_html_e( 'Fill in the API Name. You can name this whatever you like.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'Fill in the Unique ID with lowercase letters and underscores only. something_like_this', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'Add the Base URL of your API. This can be found in the docs of your API.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'Hit the Save button.', 'wpgetapi' ); ?></li>
                	<li><?php esc_html_e( 'After saving, visit the new tab that will be created.', 'wpgetapi' ); ?></li>
                </ol>
            </div>
            
            <hr>

           	<div class="box">    
           		<strong><?php esc_html_e( 'Getting Help', 'wpgetapi' ); ?></strong>     
                <ul>
                	<li><?php 
					printf(
						esc_html__( 'Visit our website to %1$s.', 'cmb2' ),
						'<a target="_blank" href="https://wpgetapi.com/docs/?utm_campaign=Docs&utm_medium=Admin&utm_source=User">view the docs</a>'
					);
					?></li>
                </ul>  
            </div>

        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;

    }


	/**
	 * Tabs don't immmediately appear on save, so this hack!
	 * @since  0.1.0
	 */
	public function redirect( $object_id, $updated ) {

		if( $object_id == 'wpgetapi_setup' ) {
			
			add_settings_error( 'wpgetapi-notices', '', 'Saved, reloading page...', 'updated' );
			settings_errors( 'wpgetapi-notices' );
			?>

			<script>
				setTimeout(function() {
				    location.reload();
				}, 1000);
			</script>
			<?php

		} else if ( strpos( $object_id, 'wpgetapi_' ) !== false ) {

			$text = apply_filters( 'wpgetapi_admin_notice_text', __( 'Saved.', 'wpgetapi' ), $object_id );
			add_settings_error( 'wpgetapi-notices', '', $text, 'updated' );
			settings_errors( 'wpgetapi-notices' );

		}

	}


	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'fields', 'title', 'options_pages' ), true ) ) {
			return $this->{$field};
		}
		if ( 'option_metabox' === $field ) {
			return $this->option_fields();
		}
		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the WpGetApi_Admin_Options object
 * @since  0.1.0
 * @return WpGetApi_Admin_Options object
 */
function wpgetapi_admin_options() {
	return WpGetApi_Admin_Options::get_instance();
}

// Get it started
wpgetapi_admin_options();

endif;