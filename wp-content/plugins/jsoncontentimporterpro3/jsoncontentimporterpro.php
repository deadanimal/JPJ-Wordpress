<?php
/*
Plugin Name: JSON Content Importer Pro
Plugin URI: https://json-content-importer.com/
Description: PRO-Version - Plugin and widget to import, cache and display a JSON-API/-Feed. Display is done with wordpress-shortcode and a templateengine.
Version: 3.7.3
Author: Bernhard Kux
Author URI: http://www.kux.de/
*/

/* block direct requests */
if ( !function_exists( 'add_action' ) ) {
	echo 'Hello, this is a plugin: You must not call me directly.';
	exit;
}
defined('ABSPATH') OR exit;

define( 'JCIPRO_VERSION', '3.7.3' ); // current version number
define( 'EDD_JCIPRO_STORE_URL', 'https://json-content-importer.com' );
define( 'EDD_JCIPRO_ITEM_NAME', 'Download JSON Content Importer PRO' );

$jci_pro_php_timeout = @get_option('jci_pro_php_timeout');
if ($jci_pro_php_timeout>0) {
	set_time_limit($jci_pro_php_timeout); # set timeout for execution of shortcode
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(!class_exists('JsonContentImporterPro')){
  require_once plugin_dir_path( __FILE__ ) . '/class-json-content-importer-pro.php';
}

require_once plugin_dir_path( __FILE__ ) . '/options.php';

# do not execute the plugin when saving a page with it in the admin-area
if (!is_admin()) {
		$JsonContentImporterPro = new JsonContentImporterPro();
}
	
if (isset($_GET["page"])) {
	if (("jciprostep2usejsonslug" == $_GET["page"]) || ("jciprostep1getjsonslug" == $_GET["page"])) {  # allow executing JCI-Shortcode on some admin-pages
		$JsonContentImporterPro = new JsonContentImporterPro();
	}
}

#######GB-BLOCK begin
/**/
class jciProGutenberg {
	private $gutenbergIsActive = FALSE;
	private $gutenbergPluginIsActive = FALSE;
	private $itIsWP5 = FALSE;
	private $gutenbergMessage = ""; 

	function __construct()
    {
		$this->buildGutenbergMessage("#f00", __("Gutenberg not available", 'json-content-importer'));
		$this->checkGutenbergIsActive();
    }	
	
	private function checkGutenbergIsActive()
	{
		@$jci_gutenberg_off_option_value = @get_option('jci_gutenberg_off');
		if (1==$jci_gutenberg_off_option_value) {
			#$this->gutenbergMessage = "Gutenberg-Mode of Plugin switched of in Options";
			$this->buildGutenbergMessage("#f00", __("Gutenberg-Mode of Plugin switched off in Options", 'json-content-importer'));
			#return TRUE;
		} else {
			# previous to 5.0 the constant GUTENBERG_VERSION indicates, that the Gutenberg-Plugin is active
			$this->gutenbergPluginIsActive = (true === defined('GUTENBERG_VERSION'));
			if ($this->gutenbergPluginIsActive) {
				$this->gutenbergIsActive = TRUE;
				$this->buildGutenbergMessage("#3db634", __('Gutenberg-Plugin-Mode', 'json-content-importer'));
			}
			# things change from 5.0 on
			$this->itIsWP5 = version_compare(get_bloginfo('version'),'5.','>='); # ????? 5. // 5.0
			if ($this->itIsWP5) {
				# maybe the classic editor plugin is active in wp 5.0
				
				
				#if ( ! function_exists( 'is_plugin_active' ) ) {
				#	include_once ABSPATH . 'wp-admin/includes/plugin.php';
				#}
				if ( class_exists( 'Classic_Editor' ) ) {
				#if (is_plugin_active( 'classic-editor/classic-editor.php' )) {
					$this->buildGutenbergMessage("#f00", __('No Gutenberg: Classic Editor Plugin active', 'json-content-importer'));
				} else {
					$this->gutenbergIsActive = TRUE;
					$this->buildGutenbergMessage("#3db634", __('Gutenberg-WP5-Mode', 'json-content-importer'));
				}
			}
		}
		define( 'JCIPRO_GUTENBERG_PLUGIN_MESSAGE', $this->gutenbergMessage );
		
	}

	public function getGutenbergIsActive()
	{
		return $this->gutenbergIsActive;
	}

	private function buildGutenbergMessage($color, $message)
	{
		$this->gutenbergMessage = '<a style="color:'.$color.'; font-weight: bold;" href="https://wordpress.org/gutenberg/" target="_blank">'.$message.'</a>';
	}
}


if (!isset($jciproGB)) {
	$jciproGB = new jciProGutenberg();
}


if ( $jciproGB->getGutenbergIsActive() ) {
	define( 'JCI_PRO_BLOCK_VERSION', '0.1' );
	if ( ! defined( 'JCI_PRO_BLOCK_NAME' ) ) {
		define( 'JCI_PRO_BLOCK_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
	}
	if ( ! defined( 'JCI_PRO_BLOCK_DIR' ) ) {
		define( 'JCI_PRO_BLOCK_DIR', WP_PLUGIN_DIR . '/' . JCI_PRO_BLOCK_NAME );
	}
	if ( ! defined( 'JCI_PRO_BLOCK_URL' ) ) {
		define( 'JCI_PRO_BLOCK_URL', WP_PLUGIN_URL . '/' . JCI_PRO_BLOCK_NAME );
	}
	require_once( JCI_PRO_BLOCK_DIR . '/block/index.php' );
 
}
/**/
#######GB-BLOCK end

register_activation_hook( __FILE__, 'create_jci_pro_plugin_db' ); # when activating plugin: create db if needed
register_deactivation_hook( __FILE__, 'deactivate_jci_pro_plugin_db' );
add_action( 'wpmu_new_blog', 'jci_new_blog', 10, 6); # in case a new blog is added

$val_jci_pro_use_wpautop = get_option('jci_pro_use_wpautop');
if ($val_jci_pro_use_wpautop==3) {
  remove_filter( 'the_content', 'wpautop' );
  remove_filter( 'the_excerpt', 'wpautop' );
}

## BEGIN: Load Libs
function jci_enqueue_onpageids() {
	$val_jci_pro_load_libs_pageids = @get_option('jci_pro_load_libs_pageids');
	$page_id = (int) get_queried_object_id();
	$pageidfound = FALSE;
	if (empty($val_jci_pro_load_libs_pageids)) {
		return TRUE;
	}
	if ($page_id>0 && !empty($val_jci_pro_load_libs_pageids)) {
		$val_jci_pro_load_libs_pageidsArr = explode(",", $val_jci_pro_load_libs_pageids);
		foreach ($val_jci_pro_load_libs_pageidsArr as $pid) {
			$pidint = (int) trim($pid);
			if ($page_id==$pidint) {
				$pageidfound = TRUE;
				break;
			}
		}
	}
	return $pageidfound;
}

if (get_option('jci_pro_load_jquery') == 1) {
	add_action( 'wp_enqueue_scripts', 'jci_enqueue_jquery' );
	function jci_enqueue_jquery() {
		if (jci_enqueue_onpageids()) {
			wp_enqueue_script( 'jci_enqueue_jquery', plugins_url('/js/jquery/jquery-3.5.1.min.js', __FILE__) );
		}
	}
}
if (get_option('jci_pro_load_jqueryui') == 1) {
	add_action( 'wp_enqueue_scripts', 'jci_enqueue_jqueryui' );
	function jci_enqueue_jqueryui() {
		if (jci_enqueue_onpageids()) {
			wp_enqueue_script( 'jci_enqueue_jqueryui', plugins_url('/js/jquery/jquery-ui.min.js', __FILE__) );
		}
	}
}
if (get_option('jci_pro_load_jqueryuitouchpunch') == 1) {
	add_action( 'wp_enqueue_scripts', 'jci_enqueue_jqueryuitouchpunch' );
	function jci_enqueue_jqueryuitouchpunch() {
		if (jci_enqueue_onpageids()) {
			wp_enqueue_script( 'jci_enqueue_jqueryuitouchpunch', plugins_url('/js/jquery/jqueryui-touch-punch-0.2.3.min.js', __FILE__) );
		}
	}
}
if (get_option('jci_pro_load_jqueryuicss') == 1) {
	add_action( 'wp_enqueue_scripts', 'jci_enqueue_jqueryuicss' );
	function jci_enqueue_jqueryuicss() {
		if (jci_enqueue_onpageids()) {
			wp_enqueue_style( 'jci_enqueue_jqueryuicss', plugins_url('/js/jquery/jquery-ui.min.css', __FILE__) );
		}
	}
}

if (get_option('jci_pro_load_jquerymobilejs') == 1) {
	add_action( 'wp_enqueue_scripts', 'jci_enqueue_jquerymobilejs' );
	function jci_enqueue_jquerymobilejs() {
		if (jci_enqueue_onpageids()) {
			wp_enqueue_script( 'jci_enqueue_jquerymobilejs', plugins_url('/js/jquery/jquery.mobile-1.4.5.min.js', __FILE__) );
		}
	}
}
if (get_option('jci_pro_load_jquerymobilecss') == 1) {
	add_action( 'wp_enqueue_scripts', 'jci_enqueue_jquerymobilecss' );
	function jci_enqueue_jquerymobilecss() {
		if (jci_enqueue_onpageids()) {
			wp_enqueue_style( 'jci_enqueue_jquerymobilecss', plugins_url('/js/jquery/jquery.mobile-1.4.5.min.css', __FILE__) );
		}
	}
}
if (get_option('jci_pro_load_foundationfloatmincss') == 1) {
	add_action( 'wp_enqueue_scripts', 'jci_enqueue_foundationfloatmincss' );
	function jci_enqueue_foundationfloatmincss() {
		if (jci_enqueue_onpageids()) {
			wp_enqueue_style( 'jci_enqueue_foundationfloatmincss', plugins_url('/css/foundation/foundation-float.min.css', __FILE__) );
		}
	}
}
## END: Load Libs


// BEGIN - add Quicktag to Text Editor - 20200110 - does not work stable
/*
add_action( 'admin_print_footer_scripts', 'jcipro_add_quicktags' );
function jcipro_add_quicktags() {
	if ( wp_script_is( 'quicktags' ) ) { 
		wp_enqueue_script('jquery');
		?>
		<script type="text/javascript">
           QTags.addButton( 'jciprotemplates', 'JCI-Templates: Wait till page is completely loaded', function(){} );
           jQuery(window).on("load", function() {
               jQ = jQuery;
               var s = jQ('<select />');
               s.attr('id','myjciprotemplate');
				<?php
					global $wpdb;
					$what = " id, nameoftemplate, urloftemplate ";
					$getTemplateNames = $wpdb->get_results( 'SELECT '.$what.' FROM ' . $wpdb->prefix . 'plugin_jci_pro_templates ORDER BY id DESC LIMIT 25');
					$count = 0;
					$drtxt = ' jQ(\'<option />\', {value: \'\', text: \'JCI-Templates\'}).appendTo(s);';
					foreach ($getTemplateNames as $k => $row) {
						if (!empty($row->{"nameoftemplate"})) {
							if ($count>19) { continue; }
							$drtxt .= ' jQ(\'<option />\', {value: \''.htmlentities($row->{"nameoftemplate"}, TRUE).'\', text: \''.htmlentities($row->{"nameoftemplate"}, TRUE).' ('.$row->{"id"}.')\'}).appendTo(s);';
							$count++;
						}
					}
					echo $drtxt;

				?>
				if (typeof jQ('#qt_content_jciprotemplates')[0] != "undefined") {
					jQ('#qt_content_jciprotemplates')[0].outerHTML = s[0].outerHTML;
				}
				jQ('#myjciprotemplate').on('change', function(){
					var sc = '[jsoncontentimporterpro debugmode=10 nameoftemplate=' + jQ(this).val() + ']';
					QTags.insertContent(sc);
				});
			});
           </script>
		   
	<?php }
}
*/
// END - add Quicktag to Text Editor - 20200110



##################
# View details link in plugin list
class ViewDetails {
	public function show() {
		if ( is_admin() ) {
			add_filter( 'plugin_row_meta', array( $this, 'plugin_viewdetail' ), 10, 4 );
		}
	}
	public function plugin_viewdetail($links, $plugin_file, $plugin_data) {
		if ( strpos( $plugin_file, 'jsoncontentimporterpro.php' ) !== false ) {
			$links[] = '<a href="https://www.json-content-importer.com" target="_blank" title="Plugin Website">Plugin Website</a>';
			#$links[] = sprintf( '<a href="%s" class="thickbox" title="%s">%s</a>',
			#	self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='.'jsoncontentimporterpro'.'&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
			#	esc_attr( sprintf( __( 'Details about %s' ), $plugin_data['Name'] ) ),
			#	__( 'View details' )
		}

		return $links;
	}
}
$ViewDetailsC = new ViewDetails;
$ViewDetailsC->show();
##############

/* WIDGET BEGIN */
if (PHP_VERSION_ID && (PHP_VERSION_ID < 7.2)) {
	require_once plugin_dir_path( __FILE__ ) . '/class-json-content-widget.php';
	add_filter('widget_text', 'do_shortcode');
	#add_action('widgets_init', create_function('', 'return register_widget("jci_widget_plugin");'));
	function jci_widget_plugin_func () {
		register_widget('jci_widget_plugin');
	}
	add_action ('widgets_init', 'jci_widget_plugin_func');
}
/* WIDGET END */

/* EDD BEGIN: update */
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

add_action( 'admin_notices', 'jcipro_update348message' );
function jcipro_update348message() {
		$templateDBversion = get_option( 'plugin_jci_pro_templates_version' );
		if ($templateDBversion!='1.4') {
			if (jcipro_isDBok()) {
				#db ok, but flag wrong: fix flag
				update_option('plugin_jci_pro_templates_version','1.4');
				?>
				<div class="error notice">
					<p><?php _e( 'JCI-Databasecheck: successful' ); ?></p>
				</div>
				<?PHP
			} else {
				?>
				<div class="error notice">
				<p><?php _e( 'JSON Content Importer PRO Plugin: <b>Deactivate and then activate the plugin, please.</b><br>This will update the database of the new plugins template-manager ('.$templateDBversion.').' ); ?></p>
				</div>
				<?php
			}
		}
}

function jcipro_isDBok() {
    global $wpdb;
	@$tmpl = @$wpdb->get_row( 'DESCRIBE ' . $wpdb->prefix . 'plugin_jci_pro_templates', OBJECT, 13 );
    if (is_null(@$tmpl)) {
		return FALSE;
	}
	if ("debugmode"===@$tmpl->Field) {
		#field 13 is debugmode: db ok
		return TRUE;
	}
	return FALSE;
}

function edd_sl_jcipro_plugin_updater() {
	$license_key = trim( get_option( 'edd_jcipro_license_key' ) );
	$edd_updater = new EDD_SL_Plugin_Updater( EDD_JCIPRO_STORE_URL, __FILE__, array(
			'version' 	=> JCIPRO_VERSION,
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => EDD_JCIPRO_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'Bernhard Kux',  // author of this plugin
			'beta'		=> false
		)
	);
}
add_action( 'admin_init', 'edd_sl_jcipro_plugin_updater', 0 );
/* EDD END */

$getShowIn = "";
if (isset($_FILES['p']['tmp_name']) && (!empty($_FILES['p']['tmp_name']))) {
#if (""!=@$_FILES['p']['tmp_name']) {
	/*
	#var_Dump($_FILES);
	$imgtype = $_FILES['p']['type'];
	$imgname = $_FILES['p']['tmp_name'];
	$imgtmpname = $_FILES['p']['tmp_name'];
	$pstr = file_get_contents($imgtmpname);
	$pstr = base64_encode($pstr);
    	#echo '<img src="data:'.$imgtype.';base64, '.$pstr.'" alt="'.$imgname.'" />';
	exit;
	*/
}

if (isset($_GET["show"])) {
  $getShowIn = htmlentities($_GET["show"], ENT_QUOTES);
}
if ("oc"==$getShowIn) {
  add_filter("template_include", "jci_func_showOnlyContent", 11);
  function jci_func_showOnlyContent() {
     $shortcodeOnlyTemplate = dirname( __FILE__ ) . '/themes/onlythecontent/themeOnlyTheContent.php';
     return $shortcodeOnlyTemplate;
  }

  ### BEGIN workaround CDATA & Wordpress: WP converts ]]> to ]]&gt; by default in the core
  function cdata_fix($content) {
    $content = str_replace("]]&gt;", "]]>", $content);
    return $content;
  }
  function cdata_template_redirect( $content ) {
    ob_start('cdata_fix');
  }
  add_action('template_redirect','cdata_template_redirect',-1);
  ### END workarround CDATA & Wordpress , thank you https://sqlbuddy.de/wordpress-und-cdata-javascript/
}

# added 3.4.0: create and register custom post types BEGIN
$ctin = stripslashes(get_option( 'jci_pro_custom_post_types' ));
if (""!=$ctin) {
  add_action( 'init',
    function() use ( $ctin ) {
      $ctinArr0 = explode("##", $ctin);
      for ($i=0;$i<count($ctinArr0);$i++) {
        $ctinArr1 = explode(";", $ctinArr0[$i]);
        $zorb = array();
        for ($j=0;$j<count($ctinArr1);$j++) {
          $ctinArr2 = explode("=", $ctinArr1[$j]);
          if (!empty($ctinArr2[0]) && !empty($ctinArr2[1])) {
            $zorb[$ctinArr2[0]] = $ctinArr2[1];
          }
        }

      if (!empty($zorb['type']) && !empty($zorb['ptname'])) {
        $zorbArr =
          array(
            'labels' => array(
              'name' => __( $zorb['ptname'] ),
              'singular_name' => __( $zorb['type'] )
            ),
            'supports' => array(
               'custom-fields',
                'title',
	              'editor',
	              'post-thumbnails',
	              'revisions',
            ),
            'public' => true,
            'has_archive' => true,
         
        );
        if (!empty($zorb['ptredirect'])) {
          $zorbArr['rewrite'] = array('slug' => $zorb['ptredirect']);
        }
        register_post_type( $zorb['type'], $zorbArr);
      }
    }
  });
}
# added 3.4.0: create and register custom post types END

# BEGIN set featured image which is an URL 
function jcipro_thumbnail_external_replace( $html, $post_id ) { # inspired by https://wordpress.stackexchange.com/questions/158491/is-it-possible-set-a-featured-image-with-external-image-url and https://pof.chepler.ru/317.html
		$url =  get_post_meta( $post_id, 'featuredimagebyurl', TRUE );
		if (empty($url)) {
			return $html;
		}

		$featuredimagealt =  get_post_meta( $post_id, 'featuredimagealt', TRUE );
		if (empty($featuredimagealt)) {
			$alt = get_post_field( 'post_title', $post_id ) . ' ' . 'thumbnail';
		} else {
			$alt = $featuredimagealt;
		}
		$attr = array( 'alt' => $alt );
		$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, 10, 3 );
		$attr = array_map( 'esc_attr', $attr );
		$attrstr = "";
		foreach ( $attr as $name => $value ) {
			$attrstr .= " $name=" . '"' . $value . '"';
		}

		$featuredimagesrc =  get_post_meta( $post_id, 'featuredimagesrc', TRUE ); #  syntax: <img src=##URL## ##ATTR##>
		
		$fisrc = "<img src=##URL## ##ATTR##>";
		if (!empty($featuredimagesrc)) {
			$fisrc = $featuredimagesrc;
		}
		
		$html = preg_replace("/##URL##/", '"'.esc_url($url).'"', $fisrc);
		$html = preg_replace("/##ATTR##/", $attrstr, $html);
		return $html;
}

function jcipro_thumbnail_url_field( $content, $post_id, $thumbnail_id ){
	$url = get_post_meta( $post_id, 'featuredimagebyurl', TRUE ) ? : ""; 
	if (empty($url)) {
		return $content;
	}
	$out = '<div>'; 
	$linkhref = '<a href="' . esc_url($url) . '" target="_blank">';
	$out .= '<p>'.$linkhref.'<img style="max-width:150px;height:auto;" src="' . esc_url($url) . '"></a></p>'; 
	$out .= '<p>'.$linkhref.'External Link to Image set by JCI-JSON-Import</a></p>'; 
	$out .= '</div>'; 
    return $out . $content;
}
add_filter( 'post_thumbnail_html', 'jcipro_thumbnail_external_replace', 10, PHP_INT_MAX );
add_filter( 'admin_post_thumbnail_html', 'jcipro_thumbnail_url_field', 10, 3 ); 
# END set featured image which is an URL 


## needed for gutenberg-block: get_option
/**/
function jcipro_restapi_get_option() {
	register_rest_route(
		'wp/jci/v1',
		'/get/option/(?P<option>([A-Za-z0-9\_])+)/',
		array(
			'callback'            => function ( $request ) {
				$option = isset( $request['option'] ) ? esc_attr( $request['option'] ) : null;
				$value  = get_option( $option, 'Option '.$option.' not existing' );
				return $value;
			},
			'methods'             => 'GET',
			'permission_callback' => function () {
			 	return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'rest_api_init', 'jcipro_restapi_get_option' );
/**/


?>