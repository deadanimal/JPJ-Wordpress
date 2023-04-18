<?php
namespace H5AP\PostType;

class AudioPlayer{
    protected static $_instance = null;
    protected $post_type = 'audioplayer';

    /**
     * construct function
     */
    public function register(){
        add_action('init', [$this, 'init']);
        if ( is_admin() ) {
            add_filter( 'post_row_actions', [$this, 'h5ap_remove_row_actions'], 10, 2 );
            add_action('edit_form_after_title', [$this, 'h5ap_shortcode_area']);
            add_filter('manage_audioplayer_posts_columns', [$this, 'h5ap_columns_head_only_audioplayer'], 10);
            add_action('manage_audioplayer_posts_custom_column', [$this, 'h5ap_columns_content_only_audioplayer'], 10, 2);
            add_filter('post_updated_messages', [$this, 'h5ap_updated_messages']);

            add_action('admin_head-post.php', [$this, 'h5ap_hide_publishing_actions']);
            add_action('admin_head-post-new.php', [$this, 'h5ap_hide_publishing_actions']);	
            add_filter( 'gettext', [$this, 'h5ap_change_publish_button'], 10, 2 );

            // add_action('use_block_editor_for_post', [$this, 'forceGutenberg'], 10, 2);
            // add_filter( 'filter_block_editor_meta_boxes', [$this, 'remove_metabox'] );
            
        }
    }

    /**
     * Create instance function
     */
    public static function instance(){
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * init
     */
    public function init(){
        register_post_type( 'audioplayer',
            array(
                'labels' => array(
                    'name' => __( 'Html5 Audio Player'),
                    'singular_name' => __( 'Audio Player' ),
                    'add_new' => __( 'Add Audio Player' ),
                    'add_new_item' => __( 'Add New Player' ),
                    'edit_item' => __( 'Edit Player' ),
                    'new_item' => __( 'New Player' ),
                    'view_item' => __( 'View Player' ),
                    'search_items'       => __( 'Search Player'),
                    'not_found' => __( 'Sorry, we couldn\'t find the Player you are looking for.' )
                ),
                'public' => false,
                'show_ui' => true, 									
                // 'publicly_queryable' => true,
                // 'exclude_from_search' => true,
                'show_in_rest' => true,
                'menu_position' => 14,
                'menu_icon' => H5AP_PLUGIN_DIR .'/img/icn.png',
                'has_archive' => false,
                'hierarchical' => false,
                'capability_type' => 'page',
                'rewrite' => array( 'slug' => 'audioplayer' ),
                'supports' => array( 'title' ),
                // 'template' => [
                //     ['h5ap/parent']
                // ],
                // 'template_lock' => 'all',
            )
        );
    }

    /**
     * Remove Row
     */
    function h5ap_remove_row_actions( $idtions ) {
        global $post;
        if( $post->post_type == 'audioplayer') {
            unset( $idtions['view'] );
            unset( $idtions['inline hide-if-no-js'] );
        }
        return $idtions;
    }

    function h5ap_shortcode_area(){
        global $post;	
        if($post->post_type=='audioplayer'){
        ?>	
        <div class="h5ap_playlist_shortcode">
                <div class="shortcode-heading">
                    <div class="icon"><span class="dashicons dashicons-format-audio"></span> <?php _e("HTML5 Audio Player", "h5ap") ?></div>
                    <div class="text"> <a href="https://bplugins.com/support/" target="_blank"><?php _e("Supports", "h5ap") ?></a></div>
                </div>
                <div class="shortcode-left">
                    <h3><?php _e("Shortcode", "h5ap") ?></h3>
                    <p><?php _e("Copy and paste this shortcode into your posts, pages and widget:", "h5ap") ?></p>
                    <div class="shortcode" selectable>[player id='<?php echo esc_attr($post->ID); ?>']</div>
                </div>
                <div class="shortcode-right">
                    <h3><?php _e("Template Include", "h5ap") ?></h3>
                    <p><?php _e("Copy and paste the PHP code into your template file:", "h5ap"); ?></p>
                    <div class="shortcode">&lt;?php echo do_shortcode('[player id="<?php echo esc_html($post->ID); ?>"]');
                    ?&gt;</div>
                </div>
            </div>
        <?php   
        }
    }
    
    // CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
    function h5ap_columns_head_only_audioplayer($defaults) {
        unset($defaults['date']);
        $defaults['directors_name'] = 'ShortCode';
        $defaults['date'] = 'Date';
        return $defaults;
    }

    function h5ap_columns_content_only_audioplayer($column_name, $post_ID) {
        if ($column_name == 'directors_name') {
            echo '<div class="h5ap_front_shortcode"><input style="text-align: center; border: none; outline: none; background-color: #1e8cbe; color: #fff; padding: 4px 10px; border-radius: 3px;" value="[player id='. esc_attr($post_ID) . ']" ><span class="htooltip">Copy To Clipboard</span></div>';
        }
    }
    
    function h5ap_updated_messages( $messages ) {
        $messages['audioplayer'][1] = __('Player updated ');
        return $messages;
    }

    public function h5ap_hide_publishing_actions(){
        $my_post_type = 'audioplayer';
        global $post;
        if($post->post_type == $my_post_type){
            echo '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
        }
    }

    function h5ap_change_publish_button( $translation, $text ) {
        if ( 'audioplayer' == get_post_type())
        if ( $text == 'Publish' )
            return 'Save';
        return $translation;
    }

    function remove_metabox($metaboxs) {
        global $post;
        $screen = get_current_screen();

        if($screen->post_type === $this->post_type){
            return false;
        }
        return $metaboxs;
    }
    
    /**
     * Force gutenberg in case of classic editor
     */
    public function forceGutenberg($use, $post)
    {
        if ($this->post_type === $post->post_type) {
            
            $isGutenberg = (boolean) get_post_meta($post->ID, 'h5ap_gutenberg_enable', true);
            if($post->post_status == 'auto-draft' ){
                update_post_meta($post->ID, 'h5ap_gutenberg_enable', true);
                return true;
            }
            if( $isGutenberg === true){
                return true;
            }else {
                remove_post_type_support($this->post_type, 'editor');
            }
            return $use;
        }

        return $use;
    }
}
// AudioPlayer::instance();