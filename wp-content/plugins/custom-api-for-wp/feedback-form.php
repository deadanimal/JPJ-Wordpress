<?php


function custom_api_client_display_feedback_form() {
	if ( 'plugins.php' != basename( $_SERVER['PHP_SELF'] ) ) {
		return;
	}
	$deactivate_reasons = array("Does not have the features I'm looking for", "Do not want to upgrade to Premium version", "Confusing Interface",
		"Bugs in the plugin", "Unable to create Custom endpoint", "Other Reasons:");
	wp_enqueue_style( 'wp-pointer' );
	wp_enqueue_script( 'wp-pointer' );
	wp_enqueue_script( 'utils' );
	
?>
<style>
   .mo_modal {
        display: none;
    overflow: hidden;
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1070;
    -webkit-overflow-scrolling: touch;
    outline: 0;

}
.mo_modal-content {
	position: relative;
    background-color: #fefefe;
    margin-left: 30%;
    margin-right: 26%;
    margin-top: 6%;
    border: 1px solid #999999;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 6px;
    -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
    box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    outline: 0;
}
.mo_custom_api_close {
	float: right;
    margin-right: 5%;
    font-size: 21px;
    font-weight: bold;
    line-height: 1;
    color: #000000;
    text-shadow: 0 1px 0 #212121;
    opacity: 0.5;
    filter: alpha(opacity=50);
}
.purp_button {
        background-color: #473970;
        border-color: #473970;
        box-shadow:0px 0px;
        height: 30px;
        color: white;
        border-radius: 4px;
        font-weight: 500;
        font-size: 12px;
        width: 60px;
    }
.purp_button:hover {
    opacity: 1;
    cursor: pointer;

}
.smm {
    text-align: center;
    width: 2vw;
    height: 2vw;
    padding: 1vw;

}
.smm:hover {
    opacity: 1;
    cursor: pointer;

}
[type=radio] { 
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

[type=radio]:checked + img {
  outline: 15px solid #473970;
}

</style>
    </head>
    <body>
    <div id="custom_api_feedback_modal" class="mo_modal">
        <div class="mo_modal-content">

            <h3 style="text-align: center; margin-top: 2%;"><b style="font-size: 1.2em;">Your Feedback 
            </b><span class="mo_custom_api_close" id="mo_custom_api_close"></span></h3>
            <hr style="width: 75%">

            <form name="f" method="post" action="" id="custom_api_client_feedback">
                <input type="hidden" name="custom_api_client_feedback" value="true"/>
                <div>
                    <p style="margin-left:2%">
                    <h4 style="margin: 2%; text-align:center; font-weight: 600; font-size: 1.2em;">We would like your opinion to improve our plugin.<br></h4>
                        <div align="center">
                            <div id="ratings" style="text-align:center">
                                <input type="radio" name="rate" id="angry" value="1"/>
                                    <label for="angry"><img class="smm" src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/angry.png'); ?>" />
                                    </label>
                                    
                                <input type="radio" name="rate" id="sad" value="2"/>
                                    <label for="sad"><img class="smm" src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/sad.png'); ?>" />
                                    </label>
                                
                                
                                <input type="radio" name="rate" id="neutral" value="3"/>
                                    <label for="neutral"><img class="smm" src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/normal.png'); ?>" />
                                    </label>
                                    
                                <input type="radio" name="rate" id="smile" value="4"/>
                                    <label for="smile">
                                    <img class="smm" src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/smile.png'); ?>" />
                                    </label>
                                    
                                <input type="radio" name="rate" id="happy" value="5" checked/>
                                    <label for="happy"><img class="smm" src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/happy.png'); ?>" />
                                    </label>
                            </div>
                            <div style="margin: auto;">
                                <h4 style="margin: 2%; font-weight: 600; font-size: 1.1em;">Tell us what happened?<br></h4>
                            </div>

                            <select style="margin: auto; margin-bottom: 10px; text-align: center; width: 60%;" name="deactivate_reason_select" id="deactivate_reason_select" required>
                                    <option value="" style="text-align:center; text-align-last: center;">Please select your reason</option>
                                
                                    <?php
                                        foreach ( $deactivate_reasons as $deactivate_reasoning ) 

                                            echo '<option id = "'. esc_attr ( $deactivate_reasoning ).'" value="'. esc_attr( $deactivate_reasoning ) .'" style="text-align:center; text-align-last: center;">'.esc_html ( $deactivate_reasoning ) .'</option>';
                                    ?>
                            </select>
                            <textarea id="query_feedback" name="query_feedback" rows="4" style="margin: auto; width: 60%;" placeholder="Write your query here.."></textarea>
                            
                            <?php $email = get_option("custom_api_authentication_admin_email");
                                if(empty($email)){
                                    $user = wp_get_current_user();
                                    $email = $user->user_email;
                                }
                            ?>
                            <div>
                            <input type="email" id="user_email" name="user_email" style="margin-bottom: 10px; text-align:center; border:0px solid black; background:#f0f3f7; width:60%;" placeholder="your email address" required value="<?php echo esc_attr( $email ); ?>" readonly="readonly"/>
                            
                            <i class="fa fa-pencil" onclick="userEmailEditActivate()" style="margin-left: -3%; cursor:pointer;"></i>
                            
                            <input type="submit" name="miniorange_feedback_submit"
                               class="purp_button" style="float:left;" value="Submit"/>
                        <input id="mo_skip" type="submit" name="miniorange_feedback_skip"
                        class="purp_button" style="float:right;" value="Skip"/>
                            </div>
                            <div>
                            
                            </div>
                        </div>
                    <div class="">
                        
                    </div>
                </div>
            </form>
            <form name="f" method="post" action="" id="mo_feedback_form_close">
                <input type="hidden" name="option" value="mo_oauth_client_skip_feedback"/>
            </form>
        </div>
    </div>
    <script>
         function userEmailEditActivate(){
            document.querySelector('#user_email').removeAttribute('readonly');
            document.querySelector('#user_email').focus();
            return false;
        }
        jQuery('a[aria-label="Deactivate Custom API for WP"]').click(function () {
            // location.href = 'https://www.google.com' ;
            var mo_modal = document.getElementById('custom_api_feedback_modal');
            var mo_skip = document.getElementById('mo_skip');
            var span = document.getElementsByClassName("mo_custom_api_close")[0];
            mo_modal.style.display = "block";
            jQuery('select[name="deactivate_reason_select"]').click(function () {
                var reason = jQuery(this).val();
                var query_feedback = jQuery('#query_feedback');
                query_feedback.removeAttr('required')

                if (reason === "Does not have the features I'm looking for") {
                    query_feedback.attr("placeholder", "Let us know what feature are you looking for");
                } else if (reason === "Other Reasons:") {
                    query_feedback.attr("placeholder", "Can you let us know the reason for deactivation");
                    query_feedback.prop('required', true);

                } else if (reason === "Bugs in the plugin") {
                    query_feedback.attr("placeholder", "Can you please let us know about the bug in detail?");

                } else if (reason === "Confusing Interface") {
                    query_feedback.attr("placeholder", "Finding it confusing? let us know so that we can improve the interface");
                } else if (reason === "Unable to create Custom endpoint") {
                    query_feedback.attr("placeholder", "We will help you create Custom endpoint shortly, if you can tell us the about the use of endpoint?");
                }


            });


            span.onclick = function () {
                mo_modal.style.display = "none";
                jQuery('#mo_feedback_form_close').submit();
            }
            mo_skip.onclick = function() {
                mo_modal.style.display = "none";
                jQuery('#mo_feedback_form_close').submit();
            }
            window.onclick = function (event) {
                if (event.target == mo_modal) {
                    mo_modal.style.display = "none";
                }
            }
            return false;

        });
    </script><?php
}

?>