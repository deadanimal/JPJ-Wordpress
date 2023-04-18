<?php
/*
 * Do Not USE "name" space because The Pro Add-On Used this Class
 */

use NinjaTables\Classes\ArrayHelper;

include_once plugin_dir_path(__FILE__) . 'ReadyMadeTable.php';
include_once plugin_dir_path(__FILE__) . 'ImportExport.php';
include_once plugin_dir_path(__FILE__) . 'DynamicConfig.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpmanageninja.com
 * @since      4.1.6
 *
 * @package    ninja-tables
 * @subpackage ninja-tables/admin/table-builder
 */
class NinjaTableBuilder
{
    public function __construct()
    {
        add_filter('upload_mimes', function ($file_types) {
            $new_filetypes        = [];
            $new_filetypes['svg'] = 'image/svg';
            $file_types           = array_merge($file_types, $new_filetypes);

            return $file_types;

        }, 10, 1);
    }

    public function table_builder_ajax_routes()
    {
        if ( ! ninja_table_admin_role()) {
            return;
        }

        ninjaTablesValidateNonce('ninja_table_admin_nonce');

        $valid_routes = [
            'update-table'     => 'update',
            'edit-table-by-id' => 'editTableById',
            'import-table'     => 'importTable',
            'export-table'     => 'exportTable',
            'get-table-config' => 'getTableConfig',
            'create-table'     => 'create',
        ];

        $requested_route = sanitize_text_field($_REQUEST['target_action']);
        if (isset($valid_routes[$requested_route])) {
            $this->{$valid_routes[$requested_route]}();
        }
        wp_die();
    }

    public function getTableConfig()
    {
        $this->getAllInitialData();
    }

    public function editTableById()
    {
        $table_id          = intval($_REQUEST['id']);
        $table_settings    = get_post_meta($table_id, '_ninja_table_builder_table_settings', true);
        $table_responsive  = get_post_meta($table_id, '_ninja_table_builder_table_responsive', true);
        $table_data        = get_post_meta($table_id, '_ninja_table_builder_table_data', true);
        $components        = $this->componentConfig();
        $ready_made_tables = $this->templateConfig();
        $table_data_info = DynamicConfig::getTableDataInfo($table_data['data'], $this->tableColumnStyling(), $this->tableRawStyling());

        wp_send_json_success([
            'settings'          => DynamicConfig::getSetting($table_settings, $this->settingConfig()),
            'responsive'        => DynamicConfig::getResponsive($table_responsive, $this->responsiveConfig()),
            'components'        => $components,
            'ready_made_tables' => $ready_made_tables,
            'table_data'        => [
                'id'         => $table_id,
                'table_name' => $table_data['table_name'],
                'data'       => $table_data_info,
                'headers'    => $table_data['headers'],
                'table'      => array_replace_recursive($this->getOtherTableConfig(), $table_data['table'])
            ]
        ], 200);
    }

    public function update()
    {
        $table_id   = intval($_REQUEST['table_id']);
        $table_html = ninjaTablesEscapeScript($_REQUEST['table_html']);
        $json       = ninjaTablesEscapeScript($_REQUEST['data']);
        $data       = json_decode(stripcslashes($json), true);

        $table_name            = ArrayHelper::get($data, 'table_data.table_name');
        $table_settings        = ArrayHelper::get($data, 'settings');
        $table_responsive      = ArrayHelper::get($data, 'responsive');
        $table_data            = ArrayHelper::get($data, 'table_data');
        $table_data['headers'] = ArrayHelper::get($data, 'table_data.headers');

        $this->wpUpdatePost($table_id, $table_name);

        $data = [
            'table_name'       => $table_name,
            'table_settings'   => $table_settings,
            'table_responsive' => $table_responsive,
            'table_data'       => $table_data,
            'table_html'       => $table_html
        ];
        $this->updatePostMeta($table_id, $data);
    }

    public function wpInsertPost($table_name)
    {
        $my_post = [
            'post_title'  => $table_name,
            'post_type'   => 'ninja-table',
            'post_status' => 'publish'
        ];

        return wp_insert_post($my_post);
    }

    public function wpUpdatePost($table_id, $table_name)
    {
        $my_post = [
            'ID'          => $table_id,
            'post_title'  => $table_name,
            'post_type'   => 'ninja-table',
            'post_status' => 'publish'
        ];

        return wp_update_post($my_post);
    }

    public function create()
    {
        $table_type = sanitize_text_field($_REQUEST['data']['table_data']['table_type']);
        $table_name = sanitize_text_field($_REQUEST['data']['table_data']['table_name']);

        if (isset($table_type) && $table_type !== '') {
            $this->generateByTemplateConfig($table_type);
        }

        $table_id              = $this->wpInsertPost($table_name);
        $data                  = sanitize_post_field('data', $_REQUEST['data'], $table_id, 'db');
        $table_data            = $data['table_data'];
        $table_data['headers'] = $this->makeTableHeader($table_data);
        $table_data['data']    = $this->makeTableRow($table_data);

        $meta_data = [
            'table_name'       => $table_name,
            'table_settings'   => $this->settingConfig(),
            'table_responsive' => $this->responsiveConfig(),
            'table_data'       => $table_data,
            'table_html'       => null
        ];

        $this->updatePostMeta($table_id, $meta_data);
    }

    public function generateByTemplateConfig($table_type)
    {
        $table            = (new ReadyMadeTable())->tableByType($table_type);
        $table_settings   = $table['table_settings'];
        $table_responsive = $table['table_responsive'];
        $table_data       = $table['table_data'];

        $table_id = $this->wpInsertPost($table_data['table_name']);

        $data = [
            'table_id'         => $table_id,
            'table_name'       => $table_type,
            'table_settings'   => $table_settings,
            'table_responsive' => $table_responsive,
            'table_data'       => $table_data,
            'table_html'       => null
        ];

        $this->updatePostMeta($table_id, $data);
    }

    public function updatePostMeta($table_id, array $data)
    {
        update_post_meta($table_id, '_ninja_tables_data_provider', 'drag_and_drop');
        update_post_meta($table_id, '_ninja_table_builder_table_html', $data['table_html']);
        update_post_meta($table_id, '_ninja_table_builder_table_settings', $data['table_settings']);
        update_post_meta($table_id, '_ninja_table_builder_table_responsive', $data['table_responsive']);
        update_post_meta($table_id, '_ninja_table_builder_table_data', $data['table_data']);

        wp_send_json_success([
            'id' => $table_id
        ]);
    }

    public function importTable()
    {
        $url      = sanitize_text_field($_REQUEST['url']);
        $fileName = 'Ninja-tables' . date('d-m-Y');

        if (isset($url) && ! empty($url)) {
            $data = ImportExport::importFromURL($url);
        } else {
            $data     = ImportExport::import();
            $fileName = sanitize_text_field($_FILES['file']['name']);
        }

        $this->importCSV($data, $fileName);
    }

    public function exportTable()
    {
        ImportExport::export();
    }

    public function importCSV($csvData, $fileName)
    {

        $table_id                  = $this->wpInsertPost($fileName);
        $table_data                = $this->getTableData();
        $table_data['table']['tr'] = count($csvData);
        $table_data['table']['tc'] = count($csvData[0]);
        $table_data['headers']     = $this->makeTableHeader($table_data);
        $table_data['table_name']  = $fileName;
        $table_data['data']        = $this->makeTableRow($table_data, $csvData);

        $data = [
            'table_name'       => $fileName,
            'table_settings'   => $this->settingConfig(),
            'table_responsive' => $this->responsiveConfig(),
            'table_data'       => $table_data,
            'table_html'       => null
        ];
        $this->updatePostMeta($table_id, $data);
    }

    public function makeTableHeader($table_data)
    {
        $headers = [];

        for ($i = 0; $i < (int)$table_data['table']['tc']; $i++) {
            $headers[] = "column_$i";
        }

        return $headers;
    }

    public function getDefaultPlaceholder($value = '')
    {
        $default_margin_padding = [
            "top"    => 0,
            "bottom" => 0,
            "left"   => 0,
            "right"  => 0,
        ];

        return [
            "name"    => __("Text", "ninja-tables"),
            "type"    => "text", // (unique)
            "icon"    => "el-icon-edit-outline",
            "has_pro" => false,
            "value"   => $value,
            "style"   => [
                "fontSize"   => 10,
                "color"      => '',
                "alignment"  => 'center',
                "margin"     => $default_margin_padding,
                "padding"    => $default_margin_padding,
                "fontWeight" => [],
            ],
        ];
    }

    public function tableRawStyling()
    {
        return [
            'trId'            => rand(1000000, 9999999),
            'backgroundColor' => '',
            'rowHeight'       => 50
        ];
    }

    public function makeTableRow($table_data, $importedData = [])
    {
        $rows = [];

        for ($i = 0; $i < $table_data['table']['tr']; $i++) {
            $columns = $table_data['table']['tc'];

            if (count($importedData) > 0) {
                $columns = $importedData[$i];
            }

            $rows[] = [
                'rows'  => $this->makeTableColumn($columns),
                'style' => $this->tableRawStyling(),
            ];
        }

        return $rows;
    }

    public function tableColumnStyling()
    {
        return [
            'tdId'              => rand(10000000, 99999999),
            'backgroundColor'   => '',
            'columnWidth'       => 150,
            'emptyCell'         => '',
            'verticalAlignment' => '',
            'rowspan'           => 1,
            'colspan'           => 1,
            'highlighted'       => [
                'has_pro'     => true,
                'active'      => false,
                'height'      => 10,
                'shadowColor' => '#888',
                'offset_y'    => 10,
                'blur_radius' => 10,
            ]
        ];
    }

    public function makeTableColumn($data)
    {
        $length = $data;

        if (is_array($data)) {
            $length = count($data);
        }

        $columns = [];

        for ($j = 0; $j < $length; $j++) {
            $defaultText = '';

            if (is_array($data)) {
                $data        = array_values($data);
                $defaultText = $data[$j];
            }

            $columns["column_" . $j] = [
                'style'   => $this->tableColumnStyling(),
                'columns' => [
                    [
                        'id'   => rand(100000000, 999999999),
                        'data' => $this->getDefaultPlaceholder($defaultText)
                    ]
                ]
            ];
        };

        return $columns;
    }

    public function getAllInitialData()
    {
        wp_send_json_success([
            'components'        => $this->componentConfig(),
            'settings'          => $this->settingConfig(),
            'responsive'        => $this->responsiveConfig(),
            'ready_made_tables' => $this->templateConfig(),
            'table_data'        => $this->getTableData()
        ], 200);
    }

    public function getTableData()
    {
        return [
            'id'         => '',
            'table_name' => 'Table Name',
            'data'       => [],
            'table_type' => null,
            'table'      => $this->getOtherTableConfig()
        ];
    }

    public function getOtherTableConfig()
    {
        return [
            'tr'    => 1,
            'tc'    => 1,
            'merge' => [
                'history' => (object)[]
            ]
        ];
    }

    public function componentConfig()
    {
        return require_once 'config/component.php';
    }

    public function settingConfig()
    {
        return require_once 'config/setting.php';
    }

    public function responsiveConfig()
    {
        return require_once 'config/responsive.php';
    }

    public function templateConfig()
    {
        return require_once 'config/templates.php';
    }
}
