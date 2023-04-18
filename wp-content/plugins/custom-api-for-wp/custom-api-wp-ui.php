<?php
require 'custom-api-handler.php';
require 'customer-register-ui.php';

function custom_api_wp_delete_api($api11)
{
    $GetApi = get_option('CUSTOM_API_WP_LIST');
    unset($GetApi[$api11]);
    update_option('CUSTOM_API_WP_LIST', $GetApi);
    update_option('custom_api_wp_message', 'API Deleted Sucessfully.');
    custom_api_wp_show_success_message();
    custom_api_wp_list_api();
    return;
}

function custom_api_wp_delete_sqlapi($api11)
{
    $GetApi = get_option('custom_api_wp_sql');
    unset($GetApi[$api11]);
    update_option('custom_api_wp_sql', $GetApi);
    update_option('custom_api_wp_message', 'API Deleted Sucessfully.');
    custom_api_error_message();
    custom_api_wp_saved_sql_api();
    return;
}

function custom_api_wp_sanitise1($var)
{
    $var = trim($var);
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = htmlspecialchars($var);
    return $var;
}

function custom_api_wp_delete_externalapi($api11)
{
    $GetApi = get_option('custom_api_save_ExternalApiConfiguration');
    unset($GetApi[$api11]);
    update_option('custom_api_save_ExternalApiConfiguration', $GetApi);
    update_option('ExternalApiResponseKey',"");
    update_option('custom_api_wp_message', 'API Deleted Sucessfully.');
    custom_api_error_message();
    custom_api_wp_saved_external_api_connection();
    return;
}

function custom_api_wp_view_api($api1)
{
    $GetApi = get_option('CUSTOM_API_WP_LIST');
    $GetForm = get_option('mo_custom_api_form');
    $Details = $GetApi[$api1];
    $MethodName = $Details['MethodName'];
    $TableName = $Details['TableName'];
    $SelectedColumn = $Details['SelectedColumn'];
    $ConditionColumn = $Details['ConditionColumn'];
    $SelectedCondtion = $Details['SelectedCondtion'];
    $SelectedParameter = $Details['SelectedParameter'];

    $api = get_site_url();
    if ($SelectedCondtion == 'no condition') {
        $ApiDisplay = "{$api}/wp-json/mo/v1/{$api1}";
    } else {
        $ApiDisplay = "{$api}/wp-json/mo/v1/{$api1}/{" . $ConditionColumn . "}";
    }

    custom_api_wp_view_api_details($ApiDisplay, $api1, $MethodName, $ConditionColumn, $SelectedCondtion, $SelectedParameter);
}


function custom_api_wp_view_sqlapi($api1){
    $GetApi = get_option('custom_api_wp_sql');
    $Details = $GetApi[$api1];
    $MethodName = $Details['method'];
    $QueryParameter = $Details['query_params'];
    $sql_query = $Details['sql_query'];
    $pattern = "/{{[A-Z]*[a-z]*_[A-Z]*[a-z]*[0-9]*}}/";
    
    $customparams = [];

    if(preg_match_all($pattern, $sql_query, $reg_array)){
        foreach($reg_array[0] as $attr){
            $mo_regex = substr($attr, 2);
            $mo_regex = substr($mo_regex, 0, -2);
            array_push($customparams, $mo_regex);
        }
    }

    $api = get_site_url();
    if (!$QueryParameter) {
        $ApiDisplay = "{$api}/wp-json/mo/v1/{$api1}";
    } else {
        
            $ApiDisplay = "{$api}/wp-json/mo/v1/{$api1}";

            if($MethodName == 'GET'){
                $ApiDisplay = $ApiDisplay . "?";
                for ($i=0; $i< sizeof($customparams); $i++) {
                    
                    $ApiDisplay = $ApiDisplay . $customparams[$i] . '=<' . $customparams[$i] . '_value>';
                    if($i != sizeof($customparams) - 1){
                        $ApiDisplay = $ApiDisplay . '&';
                    } 
                    
                }
            }
    }
  
    custom_api_wp_view_sql_api_details($ApiDisplay, $api1, $MethodName, $customparams);
}

function custom_api_wp_edit_api($api1)//MERGE ONE 
{
    $GetApi = get_option('CUSTOM_API_WP_LIST');
    $GetForm = get_option('mo_custom_api_form');

    $Details = $GetApi[$api1];
    $MethodName = $Details['MethodName'];
    $TableName = $Details['TableName'];
    $SelectedColumn = $Details['SelectedColumn'];
    $ConditionColumn = $Details['ConditionColumn'];
    $SelectedCondtion = $Details['SelectedCondtion'];
    $SelectedParameter = $Details['SelectedParameter'];

    if (isset($_POST['SendResult'])) {
        if ($GetForm['status'] == 'yes') {
            $query = $GetForm['query'];

            $api_name_edit = $GetForm['ApiName'];
            $method_name_edit = $GetForm['MethodName'];
            $table_name_edit = $GetForm['TableName'];
            $selected_column_edit = $GetForm['SelectedColumn'];
            $condition_column_edit = $GetForm['ConditionColumn'];
            $selected_condition_edit = $GetForm['SelectedCondtion'];
            $selected_parameter_edit = $GetForm['SelectedParameter'];

            $current = array(
                $api_name_edit => array(
                    "TableName" => $table_name_edit,
                    "MethodName" => $MethodName,
                    "SelectedColumn" => $selected_column_edit,
                    "ConditionColumn" => $condition_column_edit,
                    "SelectedCondtion" => $selected_condition_edit,
                    "SelectedParameter" => $selected_parameter_edit,
                    "query" => $query,
                ),

            );

            $list = get_option('CUSTOM_API_WP_LIST');
            unset($list[$api_name_edit]);
            $list[$api_name_edit] = $current[$api_name_edit];
            $api = get_site_url();

            if ($selected_condition_edit == 'no condition') {
                $ApiDisplay = "{$api}/wp-json/mo/v1/{$api_name_edit}";
            } else {
                $ApiDisplay = "{$api}/wp-json/mo/v1/{$api_name_edit}/{" . $condition_column_edit . "}";
            }

            update_option('CUSTOM_API_WP_LIST', $list);
            unset($GetForm['status']);
            update_option('mo_custom_api_form', $GetForm);
            custom_api_wp_view_api_details($ApiDisplay, $api_name_edit, $MethodName, $condition_column_edit, $selected_condition_edit, $selected_parameter_edit);
            return;
        }
    }
    
    ?>
        <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
	            <div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="padding: 15px 25px 25px 25px;margin-left:3px">

                        <form method="POST" style="visibility: hidden;">
                                <?php wp_nonce_field('CheckNonce2', 'SubmitUser2');?>
                                <input type="text" id="api_name_initial2" name="api_name_initial2" style="visibility: hidden;">
                                <input type="text" id="method_name_initial2" name="method_name_initial2" style="visibility: hidden;">
                                <input type="text" id="table_name_initial2" name="table_name_initial2" style="visibility: hidden;">
                                <input type="submit" id="SubmitForm2" name="SubmitForm2" style="visibility: hidden;">
                        </form>

                        <form method="POST">
                            <?php wp_nonce_field('CheckNonce', 'SubmitUser');?>
                            <p style="margin-top: -30px;" class="mo_custom_api_heading">Update Custom API: <span style="float:right;">  <a class="mo_custom_api_setup_guide_button" href="https://plugins.miniorange.com/wordpress-create-custom-rest-api-endpoints#step1" target="_blank">Setup Guide</a> </span></p>
                            <hr class="mo_custom_api_hr">
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels">API Name</label>
                                </div>
                                <div class='col-md-6'>
                                    <input type="text" class="mo_custom_api_SelectColumn mo_custom_api_name" id="ApiName" name="ApiName" <?php echo 'value = '. esc_attr($api1); ?> readonly>
                                </div>
                            </div>
                            <br>
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels"> Select Method</label>
                                </div>
                                <div class='col-md-6'>
                                    <select class="mo_custom_api_SelectColumn" id="MethodName" name="MethodName" disabled>
                                        <option value="GET" <?php if ($MethodName == "GET") {echo " selected='selected'";}?>>GET</option>
                                        <option value="POST" <?php if ($MethodName == "POST") {echo " selected='selected'";}?>>POST</option>
                                        <option value="PUT" <?php if ($MethodName == "PUT") {echo " selected='selected'";}?>>PUT</option>
                                        <option value="DELETE" <?php if ($MethodName == 'DELETE') {echo " selected='selected'";}?>>DELETE</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels"> Select Table</label>
                                </div>
                                <div class='col-md-6'>
                                    <select class="mo_custom_api_SelectColumn" name="select-table" id="select-table" onchange="custom_api_wp_GetTbColumn2()">
                                       <?php
                                            global $wpdb;
                                            // to get all tables
                                            $sql_query = "SHOW TABLES LIKE '%%'";
                                            $results = $wpdb->get_results($sql_query);
                                            $table_name = [];
                                            foreach ($results as $index => $value) {
                                                foreach ($value as $tableName) {
                                                    array_push($table_name, $tableName);
                                                }
                                            }
                                            $data = get_option('mo_custom_api_form2');
                                            foreach ($table_name as $tb) {
                                                echo '<option value='. esc_attr($tb);
                                                if (isset($_POST["SubmitForm2"])) {
                                                    if (!empty($data['TableName'])) {
                                                        if ($data['TableName'] == $tb) {
                                                            echo " selected='selected'";
                                                        }
                                                    }
                                                }
                                                elseif($TableName == $tb) 
                                                echo " selected='selected'";
                                                echo ' >  '. esc_html($tb) . " </option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels"> Select Columns</label>
                                </div>
                                <div class='col-md-6'>
                                    <select class="mo_custom_api_SelectColumn" id="SelectedColumn" multiple="multiple" name="SelectedColumn">
                                        <?php
                                            global $wpdb;
                                            $data = get_option('mo_custom_api_form2');
                                            if (!empty($data['TableName'])) {
                                                $table1 = $data['TableName'];
                                                $column = [];
                                                $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                foreach ($existing_columns as $col) {
                                                    array_push($column, $col);
                                                }
                                                foreach ($column as $colu) {
                                                    echo '<option value='. esc_attr($colu);
                                                    echo '>'. esc_html($colu). "</option>";
                                                }
                                            }
                                            
                                            else {
                                                $column = [];
                                                $existing_columns = $wpdb->get_col("DESC {$TableName}", 0);
                                                foreach ($existing_columns as $col) {
                                                    array_push($column, $col);
                                                }
                                                foreach ($column as $colu) {
                                                    $split = explode(",", $SelectedColumn);
                                                    echo '<option value='. esc_attr($colu);
                                                    foreach ($split as $s) {
                                                        if ($s == $colu) {
                                                            echo " selected='selected'";
                                                        }
                                                    }
                                                    echo '>'. esc_html($colu). "</option>";
                                                }
                                                
                                            }
 
                                            
                                        ?>
                                        
                                        <?php
                                            global $wpdb;
                                            $data = get_option('mo_custom_api_form2');
                                            $FormData = get_option('mo_custom_api_form');
                                            if (empty($data['TableName'])) {
                                                if (!empty($FormData['status']) && ($FormData['status'] == 'yes') && !empty($FormData['TableName'])) {
                                        
                                                    $table1 = $FormData['TableName'];
                                                    $column11 = $FormData['SelectedColumn'];
                                        
                                                    $column = [];
                                                    $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                    foreach ($existing_columns as $col) {
                                                        array_push($column, $col);
                                                    }
                                                    foreach ($column as $colu) {
                                                        $split = explode(",", $column11);
                                        
                                                        echo '<option value='. esc_attr($colu);
                                        
                                                        foreach ($split as $s) {
                                                            if ($s == $colu) {
                                                                echo " selected='selected'";
                                                            }
                                                        }
                                        
                                                        echo '>' .esc_html($colu). "</option>";
                                                    }
                                                }
                                            }
                                        ?>

                                    </select>
                                </div>
                            </div>
                            <br>

                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels">Choose Column to apply condition</label>
                                    <br>
                                    <select class="mo_custom_api_SelectColumn" id="OnColumn" name="OnColumn">
                                        <option value="" >None selected </option>
                                            <?php
                                                global $wpdb;
                                                $data = get_option('mo_custom_api_form2');

                                                if (!empty($data['TableName'])) {
                                                    $table1 = $data['TableName'];
                                                    $column = [];
                                                    $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                    foreach ($existing_columns as $col) {
                                                        array_push($column, $col);
                                                    }
                                                    foreach ($column as $colu) {
                                                        echo '<option value='. esc_attr($colu);
                                        
                                                        echo '>'. esc_html($colu). "</option>";
                                                    }

                                                }

                                                else{
                                                    $column = [];
                                                    $existing_columns = $wpdb->get_col("DESC {$TableName}", 0);
                                                    foreach ($existing_columns as $col) {
                                                        array_push($column, $col);
                                                    }
                                                    foreach ($column as $colu) {
                                                        echo '<option value='. esc_attr($colu);
                                                        if ($ConditionColumn == $colu) {
                                                            echo " selected='selected'";
                                                        }
                                                        echo '>'. esc_html($colu). "</option>";
                                                    }
                                                }

                                                unset($data['TableName']);
                                                update_option('mo_custom_api_form2', $data);
                                                
                                            ?>

                                            <?php
                                                global $wpdb;
                                                $FormData = get_option('mo_custom_api_form');
                                                if (!empty($FormData['status']) && ($FormData['status'] == 'yes') && !empty($FormData['TableName'])) {
                                                    $table1 = $FormData['TableName'];
                                            
                                                    $column = [];
                                                    $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                    foreach ($existing_columns as $col) {
                                                        array_push($column, $col);
                                                    }
                                                    foreach ($column as $colu) {
                                                        echo '<option value='.esc_attr($colu);
                                            
                                                        if ($FormData['ConditionColumn'] == $colu) {
                                                            echo " selected = 'selected' ";
                                                        }
                                            
                                                        echo '>' .esc_html($colu). "</option>";
                                                    }
                                                }
                                            ?>    
                                    </select>
                                </div>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels">Choose Condition</label>
                                    <br>
                                    <select class="mo_custom_api_SelectColumn" id="ColumnCondition" name="ColumnCondition">
                                        <option value="no condition" <?php if ($SelectedCondtion == "no condition" || isset($_POST['SubmitForm2'])) {echo " selected='selected'";}?>>no condition </option>
                                        <option value="=" <?php if ($SelectedCondtion == "=" && !isset($_POST['SubmitForm2'])) {echo " selected='selected'";}?>>Equal </option>
                                        <option value="Like" <?php if ($SelectedCondtion == "Like" && !isset($_POST['SubmitForm2'])) {echo " selected='selected'";}?>>Like</option>
                                        <option value=">" <?php if ($SelectedCondtion == "&amp;gt;" && !isset($_POST['SubmitForm2'])) {echo " selected='selected'";}?>>Greater Than</option>
                                        <option value="less than" <?php if ($SelectedCondtion == "less than" && !isset($_POST['SubmitForm2'])) {echo " selected='selected'";}?>>Less Than</option>
                                        <option value="!=" <?php if ($SelectedCondtion == "!=" && !isset($_POST['SubmitForm2'])) {echo " selected='selected'";}?>>Not Equal</option>
                                    </select>
                                </div>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels">URL Parameters<span style="font-size:12px;"> [Default: First Parameter]</span></label>
                                    <br>
                                    <select class="mo_custom_api_SelectColumn" id="ColumnParam" onchange="custom_api_wp_CustomText()" name="ColumnParam">
                                        <option value="1" <?php if ($SelectedParameter == "1") {echo " selected='selected'";}?>>First Parameter </option>
                                        <option value="2" disabled <?php if ($SelectedParameter == "2") {echo " selected='selected'";}?>>Second Parameter</option>
                                        <option value="3" disabled <?php if ($SelectedParameter == "3") {echo " selected='selected'";}?>>Third Parameter</option>
                                        <option value="4" disabled <?php if ($SelectedParameter == "4") {echo " selected='selected'";}?>>Fourth Parameter</option>
                                        <option value="5" disabled <?php if ($SelectedParameter == "5") {echo " selected='selected'";}?>>Custom value</option>
                                    </select>
                                    <div id="Param" style="visibility: hidden;">
                                        <input type="text" id="CustomParam">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <hr class="mo_custom_api_hr">
                            <input type="submit" class='mo_custom_api_create_update_btn' value="Update API" name="SendResult" id="SendResult" onclick="custom_api_wp_ShowData()">
                            <input type="text" id="QueryVal" name="QueryVal" style="visibility: hidden;">
                            <input type="text" id="Selectedcolumn11" name="Selectedcolumn11" style="visibility: hidden;">
                        </form>
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


function custom_api_wp_list_api()//MERGE TWO FINAL
{
    if(get_option('CUSTOM_API_WP_LIST'))
    {
        ?>
            <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left: 18px;">
                <div class="box-body">
                    
                    <div class="row mo_custom_api_page_layout_row">

                        <div class="col-md-8 mo_custom_api_page_layout">
                            <div style="display: flex; justify-content: space-between;">
                            <p style="margin: 15px 0px 10px 13px;"class="mo_custom_api_heading">Configured API's:</p>
                            <a class="mo_custom_api_ext_btn" style="float:right;  margin-top:12px;padding:4px" href="admin.php?page=custom_api_wp_settings&action=addapi"> Create API</a>
                            </div>
                            <table id="tbldata" class="table table-hover" style="width: 75%">
                                <thead>
                                    <tr class="header">
                                        <th style="display:none">RowId</th>
                                        <th style="font-weight:700;">API NAME</th>
                                        <th style="font-weight:700;">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyid">
                                    <?php
                                        if (get_option('CUSTOM_API_WP_LIST')) {
                                            $list = get_option('CUSTOM_API_WP_LIST');
                                            foreach ($list as $key => $value) {
                                                echo "<tr>";
                                                echo "<td class='mo_custom_api_list_api_name'>" . esc_html($key) . "</td>";
                                                echo "<td>  <button class='mo_custom_api_ext_btn' onclick = 'custom_api_wp_edit(this)'>Edit<i class='fas fa-user-edit'></i></button>&nbsp
                                                            <button class='mo_custom_api_ext_btn' onclick ='custom_api_wp_delete(this)'>Delete<i class='fas fa-user-edit'></i></button>&nbsp
                                                            <button class='mo_custom_api_ext_btn' onclick = 'custom_api_wp_view(this)'>View<i class='fas fa-user-edit'></i></button>
                                                    </td>";
                                            }
                                        }
                                    ?>
                                </tbody>
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
    else
    {
        ?>
        <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
                <div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="margin:0px 0px 0px 3px;padding:30px;padding-top: 20px;">
                        <p class="mo_custom_api_heading">Configured Custom APIs:</p>
                        <hr>
                        <h6 style="margin-bottom:18px;">You have not created any custom API, to start <a class="mo_custom_api_ext_btn" href="admin.php?page=custom_api_wp_settings&action=addapi"><button class="mo_custom_api_ext_btn"> Click here</button></a></h6>
                        <!-- <p><strong>Notice: </strong><span style="color:red">*</span>With the current plan of the plugin you can create only one custom sql API, to create more, upgrade to <a href="admin.php?page=custom_api_wp_settings&action=license"><strong>Enterprise</strong></a> plan.</p> -->
                    </div>
                <?php contact_form();
                mo_custom_api_advertisement(); ?>
                </div>
            </div>
        </div>
            <?php
    }
}


function custom_api_wp_view_sql_api_details($ApiDisplay, $ApiName, $MethodName, $customparams){

    ?>
        <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
                <div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-9 mo_custom_api_page_layout" style="margin-left:3px;padding-left: 25px;padding-top: 20px;">
                        <h5><?php echo (" <span style='color:green;font-weight:700'>" . esc_attr($MethodName) ."</span> /{$ApiName}"); ?></h5>
                        <p style="margin-top:20px;">
                        <div class="mo_custom_api_method_name"><?php echo esc_html("{$MethodName}"); ?></div>
                            <input id="mo_custom_api_copy_text" class="mo_custom_api_display" value='<?php echo esc_attr("{$ApiDisplay}"); ?>' readonly>
                            <button onclick="mo_custom_api_copy_icon()" style="border: none;background-color: white;outline:none;"><img style="width:25px;height:25px;margin-top:-6px;"  src="<?php echo esc_url(plugin_dir_url(__FILE__)); ?>/images/copy3.png"></button>
                        </p>

                        <script>
                            function mo_custom_api_copy_icon() {
                                var copyText = document.getElementById("mo_custom_api_copy_text");
                                copyText.select();
                                copyText.setSelectionRange(0, 99999);
                                navigator.clipboard.writeText(copyText.value);
                            }
                        </script>

                        <div class="mo_custom_api_view_api_table">
                            <div class="mo_custom_api_view_api_table_heading">
                                <h6>Example</h6>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td><b>Request</b></td>
                                        <td><b>Format</b></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                       if($MethodName == 'GET'){
                                            ?>
                                            <tr>
                                                <td>Curl</td>
                                                <td>curl -X GET <?php echo esc_attr("$ApiDisplay"); ?></td>
                                            </tr>
                                            <?php
                                       }
                                       else{
                                            if(sizeof($customparams)){
                                                $curlbody = '';
                                                $i = 0;
                                                for ($i=0; $i< sizeof($customparams); $i++) {
                                                    $curlbody = $curlbody . $customparams[$i] . '={' . $customparams[$i] . '_value}';
                                    
                                                    if($i != (sizeof($customparams) - 1)){
                                                        $curlbody = $curlbody . '&';
                                                    }            
                                                }
                                                ?>
                                                    <tr>
                                                        <td>Curl</td>
                                                        <td>curl -d "<?php echo esc_attr($curlbody); ?>" -X <?php echo esc_attr($MethodName); ?> <?php echo "$ApiDisplay" ?></td>
                                                    </tr>
                                                <?php
                                            }else{
                                                ?>
                                                <tr>
                                                    <td>Curl</td>
                                                    <td>curl -X <?php echo esc_attr($MethodName) ?> <?php echo esc_attr("$ApiDisplay") ?></td>
                                                </tr>
                                                <?php
                                            }
                                    }
                                    ?>
                                    
                                </tbody>
                            </table>
                        </div>
                        <?php $api=get_site_url();?>
                        <form action="<?php echo esc_attr("$api") ?>/wp-admin/admin.php?page=custom_api_wp_settings&action=sqledit&apisql=<?php echo esc_attr("$ApiName") ?>" method="POST">
                            <input class="mo_custom_api_contact_us_submit_btn" style="margin-bottom: 20px;margin-top:20px;" type="submit" value="Edit API" onclick="">
                        </form>      
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

function custom_api_wp_view_api_details($ApiDisplay, $ApiName, $MethodName, $ConditionColumn, $SelectedCondtion, $SelectedParameter)
{
    ?>
        <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
	            <div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="margin-left:3px;padding-left: 25px;padding-top: 20px;">
                        <h5><?php echo " <span style='color:green;font-weight:700'>". esc_html($MethodName). "</span>". '/'. esc_html($ApiName); ?></h5>
                        <p style="margin-top:20px;">
                            <div class="mo_custom_api_method_name"><?php echo esc_html($MethodName); ?></div>
                            <input id="mo_custom_api_copy_text1" class="mo_custom_api_display" value='<?php echo esc_attr($ApiDisplay) ?>' readonly>
                            <button onclick="mo_custom_api_copy_icon()" style="border: none;background-color: white;outline:none;"><img style="width:25px;height:25px;margin-top:-6px;"  src="<?php echo esc_attr(plugin_dir_url(__FILE__)); ?>/images/copy3.png"></button>
                        </p>

                        <script>
                            function mo_custom_api_copy_icon() {
                                var copyText = document.getElementById("mo_custom_api_copy_text1");
                                copyText.select();
                                copyText.setSelectionRange(0, 99999);
                                navigator.clipboard.writeText(copyText.value);
                            }
                        </script>

                        <?php
                            if ($SelectedCondtion != 'no condition') {
                                ?>
                                    <div class="mo_custom_api_view_api_table">
                                        <div class="mo_custom_api_view_api_table_heading">
                                            <h6>Request Body</h6>
                                        </div>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Column Name</th>
                                                    <th>Description</th>
                                                    <th>Condition Applied</th>
                                                    <th>Parameter place in API</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td> <?php echo esc_html($ConditionColumn) ?> </td>
                                                    <td>Enter data of respective column in mentioned parameter</td>
                                                    <td>
                                                        <?php
                                                            if ("&amp;gt;" == $SelectedCondtion) {
                                                                echo 'Greater Than';
                                                            } 
                                                            else{
                                                                echo esc_html($SelectedCondtion);
                                                            }
                                                        ?>
                                                    </td>
                                                    <td> <?php echo esc_html($SelectedParameter) ?> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php
                            }
                        ?>

                        <div class="mo_custom_api_view_api_table">
                            <div class="mo_custom_api_view_api_table_heading">
                                <h6>Example</h6>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td><b>Request</b></td>
                                        <td><b>Format</b></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Curl</td>
                                        <td>curl -X GET <?php echo esc_html($ApiDisplay) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php $api=get_site_url();?>
                        <form action="<?php echo esc_attr($api) ?>/wp-admin/admin.php?page=custom_api_wp_settings&action=edit&api=<?php echo esc_attr($ApiName) ?>" method="POST">
                            <input class="mo_custom_api_contact_us_submit_btn" style="margin-bottom: 20px;margin-top:20px;" type="submit" value="Edit API" onclick="">
                        </form>      
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


function custom_api_wp_invalid_notice()
{
    ?>
        <div class="error notice" style="margin-left: 3px;">
            <p>Invalid API or API Name field is empty</p>

        </div>
    <?php
}


function custom_wp_api_check_method($var)
{
    if (isset($_POST['SubmitForm1'])) {
        $data = get_option('mo_custom_api_form1');
        if (!empty($data['MethodName'])) {
            if ($data['MethodName'] == $var) {
                echo " selected='selected'";
                unset($data['MethodName']);
                update_option('mo_custom_api_form1', $data);
            }
        }
    }
    if (isset($_POST['SendResult'])) {
        $FormData = get_option('mo_custom_api_form');
        if ((!empty($FormData['MethodName'])) && ($FormData['status'] == 'yes')) {
            if ($FormData['MethodName'] == $var) {
                echo " selected='selected'";
            }
        }
    }
}


function custom_api_wp_condition($var1)
{
    if (isset($_POST["SendResult"])) {
        $FormData = get_option('mo_custom_api_form');

        if (!empty($FormData['SelectedCondtion']) && ($FormData['status'] == 'yes')) {
            if ($FormData['SelectedCondtion'] == $var1) {
                echo " selected= 'selected' ";
            }
        }
    }
}


function custom_api_wp_param($var)
{
    $FormData = get_option('mo_custom_api_form');
    if (isset($_POST["SendResult"])) {
        if (!empty($FormData['SelectedParameter']) && ($FormData['status'] == 'yes')) {
            if ($FormData['SelectedParameter'] == $var) {
                echo "selected='selected'";

                $FormData['status'] == 'no';
                update_option('mo_custom_api_form', $FormData);
            }
        }
    }
}

function custom_api_wp_add_api()//MERGE ONE FINAL
{
    $check = true;
    $GetForm = get_option('mo_custom_api_form');

    if (isset($_POST['SendResult'])) {
        if ($GetForm['status'] == 'yes') {
            $ApiName = $GetForm['ApiName'];
            if (empty($ApiName)) {
                custom_api_wp_invalid_notice();
                $check = false;
            }
            $query = $GetForm["query"];
            $MethodName = $GetForm["MethodName"];
            $SelectedTable = $GetForm["TableName"];
            $SelectedColumn = $GetForm["SelectedColumn"];
            $ConditionColumn = $GetForm["ConditionColumn"];
            $SelectedCondtion = $GetForm["SelectedCondtion"];
            $SelectedParameter = $GetForm["SelectedParameter"];

            $current = array(
                $ApiName => array(
                    "TableName" => $SelectedTable,
                    "MethodName" => $MethodName,
                    "SelectedColumn" => $SelectedColumn,
                    "ConditionColumn" => $ConditionColumn,
                    "SelectedCondtion" => $SelectedCondtion,
                    "SelectedParameter" => $SelectedParameter,
                    "query" => $query,
                ),
            );

            if (get_option('CUSTOM_API_WP_LIST')) {
                $list = get_option('CUSTOM_API_WP_LIST');

                foreach ($list as $key => $value) {
                    if ($ApiName == $key) {
                        echo '
                        <div class="error notice" style="margin-left:3px">
                            <p style="color:red;"><b>API name already exist !!</b></p>
                        </div>';

                        $check = false;
                        break;
                    }
                }
            }
            if ($check == true) {
                if (get_option('CUSTOM_API_WP_LIST')) {
                    $list[$ApiName] = $current[$ApiName];

                    $api = get_site_url();
                    if ($SelectedCondtion == 'no condition') {
                        $ApiDisplay = "{$api}/wp-json/mo/v1/{$ApiName}";
                    } else {
                        $ApiDisplay = "{$api}/wp-json/mo/v1/{$ApiName}/{" . $ConditionColumn . "}";
                    }

                    update_option('CUSTOM_API_WP_LIST', $list);
                    unset($GetForm['status']);
                    update_option('mo_custom_api_form', $GetForm);
                    custom_api_wp_view_api_details($ApiDisplay, $ApiName, $MethodName, $ConditionColumn, $SelectedCondtion, $SelectedParameter);
                    return;
                }  
                else {
                    $api = get_site_url();
                    if ($SelectedCondtion == 'no condition') {
                        $ApiDisplay = "{$api}/wp-json/mo/v1/{$ApiName}";
                    } else {
                        $ApiDisplay = "{$api}/wp-json/mo/v1/{$ApiName}/{" . $ConditionColumn . "}";
                    }
                    update_option('CUSTOM_API_WP_LIST', $current);
                    unset($GetForm['status']);
                    update_option('mo_custom_api_form', $GetForm);
                    custom_api_wp_view_api_details($ApiDisplay, $ApiName, $MethodName, $ConditionColumn, $SelectedCondtion, $SelectedParameter);
                    return;
                }
            }
        }
    }

    ?>
        <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
			    <div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="padding: 15px 25px 25px 25px;margin-left:3px">

                        <form method="POST" style="visibility: hidden;">
                            <?php wp_nonce_field('CheckNonce1', 'SubmitUser1');?>
                            <input type="text" id="api_name_initial" name="api_name_initial" style="visibility: hidden;">
                            <input type="text" id="method_name_initial" name="method_name_initial" style="visibility: hidden;">
                            <input type="text" id="table_name_initial" name="table_name_initial" style="visibility: hidden;">
                            <input type="submit" id="SubmitForm1" name="SubmitForm1" style="visibility: hidden;">
                        </form>

                        <p style="margin-top: -30px;" class="mo_custom_api_heading">Create Custom API: <span style="float:right;">  <a class="mo_custom_api_setup_guide_button" href="https://plugins.miniorange.com/wordpress-create-custom-rest-api-endpoints#step1" target="_blank">Setup Guide</a> </span></p>
                        <hr class="mo_custom_api_hr">
                        <form method="POST">
                            <?php wp_nonce_field('CheckNonce', 'SubmitUser');?>
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels"> API Name</label>
                                </div>
                                <div class='col-md-6'>
                                    <input class="mo_custom_api_name" type="text" id="ApiName" <?php $data = get_option('mo_custom_api_form1');
                                        if (isset($_POST['SubmitForm1'])) {
                                            if (!empty($data['ApiName'])) {
                                                echo 'value ="' . esc_attr($data['ApiName']) . '" ';
                                                unset($data['ApiName']);
                                                update_option('mo_custom_api_form1', $data);
                                            }
                                        }
                                        $FormData = get_option('mo_custom_api_form');
                                        if (isset($_POST['SendResult'])) {
                                            if (($FormData['status'] == 'yes') && !empty($FormData['ApiName'])) {
                                                echo 'value ="' . esc_attr($FormData['ApiName']) . '" ';
                                            }
                                        }?> name="ApiName">
                                </div>
                            </div>
                            <br>      
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels"> Select Method</label>
                                </div>
                                <div class='col-md-6'>
                                    <select required class="mo_custom_api_SelectColumn" id="MethodName" name="MethodName">
                                        <option value="GET" selected<?php custom_wp_api_check_method("GET");?>>GET</option>
                                        <option value="POST" disabled <?php custom_wp_api_check_method("POST");?>>POST &nbsp &nbsp &nbsp &nbsp<span style="text-color:red;text-size:30px;">[PREMIUM]</span></option>
                                        <option value="PUT" disabled <?php custom_wp_api_check_method("PUT");?>>PUT &nbsp &nbsp &nbsp &nbsp &nbsp<span>[PREMIUM]</span></option>
                                        <option value="DELETE" disabled <?php custom_wp_api_check_method("DELETE");?>>DELETE &nbsp &nbsp <span>[PREMIUM]</span></option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels"> Select Table</label>
                                </div>
                                <div class='col-md-6'>
                                    <select class="mo_custom_api_SelectColumn" name="select-table" onchange="custom_api_wp_GetTbColumn()" id="select-table">
                                        <?php
                                            global $wpdb;
                                            $sql = "SHOW TABLES LIKE '%%'";
                                            $results = $wpdb->get_results($sql);
                                            $table_name = [];
                                            foreach ($results as $index => $value) {
                                                foreach ($value as $tableName) {
                                                    array_push($table_name, $tableName);
                                                }
                                            }
                                            $data = get_option('mo_custom_api_form1');
                                            $FormData = get_option('mo_custom_api_form');
                                            foreach ($table_name as $tb) {
                                                echo '<option value='. esc_attr($tb);
                                                if (isset($_POST["SubmitForm1"])) {
                                                    if (!empty($data['TableName'])) {
                                                        if ($data['TableName'] == $tb) {
                                                            echo " selected='selected'";
                                                        }
                                                    }
                                                }
                                                if (isset($_POST["SendResult"])) {
                                                    if (($FormData['status'] == 'yes') && !empty($FormData['TableName'])) {
                                                        if ($FormData['TableName'] == $tb) {
                                                            echo " selected='selected'";
                                                        }
                                                    }
                                                }
                                        
                                                echo '>'. esc_html($tb). "</option>";
                                            }
                                        ?>
                                    </select>           
                                </div>
                            </div>
                            <br>         
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels"> Select Columns</label>
                                </div>
                                <div class='col-md-6'>
                                    <select class="mo_custom_api_SelectColumn" id="SelectedColumn" multiple="multiple" name="Selectedcolumn">
                                        <?php
                                            global $wpdb;
                                            $data = get_option('mo_custom_api_form1');
                                            if (!empty($data['TableName'])) {
                                                $table1 = $data['TableName'];
                                        
                                                $column = [];
                                                $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                foreach ($existing_columns as $col) {
                                                    array_push($column, $col);
                                                }
                                                foreach ($column as $colu) {
                                                    echo '<option value='. esc_attr($colu);
                                        
                                                    echo '>'. esc_html($colu). "</option>";
                                                }
                                            }
                                        ?>
                                        <?php
                                            global $wpdb;
                                            $data = get_option('mo_custom_api_form1');
                                            $FormData = get_option('mo_custom_api_form');
                                            if (empty($data['TableName'])) {
                                                if (!empty($FormData['status']) && ($FormData['status'] == 'yes') && !empty($FormData['TableName'])) {
                                        
                                                    $table1 = $FormData['TableName'];
                                        
                                                    $column11 = $FormData['SelectedColumn'];
                                        
                                                    $column = [];
                                                    $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                    foreach ($existing_columns as $col) {
                                                        array_push($column, $col);
                                                    }
                                                    foreach ($column as $colu) {
                                                        $split = explode(",", $column11);
                                        
                                                        echo '<option value='. esc_attr($colu);
                                        
                                                        foreach ($split as $s) {
                                                            if ($s == $colu) {
                                                                echo " selected='selected'";
                                                            }
                                                        }
                                        
                                                        echo '>' .esc_html($colu). "</option>";
                                                    }
                                                }
                                            }
                                        ?>
                                        
                                    </select>           
                                </div>               
                            </div>
                            <br>            
                            <div class='row'>
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels">Choose Column to apply condition</label>
                                    <br>
                                    <select class="mo_custom_api_SelectColumn custom_field" id="OnColumn" name="OnColumn">
                                        <option value="">none selected </option>
                                        <?php
                                            global $wpdb;
                                            $data = get_option('mo_custom_api_form1');
                                            if (!empty($data['TableName'])) {
                                                $table1 = $data['TableName'];
                                        
                                                $column = [];
                                                $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                foreach ($existing_columns as $col) {
                                                    array_push($column, $col);
                                                }
                                                foreach ($column as $colu) {
                                                    echo '<option value='. esc_attr($colu);
                                                    echo '>'. esc_html($colu). "</option>";
                                                }
                                        
                                                unset($data['TableName']);
                                                update_option('mo_custom_api_form1', $data);
                                            }
                                        ?>
                                        <?php
                                            global $wpdb;
                                            $FormData = get_option('mo_custom_api_form');
                                            if (!empty($FormData['status']) && ($FormData['status'] == 'yes') && !empty($FormData['TableName'])) {
                                                $table1 = $FormData['TableName'];
                                        
                                                $column = [];
                                                $existing_columns = $wpdb->get_col("DESC {$table1}", 0);
                                                foreach ($existing_columns as $col) {
                                                    array_push($column, $col);
                                                }
                                                foreach ($column as $colu) {
                                                    echo '<option value='.esc_attr($colu);
                                        
                                                    if ($FormData['ConditionColumn'] == $colu) {
                                                        echo " selected = 'selected' ";
                                                    }
                                        
                                                    echo '>' .esc_html($colu). "</option>";
                                                }
                                            }
                                        ?>    
                                    </select>
                                </div>           
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels">Choose Condition</label>
                                    <br>
                                    <select class="mo_custom_api_SelectColumn custom_field" id="ColumnCondition" name="ColumnCondition">
                                        <option value="no condition" <?php custom_api_wp_condition("no condition");?>>No Condition </option>
                                        <option value="=" <?php custom_api_wp_condition("=");?>>Equal </option>
                                        <option value="Like" <?php custom_api_wp_condition("Like");?>>Like</option>
                                        <option value=">" <?php custom_api_wp_condition("&amp;gt;");?>>Greater Than</option>
                                        <option value="less than" <?php custom_api_wp_condition("less than");?>>Less Than</option>
                                        <option value="!=" <?php custom_api_wp_condition("!=");?>>Not Equal</option>
                                    </select>
                                </div>        
                                <div class='col-md-4'>
                                    <label class="mo_custom_api_labels">URL Parameters  <span style="font-size:12px">[Default: First Parameter]</span></label>
                                    <br>
                                    <select class="mo_custom_api_SelectColumn" id="ColumnParam" onchange="custom_api_wp_CustomText()" name="ColumnParam">
                                        <option value="1">First Parameter </option>
                                        <option value="2" disabled>Second Parameter</option>
                                        <option value="3" disabled>Third Parameter</option>
                                        <option value="4" disabled>Fourth Parameter</option>
                                        <option value="5" disabled>Custom value</option>
                                    </select>
                                    <div id="Param" style="visibility: hidden;">
                                        <input type="text" id="CustomParam">
                                    </div>
                                </div>              
                            </div>
                            <br>
                            <hr class="mo_custom_api_hr">
                            <input class='mo_custom_api_create_update_btn' type="submit" value="Generate API" name="SendResult" id="SendResult" onclick="custom_api_wp_ShowData()">
                            <input type="text" id="QueryVal" name="QueryVal" style="visibility:hidden;">
                            <input type="text" id="Selectedcolumn11" name="Selectedcolumn11" style="visibility: hidden;">
                        </form>
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


function custom_api_wp_authentication()
{
    ?>
        <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
            <div class="box-body" >
                <div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="padding: 30px">
                        <div>
                            <p  class="mo_custom_api_heading" style="margin-top: -12px;">API Key Authentication:<span > <a class="mo_custom_api_plan_link" href="admin.php?page=custom_api_wp_settings&action=license">[PREMIUM]</a></span></h1>
                        </div>
                        <hr class="mo_custom_api_hr">
                        <h5>Universal API Key: </h5>
                        <br>
                        <h6>You can use the below API key to authenticate your WordPress REST APIs.</h6>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-4">
                                <h6 style="margin-top:5px;"><strong>API Key:</strong></h6>
                            </div>
                            <div class="col-md-8">
                                <input class="mo_custom_api_name" style="padding-right:40px;" type="password" id="password1" placeholder="" readonly value="kgjygfgvjgfthdrdsrye5786utyy6">&nbsp;&nbsp;
                                <img id="show_btn" style="height:20px;width:20px;margin-left:-45px;" src="<?php echo esc_attr(plugin_dir_url(__FILE__)); ?>/images/eye.png">
                                <br><br>
                                <button class="mo_custom_api_ext_btn" style="width:135px; padding:4px;"><a id="regeneratetoken" name="action" ><h6 style="font-size:14px">Generate New Key</h6></a></button>
                                <br>
                            </div>
                        </div>
                        <hr class="mo_custom_api_hr">
                        <br>
                        <div class="row">
                            <div style="margin-left:10px;">
                                <?php $restricted = array();?>
                                <h6 style="font-weight:500;font-size:1rem;margin-left:0px;">Choose HTTP Methods which you want to restrict from public access :</h6>
                                <br>
                                <input type="checkbox" id="get_check" name="get_check" value="GET" disabled>
                                <label for="get_check"> GET </label><br>
                                <input type="checkbox" id="post_check" name="post_check" value="POST" disabled>
                                <label for="post_check"> POST</label><br>
                                <input type="checkbox" id="put_check" name="put_check" value="PUT" disabled>
                                <label for="put_check"> PUT</label><br>
                                <input type="checkbox" id="del_check" name="del_check" value="DELETE" disabled>
                                <label for="del_check"> DELETE</label><br>
                                <button type="button" class="mo_custom_api_contact_us_submit_btn"  style="width:70px;margin-top:12px;" id="myBtn1">Save</button>
                            </div>
                            <div id="myModal1" class="mo_custom_api_modal">
                                <div class="modal-dialog" style="text-align: center;">
                                    <div class="modal-content">
                                        <div class="modal-header" style="text-align: center;">
                                            <h5 class="modal-title mo_custom_api_modal_title">Upgrade Required</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                           <b><p>You are on the free version. Please upgrade to <span style="color:red;">Premium+</span> Plan to use this feature.</p></b>
                                        </div>
                                        <div class="modal-footer" style="text-align: center;">
                                            <a href="admin.php?page=custom_api_wp_settings&action=license" class="mo_custom_api_upgrade_plan" style="">Click here to checkout plans</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                var modal = document.getElementById("myModal1");
                                var btn = document.getElementById("myBtn1");
                                var span = document.getElementsByClassName("close")[0];
                                btn.onclick = function() {
                                    modal.style.display = "block";
                                }
                                span.onclick = function() {
                                    modal.style.display = "none";
                                }
                                window.onclick = function(event) {
                                    if (event.target == modal) {
                                        modal.style.display = "none";
                                    }
                                }
                            </script>
                        </div>
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

function custom_api_wp_custom_sql()//SAVE CHANGES
{
    ?>
    
     <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
	    		<div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="padding:30px;padding-top: 15px;">
                        <p class="mo_custom_api_heading">Create Custom SQL API:<span style="float:right"> <a class="mo_custom_api_setup_guide_button" href="https://plugins.miniorange.com/integrate-external-third-party-rest-api-endpoints-into-wordpress#step_3" target="_blank">Setup Guide</a> </span></p>
                        <hr class="mo_custom_api_hr">
                        <form id="custom_api_wp_sql" method="post">
                            <?php wp_nonce_field('custom_api_wp_sql', 'custom_api_wp_sql_field'); ?>
                            <input type="hidden" name="option" value="custom_api_wp_sql">
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> API Name</label>
                                </div>
                                <div class=col-md-6>
                                    <input type="text" class="mo_custom_api_custom_field" id="SQLApiName" name="SQLApiName"  required value="">
                                </div>
                            </div>
                            <br>
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> Select Method</label>
                                </div>
                                <p>
                                    <div class=col-md-7>
                                        <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="MethodName" name="MethodName" onchange="change_description(this)">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                        <option value="PUT">PUT</option>
                                        <option value="DELETE">DELETE</option>
                                        </select>
                                        <span style="margin-left:15px" class="mo_custom_api_method_description" id="method_description"> Fetch data via API </span> 
                                    </div>
                                </p>
                            </div>
                            <br>
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> Enable custom query parameters:</label>
                                </div>
                                <div class=col-md-6>
                                    <input type="checkbox" class="mo_custom_api_SelectColumn" style="margin-top:5px;" id="QueryParameter" name="QueryParameter" value="1"  >
                                </div>
                            </div>
                            <br>
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> Enter SQL Query</label>
                                </div>
                                <div class=col-md-6>
                                    <textarea id="customsql" name="customsql" rows=10  class="mo_custom_api_txtarea" required></textarea>
                                </div>
                            </div>
                            <hr class="mo_custom_api_hr">
                            <input type="submit" class='mo_custom_api_create_update_btn' id="custom_api_wp_sql_submit" value="Generate API">
                        </form>
                    </div>
                    <?php contact_form();
                    mo_custom_api_advertisement();?>
                </div>
            </div>
        </div>
    <?php
}


function custom_api_wp_edit_sqlapi($api1)
{
    $GetApi = get_option('custom_api_wp_sql');
    $selectedsql = $GetApi[$api1]; 

    ?>
        <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px;">
            <div class="box-body">
	    		<div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="padding:30px;padding-top: 15px;">
                        <p class="mo_custom_api_heading">Update Custom SQL API:<span style="float:right"> <a class="mo_custom_api_setup_guide_button" href="https://plugins.miniorange.com/integrate-external-third-party-rest-api-endpoints-into-wordpress#step_3" target="_blank">Setup Guide</a> </span></p>
                        <hr class="mo_custom_api_hr">
                        <form id="custom_api_wp_sql" method="post">
                            <?php wp_nonce_field('custom_api_wp_sql', 'custom_api_wp_sql_field'); ?>
                            <input type="hidden" name="option" value="custom_api_wp_sql">
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> API Name</label>
                                </div>
                                <div class=col-md-6>
                                    <input type="text" class="mo_custom_api_custom_field" id="SQLApiName" name="SQLApiName" readonly value="<?php echo esc_attr($api1); ?>">
                                </div>
                            </div>
                            <br>
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> Select Method</label>
                                </div>
                                <p>
                                    <div class=col-md-7>
                                        <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="MethodName" name="MethodName" readonly onchange="change_description(this)">
                                            <option value="GET" <?php if ($selectedsql['method'] == 'GET') {echo 'selected';} ?> >GET</option>
                                            <option value="POST" <?php if ($selectedsql['method'] == 'POST') {echo 'selected';} ?>>POST</option>
                                            <option value="PUT" <?php if ($selectedsql['method'] == 'PUT') {echo 'selected';} ?>>PUT</option>
                                            <option value="DELETE" <?php if ($selectedsql['method'] == 'DELETE') {echo 'selected';} ?>>DELETE</option>
                                        </select>&nbsp;&nbsp;
                                        <span style="margin-left:15px" class="mo_custom_api_method_description" id="method_description"> Fetch data via API </span> 
                                    </div>
                                </p>
                            </div>
                            <br>
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> Enable custom query parameters:</label>
                                </div>
                                <div class=col-md-6>
                                    <input type="checkbox" class="mo_custom_api_SelectColumn" style="margin-top:5px;" id="QueryParameter" name="QueryParameter" value="1" <?php if ($selectedsql['query_params'] == 1) { echo "checked";} ?> >
                                </div>
                            </div>
                            <br>
                            <div class=row>
                                <div class=col-md-5>
                                    <label class="mo_custom_api_labels"> Enter SQL Query</label>
                                </div>
                                <div class=col-md-6>
                                    <textarea id="customsql" name="customsql" rows=10 class="mo_custom_api_txtarea"><?php echo esc_attr($selectedsql['sql_query']) ?></textarea>                                  
                                </div>
                            </div>
                            <hr class="mo_custom_api_hr">
                            <input type="submit" class='mo_custom_api_create_update_btn' id="custom_api_wp_sql_submit" value="Update API">
                        </form>
                    </div>
                    <?php contact_form();
                    mo_custom_api_advertisement();?>
                </div>
            </div>
        </div>
    <?php
}

function custom_api_wp_saved_sql_api()
{   
    if (get_option('custom_api_wp_sql')) {
        ?>
        <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
                    <div class="box-body" >
                        <div class="row mo_custom_api_page_layout_row">
                            <div class="col-md-8 mo_custom_api_page_layout" style="padding: 20px">
                                    <p style="margin: 0px 0px 10px 13px;" class="mo_custom_api_heading">Configured Custom SQL APIs:</p>
                                        <table id="tbldata" class="table table-hover" style="width: 75%">
                                            <thead>
                                                <tr class="header">
                                                    <th style="display:none">RowId</th>
                                                    <th>API NAME</th>
                                                    <th>METHOD NAME</th>
                                                    <th>ACTIONS</th>
                                                </tr>
                                            </thead>
        
                                            <tbody id="tbodyid">
                                                <?php
                                                    if (get_option('custom_api_wp_sql')) {
                                                        $list = get_option('custom_api_wp_sql');
                            
                                                        foreach ($list as $key => $value) {
                                                            echo "<tr>";
                                                            echo " <td class='mo_custom_api_list_api_name'>" . esc_html($key) . "</td>";
                                                            echo " <td class='mo_custom_api_list_api_name'>" . esc_html($value['method']) . "</td>";
                                                            echo "<td> <button class='mo_custom_api_ext_btn' onclick = 'custom_api_wp_edit_sql(this)'>Edit<i class='fas fa-user-edit'></i></button>&nbsp
                                                                        <button class='mo_custom_api_ext_btn' onclick ='custom_api_wp_delete_sql(this)'>Delete<i class='fas fa-user-edit'></i></button>&nbsp
                                                                        <button class='mo_custom_api_ext_btn' onclick ='custom_api_wp_view_sql(this)'>View<i class='fas fa-user-edit'></i></button>&nbsp
                                                                </td>";
                                                        }
                                                    } 
                                                ?>
                                            </tbody>

                                        </table>
                                        <p><strong>Notice: </strong><span style="color:red">*</span>With the current plan of the plugin you can create only one custom sql API, to create more, upgrade to <a href="admin.php?page=custom_api_wp_settings&action=license"><strong>Enterprise</strong></a> plan.</p>
                                    </div>
                                    <?php contact_form();
                                    mo_custom_api_advertisement(); ?>
                                </div>
                           </div>
                        </div>
                   <?php
    }

    else{
        ?>
        <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
                <div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="margin:0px 0px 0px 3px;padding:30px;padding-top: 20px;">
                        <p class="mo_custom_api_heading">Configured Custom SQL APIs:</p>
                        <hr>
                        <h6 style="margin-bottom:18px;">You have not created any custom sql API, to start <a  href="admin.php?page=custom_api_wp_settings&action=customsql"><button class="mo_custom_api_ext_btn">Click here</button></a></h6>
                        <p><strong>Notice: </strong><span style="color:red">*</span>With the current plan of the plugin you can create only one custom sql API, to create more, upgrade to <a href="admin.php?page=custom_api_wp_settings&action=license"><strong>Enterprise</strong></a> plan.</p>
                    </div>
                <?php contact_form();
                mo_custom_api_advertisement(); ?>
                </div>
            </div>
        </div>
            <?php
        }
}

function custom_api_wp_external_api_connection()
{
    $ExternalApiConfiguration = get_option("custom_api_test_ExternalApiConfiguration");
    $HeaderKey = "";
    $HeaderValue = "";
    if (isset($ExternalApiConfiguration["ExternalHeaders"]) && $ExternalApiConfiguration["ExternalHeaders"] != null && $ExternalApiConfiguration["ExternalHeaders"] > 0) {
        $HeaderArray = explode(":", $ExternalApiConfiguration["ExternalHeaders"][0]);
        $HeaderKey = $HeaderArray[0];
        $HeaderValue = $HeaderArray[1];
    }

    $BodyArray = array();
    $json_value = '';
    $bool = isset($ExternalApiConfiguration["ExternalApiPostFieldNew"]);
    if (!$bool) {
        if (isset($ExternalApiConfiguration["ExternalApiPostField"]) && $ExternalApiConfiguration["ExternalApiPostField"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "x-www-form-urlencode") {
            $BodyArray = explode("=", explode("&", $ExternalApiConfiguration["ExternalApiPostField"])[0]);
            $BodyKey = $BodyArray[0];
            $BodyValue = $BodyArray[1];
        }
        elseif(isset($ExternalApiConfiguration["ExternalApiPostField"]) && $ExternalApiConfiguration["ExternalApiPostField"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json"){
            $json_value = $ExternalApiConfiguration["ExternalApiPostField"];
        }
    }
    else{
        if (isset($ExternalApiConfiguration["ExternalApiPostFieldNew"]) && $ExternalApiConfiguration["ExternalApiPostFieldNew"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "x-www-form-urlencode") {
            $pos = strpos($ExternalApiConfiguration["ExternalApiPostFieldNew"][0], ":");
            if ($pos !== false && substr_count($ExternalApiConfiguration["ExternalApiPostFieldNew"][0], ":") > 1) {
                $bodyval = substr_replace($ExternalApiConfiguration["ExternalApiPostFieldNew"][0], "##mo_remove##", $pos, strlen(":"));
                $BodyArray = explode('##mo_remove##', $bodyval);
            } else {
                $BodyArray = explode(":", $ExternalApiConfiguration["ExternalApiPostFieldNew"][0]);
            }

            $BodyKey = $BodyArray[0];
            $BodyValue = $BodyArray[1];
        }
        elseif($ExternalApiConfiguration["ExternalApiPostFieldNew"] && $ExternalApiConfiguration["ExternalApiPostFieldNew"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json"){
            $json_value = $ExternalApiConfiguration["ExternalApiPostFieldNew"];
        }
    }

    ?>
    <div class="wrap" style="margin-top:5px;">
        <div class="box-body">

            <div class="form-horizontal">
                <div class="box-body" style="margin-left: 16px;width:99%">
                    <div class="row" style="padding: unset;">
                        <div class="col-md-12 mo_custom_api_page_layout" style="padding:25px">
               <div style="margin-top:0px;">
               <p class="mo_custom_api_heading">External API: <span style="float:right;"> <a class="mo_custom_api_setup_guide_button" href="https://developers.miniorange.com/docs/rest-api-authentication/wordpress/developerhookscustom" target="_blank">Developer Docs</a>  <a class="mo_custom_api_setup_guide_button" href="https://plugins.miniorange.com/integrate-external-third-party-rest-api-endpoints-into-wordpress#step_2" target="_blank">Setup Guide</a> </span></p>
               </div>
               <hr style="margin-top:5px;">
            <form method="POST"><?php wp_nonce_field('CheckNonce', 'SubmitUser'); ?>
            <div class=row>

                <div class=col-md-5>
                    <label class="mo_custom_api_labels"> API Name</label>
                </div>
                <div class=col-md-6>
                    <input class="mo_custom_api_custom_field" type="text" id="ExternalApiName"  name="ExternalApiName" value=<?php echo isset($ExternalApiConfiguration["ExternalApiName"]) ? esc_attr($ExternalApiConfiguration["ExternalApiName"]) : "" ?> >
                </div>
            </div>
            <br>

            <div class=row>
                <div class=col-md-5>
                    <label class="mo_custom_api_labels"> Select Method</label>
                </div>

                <div class=col-md-6>
                    <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="MethodName" name="MethodName" >
                        <option value="GET" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "GET") {echo "Selected";} ?>>GET</option>
                        <option value="POST" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "POST") {echo "Selected";} ?> >POST</option>
                        <option value="PUT" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "PUT") {echo "Selected";} ?> >PUT</option>
                        <option value="DELETE" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "DELETE") {echo "Selected";} ?> >DELETE</option>
                    </select>
                
                </div>
            </div>
            <br>

            <div class=row>
                <div class=col-md-5>
                    <label class="mo_custom_api_labels"> External API</label>
                </div>

                <div class=col-md-6>
                    <input type="text" id="ExternalApi" class="mo_custom_api_custom_field" name="ExternalApi"  placeholder="Enter External API" value=<?php echo isset($ExternalApiConfiguration["ExternalEndpoint"]) ? esc_attr(html_entity_decode(esc_attr($ExternalApiConfiguration["ExternalEndpoint"]))) : ""; ?> >
                </div>

            </div>
            <br>
            <div class=row id="ExternalApiHeaders">
                <div class=col-md-2>
                    <label class="mo_custom_api_labels"> Headers</label>
                </div>

                <div class=col-md-3>
                    <input type="text" class="mo_custom_api_custom_field" id="ExternalHeaderKey"  name="ExternalHeaderKey" placeholder="Enter Key" value=<?php echo esc_attr($HeaderKey) ?>>
                </div>

                <div class=col-md-3>
                    <input type="text" id="ExternalHeaderValue" class="mo_custom_api_custom_field" name="ExternalHeaderValue" placeholder="Enter Value"  value=<?php echo "'" . esc_attr($HeaderValue) . "'" ?>>
                </div>

                <div class=col-md-3>
                <input type="button" style="width:50px;margin-left:0px;margin-top: 5px;" class="mo_custom_api_contact_us_submit_btn" value ="Add" onclick="add_header(' ',' ')">
                </div>

            </div>
            <br>


            <div class="row" id="ExternalApiBody">
                <div class=col-md-2>
                    <label class="mo_custom_api_labels"> Request Body</label>
                </div>

                <div class=col-md-3>
                    <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="RequestBodyType" name="RequestBodyType" onchange="RequestBodyTypeOnChange()" >
                    <option value="x-www-form-urlencode" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "x-www-form-urlencode") {echo "Selected";} ?> >x-www-form-urlencode</option>
                    <option value="json" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {echo "Selected";} ?>>JSON</option>
                    </select>
                </div>


                <div class=col-md-3 id = "DivRequestBodyKey" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {
        echo 'style="display: none; "';
    } else {
        echo 'style="display: block; "';
    } ?>>
                <input type="text" id="RequestBodyKey" class="mo_custom_api_custom_field" name="RequestBodyKey" placeholder="Enter Key" value="<?php echo isset($BodyKey) ? esc_attr($BodyKey) : '' ?>">

                </div>

                <div class=col-md-3 id = "DivRequestBodyValue" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {echo 'style="display: none; "';} else {echo 'style="display: block; "';} ?>>
                <input type="text" id="RequestBodyValue" class="mo_custom_api_custom_field" name="RequestBodyValue" placeholder="Enter Value" value="<?php echo isset($BodyValue) ? esc_attr($BodyValue) : '' ?>" >

                </div>

                <div class=col-md-1 id = "DivRequestBodyAddButton" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {echo 'style="display: none; "';} else {echo 'style="display: block; "';} ?>>
                <input type="button" class="mo_custom_api_contact_us_submit_btn" style="margin-top: 5px;width:50px;margin-left:-30px" id="RequestBodyAddButton" onclick="add_request_body_param(' ',' ')" value="Add">

                </div>

                <div class=col-md-5 id="RequestBodyJsonTextArea" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {echo 'style="display: block; "';} else {echo 'style="display: none; "';} ?>>
                    <textarea id="RequestBodyJson" name="RequestBodyJson" style="height:123px;width:50%"><?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {echo esc_attr($json_value);} ?></textarea>
                </div>

            </div>

            <br>
            <div class=row>
                                    <div class=col-md-5>
                                        <label class="mo_custom_api_labels">Select Dependent API</label>
                                    </div>
                                    <div class=col-md-6>
                                    <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="SelectedAPIsColumn" name="SelectedAPIscolumn">
                                    <?php
                                        $externalapis = get_option('custom_api_save_ExternalApiConfiguration');
                                        foreach ($externalapis as $key => $value) {
                                            echo "<option value='" . $key . "'";
                                            echo isset($ExternalApiConfiguration["ExternalApiDependentConnections"]) && in_array($key, $ExternalApiConfiguration["ExternalApiDependentConnections"]) ? "selected='selected'" : "";
                                            echo ">" . esc_attr($key) . "</option>";
                                        }
                                    ?>

                                    </select>
                                    </div>
                                    
                                </div>
                                <br>
            <div class="row">
            <div class=col-md-5>
                    <label class="mo_custom_api_labels"> Response Data Type</label>
                </div>
                <div class=col-md-6>
                <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="responsebodytype" name="responsebodytype">
                    <option value="xml" disabled="true" <?php if (isset($ExternalApiConfiguration["ResponseBodyType"]) && $ExternalApiConfiguration["ResponseBodyType"] == "xml") {echo "Selected";} ?>disabled="true">XML  [ENTERPRISE]</option>
                    <option value="json" <?php if (isset($ExternalApiConfiguration["ResponseBodyType"]) && $ExternalApiConfiguration["ResponseBodyType"] == "json") {echo "Selected";} ?>>JSON</option>
                </select>
                </div>
            </div>
            <br>


            <div class=row>
                <div class=col-md-5>

                    <label class="mo_custom_api_labels"> Select Response Fields</label>
                </div>
                <div class=col-md-6>
                    <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="SelectedColumn" multiple="multiple" name="Selectedcolumn">


                        <?php

    $data = get_option('ExternalApiResponseKey');

    if ($data == "false") {
        echo "<option value='custom_api_wp_getall' >Complete Response</option>";
    } elseif (empty($data)) {
        echo "<option >---Execute External API First---</option>";
    } else {
        $SavedExternalApiConfiguration = get_option("custom_api_save_ExternalApiConfiguration");

        $ExternalSelctedResponseKey = array();
        if (!empty($ExternalApiConfiguration)) {
            if (!empty($SavedExternalApiConfiguration[$ExternalApiConfiguration["ExternalApiName"]]["ExternalApiResponseDataKey"])) {
                $ExternalSelctedResponseKey = $SavedExternalApiConfiguration[$ExternalApiConfiguration["ExternalApiName"]]["ExternalApiResponseDataKey"];
            }
        }
        foreach ($data as $colu) {
            echo "<option value='{" . esc_attr($colu) . "}'";

            echo isset($ExternalSelctedResponseKey) && in_array($colu, $ExternalSelctedResponseKey) ? "selected='selected'" : "";

            echo ">{" . esc_attr($colu) . "}</option>";
        }
    } ?>

                    </select>

                </div>

            </div>
            <hr style="margin-top:10px;">
            <input type="submit" value="Save" class="mo_custom_api_contact_us_submit_btn" name="ExternalApiConnectionSave" onclick="saveexternalapi()" > &nbsp;&nbsp;
            <input type="submit" value="Execute" class="mo_custom_api_contact_us_submit_btn" name="ExternalApiConnection" >
            <input type="text" id="ExternalHeaderCount" name="ExternalHeaderCount" style="display: none;">
            <input type="text" id="ExternalResponseBodyCount" name="ExternalResponseBodyCount" style="display: none;">
            <input type="text" id="selected_column_all" name="selected_column_all" style="visibility: hidden;">
            </form>

        </div>
    </div>
    </div>
    </div>
</div>
    </div>

    <script>
function custom_api_test_execute(){
    var myWindow = window.open('<?php echo esc_url(site_url()); // phpcs:ignore WordPress.Security.EscapeOutput?>' + '/wp-admin/?customapiexternal=testexecute', "Test Attribute Configuration", "width=600, height=600");
}
</script>
<input type="button" id="dynamic_external_ui" name="dynamic_external_ui" style="display:none;" onclick = '<?php
if (isset($ExternalApiConfiguration["ExternalHeaders"]) && (isset($ExternalApiConfiguration["ExternalApiPostFieldNew"]) || isset($ExternalApiConfiguration["ExternalApiPostField"] )) && isset($ExternalApiConfiguration["ExternalApiBodyRequestType"])) {
    echo 'add_dynamic_externalapi_ui(';
        echo esc_html(json_encode($ExternalApiConfiguration["ExternalHeaders"]));
        echo ",";
        if ($ExternalApiConfiguration["ExternalApiBodyRequestType"] != 'json' && $bool) {
            echo esc_html(json_encode($ExternalApiConfiguration["ExternalApiPostFieldNew"]));
        }
        elseif ($ExternalApiConfiguration["ExternalApiBodyRequestType"] != 'json' && !$bool) {
            echo '"' . esc_html($ExternalApiConfiguration["ExternalApiPostField"]) . '"';
        }
        else{
            echo esc_html($json_value);
        }
        echo ",";
        echo '"' . esc_html($ExternalApiConfiguration["ExternalApiBodyRequestType"]) . '';
        echo ",";
        if(empty($ExternalApiConfiguration["ExternalApiPostFieldNew"]))
        echo '"0"';
        else
        echo '"1"';
        echo ")";
    } ?> '>

<?php
}


function custom_api_wp_saved_external_api_connection()
{
    update_option("custom_api_test_ExternalApiConfiguration", ""); 
    
    if (get_option('custom_api_save_ExternalApiConfiguration')) {
        ?>
        <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
                    <div class="box-body" >
                        <div class="row mo_custom_api_page_layout_row">
                            <div class="col-md-8 mo_custom_api_page_layout" style="padding: 20px">
                                    <p style="margin: 0px 0px 10px 13px;" class="mo_custom_api_heading">Configured External APIs:</p>
                                        <table id="tbldata" class="table table-hover" style="width: 75%">
                                            <thead>
                                                <tr class="header">
                                                    <th style="display:none">RowId</th>
                                                    <th>API NAME</th>
                                                    <th>METHOD NAME</th>
                                                    <th>ACTIONS</th>
                                                </tr>
                                            </thead>
        
                                            <tbody id="tbodyid">
                                                <?php
                                                    if (get_option('custom_api_save_ExternalApiConfiguration')) {
                                                        $list = get_option('custom_api_save_ExternalApiConfiguration');
                                                        foreach ($list as $key => $value) {
                                                            echo "<tr>";
                                                            echo "<td class='mo_custom_api_list_api_name'>" . esc_html($key) . "</td>";
                                                            echo " <td style='color:#36B37E;font-size:17px;font-weight:700'>" . esc_html($value["ExternalApiRequestType"]). "</td>";
                                                            echo "<td>  <button class='mo_custom_api_ext_btn' onclick = 'editexternalapi(this)'><b>Edit</b><i class='fas fa-user-edit'></i></button>&nbsp
                                                                        <button class='mo_custom_api_ext_btn' onclick ='deleteExternalapi(this)'><b>Delete</b><i class='fas fa-user-edit'></i></button>&nbsp
                                                                        
                                                                  </td>";
                                                        }
                                                    }
                                                ?>
                                            </tbody>

                                        </table>
                                        <p><strong>Notice: </strong><span style="color:red">*</span>With the current plan of the plugin you can create only one external API connection, to create more, upgrade to <a href="admin.php?page=custom_api_wp_settings&action=license"><strong>Enterprise</strong></a> plan.</p>
                                    </div>
                                    <?php contact_form();
                                    mo_custom_api_advertisement(); ?>
                                </div>
                           </div>
                        </div>
                   <?php }

                   else{
                       ?>
                        <div class="wrap mo_custom_api_page_layout_wrap">
            <div class="box-body">
	    		<div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="margin:0px 0px 0px 3px;padding:30px;padding-top: 20px;">
                    <p class="mo_custom_api_heading">Configured External APIs:</p>
                    <hr>
                      <h6>You have not integrated any external api, to start intergration <a href="admin.php?page=custom_api_wp_settings&action=externalapi"> <button class="mo_custom_api_ext_btn"> Click here</button></a></h6><br>
                      <p><strong>Notice: </strong><span style="color:red">*</span>With the current plan of the plugin you can integrate only one External API, to integrate more upgrade to <a href="admin.php?page=custom_api_wp_settings&action=license"><strong>Enterprise</strong></a> plan.</p>
                      </div>
                      <?php contact_form();
                      mo_custom_api_advertisement(); ?> 
                   </div>
                   </div>
                   </div>
                      <?php
                   }
}


function custom_api_wp_edit_externalapi($api1)
{
    $list = get_option("custom_api_save_ExternalApiConfiguration");
    $ExternalApiConfiguration = $list[$api1];

    $HeaderKey = "";
    $HeaderValue = "";

    if (isset($ExternalApiConfiguration["ExternalHeaders"]) && $ExternalApiConfiguration["ExternalHeaders"] != null && $ExternalApiConfiguration["ExternalHeaders"] > 0) {
        $HeaderArray = explode(":", $ExternalApiConfiguration["ExternalHeaders"][0]);
        $HeaderKey = $HeaderArray[0];
        $HeaderValue = $HeaderArray[1];
    }
    
    $bool = isset($ExternalApiConfiguration["ExternalApiPostFieldNew"]);
    $BodyArray = array();
    $json_value = '';
    if (!$bool) {

        if (isset($ExternalApiConfiguration["ExternalApiPostField"]) && $ExternalApiConfiguration["ExternalApiPostField"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "x-www-form-urlencode") {
            $BodyArray = explode("=", explode("&", $ExternalApiConfiguration["ExternalApiPostField"])[0]);
            $BodyKey = $BodyArray[0];
            $BodyValue = $BodyArray[1];
        }
        elseif(isset($ExternalApiConfiguration["ExternalApiPostField"]) && $ExternalApiConfiguration["ExternalApiPostField"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json"){
            $json_value = $ExternalApiConfiguration["ExternalApiPostField"];
        }
    }
    else{
        
        if (isset($ExternalApiConfiguration["ExternalApiPostFieldNew"]) && $ExternalApiConfiguration["ExternalApiPostFieldNew"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "x-www-form-urlencode") {
            $pos = strpos($ExternalApiConfiguration["ExternalApiPostFieldNew"][0], ":");
            if ($pos !== false && substr_count($ExternalApiConfiguration["ExternalApiPostFieldNew"][0], ":") > 1) {
                $bodyval = substr_replace($ExternalApiConfiguration["ExternalApiPostFieldNew"][0], "##mo_remove##", $pos, strlen(":"));
                $BodyArray = explode('##mo_remove##', $bodyval);
            } else {
                $BodyArray = explode(":", $ExternalApiConfiguration["ExternalApiPostFieldNew"][0]);
            }
            
            $BodyKey = $BodyArray[0];
            $BodyValue = $BodyArray[1];
        }
        elseif(isset($ExternalApiConfiguration["ExternalApiPostFieldNew"]) && $ExternalApiConfiguration["ExternalApiPostFieldNew"] != null && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json"){
            $json_value = $ExternalApiConfiguration["ExternalApiPostFieldNew"];
        }
    }
    
    ?>

        <div class="wrap" style="margin-top:5px;">
            <div class="form-horizontal">
                <div class="box-body" style="margin-left: 18px;width:99%">
                    <div class="row" style="padding: unset;">
                        <div class="col-md-12 mo_custom_api_page_layout" style="padding:25px">
                                <div style="margin-top:0px;">
                                <p class="mo_custom_api_heading">Update External API: <span style="float:right;"> <a class="mo_custom_api_setup_guide_button" href="https://developers.miniorange.com/docs/rest-api-authentication/wordpress/developerhookscustom" target="_blank">Developer Docs</a>  <a class="mo_custom_api_setup_guide_button" href="https://plugins.miniorange.com/integrate-external-third-party-rest-api-endpoints-into-wordpress#step_2" target="_blank">Setup Guide</a> </span></p> 
                                 </div>
                                <hr style="margin-top:10px;">
                                <form method="POST"><?php wp_nonce_field('CheckNonce', 'SubmitUser'); ?>
                                <div class=row>
                    
                                    <div class=col-md-5>
                                        <label class="mo_custom_api_labels"> API Name</label>
                                    </div>
                                    <div class=col-md-6>
                                        <input type="text" id="ExternalApiName" class="mo_custom_api_custom_field" name="ExternalApiName" value=<?php echo isset($ExternalApiConfiguration["ExternalApiName"]) ? esc_html($ExternalApiConfiguration["ExternalApiName"]) : "" ?> readonly>
                                    </div>
                                </div>
                                <br>
                    
                                <div class=row>
                                    <div class=col-md-5>
                                        <label class="mo_custom_api_labels"> Select Method</label>
                                    </div>
                    
                                    <div class=col-md-6>
                                        <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="MethodName" name="MethodName" >
                                            <option value="GET" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "GET") {echo "Selected";} ?>>GET</option>
                                            <option value="POST" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "POST") {echo "Selected";} ?> >POST</option>
                                            <option value="PUT" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "PUT") {echo "Selected";} ?> >PUT</option>
                                            <option value="DELETE" <?php if (isset($ExternalApiConfiguration["ExternalApiRequestType"]) && $ExternalApiConfiguration["ExternalApiRequestType"] == "DELETE") {echo "Selected";} ?> >DELETE</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                    
                                <div class=row>

                                    <div class=col-md-5>
                                        <label class="mo_custom_api_labels"> External API</label>
                                    </div>
                    
                                    <div class=col-md-6>
                                        <input type="text" id="ExternalApi" class="mo_custom_api_custom_field" name="ExternalApi" placeholder="Enter External API" value=<?php echo isset($ExternalApiConfiguration["ExternalEndpoint"]) ? esc_html(html_entity_decode($ExternalApiConfiguration["ExternalEndpoint"])) : ""; ?> >
                                    </div>
                    
                                </div>
                                <br>
                                <div class=row id="ExternalApiHeaders">
                                    <div class=col-md-2>
                                        <label class="mo_custom_api_labels"> Headers</label>
                                    </div>
                    
                                    <div class=col-md-3>
                                        <input type="text" class="mo_custom_api_custom_field" id="ExternalHeaderKey"  name="ExternalHeaderKey" placeholder="Enter Key" value=<?php echo esc_attr($HeaderKey) ?>>
                                    </div>
                    
                                    <div class=col-md-3>
                                        <input type="text" class="mo_custom_api_custom_field" id="ExternalHeaderValue"  name="ExternalHeaderValue" placeholder="Enter Value"  value=<?php echo "'" . esc_attr($HeaderValue) . "'" ?>>
                                    </div>
                    
                                    <div class=col-md-3>
                                    <input type="button" style="width:50px;margin-left:0px;margin-top: 5px;" class="mo_custom_api_contact_us_submit_btn" value ="Add" onclick="add_header(' ',' ')">
                                    </div>
                    
                                </div>
                                <br>
                    
                    
                                <div class=row id="ExternalApiBody">
                                    <div class=col-md-2>
                                        <label class="mo_custom_api_labels"> Request Body</label>
                                    </div>
                                    <div class=col-md-3>
                                        <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="RequestBodyType" name="RequestBodyType" onchange="RequestBodyTypeOnChange()" >
                                        <option value="x-www-form-urlencode" <?php if (!empty($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] === "x-www-form-urlencode") {echo "Selected";} ?> >x-www-form-urlencode</option>
                                        <option value="json" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {echo "Selected";} ?>>JSON</option>
                                        </select>
                                    </div>
                
                                    <div class=col-md-3 id = "DivRequestBodyKey" <?php if (!empty($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] === "json") {echo 'style="display: none; "';} else { 'style="display: block; "';} ?>>
                                    <input type="text" id="RequestBodyKey" class="mo_custom_api_custom_field" name="RequestBodyKey" placeholder="Enter Key" value="<?php echo isset($BodyKey) ? esc_html($BodyKey) : '' ?>">
                
                                    </div>
                
                                    <div class=col-md-2 id = "DivRequestBodyValue" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] === "json") {echo 'style="display: none; "';} else {echo 'style="display: block; "';} ?>>
                                    <input type="text" id="RequestBodyValue" class="mo_custom_api_custom_field" name="RequestBodyValue" placeholder="Enter Value" value="<?php echo isset($BodyValue) ? esc_html($BodyValue) : '' ?>" >
                
                                    </div>
                
                                    <div class=col-md-1 id = "DivRequestBodyAddButton" <?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] === "json") {echo 'style="display: none; "';} else {echo 'style="display: block; "';} ?>>
                                    <input type="button" class="mo_custom_api_contact_us_submit_btn" style="margin-left: 88px;margin-top: 5px;width:50px;" id="RequestBodyAddButton" onclick="add_request_body_param(' ',' ')" value="Add">
                                    </div>
                
                                    <div class=col-md-6 id="RequestBodyJsonTextArea" <?php if (!empty($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] === "json") {echo 'style="display: block; "';} else {echo 'style="display: none; "';} ?>>
                                        <textarea id="RequestBodyJson" name="RequestBodyJson" style="height:123px;width:50%"><?php if (isset($ExternalApiConfiguration["ExternalApiBodyRequestType"]) && $ExternalApiConfiguration["ExternalApiBodyRequestType"] == "json") {echo esc_attr($json_value);} ?></textarea>
                                    </div>
                    
                                 </div>
                    
                                <br>
                                <div class="row">
                                    <div class=col-md-5>
                                        <label class="mo_custom_api_labels"> Response Data Type</label>
                                    </div>
                                    <div class=col-md-6>
                                        <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="responsebodytype" name="responsebodytype">
                                            <option value="xml" disabled="true" <?php if (isset($ExternalApiConfiguration["ResponseBodyType"]) && $ExternalApiConfiguration["ResponseBodyType"] == "xml") {echo "Selected";} ?> >XML  [ENTERPRISE]</option>
                                            <option value="json" <?php if (isset($ExternalApiConfiguration["ResponseBodyType"]) && $ExternalApiConfiguration["ResponseBodyType"] == "json") {echo "Selected";} ?>>JSON</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class=row>
                                    <div class=col-md-5>
                                        <label class="mo_custom_api_labels">Select Dependent API</label>
                                    </div>
                                    <div class=col-md-3>
                                    <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="SelectedAPIsColumn" style="color:red;align-item:center" name="SelectedAPIscolumn" readonly>
                                    <option value="[ENTERPRISE]" selected disabled>[ENTERPRISE]</option>
                                    </select>
                                    <label class="mo_custom_api_labels" ></label>
                                    </div>
                                    <div class=col-md-1>
                                        
                                    </div>
                                    
                                </div>
                                <br>
                    
                                <div class=row>
                                    <div class=col-md-5>
                    
                                        <label class="mo_custom_api_labels"> Select Response Fields</label>
                                    </div>
                                    <div class=col-md-6>
                    
                                        <select class="mo_custom_api_SelectColumn mo_custom_api_custom_field" id="SelectedColumn" multiple="multiple" name="Selectedcolumn">
                    
                    
                                            <?php

    $data = get_option('ExternalApiResponseKey');

    if ($data == "false") {
        echo "<option value='custom_api_wp_getall' >Complete Response</option>";
    } elseif (empty($data)) {
        echo "<option >---Execute External API First---</option>";
    } else {
        $list = get_option("custom_api_save_ExternalApiConfiguration");
        $SavedExternalApiConfiguration = $list[$api1];

        $ExternalSelctedResponseKey = array();
        if (isset($SavedExternalApiConfiguration["ExternalApiResponseDataKey"])) {
            $ExternalSelctedResponseKey = $SavedExternalApiConfiguration["ExternalApiResponseDataKey"];
        }

        foreach ($data as $colu) {
            echo "<option value='" . esc_attr($colu) . "'";

            echo isset($ExternalSelctedResponseKey) && in_array($colu, $ExternalSelctedResponseKey) ? "selected='selected'" : "";

            echo ">" . esc_attr($colu) . "</option>";
        }
    } ?>

                   </select>

               </div>

           </div>
           <hr style="margin-top:10px;">
            <input type="submit" value="Save" style="" class="mo_custom_api_contact_us_submit_btn" name="ExternalApiConnectionSave" onclick="saveexternalapi()" > &nbsp;&nbsp;
            <input type="submit" value="Execute" class="mo_custom_api_contact_us_submit_btn" name="ExternalApiConnection" >
            <input type="text" id="ExternalHeaderCount" name="ExternalHeaderCount" style="display: none;">
            <input type="text" id="ExternalResponseBodyCount" name="ExternalResponseBodyCount" style="display: none;">
            <input type="text" id="selected_column_all" name="selected_column_all" style="visibility: hidden;">
           </form>

   </div>
   </div>
   </div>
   </div>
   </div>
   <script>
function custom_api_test_execute(){
   var myWindow = window.open('<?php echo esc_url(site_url()); // phpcs:ignore WordPress.Security.EscapeOutput?>' + '/wp-admin/?customapiexternal=testexecute', "Test Attribute Configuration", "width=600, height=1000");
}
</script>

<input type="button" id="dynamic_external_ui" name="dynamic_external_ui" style="display:none;" onclick = '<?php
if (isset($ExternalApiConfiguration["ExternalHeaders"]) && (isset($ExternalApiConfiguration["ExternalApiPostFieldNew"]) || isset($ExternalApiConfiguration["ExternalApiPostField"] )) && isset($ExternalApiConfiguration["ExternalApiBodyRequestType"])) {
        echo 'add_dynamic_externalapi_ui(';
        echo esc_html(json_encode($ExternalApiConfiguration["ExternalHeaders"]));
        echo ",";
        if ($ExternalApiConfiguration["ExternalApiBodyRequestType"] != 'json' && isset($ExternalApiConfiguration["ExternalApiPostFieldNew"])) {
            echo esc_html(json_encode($ExternalApiConfiguration["ExternalApiPostFieldNew"]));
        }
        elseif ($ExternalApiConfiguration["ExternalApiBodyRequestType"] != 'json' && !isset($ExternalApiConfiguration["ExternalApiPostFieldNew"])) {
            echo '"' . esc_html($ExternalApiConfiguration["ExternalApiPostField"]) . '"';
        }
        else{
            echo esc_html($json_value);
        }
        echo ",";
        echo '"' . esc_html($ExternalApiConfiguration["ExternalApiBodyRequestType"]) . '"';
        echo ",";
        if(empty($ExternalApiConfiguration["ExternalApiPostFieldNew"]))
        echo '"0"';
        else
        echo '"1"';
        echo ")";
    } ?> '>

<?php
}


function custom_api_integration_page(){
    ?>
        <div class="wrap mo_custom_api_page_layout_wrap" style="margin-left:18px">
            <div class="box-body">
	    		<div class="row mo_custom_api_page_layout_row">
                    <div class="col-md-8 mo_custom_api_page_layout" style="padding:30px;padding-top: 15px;background-color: #f5f5f5;">
                        <p class="mo_custom_api_heading">Check out all our integration and use cases-</p>
                        <hr style="height: 5px;background: #1f3668;margin-top: 9px;border-radius: 30px;">

                        <div class="mo_custom_api_intg_cards">
                            <h4 class="mo_custom_api_intg_head">Woocommerce Sync products from External API.</h4>
                            <p class="mo_custom_api_intg_para">If you have a Woocommerce store having a lot of products and want to sync/import products from external inventory, supplier via APIs. Then this can be acheived using this plugin along with Woocommerce sync add-on.</p>
                            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "/images/woo-3.png"); ?>" class="mo_cusotm_api_intg_logo" alt=" Image">
                            <span class="mo_custom_api_intg_rect"></span>
                            <span class="mo_custom_api_intg_tri"></span>
                            <a class="mo_custom_api_intg_readmore" href="https://plugins.miniorange.com/woocommerce-api-product-sync-with-woocommerce-rest-apis" target="_blank">Learn More</a>
                        </div>
                        
                        <div class="mo_custom_api_intg_cards">
                            <h4 class="mo_custom_api_intg_head">Integrate external API in WordPress.</h4>
                            <p class="mo_custom_api_intg_para">If you are looking to connect your WordPress site with External APIs in order to fetch data from there and display in WordPress or want to use that data further or want to update data from WordPress to third-party app via thier APIs, then it can be acheived with our plugin's Connect to External API feature. </p>
                            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "/images/ex3.png"); ?>" class="mo_cusotm_api_intg_logo" alt=" Image">
                            <span class="mo_custom_api_intg_rect"></span>
                            <span class="mo_custom_api_intg_tri"></span>
                            <a class="mo_custom_api_intg_readmore" href="https://plugins.miniorange.com/integrate-external-third-party-rest-api-endpoints-into-wordpress" target="_blank">Learn More</a>
                        </div>

                        <div class="mo_custom_api_intg_cards">
                            <h4 class="mo_custom_api_intg_head">Create API with custom SQL query.</h4>
                            <p class="mo_custom_api_intg_para">If you want to create the custom API endpoints in WordPress using your own complex custom SQL queries which will provide you with full control over what operations you want to perform. Then, Custom SQL API feature is what you need.</p>
                            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "/images/sql-1.jpg"); ?>" class="mo_cusotm_api_intg_logo" alt=" Image">
                            <span class="mo_custom_api_intg_rect"></span>
                            <span class="mo_custom_api_intg_tri"></span>
                            <a class="mo_custom_api_intg_readmore" href="https://plugins.miniorange.com/integrate-external-third-party-rest-api-endpoints-into-wordpress#step_3" target="_blank">Learn More</a>
                        </div>

                        <div class="mo_custom_api_intg_cards">
                            <h4 class="mo_custom_api_intg_head">Zoho Webhooks and API integration in WordPress.</h4>
                            <p class="mo_custom_api_intg_para">If you are using Zoho product like Zoho subscription, CRM, Campaign ,Creator, Inventory etc and wants to connect it with your WordPress site for purposes of real-time data sync via Zoho Webhooks and Zoho REST APIs. Then we can  provide you with the customized solution for that. For more information contact us at apisupport@xecurify.com.</p>
                            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "/images/zoho.png"); ?>" class="mo_cusotm_api_intg_logo" alt=" Image">
                            <span class="mo_custom_api_intg_rect"></span>
                            <span class="mo_custom_api_intg_tri"></span>
                            <!-- <a class="mo_custom_api_intg_readmore" href="https://plugins.miniorange.com/wordpress-user-provisioning" target="_blank">Learn More</a> -->
                        </div>

                        <div class="mo_custom_api_intg_cards">
                            <h4 class="mo_custom_api_intg_head">AliDropship Sync products from External API.</h4>
                            <p class="mo_custom_api_intg_para"> If you have Alidropship products store having a lot of products and want to sync/import products from external inventory, supplier via APIs. Then this can be acheived using this plugin along with Alidropship sync add-on.</p>
                            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "/images/alidropship.png"); ?>" class="mo_cusotm_api_intg_logo" alt=" Image">
                            <span class="mo_custom_api_intg_rect"></span>
                            <span class="mo_custom_api_intg_tri"></span>
                            <!-- <a class="mo_custom_api_intg_readmore" href="https://plugins.miniorange.com/wordpress-user-provisioning" target="_blank">Learn More</a> -->
                        </div>

                        <div class="mo_custom_api_intg_cards">
                            <h4 class="mo_custom_api_intg_head">Connect External API on Woocommerce events.</h4>
                            <p class="mo_custom_api_intg_para">If you have a Woocommerce store and want to call the external/3rd-party provider APIs on various Woocommerce events like product purchase, order created, order status update, order whishlisted, user registered etc, then using the plugin's Connect to External API feature and developer hooks, this can be integrated.</p>
                            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "/images/woo-3.png"); ?>" class="mo_cusotm_api_intg_logo" alt=" Image">
                            <span class="mo_custom_api_intg_rect"></span>
                            <span class="mo_custom_api_intg_tri"></span>
                            <a class="mo_custom_api_intg_readmore" href="https://plugins.miniorange.com/woocommerce-events-integration-on-webhooks" target="_blank">Learn More</a>
                        </div>
                        <div class="mo_custom_api_intg_cards">
                            <h4 class="mo_custom_api_intg_head">Connect Google sheet to Woocommerce.</h4>
                            <p class="mo_custom_api_intg_para">If you are looking to connect your WordPress or Woocommerce with Google sheet such that data can sync between these platforms on real-time events, then the plugin can be extened to acheive that.</p>
                            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "/images/Google_Sheet.png"); ?>" class="mo_cusotm_api_intg_logo" alt=" Image">
                            <span class="mo_custom_api_intg_rect"></span>
                            <span class="mo_custom_api_intg_tri"></span>
                            <!-- <a class="mo_custom_api_intg_readmore" href="https://plugins.miniorange.com/woocommerce-api-product-sync-with-woocommerce-rest-apis" target="_blank">Learn More</a> -->
                        </div>
                        <div class="mo_custom_api_intg_cards" style="width:97.35%;height:75px;">
                            <p style="font-size: 15px;font-weight: 500;padding:26px;">If you want custom features in the plugin, just drop an email at <a href="mailto:apisupport@xecurify.com?subject=Custom API for WP - Custom Requirement">apisupport@xecurify.com</a>.</p>  
                        </div>
                    </div>
                    <?php contact_form();
                    mo_custom_api_advertisement(); ?>
                </div>
            </div>
        </div>
    <?php
}

