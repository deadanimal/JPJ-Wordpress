<?php
namespace H5AP\Base;

class AdminNotice{
    
    protected static $_instance = null;

    /**
     * construct function
     */
    public function __construct(){
        add_action('admin_notices',[$this, 'importNotice']);
        add_action('init', [$this, 'init']);
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

    public function importNotice(){
        $screen = get_current_screen();
        $closed = (boolean)get_option('h5ap-notice-import', false);


		if($screen->base === 'plugins' && isset($_GET['h5ap-import'])){
			echo "<div class='notice notice-success is-dismissible'><p>Data Imported successfully. have fun!</p></div>";
		}

        if($screen->post_type === 'audioplayer' && !$closed && !isset($_GET['h5ap-notice-import'])){
            echo "<div class='notice notice-warning h5ap-notice h5ap-notice-import'><p>If you lost your data please import data by navigating to Plugins > Html5 Audio Player > Import data.</p><p><a href='".site_url("wp-admin/$screen->parent_file&h5ap-notice-import=true")."'>Close</a></p></div>";
        }
    }

    public function init(){
        if(isset($_GET['h5ap-notice-import']) && $_GET['h5ap-notice-import'] == 'true'){
            update_option('h5ap-notice-import', true);
        }
    }
}
AdminNotice::instance();