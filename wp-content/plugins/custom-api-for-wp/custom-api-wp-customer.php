<?php

function custom_api_authentication_check_empty_or_null($value)
{
    if (!isset($value) || empty($value)) {
        return true;
    }
    return false;
}

function custom_api_check_customer()
{
    $url = get_option('cutom_api_wp_host_name') . "/moas/rest/customer/check-if-exists";
    $email = get_option("custom_api_authentication_admin_email");

    $fields = array(
        'email' => $email,
    );
    $field_string = json_encode($fields);
    $headers = array('Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic');
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

    return wp_remote_retrieve_body($response);
}

function custom_api_create_customer()
{
    $url = get_option('cutom_api_wp_host_name') . '/moas/rest/customer/add';
    $email = get_option('custom_api_authentication_admin_email');
    $phone = get_option('custom_api_authentication_admin_phone');
    $password = get_option('password');
    $firstName = get_option('custom_api_authentication_admin_fname');
    $lastName = get_option('custom_api_authentication_admin_lname');
    $company = get_option('custom_api_authentication_admin_company');

    $fields = array(
        'companyName' => $company,
        'areaOfInterest' => 'Custom Api WP',
        'firstname' => $firstName,
        'lastname' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'password' => $password,
    );
    $field_string = json_encode($fields);

    $headers = array('Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic');
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

    return wp_remote_retrieve_body($response);
}

function custom_api_auth_get_customer_key()
{
    update_option('cutom_api_wp_host_name', 'https://login.xecurify.com');
    $url = get_option('cutom_api_wp_host_name') . "/moas/rest/customer/key";
    $email = get_option("custom_api_authentication_admin_email");

    $password = get_option("password");

    $fields = array(
        'email' => $email,
        'password' => $password,
    );
    $field_string = json_encode($fields);

    $headers = array('Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic');
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

    return wp_remote_retrieve_body($response);
}
