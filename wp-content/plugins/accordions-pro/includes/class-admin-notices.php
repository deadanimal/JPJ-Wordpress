<?php
if ( ! defined('ABSPATH')) exit; // if direct access 

class class_accordions_pro_notices{

    public function __construct(){

        add_action('admin_notices', array( $this, 'free_version_missing' ));

    }

    public function free_version_missing(){

        $active_plugins = get_option('active_plugins');

        ob_start();

        if(!in_array( 'accordions/accordions.php', (array) $active_plugins )):
            ?>
            <div class="update-nag">
                <?php
                echo sprintf(__('<a href="%s">Accordions</a> plugin free version is required to work <strong>Accordions Pro</strong> version.', 'accordions-pro'), 'https://wordpress.org/plugins/accordions/')
                ?>
            </div>
        <?php
        endif;


        echo ob_get_clean();
    }

}

new class_accordions_pro_notices();