<?php

namespace FluentFormPro\Integrations\UserRegistration;

use FluentForm\App\Services\ConditionAssesor;
use FluentForm\Framework\Helpers\ArrayHelper;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

trait Getter
{
    /**
     * Get the username value from the form data
     * by formatting the shortcode properly.
     */
    public function getUsername($username, $data)
    {
        $username = str_replace(
            ['[', ']'], 
            ['.', ''], 
            $username
        );

        return ArrayHelper::get($data, $username);
    }
    public function checkCondition($parsedValue, $formData)
    {
        $conditionSettings = ArrayHelper::get($parsedValue, 'conditionals');
        if (
            !$conditionSettings ||
            !ArrayHelper::isTrue($conditionSettings, 'status') ||
            !count(ArrayHelper::get($conditionSettings, 'conditions'))
        ) {
            return true;
        }

        return ConditionAssesor::evaluate($parsedValue, $formData);
    }
    protected function resetErrormessage($errors, $msg)
    {
        if (!isset($errors['restricted'])) {
            $errors['restricted'] = [];
        }
        $errors['restricted'][] = __($msg, 'fluentformpro');
        return $errors;
    }
    public function getFormUserFeeds($form)
    {
        return wpFluent()->table('fluentform_form_meta')
            ->where('form_id', $form->id)
            ->where('meta_key', 'user_registration_feeds')
            ->get();
    }
    public function validate($settings, $settingsFields)
    {
        foreach ($settingsFields['fields'] as $field) {

            if ($field['key'] != 'CustomFields') continue;

            $errors = [];

            foreach ($field['primary_fileds'] as $primaryField) {
                if (!empty($primaryField['required'])) {
                    if (empty($settings[$primaryField['key']])) {
                        $errors[$primaryField['key']] = $primaryField['label'] . ' is required.';
                    }
                }
            }

            if ($errors) {
                wp_send_json_error([
                    'message' => array_shift($errors),
                    'errors' => $errors
                ], 422);
            }
        }

        return $settings;
    }

    protected function updateUser($parsedData, $userId)
    {
        $name = trim(ArrayHelper::get($parsedData, 'first_name'). ' ' . ArrayHelper::get($parsedData, 'last_name'));

        $data = array_filter([
            'ID' => $userId,
            'user_nicename' => $name,
            'display_name' => $name,
            'user_url' => ArrayHelper::get($parsedData, 'user_url'),
            'first_name' => ArrayHelper::get($parsedData, 'first_name'),
            'last_name' => ArrayHelper::get($parsedData, 'last_name'),
            'nickname' => ArrayHelper::get($parsedData, 'nickname'),
            'user_pass' => ArrayHelper::get($parsedData, 'password'),
            'description' => ArrayHelper::get($parsedData, 'description'),
            'user_email' => ArrayHelper::get($parsedData, 'email')
        ]);
        if ($data) {
            return wp_update_user($data);
        }
        return new \WP_Error(301, 'Update Failed');
    }

    protected function addLog($title, $status, $description, $formId, $entryId, $integrationKey)
    {
        do_action('ff_log_data', [
            'title' => $title,
            'status' => $status,
            'description' => $description,
            'parent_source_id' => $formId,
            'source_id' => $entryId,
            'component' => $integrationKey,
            'source_type' => 'submission_item'
        ]);
        return true;
    }

}
