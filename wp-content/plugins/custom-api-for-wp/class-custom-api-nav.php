<?php

require 'custom-api-wp-ui.php';
require 'custom-api-license-purchase.php';
require 'class-mo-custom-api-for-wordpress-trial.php';

function custom_api_wp_main_menu()
{
    $currenttab = "";
    if (isset($_GET['action'])) {
        $currenttab = custom_api_wp_sanitise1($_GET['action']);
    }

    if (isset($_GET['api'])) {
        $api = custom_api_wp_sanitise1($_GET['api']);
    }

    Custom_API_Admin_Menu::custom_api_auth_show_menu($currenttab);
    echo '
	<div id="mo_api_authentication_settings">';
    echo '
		<div class="mo_custom_api_miniorange_container">';
    echo '
		<table style="width:99%;">
			<tr>
				<td style="vertical-align:top;width:100%;" class="mo_api_authentication_content">';
    Custom_API_Admin_Menu::custom_api_auth_show_tab($currenttab);
    echo '</tr>
		</table>
		<div class="mo_api_authentication_tutorial_overlay" id="mo_api_authentication_tutorial_overlay" hidden></div>
		</div></div>';
}

class CustomAPISaleBanner
{
    public static function show_bfs_note()
    {
        ?>
        <div class="notice notice-info"style="padding-right: 38px;position: relative;border-color:red; background-color: #0c082f;
			transform: scaleX(1);
			background-image: url('<?php echo esc_attr(plugin_dir_url(__FILE__)); ?>/images/3px-tile.png');"><h4><center><i class="fa fa-gift" style="font-size:50px;color:red;"></i>&nbsp;&nbsp;
		<big><font style="color:white; font-size:30px;"><b>END OF THE YEAR SALE: </b><b style="color:yellow;">UPTO 50% OFF!</b></font> <br></big><font style="color:white; font-size:15px;">Contact us at <a href="mailto: apisupport@xecurify.com?subject=Custom wp API - Enquirey">apisupport@xecurify.com</a> for more details.</font></center></h4>
		<p style="text-align: center; font-size: 30px; margin-top: 0px; color:white;" id="demo"></p>
		<!-- </div> -->

		<script>
			var countDownDate = <?php echo esc_attr(strtotime('Dec 31, 2021 23:59:59')) ?> * 1000;
			var now = <?php echo esc_attr(time()) ?> * 1000;
			var x = setInterval(function() {
				now = now + 1000;
				var distance = countDownDate - now;
				var days = Math.floor(distance / (1000 * 60 * 60 * 24));
				var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
				var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
				var seconds = Math.floor((distance % (1000 * 60)) / 1000);
				document.getElementById("demo").innerHTML = days + "d " + hours + "h " +
					minutes + "m " + seconds + "s ";
				if (distance < 0) {
					clearInterval(x);
					document.getElementById("demo").innerHTML = "EXPIRED";
				}
			}, 1000);
		</script>
		<?php
    }
}

class Custom_API_Admin_Menu
{
    public static function custom_api_auth_show_menu($currenttab)
    {?>
		<div class="wrap">
			<div>
				<img style="float:left;margin-left:7px;margin-right: 15px;" src="<?php echo esc_attr(plugin_dir_url(__FILE__)); ?>/images/miniorange.png">
		    </div>
			<div>
	       	<h4 style="font-size:25px;font-weight:700;">
	            miniOrange Custom API &nbsp;
	           	<a class="add-new-h2" href="https://forum.miniorange.com/" target="_blank" rel="noopener">Ask questions on our forum</a>
				<a class="add-new-h2" href="https://wordpress.org/support/plugin/custom-api-for-wp/" target="_blank" rel="noopener">Wordpress Forum</a>
				<a class="add-new-h2" href="https://plugins.miniorange.com/custom-api-for-wordpress" target="_blank">Learn More</a>
	        </h4>
				</div>

		</div>
		<div class="wrap">
       		<?php //CustomAPISaleBanner::show_bfs_note();?>
		</div>

        <style>
            .add-new-hover:hover{
                color: white !important;
            }
        </style>
		<div id="tab">
			<h2 class="nav-tab-wrapper" style="line-height:40px;">
            <a class="nav-tab <?php if ($currenttab == 'list' || $currenttab == '') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings">Available APIs</a>&nbsp;
            <!-- <a class="nav-tab <?php if ($currenttab == 'addapi') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=addapi">Create API</a> -->
            <a class="nav-tab <?php if ($currenttab == 'savedcustomsql') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=savedcustomsql">Custom SQL APIs</a>
            <a class="nav-tab <?php if ($currenttab == 'add_auth') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=add_auth">Add Authentication</a>
            <a class="nav-tab <?php if ($currenttab == 'savedexternalapi') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=savedexternalapi">External APIs</a>
            <a class="nav-tab <?php if ($currenttab == 'apiintegration') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=apiintegration">Custom API Integration</a>
			<a class="nav-tab <?php if ($currenttab == 'register') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=register">Account Setup</a>
			<a class="nav-tab <?php if ($currenttab == 'requestfortrial') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=requestfortrial">Request for Trial</a>
            <a class="nav-tab <?php if ($currenttab == 'license') {echo 'mo_custom_api_nav_tab_active';}?>" href="admin.php?page=custom_api_wp_settings&action=license">Premium Plans</a>
            </h2>
		</div>
		<?php
}

    public static function custom_api_auth_show_tab($currenttab)
    {
        if ($currenttab === 'register') {
            if (get_option('custom_api_authentication_verify_customer')) {
                custom_api_already_customer();
            } elseif (trim(get_option('custom_api_authentication_email')) != '' && trim(get_option('mo_api_authentication_admin_api_key')) == '') {
                custom_api_already_customer();
            } else {
                register();
            }
        } elseif ($currenttab == '' || $currenttab == 'list') {
            custom_api_wp_list_api();
        } elseif ($currenttab == 'addapi') {
            custom_api_wp_add_api();
        } elseif ($currenttab == 'savedcustomsql') {
            custom_api_wp_saved_sql_api();
        } elseif ($currenttab == 'customsql') {
            custom_api_wp_custom_sql();
        } elseif ($currenttab == 'add_auth') {
            custom_api_wp_authentication();
        } elseif ($currenttab == 'savedexternalapi') {
            custom_api_wp_saved_external_api_connection();
        } elseif ($currenttab == 'externalapi') {
            custom_api_wp_external_api_connection();
        } elseif ($currenttab == 'requestfortrial') {
            Mo_Custom_Api_Admin_RFD::mo_custom_api_request_for_trial();
        } elseif ($currenttab == 'apiintegration') {
            custom_api_integration_page();
        } elseif ($currenttab == 'license') {
            custom_api_authentication_licensing_page();
        } elseif ($currenttab == 'edit') {
            if (isset($_GET['api'])) {
                $api = custom_api_wp_sanitise1($_GET['api']);
                custom_api_wp_edit_api($api);
            }
        } elseif ($currenttab == 'delete') {
            if (isset($_GET['api'])) {
                $api = custom_api_wp_sanitise1($_GET['api']);
                custom_api_wp_delete_api($api);
            }
        } elseif ($currenttab == 'view') {
            if (isset($_GET['api'])) {
                $api = custom_api_wp_sanitise1($_GET['api']);
                custom_api_wp_view_api($api);
            }
        } elseif ($currenttab == 'viewsql') {
            if (isset($_GET['api'])) {
                $api = custom_api_wp_sanitise1($_GET['api']);
                custom_api_wp_view_sqlapi($api);
            }
        } elseif ($currenttab == 'deletesql') {
            if (isset($_GET['apisql'])) {
                $api = custom_api_wp_sanitise1($_GET['apisql']);
                custom_api_wp_delete_sqlapi($api);
            }
        } elseif ($currenttab == 'sqledit') {
            if (isset($_GET['apisql'])) {
                $api = custom_api_wp_sanitise1($_GET['apisql']);
                custom_api_wp_edit_sqlapi($api);
            }
        }elseif ($currenttab == 'editexternal') {
            if (isset($_GET['apiname'])) {
                $api = custom_api_wp_sanitise1($_GET['apiname']);
                custom_api_wp_edit_externalapi($api);
            }
        } elseif ($currenttab == 'deleteexternal') {
            if (isset($_GET['apiname'])) {
                $api = custom_api_wp_sanitise1($_GET['apiname']);
                custom_api_wp_delete_externalapi($api);
            }
        }
    }

    public static function custom_api_auth_registration_view()
    {
        if (get_option('custom_api_authentication_new_customer')) {
            register();
        } elseif (get_option('custom_api_authentication_verify_customer')) {
            custom_api_already_customer();
        }
    }
}
