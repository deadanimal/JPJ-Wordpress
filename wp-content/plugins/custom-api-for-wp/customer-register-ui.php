<?php

function register()
{
    if (!custom_api_authentication_is_customer_registered()) {
        custom_api_register_ui();
    } else {
        custom_api_show_customer_info();
    }
}

function custom_api_register_ui()
{
    $current_user = wp_get_current_user();

    ?>
	    <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
	    	<div class="box-body">
	    		<div class="row mo_custom_api_page_layout_row">
	    			<div class="col-md-8 mo_custom_api_page_layout" style="padding: 30px;padding-bottom:0px; ">
	    				<form name="f" method="post" action="">
						<?php wp_nonce_field("mo_custom_api_register_customer","mo_custom_api_register_customer_field")?>
	    					<input type="hidden" name="option" value="custom_api_authentication_register_customer" />
	    					<div id="toggle1" class="mo_panel_toggle">
	    						<h5 style="margin-top:-12px;" class="mo_custom_api_heading">Register with miniOrange<small style="font-size: 16px; color:blue;font-weight:bold;"> [OPTIONAL]</small></h5>
	    						<hr>
	    					</div>
	    					<div id="panel1">
	    						<p style="font-size:1rem;font-weight:500;"><b>Why should I register? </b></p>
	    						<div id="help_register_desc" style="background: aliceblue; padding: 10px; border-radius: 10px;font-size:14px;">
	    							 You should register so that in case you need help, we can help you with step by step instructions.
	    							<b>You will also need a miniOrange account to upgrade to the premium version of the plugins.</b> We do not store any information except the email that you will use to register with us.
	    						</div>
	    						<table style="margin-left:-10px;" class="mo_custom_api_account_setup_form">
	    							<tr>
	    								<td><b><font color="#FF0000" >*</font>Email:</b></td>
	    								<td>
	    									<input class="register_ui_input" type="email" name="email" style="width: 150%;" required placeholder="person@example.com" value="<?php echo esc_attr(get_option('custom_api_authentication_admin_email')); ?>" />
	    								</td>
	    							</tr>
	    							<tr class="hidden">
	    								<td><b><font color="#FF0000">*</font>Website/Company Name:</b></td>
	    								<td>
	    									<input class="" type="text" name="company" required placeholder="Enter website or company name" value="<?php echo esc_attr($_SERVER['SERVER_NAME']); ?>" />
	    								</td>
	    							</tr>
	    							<tr class="hidden">
	    								<td><b>&nbsp;&nbsp;First Name:</b></td>
	    								<td><input class="" type="text" name="fname" placeholder="Enter first name" value="<?php echo esc_attr($current_user->user_firstname); ?>" /></td>
	    							</tr>
	    							<tr class="hidden">
	    								<td><b>&nbsp;&nbsp;Last Name:</b></td>
	    								<td><input class="" type="text" name="lname" placeholder="Enter last name" value="<?php echo esc_attr($current_user->user_lastname); ?>" /></td>
	    							</tr>
	    							<tr class="hidden">
	    								<td><b>&nbsp;&nbsp;Phone number :</b></td>
	    								<td><input class="" type="text" name="phone" pattern="[\+]?([0-9]{1,4})?\s?([0-9]{7,12})?" id="phone" title="Phone with country code eg. +1xxxxxxxxxx" placeholder="Phone with country code eg. +1xxxxxxxxxx" value="<?php echo esc_attr(get_option('custom_api_authentication_admin_phone')); ?>" />
	    									This is an optional field. We will contact you only if you need support.
	    								</td>
	    							</tr>
	    							<tr class="hidden">
	    								<td></td>
	    								<td>We will call only if you need support.</td>
	    							</tr>
	    							<tr>
	    								<td><b><font color="#FF0000">*</font>Password:</b></td>
	    								<td><input class="register_ui_input" required style="width: 150%;" type="password" name="password" placeholder="Choose your password (Min. length 8)" /></td>
	    							</tr>
	    							<tr>
	    								<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
	    								<td><input class="register_ui_input" required style="width: 150%;" type="password" name="confirmPassword" placeholder="Confirm your password" /></td>
	    							</tr>
	    							<tr>
	    								<td>&nbsp;</td>
	    								<td>
	    									<br>
	    									<input type="submit" id="custom_submit" name="submit" class="mo_custom_api_button" style="width:70px;" value="Register"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    									<input type="button" name="custom_api_goto_login" id="custom_api_goto_login" style="color:white;" value="Already have an account?" class="mo_custom_api_button" />&nbsp;&nbsp;
	    								</td>
	    							</tr>
	    						</table>
	    					</div>
	    				</form>
	    				<form name="f5" method="POST" action="" id="custom_api_authentication_goto_login_form1" style="visibility: hidden;">
						    <?php wp_nonce_field("mo_custom_api_goto_login_form1","mo_custom_api_goto_login_form1_field")?>
	    					<input type="text" name="option2" value="custom_api_authentication_goto_login1" style="visibility: hidden;">
	    				</form>
	    			</div>
	    			<?php 
	    			    contact_form(); 
	    			    mo_custom_api_advertisement();
	    			?>
                </div>
	    	</div>
	    </div>
    
	    <script>
	    	jQuery('#custom_api_goto_login').click(function() {
	    		jQuery('#custom_api_authentication_goto_login_form1').submit();
	    	});
	    </script>  
    <?php
}


function contact_form()
{
    ?>
	    <div style="margin-left:-6px;" class="col-md-4">
	    	<div class="mo_custom_api_support_layout">
	    		<div>
                    <h6 style="margin-top: 14px;margin-bottom: 15px;" class="mo_custom_api_heading">Contact Us</h6>
	    			<p style="font-size: 12px;font-weight: 500;">
	    			    Need any help? Want to learn more or couldn't find what you are looking for ?
	    				<br>
                        Just send us a query so we can help you.
	    			</p>
	    			<form method="post" action="">
					<?php wp_nonce_field('mo_custom_api_submit_contact_us', 'mo_custom_api_submit_contact_us_field');?>
	    				<input type="hidden" name="option" value="custom_api_wp_contact_us_query_option" />
	    				<table class="mo_custom_api_settings_table">
	    					<tr>
	    						<td><input type="email" class="mo_custom_api_table_textbox" required name="custom_api_wp_contact_us_email" placeholder="Enter email here" value=""></td>
	    					</tr>
	    					<tr>
	    						<td><input type="tel" id="contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" placeholder="Enter phone here" class="mo_custom_api_table_textbox" name="custom_api_wp_contact_us_phone" value=""></td>
	    					</tr>
	    					<tr>
	    						<td><textarea class="mo_custom_api_table_textbox" onkeypress="custom_api_wp_valid_query(this)" placeholder="Enter your query here" onkeyup="custom_api_wp_valid_query(this)" onblur="custom_api_wp_valid_query(this)" required name="custom_api_wp_contact_us_query" rows="4" style="resize: vertical;"></textarea></td>
	    					</tr>
	    				</table>
	    				<div style="margin-top:10px">
	    					<input type="submit" name="submit" class="mo_custom_api_contact_us_submit_btn"  />
	    				</div>
	    				<br>
	    				<p style="font-size: 13px;font-weight: 500;">If you want custom features in the plugin, just drop an email at <a href="mailto:apisupport@xecurify.com?subject=Custom API for WP - Enquiry">apisupport@xecurify.com</a>.</p>
	    			</form>
	    		</div>
	    	</div>
	    <!-- </div> -->
    <?php
}

function custom_api_already_customer()
{

    ?>
	    <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
	    	<div class="box-body">
	    		<div class="row mo_custom_api_page_layout_row">
	    			<div class="col-md-8 mo_custom_api_page_layout" style="padding: 30px;padding-bottom:0px;">
	    				<form name="f" method="post" action="">
						<?php wp_nonce_field("mo_cusotm_api_verify_customer","mo_cusotm_api_verify_customer_field")?>
	    					<input type="hidden" name="option" value="custom_api_authentication_verify_customer" />
	    					<div id="toggle1" class="mo_panel_toggle">
	    						<h4 style="margin-top:-8px;margin-bottom:10px" class="mo_custom_api_heading">Login with miniOrange:</h4>
	    					</div>
	    					<p><b>Please enter your miniOrange email and password.<br /> <a style="color:red;"href="#custom_api_forgot_password_link">Forgot Password?</a></b></p>
	    					<script>
	    		              jQuery("a[href=\"#custom_api_forgot_password_link\"]").click(function(){
	    			            window.open('https://login.xecurify.com/moas/idp/resetpassword');
	    			           });
	    	                </script>
	    					<div id="panel1">
	    						<table class="mo_custom_api_account_setup_form">
	    							<tr style="border-sapciing:20px;">
	    								<td><b><font color="#FF0000">*</font>Email:</b></td>
	    								<td><input class="register_ui_input" style="width: 100%;" type="email" name="email" required placeholder="person@example.com" value="<?php echo esc_attr(get_option('custom_api_authentication_admin_email')); ?>" /></td>
	    							</tr>
	    							<tr>
	    							    <td><b><font color="#FF0000">*</font>Password:</b></td>
	    							    <td><input class="register_ui_input" style="width: 100%;" required type="password" name="password" placeholder="Choose your password" /></td>
	    							</tr>
	    							<tr>
	    								<td>&nbsp;</td>
	    								<td>
	    									<br><input type="submit" name="submit" value="Login" style="margin:auto; width:70px;" class="mo_custom_api_button" />
	    									<input style="visibility: hidden; width: 0%;" type="label">&nbsp;&nbsp;&nbsp; <input type="button" name="back-button" id="mo_api_authentication_back_button" style="margin:auto; width:70px;" value="Back" class="mo_custom_api_button" />
	    					            </td>	
	    					        </tr>
	    					    </table>
	    					</div>
	    				</form>
	    				<form name="f5" method="POST" action="" id="custom_api_authentication_goto_register_form" style="visibility: hidden;">
						<?php wp_nonce_field("mo_custom_api_goto_register_form","mo_custom_api_goto_register_form_field")?>
	    					<input type="text" name="option2" value="custom_api_authentication_goto_register" style="visibility: hidden;">
	    				</form>
	    		    </div>
	    		    <?php 
	    			    contact_form();
	    				mo_custom_api_advertisement();
	    		    ?>
                </div>
	        </div>
	    </div>
    
	    <script>
	    	jQuery('#mo_api_authentication_back_button').click(function() {
	    		jQuery('#custom_api_authentication_goto_register_form').submit();
	    	});
	    </script>
    <?php
}

function mo_custom_api_advertisement(){
	?>
	<div class="mo_custom_api_adv">
        <script type='text/javascript'>
           !function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content");for(c=0;c<i.length;c++){if(d=i[c],!d.getAttribute("data-secret"))f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f);if(g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d)if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.removeAttribute("style"),"height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(~~g<200)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);
        </script>
        <iframe sandbox="allow-scripts" security="restricted" src="https://wordpress.org/plugins/wp-rest-api-authentication/embed/" width="99%"  title="&#8220;WordPress REST API Authentication &#8211;&#8221; &#8212; Plugin Directory" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content"></iframe>
    </div>
	<?php
}

function custom_api_show_customer_info()
{

    ?>

	    <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
	    	<div class="box-body">
	    		<div class="row mo_custom_api_page_layout_row">
	    			<div class="col-md-8 mo_custom_api_page_layout" style="padding: 30px">
	    				<h5 style="margin-top:-8px;margin-bottom:20px" class="mo_custom_api_heading">miniOrange Account Information:</h5>
	    				<table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
	    					<tr>
	    						<td style="width:45%; padding: 10px;">miniOrange Account Email</td>
	    						<td style="width:55%; padding: 10px;"><?php echo esc_attr(get_option('custom_api_authentication_admin_email')); ?></td>
	    					</tr>
	    					<tr>
	    						<td style="width:45%; padding: 10px;">Customer ID</td>
	    						<td style="width:55%; padding: 10px;"><?php echo esc_attr(get_option('custom_api_authentication_admin_customer_key')) ?></td>
	    					</tr>
	    				</table>
	    				<br>
    
	    				<table>
	    					<tr>
	    						<td>
	    							<form name="f1" method="post" action="" id="mo_api_authentication_goto_login_form">
									<?php wp_nonce_field("mo_custom_api_goto_login_form","mo_custom_api_goto_login_form_field")?>
	    								<input type="text" value="change_miniorange" name="option" style="display:none;">
	    								<input type="submit" value="Change Account" class="mo_custom_api_button" />
	    							</form>
	    						</td>
	    					</tr>
	    				</table>
    
                    </div>
	    			<?php 
	    			    contact_form();
	    				mo_custom_api_advertisement();
	    			?>		
                </div>
	    	</div>
	    </div>
    <?php
}
