<?php
if ( ! defined('ABSPATH')) exit;  // if direct access



add_filter('accordions_metabox_navs', 'accordions_pro_metabox_navs');

function accordions_pro_metabox_navs($settings_tabs){

    global $post;
    $post_id = $post->ID;

    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $current_tab = isset($accordions_options['current_tab']) ? $accordions_options['current_tab'] : 'query_member';

    $settings_tabs[] = array(
        'id' => 'stats',
        'title' => sprintf(__('%s Stats','accordions-pro'), '<i class="fas fa-chart-line"></i>'),
        'priority' => 5,
        'active' => ($current_tab == 'stats') ? true : false,

    );

    foreach ($settings_tabs as $tabIndex=>$tab){
        $tab_id = isset($tab['id']) ? $tab['id'] : '';

        if($tab_id == 'buy_pro'){
            unset($settings_tabs[$tabIndex]);
        }

    }


    return $settings_tabs;
}

















add_action('accordions_metabox_content_general', 'accordions_pro_metabox_content_general', 20);

function accordions_pro_metabox_content_general($post_id){
    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id, 'accordions_options', true);
    $accordion = isset($accordions_options['accordion']) ? $accordions_options['accordion'] : array();

    $enable_search = isset($accordion['enable_search']) ? $accordion['enable_search'] : '';
    $search_placeholder_text = isset($accordion['search_placeholder_text']) ? $accordion['search_placeholder_text'] : '';

    ?>

    <div class="section">
        <?php
        $args = array(
            'id'		=> 'enable_search',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Enable search','accordions-pro'),
            'details'	=> __('Display search input field.','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $enable_search,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'search_placeholder_text',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Search placeholder text','accordions-pro'),
            'details'	=> __('You can set custom placeholder text.','accordions-pro'),
            'type'		=> 'text',
            'value'		=> $search_placeholder_text,
            'placeholder' => __('Search here...','accordions-pro'),
        );

        $settings_tabs_field->generate_field($args);




        ?>
    </div>
        <?php

}


add_action('accordions_metabox_content_accordion_options', 'accordions_pro_metabox_content_accordion_options', 20);

function accordions_pro_metabox_content_accordion_options($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordion = isset($accordions_options['accordion']) ? $accordions_options['accordion'] : array();
    $enable_url_hash = isset($accordion['enable_url_hash']) ? $accordion['enable_url_hash'] : '';

    $click_scroll_top = isset($accordion['click_scroll_top']) ? $accordion['click_scroll_top'] : '';
    $click_scroll_top_offset = isset($accordion['click_scroll_top_offset']) ? $accordion['click_scroll_top_offset'] : '';
    $header_toggle = isset($accordion['header_toggle']) ? $accordion['header_toggle'] : '';
    $animate_style = isset($accordion['animate_style']) ? $accordion['animate_style'] : '';

    $animate_delay = isset($accordion['animate_delay']) ? $accordion['animate_delay'] : '';
    $expand_collapse_display = isset($accordion['expand_collapse_display']) ? $accordion['expand_collapse_display'] : '';
    $expand_collapse_bg_color = isset($accordion['expand_collapse_bg_color']) ? $accordion['expand_collapse_bg_color'] : '';
    $expand_collapse_text = isset($accordion['expand_collapse_text']) ? $accordion['expand_collapse_text'] : '';

    $is_child = isset($accordion['is_child']) ? $accordion['is_child'] : '';

    ?>

    <div class="section">
        <?php

        $args = array(
            'id'		=> 'enable_url_hash',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Enable url hash','accordions-pro'),
            'details'	=> __('Enable url hash','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $enable_url_hash,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'click_scroll_top',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Click header to scroll top','accordions-pro'),
            'details'	=> __('When click on accordion header it will automatically scroll top','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $click_scroll_top,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'click_scroll_top_offset',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Offset from top when scroll top enabled','accordions-pro'),
            'details'	=> __('Offset Value: add some value to fix the top position, ex: 120, or -120','accordions-pro'),
            'type'		=> 'text',
            'value'		=> $click_scroll_top_offset,
            'placeholder' => __('120','accordions-pro'),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'header_toggle',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Enable header text toggle','accordions-pro'),
            'details'	=> __('When user click on header text will toggled','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $header_toggle,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'animate_style',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Animation style','accordions-pro'),
            'details'	=> __('Animation style for accordion','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $animate_style,
            'args'		=> array(
                'linear'	=> __('linear','accordions-pro'),
                'swing'	=> __('swing','accordions-pro'),
                'easeInQuad'	=> __('easeInQuad','accordions-pro'),
                'easeOutQuad'	=> __('easeOutQuad','accordions-pro'),
                'easeInOutQuad'	=> __('easeInOutQuad','accordions-pro'),
                'easeInCubic'	=> __('easeInCubic','accordions-pro'),
                'easeOutCubic'	=> __('easeOutCubic','accordions-pro'),
                'easeInOutCubic'	=> __('easeInOutCubic','accordions-pro'),
                'easeInQuart'	=> __('easeInQuart','accordions-pro'),
                'easeOutQuart'	=> __('easeOutQuart','accordions-pro'),
                'easeInOutQuart'	=> __('easeInOutQuart','accordions-pro'),
                'easeInQuint'	=> __('easeInQuint','accordions-pro'),
                'easeOutQuint'	=> __('easeOutQuint','accordions-pro'),
                'easeInOutQuint'	=> __('easeInOutQuint','accordions-pro'),
                'easeInExpo'	=> __('easeInExpo','accordions-pro'),
                'easeOutExpo'	=> __('easeOutExpo','accordions-pro'),
                'easeInOutExpo'	=> __('easeInOutExpo','accordions-pro'),
                'easeInSine'	=> __('easeInSine','accordions-pro'),
                'easeOutSine'	=> __('easeOutSine','accordions-pro'),
                'easeInOutSine'	=> __('easeInOutSine','accordions-pro'),
                'easeInCirc'	=> __('easeInCirc','accordions-pro'),
                'easeOutCirc'	=> __('easeOutCirc','accordions-pro'),
                'easeInOutCirc'	=> __('easeInOutCirc','accordions-pro'),

                'easeInElastic'	=> __('easeInElastic','accordions-pro'),
                'easeOutElastic'	=> __('easeOutElastic','accordions-pro'),
                'easeInOutElastic'	=> __('easeInOutElastic','accordions-pro'),
                'easeInBack'	=> __('easeInBack','accordions-pro'),
                'easeOutBack'	=> __('easeOutBack','accordions-pro'),
                'easeInOutBack'	=> __('easeInOutBack','accordions-pro'),
                'easeInBounce'	=> __('easeInBounce','accordions-pro'),
                'easeOutBounce'	=> __('easeOutBounce','accordions-pro'),
                'easeInOutBounce'	=> __('easeInOutBounce','accordions-pro'),


            ),
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'animate_delay',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Animation delay','accordions-pro'),
            'details'	=> __('Animation delay time in millisecond.','accordions-pro'),
            'type'		=> 'text',
            'value'		=> $animate_delay,
            'placeholder' => __('500','accordions-pro'),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'expand_collapse_display',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Display expand/collapse all button.','accordions-pro'),
            'details'	=> __('This is useful to expand/collapse al together.','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $expand_collapse_display,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'		=> 'expand_collapse_bg_color',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Expand/collapse all button background color','accordions-pro'),
            'details'	=> __('Set custom background color for expand/collapse all button','accordions-pro'),
            'type'		=> 'colorpicker',
            'value'		=> $expand_collapse_bg_color,
            'placeholder' => '',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'expand_collapse_text',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Expand/collapse all text','accordions-pro'),
            'details'	=> __('Set custom background text for expand/collapse all button, use | to separate','accordions-pro'),
            'type'		=> 'text',
            'value'		=> $expand_collapse_text,
            'placeholder' => 'Expand all | Collapse all',
        );

        $settings_tabs_field->generate_field($args);


        ?>

    </div>
    <?php

}



add_action('accordions_metabox_content_tabs_options', 'accordions_pro_metabox_content_tabs_options', 20);

function accordions_pro_metabox_content_tabs_options($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $tabs = isset($accordions_options['tabs']) ? $accordions_options['tabs'] : '';


    $tabs_is_vertical = isset($tabs['is_vertical']) ? $tabs['is_vertical'] : '';
    $navs_width_ratio = isset($tabs['navs_width_ratio']) ? $tabs['navs_width_ratio'] : '';
    $tabs_icon_toggle = isset($tabs['tabs_icon_toggle']) ? $tabs['tabs_icon_toggle'] : '';

    ?>

    <div class="section">

        <?php

        $args = array(
            'id'		=> 'is_vertical',
            'parent'		=> 'accordions_options[tabs]',
            'title'		=> __('Vertical tabs','accordions-pro'),
            'details'	=> __('To display vertical tabs.','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $tabs_is_vertical,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'navs_width_ratio',
            'parent'		=> 'accordions_options[tabs]',
            'title'		=> __('Vertical tabs nav width ratio','accordions-pro'),
            'details'	=> __('Width ratio between nav and content.','accordions-pro'),
            'type'		=> 'range',
            'value'		=> $navs_width_ratio,
            'default'		=> '30',

        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'icon_toggle',
            'parent'		=> 'accordions_options[tabs]',
            'title'		=> __('Navs icon toggle','accordions-pro'),
            'details'	=> __('Enable toggling navs icons','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $tabs_icon_toggle,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);



        ?>

    </div>
        <?php






}







add_action('accordions_metabox_content_stats', 'accordions_pro_metabox_content_stats', 10);

function accordions_pro_metabox_content_stats($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id, 'accordions_options', true);

    $accordions_content = isset($accordions_options['content']) ? $accordions_options['content'] : array();

    $enable_stats = isset($accordions_options['enable_stats']) ? $accordions_options['enable_stats'] : '';
    $track_header = get_post_meta($post_id, 'track_header', true);

    //var_dump($track_header);

    ?>

    <div class="section">
        <div class="section-title"><?php echo __('General style','accordions-pro'); ?></div>
        <p class="description section-description"><?php echo __('Some general style options','accordions-pro'); ?></p>

        <?php

        $args = array(
            'id'		=> 'enable_stats',
            'parent'		=> 'accordions_options',
            'title'		=> __('Enable click track on header.','accordions-pro'),
            'details'	=> __('Tracking user interest where users clicked','accordions-pro'),
            'type'		=> 'select',
            'value'		=> $enable_stats,
            'args'		=> array(
                'no'	=> __('No','accordions-pro'),
                'yes'	=> __('Yes','accordions-pro'),
            ),
        );

        $settings_tabs_field->generate_field($args);



        ob_start();
        ?>
        <div class="">
            <table class="widefat" cellspacing="0">
                <thead>
                <tr>

                    <th id="cb" class="manage-column column-cb check-column" scope="col"></th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Header title</th>
                    <th id="columnname" class="manage-column column-columnname num" scope="col">Total Click</th>

                </tr>
                </thead>

                <tfoot>
                <tr>

                    <th class="manage-column column-cb check-column" scope="col"></th>
                    <th class="manage-column column-columnname" scope="col">Header title</th>
                    <th class="manage-column column-columnname num" scope="col">Total Click</th>

                </tr>
                </tfoot>

                <tbody>

                <?php


                if(!empty($accordions_content))
                    foreach ($accordions_content as $index => $accordion){
                        $accordion_header = isset($accordion['header']) ? $accordion['header'] : '';

                        ?><tr>
                        <th class="check-column" scope="row"></th>
                        <td  class="column-columnname"><?php echo $accordion_header; ?></td>
                        <td style="text-align: center" class="column-columnname" scope="col"><?php if(!empty($track_header['header-'.$index])) echo $track_header['header-'.$index]; else echo '0'; ?></td>
                        </tr>

                        <?php
                    }
                ?>

                </tbody>
            </table>
        </div>

        <?php

        $html = ob_get_clean();

        $args = array(
            'id' => 'accordions_items_stats',
            'title' => __('Stats for accordion', 'accordions-pro'),
            'details' => __('You will see click count on accordion headers.', 'accordions-pro'),

            'type' => 'custom_html',
            'html' => $html,
        );
        $settings_tabs_field->generate_field($args);





        ?>

    </div>
    <?php

}





add_filter('accordions_content_fields', 'accordions_pro_content_fields', 10);

function accordions_pro_content_fields($meta_fields){

    $meta_fields_new = array(

            array(
                'id'		=> 'toggled_text',
                'css_id'		=> 'header_TIMEINDEX',
                'title'		=> __('Toggled text','accordions-pro'),
                'details'	=> __('Write toggled text.','accordions-pro'),
                'type'		=> 'text',
                'value'		=> '',
                'default'		=> '',
                'placeholder'   => '',
            ),
            array(
                'id'		=> 'active_icon',
                'css_id'		=> 'header_TIMEINDEX',
                'title'		=> __('Active icon','accordions-pro'),
                'details'	=> __('Set active icon for this section.','accordions-pro'),
                'type'		=> 'text',
                'value'		=> '',
                'default'		=> '',
                'placeholder'   => '',
            ),
            array(
                'id'		=> 'inactive_icon',
                'css_id'		=> 'header_TIMEINDEX',
                'title'		=> __('Inactive icon','accordions-pro'),
                'details'	=> __('Set inactive icon for this section','accordions-pro'),
                'type'		=> 'text',
                'value'		=> '',
                'default'		=> '',
                'placeholder'   => '',
            ),
            array(
                'id'		=> 'background_color',
                'css_id'		=> 'background_color_TIMEINDEX',
                'title'		=> __('Header background color','accordions-pro'),
                'details'	=> __('Set background color for this section header','accordions-pro'),
                'type'		=> 'colorpicker',
                'value'		=> '',
                'default'		=> '',
                'placeholder'   => '',
            ),

            array(
                'id'		=> 'background_img',
                'css_id'		=> 'background_img_TIMEINDEX',
                'title'		=> __('Header background image','accordions-pro'),
                'details'	=> __('Set background image for this section header','accordions-pro'),
                'type'		=> 'media_url',
                'value'		=> '',
                'default'		=> '',
                'placeholder'   => '',
            ),
            array(
                'id'		=> 'is_active',
                'css_id'		=> 'is_active_TIMEINDEX',
                'title'		=> __('Is active','accordions-pro'),
                'details'	=> __('Make this accordion active/open on page load.','accordions-pro'),
                'type'		=> 'select',
                'value'		=> '',
                'default'		=> 'no',
                'args'		=> array(
                    'no'	=> __('No','accordions-pro'),
                    'yes'	=> __('Yes','accordions-pro'),
                ),
            )


    );


    return array_merge($meta_fields, $meta_fields_new);

}