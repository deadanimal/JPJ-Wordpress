<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


add_action('accordions_main', 'accordions_main_expand_collapse', 10);

function accordions_main_expand_collapse($atts){

    $post_id = isset($atts['id']) ? $atts['id'] : '';
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordion = isset($accordions_options['accordion']) ? $accordions_options['accordion'] : array();

    $expand_collapse_display = isset($accordion['expand_collapse_display']) ? $accordion['expand_collapse_display'] : '';
    $expand_collapse_bg_color = isset($accordion['expand_collapse_bg_color']) ? $accordion['expand_collapse_bg_color'] : '';
    $expand_collapse_text = isset($accordion['expand_collapse_text']) ? $accordion['expand_collapse_text'] : '';

    if(!empty($expand_collapse_text)){
        $expand_collapse_text_arr = explode('|', $expand_collapse_text);

        $expand_all_text = isset($expand_collapse_text_arr[0]) ? $expand_collapse_text_arr[0] : __("Expand all", 'accordions');
        $collapse_all_text = isset($expand_collapse_text_arr[1]) ? $expand_collapse_text_arr[1] : __("Collapse all", 'accordions');
    }else{
        $expand_all_text =  __("Expand all", 'accordions');
        $collapse_all_text =  __("Collapse all", 'accordions');
    }


    if($expand_collapse_display=='yes'){
        ?>
        <div id="expand-collapse-<?php echo $post_id; ?>" class="expand-collapse" accordion-id="<?php echo $post_id; ?>">
            <span class="expand"><i class="fas fa-expand"></i> <?php echo $expand_all_text; ?></span><span class="collapse"><i class="fas fa-compress"></i> <?php echo $collapse_all_text; ?></span>
        </div>
        <script>
            jQuery(document).ready(function($){
                $("#accordions-<?php echo $post_id; ?> .expand-collapse").click(function() {
                    if( $(this).hasClass("active") ) $(this).removeClass("active");
                    else $(this).addClass("active");
                    accordion_id = $(this).attr("accordion-id");
                    $("#accordions-"+accordion_id+" .ui-accordion-header:not(.ui-state-active)").next().slideToggle();
                });
            })
        </script>
        <style type="text/css">
            .accordions-<?php echo $post_id; ?> .expand-collapse{
                background-color: <?php echo $expand_collapse_bg_color; ?> !important;
            }
        </style>
        <?php
    }



}


add_action('accordions_main', 'accordions_main_search', 15);

function accordions_main_search($atts){

    $post_id = isset($atts['id']) ? $atts['id'] : '';
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordion = isset($accordions_options['accordion']) ? $accordions_options['accordion'] : array();

    $enable_search = isset($accordion['enable_search']) ? $accordion['enable_search'] : 'no';
    $search_placeholder_text = isset($accordion['search_placeholder_text']) ? $accordion['search_placeholder_text'] : '';

    if($enable_search == 'yes'){
        ?>
        <div id="search-input-<?php echo $post_id; ?>" class="search-input-wrap" >
            <input class="search-input" placeholder="<?php echo $search_placeholder_text; ?>" value="">
        </div>
        <script>
            jQuery(document).ready(function($){
                jQuery(document).on('keyup', '#search-input-<?php echo $post_id; ?> input.search-input', function(){
                    keyword = jQuery(this).val().toLowerCase();
                    content_head = [];
                    content_body = [];
                    $('#accordions-<?php echo $post_id; ?> .items  .accordions-head-title').each(function( index ) {
                        content = $( this ).text().toLowerCase();
                        content_head[index] = content;
                        $( this ).parent().removeClass("accordion-header-active");
                        $( this ).parent().removeClass("ui-state-active");
                    });
                    $('#accordions-<?php echo $post_id; ?> .items  .accordion-content').each(function( index ) {
                        $( this ).hide();
                        content = $( this ).text().toLowerCase();
                        content_body[index] = content + ' ' + content_head[index];
                        n = content_body[index].indexOf(keyword);
                        if(n<0){
                            $( this ).prev().hide();
                        }else{
                            $( this ).prev().show();
                        }
                    });
                })
            })
        </script>
        <?php
    }
}



add_action('accordions_main', 'accordions_pro_main_scripts', 50);

function accordions_pro_main_scripts($atts){

    $post_id = isset($atts['id']) ? $atts['id'] : '';

    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $enable_stats = isset($accordions_options['enable_stats']) ? $accordions_options['enable_stats'] : 'no';


    $accordion = isset($accordions_options['accordion']) ? $accordions_options['accordion'] : array();
    $enable_url_hash = isset($accordion['enable_url_hash']) ? $accordion['enable_url_hash'] : '';
    $header_toggle = !empty($accordion['header_toggle']) ? $accordion['header_toggle'] : 'no';
    $click_scroll_top = isset($accordion['click_scroll_top']) ? $accordion['click_scroll_top'] : '';
    $click_scroll_top_offset = !empty($accordion['click_scroll_top_offset']) ? $accordion['click_scroll_top_offset'] : 100;
    $expanded_other = isset($accordion['expanded_other']) ? $accordion['expanded_other'] : 'no';

    ?>
    <script>
        jQuery(document).ready(function($){
            $( "#accordions-<?php echo $post_id; ?> .items" ).on( "accordionactivate", function( event, ui ) {
                <?php if($click_scroll_top == 'yes'):?>
                    if(!$.isEmptyObject(ui.newHeader.offset())) {
                        $("html:not(:animated), body:not(:animated)").animate({ scrollTop: ui.newHeader.offset().top + <?php echo $click_scroll_top_offset; ?> }, "slow");
                    }
                <?php endif; ?>
            } );
            if(typeof accordions_active_index_<?php echo $post_id; ?> != 'undefined'){
                for(var k in accordions_active_index_<?php echo $post_id; ?>) {
                    console.log(accordions_active_index_<?php echo $post_id; ?>[k]);
                    index = accordions_active_index_<?php echo $post_id; ?>[k];
                    $("#accordions-<?php echo $post_id; ?> .items").accordion("option", "active", index);
                }
            }
            <?php if($enable_stats =='yes'): ?>
                $("#accordions-<?php echo $post_id; ?> .accordions-head").click(function () {
                    header_id = $(this).attr('header_id');
                    post_id = $(this).attr('post_id');
                    $.ajax({
                        type: 'POST',
                        context: this,
                        url:accordions_ajax.accordions_ajaxurl,
                        data: {
                            "action" 	: "accordions_ajax_track_header",
                            "header_id" : header_id,
                            "post_id" : post_id,
                        },
                        success: function( data ) {
                            //console.log(data);
                        }
                    });
                });
            <?php endif; ?>
            <?php if($header_toggle == 'yes'): ?>
                $("#accordions-<?php echo $post_id; ?> .accordions-head").click(function () {
                    toogle_text = $(this).attr('toggle-text');
                    main_text = $(this).attr('main-text');
                    if( $(this).hasClass('ui-state-active') ){
                        if( toogle_text != null && toogle_text != ''){
                            $(this).children('.accordions-head-title').html(toogle_text);
                        }
                    } else {
                        if( main_text != null  && main_text != ''){
                            $(this).children('.accordions-head-title').html(main_text);
                        }
                    }
                    id = $(this).attr( 'id' );
                });
            <?php endif; ?>
            <?php if($enable_url_hash == 'yes'): ?>
                var hash = window.location.hash;

                //console.log(hash);

                if (hash) {
                    index = $("#accordions-<?php echo $post_id; ?> "+hash).attr('itemcount');

                    if(index){
                        //console.log(index);
                        //index = index.replace('ui-id-','');

                        //header = $("#accordions-<?php echo $post_id; ?> "+hash);

                        index = parseInt(index);
                        //console.log(header.offset().top);


                        //$("html:not(:animated), body:not(:animated)").animate({ scrollTop: header.offset().top + 800 }, "slow");

                        $("#accordions-<?php echo $post_id; ?> .items").accordion("option", "active", index);
                    }

                }
            <?php endif; ?>
        })
    </script>
    <?php
}


