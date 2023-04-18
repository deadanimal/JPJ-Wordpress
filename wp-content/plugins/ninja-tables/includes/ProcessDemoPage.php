<?php namespace NinjaTables\Classes;

use NinjaTable\TableDrivers\NinjaFooTable;

class ProcessDemoPage
{
    public function handleExteriorPages()
    {
        $tableId = null;

        if (isset($_GET['ninjatable_preview'])) {
            $tableId = intval($_GET['ninjatable_preview']);
        }

        if ($tableId) {
            if (ninja_table_admin_role()) {
                do_action('ninja_tables_will_render_table', $tableId);

                wp_enqueue_style('ninja-tables-preview',
                    plugin_dir_url(__DIR__) . "assets/css/ninja-tables-preview.css");

                $this->renderPreview($tableId);
            }
        }
    }

    public function renderPreview($table_id)
    {
        NinjaFooTable::enqueuePublicCss();

        $table = get_post($table_id);

        if ($table) {
            include NINJA_TABLES_DIR_PATH . 'public/views/frameless/show_preview.php';
            exit();
        }
    }

    public function ninjaTableBuilderPreview()
    {
        if (isset($_GET['ninjatable_builder_preview']) && $_GET['ninjatable_builder_preview']) {
            if (ninja_table_admin_role()) {
                wp_enqueue_style('ninja-tables-preview',
                    plugin_dir_url(__DIR__) . "assets/css/ninja-tables-preview.css");

                $table_id = intval($_GET['ninjatable_builder_preview']);
                $table    = get_post($table_id);

                if ($table) {
                    echo ninjaTablesLoadView('public/views/frameless/show_ntb_preview', [
                        'table_id' => $table_id
                    ]);
                    exit;
                }
            }
        }
    }
}
