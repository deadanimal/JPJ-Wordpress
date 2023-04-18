<?php
if ( ! class_exists('NinjaDB\ModelTrait')) {
    include NINJA_TABLES_DIR_PATH . 'includes/libs/ninjadb/src/ModelTrait.php';
}

if ( ! class_exists('NinjaDB\BaseModel')) {
    include NINJA_TABLES_DIR_PATH . 'includes/libs/ninjadb/src/BaseModel.php';
}

// make available ninjaDB as global scope
if ( ! function_exists('ninjaDB')) {
    function ninjaDB($table = false)
    {
        return new NinjaDB\BaseModel($table);
    }
}
