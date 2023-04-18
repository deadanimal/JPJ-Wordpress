<?php


function custom_api_wp_show_success_message()
{
    remove_action('admin_notices', 'custom_api_success_message');
    add_action('admin_notices', 'custom_api_error_message');
}

function custom_api_wp_show_error_message()
{
    remove_action('admin_notices', 'custom_api_error_message');
    add_action('admin_notices', 'custom_api_success_message');
}

function custom_api_wp_empty_or_null($value)
{
    if (!isset($value) || empty($value)) {
        return true;
    }
    return false;
}

function custom_api_success_message()
{
    $class = "error";
    $message = get_option('custom_api_wp_message');
    echo "<div style='margin-left:6px;' class='" . esc_html($class) . "'> <p>" . esc_html($message) . "<button type='button' class='close' data-dismiss='modal'>&times;</button></p></div>";
}

function custom_api_error_message()
{
    $class = "updated";
    $message = get_option('custom_api_wp_message');
    echo "<div style='margin-left:6px;' class='" . esc_html($class) . "'><p>" . esc_html($message) . "<button type='button' class='close' data-dismiss='modal'>&times;</button></p></div>";
}

function check_internet_connection()
{
    return (bool) @fsockopen('test.miniorange.in', 443, $iErrno, $sErrStr, 5);
}

function get_timestamp()
{
    $url = get_option('cutom_api_wp_host_name') . '/moas/rest/mobile/get-timestamp';
    $headers = array('Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic');
    $args = array(
        'method' => 'POST',
        'body' => array(),
        'timeout' => '15',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => $headers,

    );

    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: " . esc_html($error_message);
        exit();
    }

    return wp_remote_retrieve_body($response);
}

function custom_api_send_email_alert($email, $phone, $message,$rating)
{

    if (!check_internet_connection()) {
        return;
    }

    $url = get_option('cutom_api_wp_host_name') . '/moas/api/notify/send';
    $defaultCustomerKey = "16555";
    $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

    $customerKey = $defaultCustomerKey;
    $apiKey = $defaultApiKey;

    $currentTimeInMillis = get_timestamp();
    $stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
    $hashValue = hash("sha512", $stringToHash);
    $customerKeyHeader = "Customer-Key: " . $customerKey;
    $timestampHeader = "Timestamp: " . $currentTimeInMillis;
    $authorizationHeader = "Authorization: " . $hashValue;
    $fromEmail = $email;
    $subject = "Feedback: Custom API for WP";
    $site_url = site_url();

    global $user;
    $user = wp_get_current_user();
    $query = '[Custom API for WP - ' . CUSTOM_API_FOR_WORDPRESS_VERSION . ' ] : ' . $message;
    if($message=="Feedback skipped")
    {
        $content = '<div >Hello, <br><br>First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br><p style="color:#FF0000">'.$query.'</p></div>';
    }
    else
        $content = '<div >Hello, <br><br>First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>Rating: '.$rating.'<br><br>Query :' . $query . '</div>';

    $fields = array(
        'customerKey' => $customerKey,
        'sendEmail' => true,
        'email' => array(
            'customerKey' => $customerKey,
            'fromEmail' => $fromEmail,
            'bccEmail' => 'apisupport@xecurify.com',
            'fromName' => 'miniOrange',
            'toEmail' => 'apisupport@xecurify.com',
            'toName' => 'apisupport@xecurify.com',
            'subject' => $subject,
            'content' => $content,
        ),
    );
    $field_string = json_encode($fields);
    $headers = array('Content-Type' => 'application/json');
    $headers['Customer-Key'] = $customerKey;
    $headers['Timestamp'] = $currentTimeInMillis;
    $headers['Authorization'] = $hashValue;
    $args = array(
        'method' => 'POST',
        'body' => $field_string,
        'timeout' => '15',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => $headers,

    );

    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: " . esc_html($error_message);
        exit();
    }
}

function custom_api_authentication_is_customer_registered()
{
    $email = get_option('custom_api_authentication_admin_email');
    $customerKey = get_option('custom_api_authentication_admin_customer_key');
    if (!$email || !$customerKey || !is_numeric(trim($customerKey))) {

        return 0;
    } else {
        return 1;
    }
}

function mo_custom_api_send_trial_alert($email, $trial_plan, $message, $subject)
    {
    if (!check_internet_connection()) {
        return;
    }
    $url = get_option('cutom_api_wp_host_name') . '/moas/api/notify/send';
    $defaultCustomerKey = "16555";
    $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

    $customerKey = $defaultCustomerKey;
    $apiKey = $defaultApiKey;

    $currentTimeInMillis = get_timestamp();
    $stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
    $hashValue = hash("sha512", $stringToHash);
    $customerKeyHeader = "Customer-Key: " . $customerKey;
    $timestampHeader = "Timestamp: " . $currentTimeInMillis;
    $authorizationHeader = "Authorization: " . $hashValue;
    $fromEmail = $email;
    $site_url = site_url();

    global $user;
    $user = wp_get_current_user();

    $content = '<div >Hello, </a><br><br><b>Email :</b><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br><b>Requested Trial for :</b> ' . $trial_plan . '<br><br><b>Requirements (Usecase) :</b> ' . $message . '</div>';

    $fields = array(
        'customerKey' => $customerKey,
        'sendEmail' => true,
        'email' => array(
            'customerKey' => $customerKey,
            'fromEmail' => $fromEmail,
            'bccEmail' => 'apisupport@xecurify.com',
            'fromName' => 'miniOrange',
            'toEmail' => 'apisupport@xecurify.com',
            'toName' => 'apisupport@xecurify.com',
            'subject' => $subject,
            'content' => $content,
        ),
    );
    $field_string = json_encode($fields);
    $headers = array('Content-Type' => 'application/json');
    $headers['Customer-Key'] = $customerKey;
    $headers['Timestamp'] = $currentTimeInMillis;
    $headers['Authorization'] = $hashValue;
    $args = array(
        'method' => 'POST',
        'body' => $field_string,
        'timeout' => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => $headers,

    );

    $response = wp_remote_post($url, $args);
    $body = wp_remote_retrieve_body($response);
    $body = json_decode($body, true);
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: " . esc_html($error_message);
        exit();
    }
    elseif(isset($body) && $body['status']== "ERROR"){
        return "WRONG_FORMAT";
        exit();
    }
        return true;
}


