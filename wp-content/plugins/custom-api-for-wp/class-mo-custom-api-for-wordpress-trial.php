<?php

class Mo_Custom_Api_Admin_RFD
{
    public static function mo_custom_api_request_for_trial()
    {
        self::demo_request();
    }

    public static function demo_request()
    {
        $democss = "width: 350px; height:35px;";
	    ?>
        <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
            <div class="box-body">
            	<div class="row mo_custom_api_page_layout_row">
            		<div class="col-md-8 mo_custom_api_page_layout" style="padding: 25px;padding-bottom:0px;">
            			<p class="mo_custom_api_heading" style="margin-left:10px"> Trial Request for Pro plans: </p>
            			
            			<form method="post" action="">
						<?php wp_nonce_field('mo_custom_api_trial_request', 'mo_custom_api_trial_request_field'); ?>
            			    <input type="hidden" name="option" style="<?php echo esc_attr($democss); ?>" value="mo_custom_api_trial_request_form" />
            			    <table class="mo_custom_api_trial_table">
            			    	<tr>
            			    		<td><strong>Email ID : </strong></td>
            			    		<td><input required type="email" style="<?php echo esc_attr($democss); ?>" name="mo_custom_api_trial_email" required placeholder="Email id" value="<?php echo esc_attr(get_option("mo_custom_api_trial_admin_email")); ?>" /></td>
            			    	</tr>
            			    	<tr>
            			    		<td><strong>Select Premium plan: </strong></td>
            			    		<td>
            			    			<select required  name="mo_custom_api_trial_plan" style="<?php echo esc_attr($democss); ?>" id="mo_custom_api_trial_plan_id">
            			    				<option value="">-------------------- Select Plan ------------------</option>
            			    				<option value="Custom-API-Enterprise">WP custom API Enterprise Plugin</option>
            			    				<option value="Custom-API-Premium">WP custom API Premium Plugin</option>
            			    				<option value="Not Sure">Not Sure</option>
            			    			</select>
            			    		</td>
            			      	</tr>
                                <tr>
            			    	  	<td><strong>Usecase and Requirements: </strong></td>
            			    		<td>
            			    		    <textarea type="text" style="width:350px; height:100px;" minlength="15" name="mo_custom_api_trial_usecase"  placeholder="Write us about your usecase" required value=""></textarea>
            			    		</td>
            			    	</tr>
                                <tr>
            			    		<td></td>
                                    <td>
                                        <input type="submit" class="mo_custom_api_button"  name="submit" value="Submit Trial Request" />
                                    </td>
                                </tr>
            			    </table>
	                    </form>
            			
                        <div style="margin-left: 10px;">
            			    <strong>NOTE:</strong> You will receive the email shortly with the demo details once you successfuly make the demo/trial request. If not received, please check out your spam folder or contact us at <a href="mailto:apisupport@xecurify.com">apisupport@xecurify.com</a>.
            	        </div>
        			    <br>
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
}
