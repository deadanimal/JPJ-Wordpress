<?php

function custom_api_authentication_licensing_page()
{
    $url=get_option('cutom_api_wp_host_name');
    ?>
        <input type="hidden" value="<?php echo esc_attr(custom_api_authentication_is_customer_registered()); ?>" id="mo_customer_registered">
        <form style="display:none;" id="loginform" action="<?php echo esc_attr($url) . '/moas/login'; ?>" target="_blank" method="post">
            <input type="email" name="username" value="<?php echo esc_attr(get_option('custom_api_authentication_admin_email')); ?>"/>
            <input type="text" name="redirectUrl" value="<?php echo esc_attr($url) . '/moas/initializepayment'; ?>"/>
            <input type="text" name="requestOrigin" id="requestOrigin"/>
        </form>
        
        <form style="display:none;" id="viewlicensekeys" action="<?php echo esc_attr($url) . '/moas/login'; ?>" target="_blank" method="post">
            <input type="email" name="username" value="<?php echo esc_attr(get_option('custom_api_authentication_admin_email')); ?>"/>
            <input type="text" name="redirectUrl" value="<?php echo esc_attr($url . '/moas/viewlicensekeys'); ?>"/>
        </form>
        <!-- End Important JSForms -->
        <!-- Licensing Table -->
        <div class="mo_custom_api_licensing_container">
            <div class="mo_custom_api_licensing_header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6 moct-align-right">
                            &nbsp;
                        </div>
                        <div class="col-6 moct-align-right">
                            &nbsp;
                        </div>
                    </div>
                    <div class="row justify-content-center mx-15">
                        <div class="col-2 moct-align-center">
                        </div>
                        <!-- Licensing Plans -->
                        <!-- free Plan -->
                        <div class="row">
                            <div class="col-4 moct-align-center">
                                <div class="mo_custom_api_licensing_plan card-body">
                                    <!-- Plan Header -->
                                    <div class="mo_custom_api_licensing_plan_header">
                                        <div class="mo_custom_api_licensing_plan_name">Standard</div>
                                        <div class="mo_custom_api_licensing_plan_price"><sup>$</sup>0<sup>*</sup></div>
                                        <div class="mo_custom_api_licensing_plan_usp">( Single HTTP  <br>{GET} Method )</div>
                                    </div>
                                    <br>
                                    <br>
                                    <button class="mo_custom_api_license_button">Current Plan</button>
                                    <!-- Plan Header End -->
                                    <!-- Plan Feature List -->
                                    <div class="mo_custom_api_licensing_plan_feature_list">
                                        <ul>
                                            <li>&#9989;&emsp;Unlimited Custom API's(endpoints) can be made with HTTP GET</li>
                                            <li>&#9989;&emsp;Fetch any type of data<br> 1. User Roles and Capabilities<br> 2. WP users and metadata <br>3. Featured Images <br> 4. Custom data,posts,pages,etc.</li>
                                            <li>&#9989;&emsp;Fetch operation available with single WHERE condition </li>
                                            <li>&#10060;&emsp;<span class="text-muted">Support for GET, POST, PUT & DELETE methods</span></li>
                                            <li>&#10060;&emsp;<span class="text-muted">Filters included</span> </li>
                                            <li>&#10060;&emsp;<span class="text-muted">All CRUD opertations supported</span></li>
                                            <li>&#10060;&emsp;<span class="text-muted">Restrict Public Access to WP REST APIs</span></li>
                                            <li>&#10060;&emsp;<span class="text-muted">API key authentication method to protect APIs</span></li>
                                            <li>&#10060;&emsp;<span class="text-muted">Support for API creation with GUI based custom SQL query</span></li>
                                            <li>&#10060;&emsp;<span class="text-muted">Support for External APIs Connection</span></li>
                                        </ul>
                                    </div>
                                    <!-- Plan Feature List End -->
                                </div>
                            </div>
                            <!-- Standard Plan End -->
                            <!-- Premium Plan -->
    
                            <div class="col-4 moct-align-center">
                                <div class="mo_custom_api_licensing_plan card-body">
                                    <!-- Plan Header -->
                                    <div class="mo_custom_api_licensing_plan_header">
                                        <div class="mo_custom_api_licensing_plan_name">Premium</div>
                                        <div class="mo_custom_api_licensing_plan_price"><sup>$</sup>149<sup>*</sup></div>
                                        <div class="mo_custom_api_licensing_plan_usp">( Multiple HTTP <br>Methods )</div>
                                    </div>
                                    <br>
                                    <br>
    
                                    <button class="mo_custom_api_license_button"  onclick="upgradeform('wp_rest_custom_api_for_wp_premium_plan')">Buy Now</button>
    
                                    <!-- Plan Header End -->
                                    <!-- Plan Feature List -->
                                    <div class="mo_custom_api_licensing_plan_feature_list">
                                        <ul>
                                            <li>&#9989;&emsp;Unlimited Custom API's(endpoints) can be made with HTTP GET, POST, PUT, DELETE</li>
                                            <!-- <li>&#9989;&emsp;Fetch </li> -->
                                            <li>&#9989;&emsp;Fetch any type of data<br> 1. User Roles and Capabilities<br> 2. WP users and metadata <br>3. Featured Images <br> 4. Custom data,posts,pages,etc.<br>5. Custom JSON content/JSON file</li>
                                            <li>&#9989;&emsp;Fetch operation available multiple custom conditions</li>
                                            <li>&#9989;&emsp;Support for Custom Namespace</li>
                                            <li>&#9989;&emsp;Support for GET, POST, PUT & DELETE methods</li>
                                            <li>&#9989;&emsp;Filters included </li>
                                            <li>&#9989;&emsp;All CRUD opertations supported</li>
                                            <li>&#9989;&emsp;Restrict Public Access to WP REST APIs</li>
                                            <li>&#9989;&emsp;API key authentication method to protect APIs</li>
                                            <li>&#10060;&emsp;<span class="text-muted">Support for API creation with GUI based custom SQL query</span></li>
                                            <li>&#10060;&emsp;<span class="text-muted">Support for External APIs Connection</span></li>
                                        </ul>
                                    </div>
                                    <!-- Plan Feature List End -->
                                </div>
                            </div>
    
                            <!-- <h>start****</h> -->
                            <div class="col-4 moct-align-center">
                            <div class="mo_custom_api_licensing_plan card-body">
                                    <!-- Plan Header -->
                                    <div class="mo_custom_api_licensing_plan_header">
                                        <div class="mo_custom_api_licensing_plan_name">Enterprise</div>
                                        <div class="mo_custom_api_licensing_plan_price"><sup>$</sup>249<sup>*</sup></div>
                                        <div class="mo_custom_api_licensing_plan_usp">( Multiple HTTP <br>Methods )</div>
                                    </div>
                                    <br>
                                    <br>
 
                                    <button class="mo_custom_api_license_button" onclick="upgradeform('wp_rest_custom_api_for_wp_enterprise_plan')">Buy Now</button>
 
                                    <!-- Plan Header End -->
                                    <!-- Plan Feature List -->
 
 
                                    <div class="mo_custom_api_licensing_plan_feature_list">
                                        <ul>
                                            <li>&#9989;&emsp;All Features of Premium Plugin</li>
                                            <li>&#9989;&emsp;Unlimited Custom API's(endpoints) can be made</li>
                                            <li>&#9989;&emsp;Create Custom API endpoints with custom SQL Query</li>
                                            <li>&#9989;&emsp;<span class="text-muted">Support for Unlimited External APIs Connection </li>
                                            <li>&#9989;&emsp;<span class="text-muted"> External API integration to fetch data in the WordPress, update data on External API provider side.</span></li>
                                            <li>&#9989;&emsp;<span class="text-muted">Support for Dynamic header in API Request </span> </   l                  i>
                                            <li>&#9989;&emsp;<span class="text-muted">Support for GET, POST, PUT & DELETE methods</span></li>
                                            <li>&#9989;&emsp;<span class="text-muted">Dynamic WordPress hooks for each External API Connection to perform operations on external data</span></li>
                                            <li>&#9989;&emsp;<span class="text-muted">Integration on any WordPress event or any third-party plugin event/action.</span></li>
                                            <li>&#9989;&emsp;<span class="text-muted">Compatibility with third-party plugin’s payment gateways</span></li>
                                        </ul>
                                    </div>
                                    <!-- Plan Feature List End -->
                                </div>
                            </div>
                        </div>

                         <!-- <h>enddd*******</h> -->
                        <!-- Premium Plan End -->
                        <!-- Enterprise Plan -->
                        <div class="col-2 moct-align-center">
                        </div>
                        <!-- Enterprise Plan End -->
                        <!-- Licensing Plans End -->
                        <div class=mo_custom_api_licensing>
                            <h6 class="mo_custom_api_licensing_heading">LICENSING POLICY</h6>
                            <span style="color: red;">*</span>Cost applicable for one instance only. Licenses are perpetual and the Support Plan includes 12 months of maintenance (support and version updates). You can renew maintenance after 12 months at 50% of the current license cost.
                            <br>
                            <br>
                            <span style="color: red;">*</span>We provide deep discounts on bulk license purchases and pre-production environment licenses. As the no. of licenses increases, the discount percentage also increases. Contact us at <a href="mailto:apisupport@xecurify.com" target="_blank">apisupport@xecurify.com</a> for more information.
                            <br>
                            <br>
                            <strong>Note:</strong> All the data remains within your premises/server. We do not provide the developer license for our paid plugins and the source code is protected. It is strictly prohibited to make any changes in the code without having written permission from miniOrange. There are hooks provided in the plugin which can be used by the developers to extend the plugin's functionality.
                            <br>
                            <br>
                            At miniOrange, we want to ensure you are 100% happy with your purchase. If the premium plugin you purchased is not working as advertised and you've attempted to resolve any issues with our support team, which couldn't get resolved. Please email us at <a href="mailto:apisupport@xecurify.com?subject=Custom API for WP - Enquiry">apisupport@xecurify.com</a> for any queries regarding the return policy.
                            <br>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Licensing Table -->
        <a  id="mobacktoaccountsetup" style="display:none;" href="<?php echo esc_url(add_query_arg(array('action' => 'register'), htmlentities($_SERVER['REQUEST_URI']))); ?>">Back</a>
        <!-- JSForms Controllers -->
        <a id="mobacktocontactform" style="display:none;" href="<?php echo esc_url(get_site_url()) . '/wp-admin/admin.php?page=custom_api_wp_settings'; ?>">Back2</a>
        <script>
            function customplanupgrade() {
                planType = document.getElementById('wp-rest-api-custom-plan-select').value;
                upgradeform(planType);
            }

            function upgradeform(planType) {
                if(planType === "") {
                    location.href = "https://wordpress.org/plugins/wp-rest-api-authentication/";
                    return;
                } else {
                    jQuery('#requestOrigin').val(planType);
                    if(jQuery('#mo_customer_registered').val()==1)
                        jQuery('#loginform').submit();
                    else{
                        location.href = jQuery('#mobacktoaccountsetup').attr('href');
                    }
                }

            }
            function upgradeform2() {
                location.href = jQuery('#mobacktocontactform').attr('href');
            }

            function getlicensekeys() {
                jQuery('#viewlicensekeys').submit();
            }
        </script>
        <!-- End JSForms Controllers -->
    <?php
}
?>