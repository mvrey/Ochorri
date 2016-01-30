<?php
define ("DEFAULT_PATH", "../../models/");

//Change settings on this function if change in directory tree
function get_file_structure($name, $type, $path=DEFAULT_PATH) {

    $pathName = strtolower(substr($name, 0, 1)).substr($name, 1);
    switch ($type)
        {
        case 'class': $path = $path.$pathName.'/'.$name.'.class.php'; break;
        case 'DAO': $path = $path.$pathName.'/'.$name.'DAO.php'; break;
        }
    return ($path);
}

function require_model ($model, $path=DEFAULT_PATH, $absolute_path=false) {
    require (get_file_structure($model, 'class', $path));
    require (get_file_structure($model, 'DAO', $path));
}

function require_once_model ($model, $path=DEFAULT_PATH, $absolute_path=false) {
    require_once (get_file_structure($model, 'class', $path));
    require_once (get_file_structure($model, 'DAO', $path));
}

function include_model ($model, $path=DEFAULT_PATH, $absolute_path=false) {
    include (get_file_structure($model, 'class', $path));
    include (get_file_structure($model, 'DAO', $path));
}

function include_once_model ($model, $path=DEFAULT_PATH, $absolute_path=false) {
    include_once (get_file_structure($model, 'class', $path));
    include_once (get_file_structure($model, 'DAO', $path));
}

?>
