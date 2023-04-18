<?php
if ( ! defined('ABSPATH')) exit;  // if direct access



add_filter('accordions_settings_tabs', 'accordions_pro_settings_tabs');

function accordions_pro_settings_tabs($settings_tabs){

    $current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'general';

    $settings_tabs[] = array(
        'id' => 'license',
        'title' => sprintf(__('%s License','accordions-pro'),'<i class="fas fa-unlock"></i>'),
        'priority' => 20,
        'active' => ($current_tab == 'license') ? true : false,
    );

    foreach ($settings_tabs as $tabIndex=>$tab){
        $tab_id = isset($tab['id']) ? $tab['id'] : '';

        if($tab_id == 'buy_pro'){
            unset($settings_tabs[$tabIndex]);
        }

    }


    return $settings_tabs;
}


add_action('accordions_settings_content_license', 'accordions_pro_settings_content_license');

if(!function_exists('accordions_pro_settings_content_license')) {
    function accordions_pro_settings_content_license($tab){

        $settings_tabs_field = new settings_tabs_field();
        $accordions_settings = get_option( 'accordions_settings' );
        $license_key = isset($accordions_settings['license_key']) ? $accordions_settings['license_key'] : '';

        ?>
        <div class="section">
            <div class="section-title"><?php echo __('Activate license', 'accordions-pro'); ?></div>
            <p class="description section-description"><?php echo __('Activate license to get automatic update.', 'accordions-pro'); ?></p>

            <?php




            //echo '<pre>'.var_export($check_license_on_server, true).'</pre>';

            $args = array(
                'id'		=> 'license_key',
                'parent'		=> 'accordions_settings',
                'title'		=> __('License key','accordions-pro'),
                'details'	=> __('Write your license key here, to get license key please visit <a href="https://www.pickplugins.com/my-account/license-keys/">https://www.pickplugins.com/my-account/license-keys/</a>','accordions-pro'),
                'type'		=> 'text',
                'value'		=> $license_key,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            if(!empty($license_key)):

                $class_accordions_pro_license = new class_accordions_pro_license();
                $check_license_on_server = $class_accordions_pro_license->check_license_on_server($license_key);

                $license_status = isset($check_license_on_server['license_status']) ? $check_license_on_server['license_status'] : '';
                $date_expiry = isset($check_license_on_server['date_expiry']) ? $check_license_on_server['date_expiry'] : '';
                $date_created = isset($check_license_on_server['date_created']) ? $check_license_on_server['date_created'] : '';
                $mgs = isset($check_license_on_server['mgs']) ? $check_license_on_server['mgs'] : '';

                ob_start();
                ?>

                <p>Status: <?php echo $license_status; ?></p>
                <p>Expire date: <?php echo $date_expiry; ?></p>
                <p>Created: <?php echo $date_created; ?></p>
                <p>Message: <?php echo $mgs; ?></p>

                <?php

                $html = ob_get_clean();

                $args = array(
                    'id'		=> 'license_status',
                    'parent'		=> 'accordions_settings',
                    'title'		=> __('License status','accordions-pro'),
                    'details'	=> '',
                    'type'		=> 'custom_html',
                    'html'		=> $html,

                );

                $settings_tabs_field->generate_field($args);
            endif;



            ?>

        </div>
        <?php

    }
}
